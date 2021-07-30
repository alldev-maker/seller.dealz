import argparse


def arguments():
    """
    Initialize argument parser and required video parameter.
    :return: The parsed argument object.
    """
    ap = argparse.ArgumentParser()
    ap.add_argument("-v", "--video", required=True, help="Path to the video.")

    return vars(ap.parse_args())


def get_eye_classifier():
    """
    Get the filename and path for the eye classifier.
    :return: The string filename and path of eye classifier.
    """
    return "classifiers/haarcascade_eye.xml"


def get_face_classifier():
    """
    Get the filename and path for the face classifier.
    :return: The string filename and path for the face classifier.
    """
    return "classifiers/haarcascade_frontalface_default.xml"


def get_distance_reference():
    """
    The default distance reference to properly detect the slouch of the user.
    :return: The float value of distance reference.
    """
    return 163.51758315239374


def get_thoracolumbar_tolerance():
    """
    Use to adjust the sensitivity of slouch detection. Sane values will 0.05 - 0.3.
    :return: The float value of thoracolumbar tolerance.
    """
    return 0.10
