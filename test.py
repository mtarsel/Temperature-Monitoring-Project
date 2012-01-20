import MySQLdb
#insert hostname, username, password and database name below
conn = MySQLdb.connect (host = "localhost", user = "admin", passwd = "password", db = "monitor")
cursor = conn.cursor ()

#declare number of sensors
sensor = 2
temp=60

#database contains 2 tables - data and sensors
command = "INSERT INTO `monitor`.`data` (`id`, `sensor_id`, `time`, `temp`) VALUES (NULL, '%d', CURRENT_TIMESTAMP, '%d');" % (sensor, temp, )
#the database will place a timestamp when data is read in to produce a graph
cursor.execute(command);

#within data table 'data' there are these rows below
cursor.execute ("SELECT * FROM data")
row = cursor.fetchone ()
print "id",row[0]
print "sensor",row[1]
print "time",row[2]
print "temp",row[3]
cursor.close ()
conn.close ()
#getData for all sensors
#writeToDataBase(sensor, temp)
