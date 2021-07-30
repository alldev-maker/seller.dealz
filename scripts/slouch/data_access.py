import datetime
import mysql.connector
from mysql.connector import Error
import math

class SlouchDataAccess:
    tbl_results  = 'bme_quizmaster.quizzes_logs'
    tbl_data_raw = 'bme_quizmaster.quizzes_logs_slouches'
    tbl_data_sec = 'bme_quizmaster.quizzes_logs_slouches_sec'
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
            query = ("UPDATE " + self.tbl_results + " SET slouch_status = 0, slouch_start = %s WHERE id = %s")

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
            query = ("UPDATE " + self.tbl_results + " SET slouch_status = 1, slouch_end = %s WHERE id = %s")

            self.cursor.execute(query, (current_ts.strftime("%Y-%m-%d %H:%M:%S"), result_id))

            self.mydb.commit()

        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()

    def save_slouch(self, result_id, slouch_list_data):
        """
        :param filename: Filename of the slouch list.
        :param slouch_list_data: List of the slouch and time.
        :return: No return value.
        """

        try:
            current_ts = datetime.datetime.now()

            query = ("DELETE FROM " + self.tbl_data_raw + " WHERE result_id = %s")
            self.cursor.execute(query, (result_id,))

            for slouch_data in slouch_list_data:
                query = ("INSERT INTO " + self.tbl_data_raw + " (result_id, slouch_flag, slouch_count, body_slouched, body_straight, tilt_flag, tilt_count, head_tilted, head_straight, clock, created_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)")
                self.cursor.execute(query, (
                    result_id,
                    slouch_data["slouch_flag"],
                    slouch_data["slouch_count"],
                    str(slouch_data["body_slouch"]),
                    str(slouch_data["body_straight"]),
                    slouch_data["tilt_flag"],
                    slouch_data["tilt_count"],
                    str(slouch_data["head_tilted"]),
                    str(slouch_data["head_straight"]),
                    str(slouch_data["time"]),
                    current_ts.strftime("%Y-%m-%d %H:%M:%S")
                ))

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

            query = ("SELECT slouch_count, body_straight, body_slouched, clock FROM " + self.tbl_data_raw + " WHERE result_id = %s ORDER BY id ASC")
            self.cursor.execute(query, (result_id,))
            result = self.cursor.fetchall()

            slouches = []
            for i in range(1, secs+1):
                slouchDict = {
                    "time"    : i,
                    "msec"    : 0,
                    "count"   : 0,
                    "duration": 0,
                }
                slouches.append(slouchDict)
                print(slouches[i-1])

            startSlouch = None
            isSlouching = False

            for slouch in result:
                count   = slouch[0]
                pos_straight = slouch[1]
                pos_slouched = slouch[2]
                time   = slouch[3]

                if count == 1:
                    startSlouch = slouch;

                if count >= 30:
                    isSlouching = True;

                if count == 0:
                    startSlouch = None;
                    isSlouching = False;

                    continue;

                if isSlouching:
                    clock = math.floor( slouch[3] / 1000 );

                    slouchDict = {
                        "time"    : clock,
                        "msec"    : startSlouch[3],
                        "count"   : 1,
                        "duration": time - startSlouch[3],
                    }
                    if (clock <= secs) :
                        slouches[clock-1] = slouchDict


            for slouch in slouches:
                self.cursor.execute("INSERT INTO " + self.tbl_data_sec + " (result_id, slouch_start, slouch_count, slouch_duration, clock) VALUES (%s, %s, %s, %s, %s) ", (result_id, slouch["msec"], slouch["count"], slouch["duration"], slouch["time"]))
