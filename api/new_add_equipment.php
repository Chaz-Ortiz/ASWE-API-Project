<?php
//Needed: device type, manufacturer type, serial number
$did=$_REQUEST['did'];
$mid=$_REQUEST['mid'];
$sn=$_REQUEST['sn'];
$output=array();
if ($did==NULL || !isset($_REQUEST['did']))//device ID was blank
{
	//log error and access
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing device id.';
	$output[]='Action: list_devices';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

if (!ctype_digit($did))// the device id containd non digit charachters
{
	// log error and status
	$output[]='Status ERROR';
	$output[]='SMSG: Invalid or missing device id';
	$output[]='Action: List devices';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
$dblink=db_connect("equipment");// connect to database
$sql="Select `auto_id` from `device_types` where `auto_id`='$did' and `status`='active'";// NOTE: my device_types has `status` with active/inactive? not yet!!
$result=$dblink->query($sql) or
	die("Something went wrong with $sql".$dblink->error);//you should log error message here
if ($result->num_rows<=0)//Nothing was returned from db query
{
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing device id.';// device id could be out of our valid range
	$output[]='Action: list_devices';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
if ($mid==NULL || !isset($_REQUEST['mid']))//device ID was blank
{
	// log the error
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing manufacturer id.';
	$output[]='Action: list_maufacturers';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
if (!ctype_digit($mid))// the manufacturer id containd non digit charachters
{
	// log error and status
	$output[]='Status ERROR';
	$output[]='SMSG: Invalid or missing manufaturer id';
	$output[]='Action: list_manufacturers';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
//$dblink=db_connect("equipment");// connect to database
$sql="Select `auto_id` from `manu_types` where `auto_id`='$mid' and `status`='active'";// NOTE: my device_types has `status` with active/inactive? not yet!!
$result=$dblink->query($sql) or
	die("Something went wrong with $sql".$dblink->error);//you should log error message here
if ($result->num_rows<=0)//Nothing was returned from db query
{
	// log error and access
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing manufacturer id.';// device id could be out of our valid range
	$output[]='Action: list_manufacturers';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
if ($sn==NULL || !isset($_REQUEST['sn']))//serial number was blank
{
	// log the error
	$output[]='Status: Error';
	$output[]='MSG: Invalid or missing serial number.';
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
// no errors were detected, check for duplicates
$sql="Select `auto_id` from `devices` where `serial_number`='$sn'";
$result=$dblink->query($sql) or 
	die("<p>Error running insert:<br>$sql<br>".$dblink->error);
if ($result->num_rows>0)// serial number already in database
{
	// log the error
	$output[]='Status: Error';
	$output[]='MSG: Duplicate or missing serial number.';
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
$sql = "INSERT INTO `devices` (`device_type`, `manufacturer`, `serial_number`) VALUES ('$device', '$manufacturer', '$serialNumber')";
$dblink->query($sql) or 
	die("<p>Error running insert:<br>$sql<br>".$dblink->error);
//l og success
$output[]='Status: Success';
$output[]='MSG: New equipment sucessfully added.';
$output[]='Action: home';
$responseData=json_encode($output);
echo $responseData;
die();
?>
