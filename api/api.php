<?php
// build header info for requestingf service
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
//Build payload array
//$output[]='Status: Success';
//$output[]='MSG: Main endpoint reached';
//$output[]='Action: None';
// Use to debug array
//echo '<pre>';
//print_r($output);
//echo '<pre>';
//Convert array to json
//$responseData=json_encode($output);
//echo $responseData;

// Grab endpoint from URL
$url=$_SERVER['REQUEST_URI'];
//echo "<p>Raw Data: $url</p>";
//echo "<br>";
$path=parse_url($url, PHP_URL_PATH);
//echo "<p>Route Data: $path</p>";
$pathClean=trim($path,"/");
//echo "<p>Trimmed Route Data: $pathClean</p>";
// Uses the / delimiter to create an array
$pathComponents=explode("/",$pathClean);
// echo '<pre';
// print_r($pathComponents);
//echo '</pre>';
$endPoint=$pathComponents[1];
//echo "<p>Route is: $endPoint</p>";
//$did=$_REQUEST['did'];
//$something=$_REQUEST['something'];
include("../functions.php");
switch($endPoint) 
{
	case "list_devices":
        include("list_devices.php");
        break;
	case "list_manufacturers":
        include("list_manufacturers.php");
        break;	
	case "add_equipment":
		include ("add_equipment.php");
		break;
	case "add_device_type":
		include("add_device_type.php");
		break;
	case "add_manufacturer":
		include("add_manufacturer.php");
		break;
	case "search_by_device":
		include("search_by_device.php");
		break;	
	case "search_by_manufacturer":
		include("search_by_manufacturer.php");
		break;	
	case "search_by_serial":
		include("search_by_serial.php");
		break;		
	case "search_all":
		include("search_all.php");
		break;
    default:
        //header('Content-Type: application/json');
        //header('HTTP/1.1 200 OK');
        // your code should have logging
        // logging goes here -> send the data to a database
        //////////////////////////////////////////
        $output[]='Status: ERROR';
        $output[]='MSG: Invalid or missing endpoint';
        $output[]='Action: None';
        $responseData=json_encode($output);
        echo $responseData;
        break;
}
?>



