#!/usr/bin/python3
import os
import cv2
import numpy as np
from keras.models import load_model
from statistics import mode
from utils.datasets import get_labels
# from utils.inference import detect_faces
from utils.inference import draw_text
from utils.inference import draw_bounding_box
from utils.inference import apply_offsets
# from utils.inference import load_detection_model
from utils.preprocessor import preprocess_input
import argparse
import time
import datetime
from data_access import DataAccess

# Set custom arguments to enable passing of video directory string as parameter.
parser = argparse.ArgumentParser(description='Emotion recognition.')
parser.add_argument('--id', default=None, help='Result ID taken from the QSM plugin')
parser.add_argument("--webm", default=None, help="Directory of the webm video to be processed by emotion recognition.")

argum = parser.parse_args()

if os.path.isfile(parser.parse_args().webm):   

    USE_WEBCAM = False  # If false, loads video file source

    # Get the directory of this script.
    SCRIPT_DIR = os.path.dirname(os.path.realpath(__file__))

    # parameters for loading data and images
    emotion_model_path = SCRIPT_DIR + '/models/emotion_model.hdf5'
    emotion_labels = get_labels('fer2013')

    # hyper-parameters for bounding boxes shape
    frame_window = 10
    emotion_offsets = (20, 40)

    # loading models
    face_cascade = cv2.CascadeClassifier(SCRIPT_DIR + '/models/haarcascade_frontalface_default.xml')
    emotion_classifier = load_model(emotion_model_path)

    # getting input model shapes for inference
    emotion_target_size = emotion_classifier.input_shape[1:3]

    # starting lists for calculating modes
    emotion_window = []

    # starting video streaming
    # cv2.namedWindow('window_frame')

    DataAccess().mark_start_time(argum.id)

    print("[Emotion] Recognition Start...")
    print("");

    # Select video or webcam feed
    cap = None
    if USE_WEBCAM:
        cap = cv2.VideoCapture(0)  # Webcam source
    else:
        cap = cv2.VideoCapture(argum.webm)  # Video file source

    # Initialize variable for emotion list
    emotion_list_data = []

    print("------------------------------------")
    print("Frame Time | Emotion    | Prob.     ")
    print("------------------------------------")

    while cap.isOpened():  # True:
        ret, bgr_image = cap.read()

        # Is video capture done?
        if bgr_image is None:
            # Yes. Break out of loop.
            break

        gray_image = cv2.cvtColor(bgr_image, cv2.COLOR_BGR2GRAY)
        rgb_image = cv2.cvtColor(bgr_image, cv2.COLOR_BGR2RGB)

        faces = face_cascade.detectMultiScale(gray_image, scaleFactor=1.1, minNeighbors=5, minSize=(30, 30), flags=cv2.CASCADE_SCALE_IMAGE)

        for face_coordinates in faces:

            x1, x2, y1, y2 = apply_offsets(face_coordinates, emotion_offsets)
            gray_face = gray_image[y1:y2, x1:x2]
            try:
                gray_face = cv2.resize(gray_face, emotion_target_size)
            except:
                continue

            gray_face = preprocess_input(gray_face, True)
            gray_face = np.expand_dims(gray_face, 0)
            gray_face = np.expand_dims(gray_face, -1)
            emotion_prediction = emotion_classifier.predict(gray_face)
            emotion_probability = np.max(emotion_prediction)
            emotion_label_arg = np.argmax(emotion_prediction)
            emotion_text = emotion_labels[emotion_label_arg]
            emotion_window.append(emotion_text)

            if len(emotion_window) > frame_window:
                emotion_window.pop(0)
            try:
                emotion_mode = mode(emotion_window)
            except:
                continue

            if emotion_text == 'angry':
                color = emotion_probability * np.asarray((255, 0, 0))
            elif emotion_text == 'sad':
                color = emotion_probability * np.asarray((0, 0, 255))
            elif emotion_text == 'happy':
                color = emotion_probability * np.asarray((255, 255, 0))
            elif emotion_text == 'surprise':
                color = emotion_probability * np.asarray((0, 255, 255))
            else:
                color = emotion_probability * np.asarray((0, 255, 0))

            msec = round(cap.get(cv2.CAP_PROP_POS_MSEC))

            emotion_list_data.append({
                "emotion": emotion_label_arg,
                "prediction": str(emotion_prediction),
                "probability": str(emotion_probability),
                "time": msec
            })

            print("%10d | %10s | %10.6f" %
                (
                    msec,
                    emotion_text,
                    emotion_probability,
                )
            )

            color = color.astype(int)
            color = color.tolist()

            draw_bounding_box(face_coordinates, rgb_image, color)
            draw_text(face_coordinates, rgb_image, emotion_mode, color, 0, -45, 1, 1)

        bgr_image = cv2.cvtColor(rgb_image, cv2.COLOR_RGB2BGR)
        # cv2.imshow('window_frame', bgr_image)

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

    print("[Emotions] Saving detected emotions...")

    DataAccess().save_emotion(argum.id, emotion_list_data)
    DataAccess().mark_end_time(argum.id)

    print("[Emotions] Save completed.")

else:
    
    print('File', '[' + parser.parse_args().webm + ']', 'does not exists.')
