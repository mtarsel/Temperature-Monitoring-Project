<?php

connect();

class sensor
{
	private $id;
	private $nickname;
	private $x;
	private $y;

	#grabs the latest data from the database specified by time
	private function latestData()
	{
		$result = mysql_query("SELECT * FROM `data` WHERE sensor_id =".$this->id." ORDER BY `time` DESC limit 1");
		return mysql_fetch_object($result, 'data');
	}
	
	#print Heat Mapping	with lateset data from database
	public function hMapPrint()
	{
		echo "{x: ".($this->x*100).", y: ".($this->y*120).", count: ".$this->latestData()->temp."},";
	}

	#gathers graphing data to display with highcharts API
	public function graph($lookBack, $skip)
	{
		echo "{";
		echo "name: '$this->nickname',";
		echo "data: [";
		$i =0;
	
		#specifies the location in the MySQL database where to read temperature.
		#Read test.py for more information on configuring database
		$result = mysql_query("SELECT * FROM `data` WHERE `sensor_id`=".$this->id." AND `time` > DATE_ADD(NOW(), INTERVAL -".$lookBack." MINUTE)");
		while($row = mysql_fetch_object($result, 'data'))
		{
			if( $i == $skip)
			{
				echo "[" . $row->getDate() . ", $row->temp],";
				$i = 0;
			}
			$i++;
		}
		echo "]";
		echo "},";
	}
}

class data
{
	private $id;
	public $sensor_id;
	public $time;
	public $temp;
	public function getDate()
	{
		$timeParsed = strtotime($this->time);
		list($year, $month, $other) = split('-',$this->time);
		list($day, $other) = split(' ', $other);
		list($hour, $minute, $second) = split('[-./:]', $other);
		return "Date.UTC($year, $month-1, $day, $hour, $minute, $second)";
	}
}

function connect()#function to connect to MySQL database
{
	//host, username, passsword
	mysql_connect('localhost', 'admin', 'password');
	mysql_select_db("monitor");//database name
}

function sensors()#specifies which sensor to grab data (temperature) from to
#to display on the graph
{
	$res = array();
	$result = mysql_query("SELECT * FROM sensors");
	$i=0;
	while($row = mysql_fetch_object($result, 'sensor'))
	{
		$res[$i] = $row;
		$i++;
	}
	return $res;
}

#prints which sensor is reading the temperature to the graph
function hMapPrint()
{
	$sensors = sensors();
	foreach($sensors as &$sensor)
	{
		echo $sensor->hMapPrint();
	}
}

function graphData($lookBack, $skip)
{
	$sensors = sensors();
	foreach($sensors as &$sensor)
	{
		$sensor->graph($lookBack, $skip);
	}
}

?>
<html>
	<head>
		<title>Server Room Temperatures</title>
		<script type="text/javascript" src="heatmap.js"></script>
		<script type="text/javascript"> 
			window.onload = function()
			{
				var map = h337.create({"element":document.getElementById("heatmapArea"), "radius":50, "visible":true});
				map.store.setDataSet({ max: 38, data: [<?php hMapPrint(); ?>]});
			};
		</script>
		<style>
			#heatmapArea 
			{
				position:relative;
				float:center;
				width:600px;
				height:400px;
				background-image:url(/graphic.png);/*this is the background
picture for the heatmapping picture at the bottom*/
			}
		</style>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="/highcharts/js/highcharts.js"></script>
		<script type="text/javascript" src="/highcharts/js/modules/exporting.js"></script>
		<script type="text/javascript">
		var chart;
		jQuery(document).ready(function() {
			chart = new Highcharts.Chart({
				chart: {
					renderTo: 'container', 
					defaultSeriesType: 'spline',
					zoomType: 'x'
				},
				title: {
					text: 'COSI Server Room Temperature'
				},
				subtitle: {
					text: 'Drag to Zoom'
				},
				xAxis: {
			            	type: 'datetime',
					title: {
						enabled: true,
						text: 'Time'
					},
					startOnTick: true,
					endOnTick: false,
					showLastLabel: true
				},
				yAxis: {
					title: {
						text: 'Temperature (c)'
					}
				},
				tooltip: {
			            formatter: function() {
			                return Highcharts.dateFormat("%B %e, %H:%M\n", this.x) + ': ' + Highcharts.numberFormat(this.y, 2) + "C";
			            }
        			},
				legend: {
					layout: 'vertical',
					align: 'left',
					verticalAlign: 'top',
					x: 100,
					y: 0,
					floating: true,
					//backgroundColor: Highcharts.theme.legendBackgroundColor || '#FFFFFF',
					borderWidth: 1
				},
				plotOptions: {
					scatter: {
						marker: {
							radius: 5,
							states: {
								hover: {
									enabled: true,
									ilineColor: 'rgb(100,100,100)'
								}
							}
						},
						states: {
							hover: {
								marker: {
									enabled: false
								}
							}
						}
					}
				},
				//minutes, skip
				//will specifiy the x-axis of graph to change range
				series: [
					<?php 
						$minutes = 60*24;
						if(isset($_GET["interval"]))
						{
							switch($_GET["interval"])
							{
								case "1 Year":
									$minutes = 60*24*30*12;
									break;
								case "1 Month":
									$minutes = 60*24*30;
									break;
								case "1 Week":
									$minutes = 60*24*7;
									break;
								case "1 Day":
									$minutes = 60*24;
									break;
								case "6 Hours":
									$minutes = 60*6;
									break;
							}		
							
						}
						graphData($minutes, round($minutes/150));
					?>]
			});
			
		});
	</script>
	</head><!--Specifically check style.css for specific styles -->
	<body>
		<p><strong>Current temperatures:</strong> </p>
		<div align="right">
		<?php
		function ic( $item )
		{
			if(isset($_GET["interval"]) && $_GET["interval"] == $item)
				echo " selected ";
			if(!isset($_GET["interval"]) && $item == "1 Day")
				echo " selected ";
		}
		?>
			<form method="get" name="timestuff">
				<select name="interval" onChange="document.forms['timestuff'].submit();">
					<option <?php ic("1 Year")?>>1 Year</option>
					<option <?php ic("1 Month")?>>1 Month</option>
					<option <?php ic("1 Week")?>>1 Week</option>
					<option <?php ic("1 Day")?>>1 Day</option>
					<option <?php ic("6 Hours")?>>6 Hours</option>
				</select><br>
			</form>
		</div>
		<div id="container" class="highcharts-container" style="height:410px; margin: 0 2em; clear:both; min-width: 600px"> </div>
		
		<p><strong>Heat Mapping:</strong> </p>
		<div id="heatmapArea"></div><br>
	</body>
</html>
