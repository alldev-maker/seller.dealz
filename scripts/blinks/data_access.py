import datetime
import mysql.connector
from mysql.connector import Error
import math


class DataAccess(object):
    tbl_results  = 'bme_quizmaster.quizzes_logs'
    tbl_data_raw = 'bme_quizmaster.quizzes_logs_blinks'
    tbl_data_sec = 'bme_quizmaster.quizzes_logs_blinks_sec'
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
            query = ("UPDATE " + self.tbl_results + " SET blinks_start = %s WHERE id = %s")

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
            query = ("UPDATE " + self.tbl_results + " SET blinks_status = 1, blinks_end = %s WHERE id = %s")

            self.cursor.execute(query, (current_ts.strftime("%Y-%m-%d %H:%M:%S"), result_id))

            self.mydb.commit()

        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()

    def save_blinks(self, result_id, blink_list_data):
        """
        :param filename: Filename of the blink list.
        :param blink_list_data: List of the blink and time.
        :return: No return value.
        """

        try:
            query = ("DELETE FROM " + self.tbl_data_raw + " WHERE result_id = %s")
            self.cursor.execute(query, (result_id,))

            for blink_data in blink_list_data:
                query = ("INSERT INTO " + self.tbl_data_raw + "(result_id, blinks, count, open, closed, clock) VALUES (%s, %s, %s, %s, %s, %s)")
                self.cursor.execute(
                    query,
                    (
                        result_id,
                        int(blink_data["blink"]),
                        int(blink_data["count"]),
                        float(blink_data["eyesOpened"]),
                        float(blink_data["eyesClosed"]),
                        float(blink_data["time"])
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

        query = ("SELECT clock FROM " + self.tbl_data_raw + " WHERE result_id = %s ORDER BY clock DESC LIMIT 1")
        self.cursor.execute(query, (result_id,))
        result = self.cursor.fetchone()
        secs   = math.floor(result[0] / 1000)

        query = ("SELECT blinks, count, open, closed, clock FROM " + self.tbl_data_raw + " WHERE result_id = %s ORDER BY id ASC")
        self.cursor.execute(query, (result_id,))
        result = self.cursor.fetchall()

        blinks = []
        for i in range(1, secs + 1):
            blinkDict = {
                "time"    : i,
                "msec"    : 0,
                "count"   : 0,
                "duration": 0,
            }
            blinks.append(blinkDict)

        startBlink = None
        isBlinking = False

        for blink in result:
            blnk   = blink[0]
            count  = blink[1]
            opened = blink[2]
            closed = blink[3]
            time   = blink[4]

            clock = math.ceil( time / 1000 );

            if count == 1:
                startBlink = blink;

            if count == 0:
                startBlink = None;
                isBlinking = False;

                continue;

            if count >= 3:
                if (clock - 1 < len(blinks)) :
                    blinkDict = {
                        "time"    : clock,
                        "msec"    : startBlink[4],
                        "count"   : 1,
                        "duration": time - startBlink[4],
                    }
                    blinks[clock-1] = blinkDict

        i = 1
        for blink in blinks:
            self.cursor.execute("INSERT INTO " + self.tbl_data_sec + " (result_id, start, count, duration, clock) VALUES (%s, %s, %s, %s, %s) ", (result_id, blink["msec"], blink["count"], blink["duration"], blink["time"]))
            i += 1
