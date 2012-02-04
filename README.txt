=== Temperature Monitoring Project ===
Please visit http://docs.cslabs.clarkson.edu/wiki/Temperature_Monitoring_Project
for more info.

Project is located here:
http://monitor.cslabs.clarkson.edu

This is an Open Source Temperature Monitoring Project for a server room. It will read the temperature through a serial port and display the temperature using HighCharts 2.1.6 API. This version also includes a heatmap at the bottom of the website.

== Description ==

This project is an effort to increase the amount of temperature sensors in the server room at Clarkson University's Applied CSLabs. This will enable us to receive more precise and accurate temperature readings in the server room as well a more accurate location of any problems that may arise due to server temperatures. 
This configuration was made using Python, JavaScript and PHP programming languages. This will store temperatures, time and location data from the sensors in a MySQL database. 

* First the Python script 'test.py' will read temperatures from the sensors into the serial port and send them directly to the MySQL database to be stored. 
* Using 'main.php' we will re-call the data from the database. 'index.php' 'header.php' and 'footer.php' are used to dsplay the website in a user-friendly format. 
The website also includes a heatmapping function to provide a display for visible temperature change. 


==Hardware==

-Microcontroller PIC 12C509 
  specs:
	* high-performance RISC CPU
	* Operating Speed: DC - 4MHz clock input
	* 1024 Byte EPROM Program memo
	* 41 Byte RAM Data Memory 
	* Internal 4MHz RC Oscillator
-1N4003 diode
-78L05 voltage regulator
-100uF electrolytic capacitor
-n+1 10uF tantalum capacitors 10V
-n DS1820 sensors
-n 2.2k pull-up resistors
-printed circuit board
-female 9-pin or 25-pin D connector
*** n is the number of temperature sensors***

Temperature monitoring kit can be found here:
http://www.qkits.com/serv/qkits/diy/pages/QK145.asp

== Installation ==

1. Install phpmyadmin (http://www.phpmyadmin.net/home_page/downloads.php) on your linux server and enter hostname, username and password of database into 'test.py'

2. place all files in the repository into '/var/www/' directory 
NOTE: We used HighCharts 2.1.6 for this project but I would recommend downloading the most updated version (http://www.highcharts.com/)

Any questions? email tarselmj@clarkson.edu

