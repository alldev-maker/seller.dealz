import datetime
import mysql.connector
from mysql.connector import Error
import math


class DataAccess(object):

    tbl_results  = 'bme_quizmaster.quizzes_logs'
    tbl_data_raw = 'bme_quizmaster.quizzes_logs_emotions'
    tbl_data_sec = 'bme_quizmaster.quizzes_logs_emotions_sec'
    prc_data     = ''

    THRESHOLD = 0.6

    FACE_PINCHED_EYEBROWS = 0
    FACE_PINCHED_NOSE = 1
    FACE_WIDE_EYES = 2
    FACE_SMILE = 3
    FACE_FROWN = 4
    FACE_RAISED_EYEBROWS = 5
    FACE_RELAXED = 6

    def __init__(self):
        self.mydb = mysql.connector.connect(
            host="127.0.0.1",
            user="bme_quizmaster",
            passwd="Hy1qOV3PjVUodJnf"
        )

        self.cursor = self.mydb.cursor()


    def mark_start_time(self, result_id):
        try:
            current_ts = datetime.datetime.now()
            query = ("UPDATE " + self.tbl_results + " SET emotion_status = 0, emotion_start = %s WHERE id = %s")

            self.cursor.execute(query, (current_ts.strftime("%Y-%m-%d %H:%M:%S"), result_id))

            self.mydb.commit()

        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()


    def mark_end_time(self, result_id):
        try:
            current_ts = datetime.datetime.now()
            query = ("UPDATE " + self.tbl_results + " SET emotion_status = 1, emotion_end = %s WHERE id = %s")

            self.cursor.execute(query, (current_ts.strftime("%Y-%m-%d %H:%M:%S"), result_id))

            self.mydb.commit()

        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()

    def save_emotion(self, result_id, emotion_list_data):
        """
        :param filename: Filename of the emotion list.
        :param emotion_list_data: List of the emotion and time.
        :return: No return value.
        """

        try:
            query = ("DELETE FROM " + self.tbl_data_raw + " WHERE result_id = %s")
            self.cursor.execute(query, (result_id,))

            for emotion_data in emotion_list_data:
                query = ("INSERT INTO " + self.tbl_data_raw + "(result_id, emotion, prediction,	probability, clock) VALUES (%s, %s, %s, %s, %s)")
                self.cursor.execute(
                    query,
                    (
                        result_id,
                        str(emotion_data["emotion"]),
                        str(emotion_data["prediction"]),
                        float(emotion_data["probability"]),
                        float(emotion_data["time"]),
                    )
                )

            self.save_average_sec(result_id)

            self.mydb.commit()

        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()

    def save_average_sec(self, result_id):
        query = ("DELETE FROM " + self.tbl_data_sec + " WHERE result_id = %s")
        self.cursor.execute(query, (result_id,))

        self.save_average_sec_single(result_id, self.FACE_PINCHED_EYEBROWS)
        self.save_average_sec_single(result_id, self.FACE_PINCHED_NOSE)
        self.save_average_sec_single(result_id, self.FACE_WIDE_EYES)
        self.save_average_sec_single(result_id, self.FACE_SMILE)
        self.save_average_sec_single(result_id, self.FACE_FROWN)
        self.save_average_sec_single(result_id, self.FACE_RAISED_EYEBROWS)
        self.save_average_sec_single(result_id, self.FACE_RELAXED)


    def save_average_sec_single(self, result_id, emotion):
        query = ("SELECT emotion, probability, clock FROM " + self.tbl_data_raw + " WHERE result_id = %s AND emotion = %s ORDER BY id ASC")

        self.cursor.execute(query, (result_id, emotion))
        result = self.cursor.fetchall()

        base_clock = 0
        i          = 1
        averaging  = 1
        avgstart   = 0
        avgprob    = []
        for data in result:
            prob  = data[1]
            time  = data[2]
            clock = math.floor( time / 1000 );

            if i == 1 :
                base_clock = clock;
                avgstart   = base_clock;

            if (clock < avgstart + averaging) :
                avgprob.append(prob)
            else :
                avgprob.append(prob)

                maxprob  = max(avgprob);

                if (maxprob < self.THRESHOLD):
                    maxprob = 0

                #time = avgstart - base_clock

                self.cursor.execute(
                    "INSERT INTO " + self.tbl_data_sec + " (result_id, emotion, probability, clock) VALUES (%s, %s, %s, %s) ",
                    (result_id, emotion, maxprob, clock)
                )

                avgstart  = clock;

            i += 1

        #if (len(avgprob) > 0) :
            #maxprob  = max(avgprob);

            #if (maxprob < self.THRESHOLD):
                #maxprob = 0

            #time = avgstart - base_clock

            #self.cursor.execute(
                #"INSERT INTO " + self.tbl_data_sec + " (result_id, emotion, probability, clock) VALUES (%s, %s, %s, %s) ",
                #(result_id, emotion, maxprob, time)
            #)

        self.mydb.commit()


