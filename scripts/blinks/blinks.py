#!/usr/bin/python3

import os
# import the necessary packages
from scipy.spatial import distance as dist
from imutils.video import FileVideoStream
# from imutils.video import VideoStream
from imutils import face_utils
# import numpy as np
import argparse
import imutils
import time
import dlib
import cv2
import timeit
import datetime
import data_access


def eye_aspect_ratio(eye):
	# compute the euclidean distances between the two sets of
	# vertical eye landmarks (x, y)-coordinates
	A = dist.euclidean(eye[1], eye[5])
	B = dist.euclidean(eye[2], eye[4])

	# compute the euclidean distance between the horizontal
	# eye landmark (x, y)-coordinates
	C = dist.euclidean(eye[0], eye[3])

	# compute the eye aspect ratio
	ear = (A + B) / (2.0 * C)

	# return the eye aspect ratio
	return ear

SCRIPT_DIR = os.path.dirname(os.path.realpath(__file__))

# construct the argument parse and parse the arguments
parser = argparse.ArgumentParser(description='Blinks count.')
parser.add_argument('--id', default=None, help='Result ID taken from the QSM plugin')
parser.add_argument("--video", default=None, help="path to input video file")

argum = parser.parse_args()

# define two constants, one for the eye aspect ratio to indicate
# blink and then a second constant for the number of consecutive
# frames the eye must be below the threshold
EYE_AR_THRESH = 0.269687 # Originally 0.3
EYE_AR_CONSEC_FRAMES = 3

# initialize the frame counters and the total number of blinks
COUNTER = 0
TOTAL = 0

# initialize dlib's face detector (HOG-based) and then create
# the facial landmark predictor
print("[Blinks] Loading facial landmark predictor...")
detector = dlib.get_frontal_face_detector()
predictor = dlib.shape_predictor(SCRIPT_DIR + "/shape_predictor_68_face_landmarks.dat")
# predictor = dlib.shape_predictor(args["shape_predictor"])

# grab the indexes of the facial landmarks for the left and
# right eye, respectively
(lStart, lEnd) = face_utils.FACIAL_LANDMARKS_IDXS["left_eye"]
(rStart, rEnd) = face_utils.FACIAL_LANDMARKS_IDXS["right_eye"]

# Initialize timer for eyes open and close.
openEyesStartTime = 0
closeEyesStartTime = 0

# Initialize list variable for saving the blink data.
blink_list_data = []
openedTimeDifference = 0
closedTimeDifference = 0

# Frame Timestamp
openedTimestamp = 0;
closedTimestamp = 0;

# start the thread
print("[Blinks] Counting blinks start...")
cap = cv2.VideoCapture(argum.video)  # Video file source

# time start
data_access.DataAccess().mark_start_time(argum.id)

print("------------------------------------");
print("Frame Time |   Ear   <  THSH   Count");
print("------------------------------------");

while cap.isOpened():  # True:
    ret, frame = cap.read()

    # Is video capture done?
    if frame is None:
        # Yes. Break out of loop.
        break

    frame = imutils.resize(frame, width=450)
    gray  = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    msec  = round(cap.get(cv2.CAP_PROP_POS_MSEC))

    # detect faces in the grayscale frame
    rects = detector(gray, 0)

    # loop over the face detections
    for rect in rects:
        # determine the facial landmarks for the face region, then
        # convert the facial landmark (x, y)-coordinates to a NumPy
        # array
        shape = predictor(gray, rect)
        shape = face_utils.shape_to_np(shape)

        # extract the left and right eye coordinates, then use the
        # coordinates to compute the eye aspect ratio for both eyes
        leftEye = shape[lStart:lEnd]
        rightEye = shape[rStart:rEnd]
        leftEAR = eye_aspect_ratio(leftEye)
        rightEAR = eye_aspect_ratio(rightEye)

        # average the eye aspect ratio together for both eyes
        ear = (leftEAR + rightEAR) / 2.0

        # compute the convex hull for the left and right eye, then
        # visualize each of the eyes
        leftEyeHull = cv2.convexHull(leftEye)
        rightEyeHull = cv2.convexHull(rightEye)
        cv2.drawContours(frame, [leftEyeHull], -1, (0, 255, 0), 1)
        cv2.drawContours(frame, [rightEyeHull], -1, (0, 255, 0), 1)

        # check to see if the eye aspect ratio is below the blink
        # threshold, and if so, increment the blink frame counter
        if ear < EYE_AR_THRESH:
            COUNTER += 1

            # Start timer when eyes closed.
            if closeEyesStartTime == 0:
                closeEyesStartTime = msec

            # When eyes closed, get the difference from openEyesStartTime.
            openedTimeDifference = (msec - openEyesStartTime)
            openEyesStartTime = 0

            closedTimestamp = msec
            openedTimestamp = 0

        # otherwise, the eye aspect ratio is not below the blink threshold
        else:
            # if the eyes were closed for a sufficient number of then increment the total number of blinks
            if COUNTER >= EYE_AR_CONSEC_FRAMES:
                TOTAL += 1

                # When eyes opened, get the difference from closeEyesStartTime.
                closedTimeDifference = (msec - closeEyesStartTime)
                closeEyesStartTime = 0

                closedTimestamp = 0;
            else:
                # Start timer when eyes opened.
                if openEyesStartTime == 0:
                    openEyesStartTime = msec

                openedTimestamp = msec

            # reset the eye frame counter
            COUNTER = 0

        print("%10d | %1.5f < %1.5f %5d" % (msec, ear, EYE_AR_THRESH, TOTAL));

        blink_list_data.append({
            "blink": TOTAL,
            "count": COUNTER,
            "eyesOpened": openedTimestamp,
            "eyesClosed": closedTimestamp,
            "time": msec
        })


cap.release()
cv2.destroyAllWindows()

# Save list of blink data to database.
print("[Blinks] Saving to the database...")
data_access.DataAccess().save_blinks(argum.id, blink_list_data)

# time ebd
data_access.DataAccess().mark_end_time(argum.id)
print("[Blinks] Done!")