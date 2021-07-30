#!/usr/bin/python3
import os
import cv2
import numpy
import argparse
import time
import datetime
import data_access

# Get the directory of this script.
SCRIPT_DIR = os.path.dirname(os.path.realpath(__file__))

def show_image_with_data(frame, blinks, irises, err=None):
    """
    Helper function to draw points on eyes and display frame
    :param frame: image to draw on
    :param blinks: number of blinks
    :param irises: array of points with coordinates of irises
    :param err: for displaying current error in Lucas-Kanade tracker
    :return:
    """
    font = cv2.FONT_HERSHEY_SIMPLEX
    if err:
        cv2.putText(frame, str(err), (20, 450), font, 1, (255, 255, 255), 1, cv2.LINE_AA)

    cv2.putText(frame, 'blinks: ' + str(blinks), (10, 30), font, 1, (255, 255, 255), 1, cv2.LINE_AA)

    for w, h in irises:
        cv2.circle(frame, (w, h), 2, (0, 255, 0), 2)

    # cv2.imshow('Eyeris detector', frame)


class ImageSource(object):
    """
    Returns frames from camera
    """
    def __init__(self, args):
        self.capture = cv2.VideoCapture(args.webm)

    def get_current_frame(self, gray=False):
        ret, frame = self.capture.read()
        frame = cv2.flip(frame, 1)
        if not gray:
            return frame
        return cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

    def release(self):
        self.capture.release()


class CascadeClassifier:
    """
    This classifier is trained by default in OpenCV
    """
    def __init__(self, glasses=True):
        if glasses:
            self.eye_cascade = cv2.CascadeClassifier(SCRIPT_DIR + '/haar/haarcascade_eye_tree_eyeglasses.xml')
        else:
            self.eye_cascade = cv2.CascadeClassifier(SCRIPT_DIR + '/haar/haarcascade_eye.xml')

    def get_irises_location(self, frame_gray):
        eyes = self.eye_cascade.detectMultiScale(frame_gray, 1.3, 5)  # if not empty - eyes detected
        irises = []

        for (ex, ey, ew, eh) in eyes:
            iris_w = int(ex + float(ew / 2))
            iris_h = int(ey + float(eh / 2))
            irises.append([numpy.float32(iris_w), numpy.float32(iris_h)])

        return numpy.array(irises)


class LucasKanadeTracker:
    """
    Lucaas-Kanade tracker used for minimizing cpu usage and blinks counter
    """
    def __init__(self, blink_threshold=9):
        # Parameters for lucas kanade optical flow
        self.lk_params = dict(winSize=(15, 15), maxLevel=2, criteria=(cv2.TERM_CRITERIA_EPS | cv2.TERM_CRITERIA_COUNT, 10, 0.03))
        self.blink_threshold = blink_threshold

    def track(self, old_gray, gray, irises, blinks, blink_in_previous):
        lost_track = False
        p1, st, err = cv2.calcOpticalFlowPyrLK(old_gray, gray, irises, None, **self.lk_params)

        if st[0][0] == 0 or st[1][0] == 0:  # lost track on eyes
            lost_track = True
            blink_in_previous = False
        elif err[0][0] > self.blink_threshold or err[1][0] > self.blink_threshold:  # high error rate in klt tracking
            lost_track = True

            if not blink_in_previous:
                blinks += 1
                blink_in_previous = True
        else:
            blink_in_previous = False
            irises = []
            for w, h in p1:
                irises.append([w, h])
            irises = numpy.array(irises)
        return irises, blinks, blink_in_previous, lost_track


class EyerisDetector:
    """
    Main class which use image source, classifier and tracker to estimate iris postion
    Algorithm used in detector is designed for one person (with two eyes)
    It can detect more than two eyes, but it tracks only two
    """
    def __init__(self, result_id, image_source, classifier, tracker):
        self.result_id = result_id
        self.tracker = tracker
        self.classifier = classifier
        self.image_source = image_source
        self.irises = []
        self.blink_in_previous = False
        self.blinks = 0
        self.eyeris_data_list = []

    def run(self):
        data_access.DataAccess().mark_start_time(self.result_id);
        print("[Eyeris] Running...")

        print("")

        print("------------------------------------")
        print("Frame Time | Width      | Height    ")
        print("------------------------------------")

        k = cv2.waitKey(30) & 0xff

        while k != 27:  # ESC
            frame = self.image_source.get_current_frame()
            msec = round(self.image_source.capture.get(cv2.CAP_PROP_POS_MSEC))

            # Did frame for video already closed?
            if frame is None:
                # Yes.
                # Break out of WHILE loop.
                break

            gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

            if len(self.irises) >= 2:  # irises detected, track eyes
                track_result = self.tracker.track(old_gray, gray, self.irises, self.blinks, self.blink_in_previous)
                self.irises, self.blinks, self.blink_in_previous, lost_track = track_result

                if lost_track:
                    self.irises = self.classifier.get_irises_location(gray)
                    print("%10d | ----.----- | ----.-----" %
                        (
                            msec,
                        )
                    )

                    self.eyeris_data_list.append({
                        "iris_w": 0,
                        "iris_h": 0,
                        "blink": self.blinks,
                        "time": msec
                    })

                else:
                    print("%10d | %10.5f | %10.5f" %
                        (
                            msec,
                            self.irises[0][0],
                            self.irises[0][1],
                        )
                    )

                    self.eyeris_data_list.append({
                        "iris_w": self.irises[0][0],
                        "iris_h": self.irises[0][1],
                        "blink": self.blinks,
                        "time": msec
                    })

            else:  # cannot track for some reason -> find irises
                self.irises = self.classifier.get_irises_location(gray)

                print("%10d | ----.----- | ----.-----" %
                    (
                        msec,
                    )
                )

                self.eyeris_data_list.append({
                    "iris_w": 0,
                    "iris_h": 0,
                    "blink": self.blinks,
                    "time": msec
                })

            show_image_with_data(frame, self.blinks, self.irises)
            k = cv2.waitKey(30) & 0xff
            old_gray = gray.copy()

        self.image_source.release()
        cv2.destroyAllWindows()

        print("------------------------------------")
        print("")
        print("[Eyeris] Scanning finished.")
        print("[Eyeris] Saving data...")

        # Save list of eyeris data to database.
        data_access.DataAccess().save_eyeris(self.result_id, self.eyeris_data_list)
        data_access.DataAccess().mark_end_time(self.result_id);
        print("Done.")


        
parser = argparse.ArgumentParser(description='Iris detector.')
parser.add_argument('--id', default=None, help='Result ID taken from the QSM plugin')
parser.add_argument('--webm', default=None, help='The directory of the webm video to be processed.')

args = parser.parse_args()

if os.path.isfile(args.webm):   
    eyeris_detector = EyerisDetector(result_id=args.id, image_source=ImageSource(args), classifier=CascadeClassifier(), tracker=LucasKanadeTracker())
    eyeris_detector.run()
else:
    print('[Eyeris] File', '[' + args.webm + ']', 'does not exists.')
