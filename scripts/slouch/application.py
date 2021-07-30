import os
import helper
from math import sqrt, atan
import cv2
import time
import datetime
import data_access

SCRIPT_DIR = os.path.dirname(os.path.realpath(__file__))

class SlouchApp:
    def __init__(self, arguments):
        self.arguments = arguments
        self.use_webcam = False
        self.start_detect_slouch = False
        self.eye_classifier = SCRIPT_DIR + '/' + helper.get_eye_classifier()
        self.face_classifier = SCRIPT_DIR + '/' + helper.get_face_classifier()
        self.distance_reference = helper.get_distance_reference()
        self.thoracolumbar_tolerance = helper.get_thoracolumbar_tolerance()
        self.slouch_list_data = []
        self.start_time = 0

        self.SLOUCH_COUNT = 0
        self.TILT_COUNT   = 0

        # Defaults. Adjust when needed.
        self.tilt_angle = 0.33

        # These will be used to compute for time difference for slouching and sitting straight.
        self.is_slouching = 0
        self.is_straighten = 0

        # Timestamp for slouch and straight
        self.ts_body_slouch   = 0;
        self.ts_body_straight = 0;

        # These will be used to save the time difference for slouching and sitting straight.
        self.slouched_time_difference = 0
        self.straighten_time_difference = 0

        # These will be used to compute for time difference for head tilted and head straight.
        self.is_head_tilted = 0
        self.is_head_straight = 0

        # Timestamp for tilted and straight
        self.ts_head_tilted   = 0;
        self.ts_head_straight = 0;

        # These will be used to save the time difference for head tilted and head straight.
        self.head_tilted_time_difference = 0
        self.head_straight_time_difference = 0

        # Initialize face distance and head tilt to 0.
        self.distance = 0
        self.angle = 0

        # Did user intend to use webcam?
        if not self.use_webcam:
            # No.
            # Use video passed in the parameter.
            #args = helper.arguments()
            self.video = cv2.VideoCapture(self.arguments.video)
        else:
            # Yes.
            # Set self.video to 0.
            self.video = cv2.VideoCapture(0)

    def open(self):
        """
        Open the webcam or video.
        """
        # Timer in seconds.
        #self.start_time = time.time()

        # Mark start time.
        data_access.SlouchDataAccess().mark_start_time(self.arguments.id);

        print("[Slouch] Detection start.")
        print("")
        print("------------------------------------------------------------------------------------------------------")
        print("            |               Position               |                       Head                       ")
        print(" Frame Time |--------------------------------------|--------------------------------------------------")
        print("            | Count      | Slouch     | Straight   | Angle      | Count      | Tilted     | Straight  ")
        print("------------------------------------------------------------------------------------------------------")

        # Keep showing video.
        while self.video.isOpened():
            # Read the video.
            frame_read_flag, bgr_image = self.video.read()

            # Are there any frames yet?
            if bgr_image is None:
                # No.
                # Stop video capture.
                break

            # Does user wanted to use webcam?
            if self.use_webcam:
                # Yes.
                # Display quit option.
                cv2.putText(bgr_image, "Press Q to quit.", (10, 15), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 255), 1)

                # Did user locked in their proper posture?
                if self.start_detect_slouch:
                    # Yes.
                    # Start detecting slouch.
                    self.process_slouch_detection(bgr_image)
                else:
                    # No.
                    # Show controls for setting distance reference.
                    cv2.putText(bgr_image, "Sit properly, press C to lock in your proper sitting position", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1)
                    cv2.putText(bgr_image, "Press D to use default sitting position", (10, 50), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1)

                # Display webcam/video output to window.
                cv2.imshow("Slouch Detection", bgr_image)

                key_press = cv2.waitKey(1) & 0xFF

                # Did user pressed `q`?
                if key_press == ord("q"):
                    # Yes.
                    # Break out of loop to stop video capture.
                    break
                elif key_press == ord("c"):
                    if not self.start_detect_slouch:
                        detected_face_flag, face = self.detect_face(bgr_image)

                        # Are there any faces detected?
                        if detected_face_flag:
                            # Yes.
                            # Set distance reference.
                            self.distance_reference = self.determine_camera_face_distance(face)
                            print("Using user's current distance")
                        else:
                            print("Using default distance reference")

                    self.start_detect_slouch = True
                elif key_press == ord("d"):
                    if not self.start_detect_slouch:
                        self.start_detect_slouch = True
            else:
                # No.

                # First, get the first frame for reference.
                if not self.start_detect_slouch:
                    detected_face_flag, face = self.detect_face(bgr_image)

                    # Are there any faces detected?
                    if detected_face_flag:
                        # Yes.
                        # Set distance reference.
                        self.distance_reference = self.determine_camera_face_distance(face)

                    self.start_detect_slouch = True

                # Start slouch detection.
                self.process_slouch_detection(bgr_image)

        # Release the video capture and destroy all windows if user opted to stop the video capture.
        self.video.release()
        cv2.destroyAllWindows()

        print("")
        print("[Slouch] Saving the result to the database...")

        # Save slouch data to database.
        data_access.SlouchDataAccess().save_slouch(self.arguments.id, self.slouch_list_data)

        # Mark end time.
        data_access.SlouchDataAccess().mark_end_time(self.arguments.id);

        print("[Slouch] Detection done.")

    def process_slouch_detection(self, bgr_image):
        # Detect face.
        detected_face_flag, face = self.detect_face(bgr_image)

        msec = round(self.video.get(cv2.CAP_PROP_POS_MSEC))

        # Are there any face detected?
        if detected_face_flag:
            # Yes.
            # Determine distance of face from the camera.
            self.distance = self.determine_camera_face_distance(face)

            # Detect eyes to determine head angle or if head is tilted.
            x, y, w, h = face
            face_image = bgr_image[y:y + h, x:x + w]
            head_tilt_detected_flag, self.angle = self.determine_head_angle(face_image)

            slouch_flag, head_tilt_flag = self.is_user_slouching()

            # Is user slouching?
            if slouch_flag:
                # Yes.
                self.SLOUCH_COUNT += 1

                self.ts_body_slouch   = msec
                self.ts_body_straight = 0

                # Start timer when user slouched.
                if self.is_slouching == 0:
                    self.is_slouching = msec
                    self.slouched_time_difference = 0

                # When user started slouching, get the difference from is_straighten.
                if self.is_straighten != 0:
                    self.straighten_time_difference = (msec - self.is_straighten)
                    self.is_straighten = 0

                # Show texts if user opted to use webcam.
                if self.use_webcam:
                    cv2.putText(bgr_image, "You are slouching", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1)
            else:
                # No.
                self.SLOUCH_COUNT = 0

                self.ts_body_slouch   = 0
                self.ts_body_straight = msec

                # Start timer when user sat properly.
                if self.is_straighten == 0:
                    self.is_straighten = msec
                    self.straighten_time_difference = 0

                # When user stopped slouching, get the difference from is_slouching.
                if self.is_slouching != 0:
                    self.slouched_time_difference = (msec - self.is_slouching)
                    self.is_slouching = 0

                # Show texts if user opted to use webcam.
                if self.use_webcam:
                    cv2.putText(bgr_image, "You are sitting properly", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 1)

            # Was the head tilting properly determined?
            if head_tilt_detected_flag:
                # Yes.

                # Was head tilting?
                if head_tilt_flag:
                    # Yes.
                    self.TILT_COUNT += 1

                    self.ts_head_tilted   = msec
                    self.ts_head_straight = 0

                    # Start timer when user's head tilted.
                    if self.is_head_tilted == 0:
                        self.is_head_tilted = msec
                        self.head_tilted_time_difference = 0

                    # When user's head tilted, get the difference from is_head_straight.
                    if self.is_head_straight != 0:
                        self.head_straight_time_difference = (msec - self.is_head_straight)
                        self.is_head_straight = 0

                    # Show texts if user opted to use webcam.
                    if self.use_webcam:
                        cv2.putText(bgr_image, "Head tilting", (10, 50), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1)
                else:
                    # No.
                    self.TILT_COUNT = 0

                    self.ts_head_tilted   = 0
                    self.ts_head_straight = msec

                    # Start timer when user's head straightened.
                    if self.is_head_straight == 0:
                        self.is_head_straight = msec
                        self.head_straight_time_difference = 0

                    # When user's head straightened, get the difference from is_head_tilted.
                    if self.is_head_tilted != 0:
                        self.head_tilted_time_difference = (msec - self.is_head_tilted)
                        self.is_head_tilted = 0

                    # Show texts if user opted to use webcam.
                    if self.use_webcam:
                        cv2.putText(bgr_image, "Head straight-up", (10, 50), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (255, 0, 0), 1)
            else:
                # Unable to detect

                 self.ts_head_tilted   = 0
                 self.ts_head_straight = 0


            print("%11d | %10d | %10d | %10d | %10.3f | %10d | %10d | %10d" %
                (
                    msec,
                    self.SLOUCH_COUNT,
                    self.ts_body_slouch,
                    self.ts_body_straight,
                    self.angle if isinstance(self.angle, float) else 999999.999,
                    self.TILT_COUNT,
                    self.ts_head_tilted,
                    self.ts_head_straight
                )
             )


            # Push slouching data to dictionary.
            self.slouch_list_data.append({
                "slouch_flag"  : slouch_flag,
                "slouch_count" : self.SLOUCH_COUNT,
                "body_slouch"  : self.ts_body_slouch,
                "body_straight": self.ts_body_straight,
                "tilt_flag"    : head_tilt_flag,
                "tilt_count"   : self.TILT_COUNT,
                "tilt_angle"   : self.angle,
                "head_tilted"  : self.ts_head_tilted,
                "head_straight": self.ts_head_straight,
                "time": msec
            })

    def detect_face(self, bgr_image):
        """
        Detect face using face classifier.
        :param bgr_image: The video capture where the face will be detected.
        :return: The result whether the face is found and the face found or an error message.
        """
        # Load face classifier.
        face_classifier = cv2.CascadeClassifier(self.face_classifier)

        # Detect face in the image.
        faces = face_classifier.detectMultiScale(image=bgr_image, scaleFactor=1.1, minNeighbors=5, minSize=(40, 40),
                                                 flags=cv2.CASCADE_SCALE_IMAGE)

        try:
            # Assume largest face is the subject.
            # Index 0 is largest face.
            face = faces[0]

            return True, face
        except IndexError:
            # No face found. Return false and error message.
            return False, "No faces found."

    def determine_camera_face_distance(self, face):
        """
        Determine the distance of the face from the camera.
        :param face: The detected face.
        :return:
        """
        # Unpack the face position (x, y) and dimensions (w, h) from found face.
        x, y, w, h = face

        # Calculate the distance.
        distance = sqrt(y**2 + w**2)

        return distance

    def determine_head_angle(self, face_image):
        """
        Detect eyes to determine the angle of the head tilting.
        :return: The angle of the head tilting.
        """
        # Load eye classifier.
        eye_classifier = cv2.CascadeClassifier(self.eye_classifier)

        # Detect eyes.
        eyes = eye_classifier.detectMultiScale(face_image)

        # Are there any eyes detected and is it more than 1 eye detected?
        if len(eyes) > 1:
            # Yes.
            # Read only the first 2 eyes detected.
            left_eye = eyes[0]
            right_eye = eyes[1]

            slope = (left_eye[1] - right_eye[1] / left_eye[0] - right_eye[0])
            angle = abs(atan(slope))

            return True, angle
        else:
            return False, None

    def is_user_slouching(self):
        """
        Check whether user is slouching.
        :return: Boolean value to determine slouching.
        """
        c_min = self.distance_reference * (1.0 - self.thoracolumbar_tolerance)
        c_max = self.distance_reference * (1.0 + self.thoracolumbar_tolerance)

        slouching_flag = True
        head_tilt_flag = False

        # Is user slouching?
        if c_min <= self.distance <= c_max:
            # No.
            slouching_flag = False

        # Is user's head tilted?
        if isinstance(self.angle, float):
            if self.angle > self.tilt_angle:
                # Yes.
                head_tilt_flag = True

        return slouching_flag, head_tilt_flag
