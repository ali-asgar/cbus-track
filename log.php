<?php
	include_once("php/config.inc");
	$coord = $_GET["text"];
	$bus_id = $_GET["id"];
	$speed = $_GET["speed"];
	$speed = $speed * 1.852;
	
	list($lat, $lon) = split('\$',$coord);
	
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set('Asia/Calcutta');

	$NS = substr($lat, -1);
	$EW = substr($lon, -1);

	$lat = substr($lat, 0, -1);
	$lon = substr($lon, 0, -1);

	if($NS == 'S') 
		$lat = '-'.$lat;

	if($EW == 'W') 
		$lon = '-'.$lon;

	$lat = convert($lat);
	$lon = convert($lon); 

	$time = date('Y/m/d H:i:s');
	$query = "insert into bus_log (lat,lon,time,speed,bus_id) values ('$lat','$lon','$time','$speed',$bus_id);";
	//echo $query;
	$result = mysql_query($query);
	echo $result;

	include('php/pusher.inc');
	$pusher = new Pusher($key, $secret, $app_id);
	$array = array('bus_id' => $bus_id,
		 'lat'=> $lat, 
		 'lon' => $lon, 
		 'time' => $time ,
		 'speed' => $speed
		 );
	$pusher->trigger('track-channel', 'bus-moved', $array );



	function convert($coord) {
		list($deg, $second) = split('\.',$coord);

		$minute = substr($deg,-2);

		$deg = substr($deg, 0, -2);

		$second = ".".$second;
		$second = $second*60;
		
		return DMStoDEC($deg, $minute, $second);
	}
	function DMStoDEC($deg, $min, $sec)
	{
		// Converts DMS ( Degrees / minutes / seconds ) 
		// to decimal format longitude / latitude

	    return $deg+( (($min*60) + ($sec)) /3600);
	} 
?>