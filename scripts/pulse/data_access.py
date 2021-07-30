import datetime
import mysql.connector
from mysql.connector import Error
import math

class DataAccess(object):
    tbl_results  = 'bme_quizmaster.quizzes_logs'
    tbl_data_raw = 'bme_quizmaster.quizzes_logs_pulses'
    tbl_data_sec = 'bme_quizmaster.quizzes_logs_pulses_sec'
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
            query = ("UPDATE " + self.tbl_results + " SET pulse_status = 0, pulse_start = %s WHERE id = %s")

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
            query = ("UPDATE " + self.tbl_results + " SET pulse_status = 1, pulse_end = %s WHERE id = %s")

            self.cursor.execute(query, (current_ts.strftime("%Y-%m-%d %H:%M:%S"), result_id))

            self.mydb.commit()

        except Error as e:
            print(e)
        finally:
            self.cursor.close()
            self.mydb.close()

    def save_bpm(self, result_id, bpm_list_data):
        try:
            query = ("DELETE FROM " + self.tbl_data_raw + " WHERE result_id = %s")
            self.cursor.execute(query, (result_id,))

            for bpm_data in bpm_list_data:
                query = ("INSERT INTO " + self.tbl_data_raw + "(result_id, rate, clock) VALUES (%s, %s, %s)")
                self.cursor.execute(query, (result_id, float(bpm_data["bpm"]), float(bpm_data["time"])))

            self.cursor.callproc("bme_quizmaster.sp_save_pulse_avg", (result_id,))

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

        query = ("SELECT rate, clock FROM " + self.tbl_data_raw + " WHERE result_id = %s ORDER BY id ASC")

        self.cursor.execute(query, (result_id,))
        result = self.cursor.fetchall()

        base_clock = 0
        i          = 1
        averaging  = 1
        avgstart   = 0
        avgpulses  = []

        for pulse in result:
            rate = pulse[0]
            time = pulse[1]

            clock = math.floor( time / 1000 );

            if (i == 1) :
                base_clock = clock;
                avgstart   = base_clock;

            if (clock < avgstart + averaging) :
                avgpulses.append(math.floor(rate));
            else :
                avgpulses.append(math.floor( rate ));

                bpm  = math.floor(sum(avgpulses) / len(avgpulses));
                time = avgstart - base_clock

                self.cursor.execute("INSERT INTO " + self.tbl_data_sec + " (result_id, rate, clock) VALUES (%s, %s, %s) ", (result_id, bpm, clock))


                avgstart  = clock;

            i += 1

        #if (len(avgpulses) > 0) :
            #bpm  = math.floor(sum(avgpulses) / len(avgpulses));
            #time = avgstart - base_clock

            #self.cursor.execute("INSERT INTO " + self.tbl_data_sec + " (result_id, rate, clock) VALUES (%s, %s, %s) ", (result_id, bpm, time))
