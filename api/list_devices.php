<?php
$dblink=db_iconnect("equipment");
$sql="Select `auto_id`, `name` from `device_types` where `status`='active'";
$result=$dblink->query($sql) or
	die("Something went wrong with $sql".$dblink->error);// log this error into a database, send out appropriate Json error message
$devices=array();
while ($data=$result->fetch_array(MYSQLI_ASSOC))//BUILD the devices array
{	
	$devices[$data['auto_id']]=$data['name'];//use the auto id to bind to the corresponding name
}
// add the data for all devices
$devices[]="All Devices";
// log successful call to endpoint
//log_activity($endPoint,$_SERVER['REMOTE_ADDR'],"none");
// build the json payload
$output=array();
$output[]='Status: Success';
$output[]='MSG: '.json_encode($devices);
$output[]='Action: Proceed';
$responseData=json_encode($output);
echo $responseData;
die();
?>