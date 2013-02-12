import serial
import os
import time
import sys
import MySQLdb

port = "/dev/ttyS0"
ser = serial.Serial(port,2400,timeout=10)
ser.readline()

def getData():
        sensor = ser.readline().split()
        print sensor
        conn = MySQLdb.connect (host = "localhost", user = "user", passwd = "password", db = "db_name")
        cursor = conn.cursor ()

        command = "INSERT INTO `monitor`.`data` (`id`, `sensor_id`, `time`, `temp`) VALUES (NULL, '%s', CURRENT_TIMESTAMP, '%s');" % (sensor[0], sensor[1])
        cursor.execute(command);
        cursor.close ()
        conn.close ()
        return
for i in range(5):
        getData()

ser.close()

#getData for all sensors
#writeToDataBase(sensor, temp)
