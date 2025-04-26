<?php
//Needed: device type, manufacturer type, serial number
$did=$_REQUEST['did'];
$mid=$_REQUEST['mid'];
$sn=$_REQUEST['sn'];
if ($did==NULL || !isset($_REQUEST['did']))//device ID was blank
{
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing device id.';
	$output[]='Action: list_devices';
}




if ($mid==NULL || !isset($_REQUEST['mid']))//device ID was blank
{
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing manufacturer id.';
	$output[]='Action: list_maufacturers';
}
if ($sn==NULL || !isset($_REQUEST['sn']))//device ID was blank
{
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing serial number.';
	$output[]='Action: list_maufacturers';
}

$output[]='Status: Success';
$output[]='MSG: Correctt data recieved.';
$output[]='Action: home';
$responseData=json_encode($output);
echo $responseData;
die();
?>
