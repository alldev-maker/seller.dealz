import datetime
import mysql.connector
from mysql.connector import Error
import math


class DataAccess(object):

    tbl_results  = 'bme_quizmaster.quizzes_logs'
    tbl_data_raw = 'bme_quizmaster.quizzes_logs_irises'
    tbl_data_sec = 'bme_quizmaster.quizzes_logs_irises_sec'
    prc_data     = ''

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
            query = ("UPDATE " + self.tbl_results + " SET iris_status = 0, iris_start = %s WHERE id = %s")

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
            query = ("UPDATE " + self.tbl_results + " SET iris_status = 1, iris_end = %s WHERE id = %s")

            self.cursor.execute(query, (current_ts.strftime("%Y-%m-%d %H:%M:%S"), result_id))

            self.mydb.commit()
        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()

    def save_eyeris(self, result_id, eyeris_list_data):
        """
        :param filename: Filename of the eyeris list.
        :param eyeris_list_data: List of the eyeris and time.
        :return: No return value.
        """

        try:
            query = ("DELETE FROM " + self.tbl_data_raw + " WHERE result_id = %s")
            self.cursor.execute(query, (result_id,))

            for eyeris_data in eyeris_list_data:
                query = ("INSERT INTO " + self.tbl_data_raw + "(result_id, width, height, blinks, clock) VALUES (%s, %s, %s, %s, %s)")
                self.cursor.execute(
                    query,
                    (
                        result_id,
                        float(eyeris_data["iris_w"]),
                        float(eyeris_data["iris_h"]),
                        int(eyeris_data["blink"]),
                        float(eyeris_data["time"]),
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

        query = ("SELECT width, height, clock FROM " + self.tbl_data_raw + " WHERE result_id = %s ORDER BY id ASC")
        self.cursor.execute(query, (result_id,))
        result = self.cursor.fetchall()

        base_clock = 0
        i          = 1
        averaging  = 1
        avgstart   = 0
        avgW  = []
        avgH  = []

        for iris in result:
            width  = iris[0]
            height = iris[1]
            time   = iris[2]

            clock = math.floor( time / 1000 );

            if (i == 1) :
                base_clock = clock;
                avgstart   = base_clock;

            if (clock < avgstart + averaging) :
                avgW.append(width);
                avgH.append(height);
            else :
                avgW.append(width);
                avgH.append(height);

                #w  = math.floor(sum(avgW) / len(avgW));
                #h  = math.floor(sum(avgH) / len(avgH));

                w = max(avgW)
                h = max(avgH)
                time = avgstart - base_clock

                self.cursor.execute("INSERT INTO " + self.tbl_data_sec + " (result_id, width, height, clock) VALUES (%s, %s, %s, %s) ", (result_id, w, h, clock))

                avgstart  = clock;

            i += 1

        #if (len(avgW) > 0 and len(avgH) > 0) :
            #w  = math.floor(sum(avgW) / len(avgW));
            #h  = math.floor(sum(avgH) / len(avgH));
            #w = max(avgW)
            #h = max(avgH)

            #time = avgstart - base_clock

            #self.cursor.execute("INSERT INTO " + self.tbl_data_sec + " (result_id, width, height, clock) VALUES (%s, %s, %s, %s) ", (result_id, w, h, time))

