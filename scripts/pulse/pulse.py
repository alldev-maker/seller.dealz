#!/usr/bin/python3
import os
from lib.device import Camera
from lib.processors_noopenmdao import findFaceGetPulse
from lib.interface import plotXY, imshow, waitKey, destroyWindow
# from cv2 import moveWindow
import cv2
import argparse
# import numpy as np
import datetime
# TODO: work on serial port comms, if anyone asks for it
# from serial import Serial
import socket
import sys
import time
import data_access

# Get the directory of this script.

class getPulseApp(object):
    detected_flag = False
    """
    Python application that finds a face in a webcam stream, then isolates the
    forehead.

    Then the average green-light intensity in the forehead region is gathered
    over time, and the detected person's pulse is estimated.
    """

    def __init__(self, args):
        # Imaging device - must be a connected camera (not an ip camera or mjpeg
        # stream)
        serial = args.serial
        baud = args.baud
        self.send_serial = False
        self.send_udp = False
        if serial:
            self.send_serial = True
            if not baud:
                baud = 9600
            else:
                baud = int(baud)
            self.serial = Serial(port=serial, baudrate=baud)

        udp = args.udp
        if udp:
            self.send_udp = True
            if ":" not in udp:
                ip = udp
                port = 5005
            else:
                ip, port = udp.split(":")
                port = int(port)
            self.udp = (ip, port)
            self.sock = socket.socket(socket.AF_INET, # Internet
                 socket.SOCK_DGRAM) # UDP

        self.camera = ''
        self.cameras = []
        self.selected_cam = 0
        # for i in range(3):
        #     camera = Camera(camera=i)  # first camera by default
        #     if camera.valid or not len(self.cameras):
        #         self.cameras.append(camera)
        #     else:
        #         break

        # Is --webm argument present?
        if parser.parse_args().video is not None:
            # Yes.
            # Process webm video.
            camera = Camera(parser.parse_args().video)
            if camera.valid or not len(self.cameras):
                self.camera = camera
                self.cameras.append(camera)

        self.w, self.h = 0, 0
        self.pressed = 0
        # Containerized analysis of received image frames (an openMDAO assembly)
        # is defined next.

        # This assembly is designed to handle all image & signal analysis,
        # such as face detection, forehead isolation, time series collection,
        # heart-beat detection, etc.

        # Basically, everything that isn't communication
        # to the camera device or part of the GUI
        self.processor = findFaceGetPulse(bpm_limits=[50, 160],
                                          data_spike_limit=2500.,
                                          face_detector_smoothness=10.)

        # Init parameters for the cardiac data plot
        self.bpm_plot = False
        self.plot_title = "Data display - raw signal (top) and PSD (bottom)"

        # Maps keystrokes to specified methods
        # (A GUI window must have focus for these to work)
        self.key_controls = {"s": self.toggle_search, "d": self.toggle_display_plot, "c": self.toggle_cam}  # "f": self.write_csv

        # Init file for BPM output.
        # self.filename = "bpm.csv"
        # self.file = open(self.filename, "w")

        # Init empty list to contain bpm data and time.
        self.bpm_list_data = []

    def toggle_cam(self):
        if len(self.cameras) > 1:
            self.processor.find_faces = True
            self.bpm_plot = False
            destroyWindow(self.plot_title)
            self.selected_cam += 1
            self.selected_cam = self.selected_cam % len(self.cameras)

    # def write_csv(self):
    #     """
    #     Writes current data to a csv file
    #     """
    #     fn = "Webcam-pulse" + str(datetime.datetime.now())
    #     fn = fn.replace(":", "_").replace(".", "_")
    #     data = np.vstack((self.processor.times, self.processor.samples)).T
    #     np.savetxt(fn + ".csv", data, delimiter=',')
    #     print("Writing csv")

    def toggle_search(self):
        """
        Toggles a motion lock on the processor's face detection component.

        Locking the forehead location in place significantly improves
        data quality, once a forehead has been sucessfully isolated.
        """
        # state = self.processor.find_faces.toggle()
        state = self.processor.find_faces_toggle()
        print("Face Detection Lock", not state)

    def toggle_display_plot(self):
        """
        Toggles the data display.
        """
        if self.bpm_plot:
            print("BPM plot disabled.")
            self.bpm_plot = False
            destroyWindow(self.plot_title)
        else:
            print("BPM plot enabled.")
            if self.processor.find_faces:
                self.toggle_search()
            self.bpm_plot = True
            self.make_bpm_plot()
            # moveWindow(self.plot_title, self.w, 0)

    def make_bpm_plot(self):
        """
        Creates and/or updates the data display
        """
        # plotXY([[self.processor.times,
        #          self.processor.samples],
        #         [self.processor.freqs,
        #          self.processor.fft]],
        #        labels=[False, True],
        #        showmax=[False, "bpm"],
        #        label_ndigits=[0, 0],
        #        showmax_digits=[0, 1],
        #        skip=[3, 3],
        #        name=self.plot_title,
        #        bg=self.processor.slices[0])

        # Write to file the bpm.
        #print("BPM:", self.processor.bpm, "Times:", self.processor.times);
        #if self.processor.bpm != 0:
        
        msec = round(self.camera.cam.get(cv2.CAP_PROP_POS_MSEC))
        
        self.bpm_list_data.append({
            "bpm": self.processor.bpm,
            "time": msec
        })

        print("%10d | %7d " %
            (
                msec,
                self.processor.bpm,
            )
        )
        
        # self.file.write(str(self.processor.bpm) + ", " + str(time.time() * 1000) + "\n")

    def key_handler(self):
        """
        Handle keystrokes, as set at the bottom of __init__()

        A plotting or camera frame window must have focus for keypresses to be
        detected.
        """

        self.pressed = waitKey(25) & 255  # wait for keypress for 10 ms
        if self.pressed == 27:  # exit program on 'esc'
            print("Exiting...")
            for cam in self.cameras:
                cam.cam.release()
            if self.send_serial:
                self.serial.close()

            # Close file.
            # self.file.close()

            sys.exit()

        for key in self.key_controls.keys():
            if chr(self.pressed) == key:
                self.key_controls[key]()

    def main_loop(self):
        try:
            """
                    Single iteration of the application's main loop.
                    """
            # Get current image frame from the camera
            frame = self.cameras[self.selected_cam].get_frame()
                        
            self.h, self.w, _c = frame.shape

            # display unaltered frame
            # imshow("Original",frame)

            # set current image frame to the processor's input
            self.processor.frame_in = frame
            # process the image frame to perform all needed analysis
            # Undetected forehead will have coordinates of [1, 1, 0, 0].
            undetected_forehead = [1, 1, 0, 0]

            # Get coordinates of forehead from processors run.
            forehead = self.processor.run(self.selected_cam)

            # Is forehead detected?
            if not self.detected_flag and forehead != undetected_forehead:
                # Yes.
                # Set detected forehead flag to True.
                # Auto lock-in forehead and bpm plotting.
                #print("[Pulse] Forehead detected... locking in forehead.")
                self.detected_flag = True
                self.toggle_search()
                self.toggle_display_plot()

            # collect the output frame for display
            # output_frame = self.processor.frame_out

            # show the processed/annotated output frame
            # imshow("Processed", output_frame)


            # create and/or update the raw data display if needed
            if self.bpm_plot:
                self.make_bpm_plot()

            if self.send_serial:
                self.serial.write(str(self.processor.bpm) + "\r\n")

            if self.send_udp:
                self.sock.sendto(str(self.processor.bpm), self.udp)

            # handle any key presses
            self.key_handler()

            return True

        except AttributeError as e:
            print(e)

        return False


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Webcam pulse detector.')
    parser.add_argument('--id', default=None, help='Result ID taken from the QSM plugin')
    parser.add_argument('--serial', default=None,
                        help='serial port destination for bpm data')
    parser.add_argument('--baud', default=None,
                        help='Baud rate for serial transmission')
    parser.add_argument('--udp', default=None,
                        help='udp address:port destination for bpm data')
    parser.add_argument('--video', default=None,
                        help='The video file name to be processed.')

    args = parser.parse_args()
    
    
    if os.path.isfile(parser.parse_args().video):    
    
        App = getPulseApp(args)

        loopFlag = True

        data_access.DataAccess().mark_start_time(args.id)

        print("[Pulse] Start BPM plot.")
        print("")

        print("--------------------")
        print("Frame Time | BPM    ")
        print("--------------------")


        while loopFlag:
            loopFlag = App.main_loop()

        print("[Pulse] Saving BPM data with Result ID [" + args.id + "].")

        data_access.DataAccess().save_bpm(args.id, App.bpm_list_data)
        data_access.DataAccess().mark_end_time(args.id);

        print("[Pulse] End BPM plot.")
    
    else:
    
        print('[Pulse] File', '[' + parser.parse_args().video + ']', 'does not exists.')
