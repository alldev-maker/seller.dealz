#!/usr/bin/python3

import application
import argparse

# construct the argument parse and parse the arguments
parser = argparse.ArgumentParser(description='Slouch and Tilt count.')
parser.add_argument('--id', default=None, help='Result ID taken from the QSM plugin')
parser.add_argument("--video", default=None, help="path to input video file")

argum = parser.parse_args()

slouch = application.SlouchApp(argum)

slouch.open()