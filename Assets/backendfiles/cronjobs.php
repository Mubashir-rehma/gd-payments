<?php

include './config.php';

// $unitdetail = $mysqli->query("SELECT * FROM truck_details") or die($mysqli->error);

// $i = 0;
// foreach ($unitdetail as $row) {
//     $i++;
//     // $statusLink = ($row['Status'] == 'on_Hold') ? 'driverstatus.php?action_type=removehold&id=' . $row['truck_id'] : 'driverstatus.php?action_type=hold&id=' . $row['truck_id'];
//     // $statusTooltip = ($row['Status'] == 'on_Hold') ? 'Click to unhold' : 'Click to Put on Hold';

//     if (new DateTime() >= new DateTime($row['arrival_date']) & $row['Status'] == "not_Available") {
//         $id = $row['truck_id'];
//         $mysqli->query("UPDATE truck_details SET Status='available', arrival_date=NULL where truck_id='$id'") or die($mysqli->error);
//         print($row['truckNumber'] . "   " . $row['Status'] . "   " . $row['arrival_date'] . "\n");
//     }
// }

// $query = "select broker_id, brokerName, broker_company from broker_details where broker_company = ''";
// $brokers = $mysqli->query($query);
// foreach($brokers as $row){
//     // print("  id  ". $row['id']. "  Broker  " . $row['Broker'] . "<br>");
//     $brokerName = $row['brokerName'];
//     $broker_id = $row['broker_id'];
//     $broker_company = $row['broker_company'];

//     // $query = "update broker_details set broker_company='$brokerName' where broker_id='$broker_id'";
//     // $mysqli->query($query);

//     print( "  Broker Id:   " . $broker_id. "   Broker Name:  " . $brokerName ."  Broker Company:  " . $broker_company . "<br>");

//     // $brokerid = $mysqli->query("select truck_id, truckNumber from truck_details where truckNumber = '$broker'");

//     // foreach($brokerid as $bd){
//         // $truck_id = $bd['truck_id'];
//         // $id = $row['id'];

//         // $mysqli->query("update newload set truck_Number='$truck_id' where id = '$id'");
//         // print("  id from NL " . $row['id'] .  "  id from BT  " . $bd['truck_id'] . "  Broker from NL  " . $row['truck_Number'] . "  Broker from BT  " . $bd['truckNumber'] . "<br>");
//     // }
// }

$query = "UPDATE newload set status='load_paid' where status='load_delivered'";
$mysqli->query($query) or die($mysqli->error);




 // Set the timezone to UTC
date_default_timezone_set('UTC');

// Get the current UTC time
$utcTime = new DateTime();

// Set the timezone to EST (Eastern Standard Time)
$utcTime->setTimezone(new DateTimeZone('America/New_York'));

// Get the current time in EST
$estTime = $utcTime->format('Y-m-d H:i:s');

// 6AM Automatic
$timezone = "UPDATE truck_details SET Status = 'available' WHERE Status = 'not_available' AND $estTime >= '06:00:00'";
$mysqli->query($timezone) or die($mysqli->error);

// 5AM EST 

$time = "UPDATE truck_details SET Status = 'not_available' WHERE (Status = 'available' OR Status = 'locally_available') AND $estTime >= '05:00:00'";
$mysqli->query($time) or die($mysqli->error);

// $unitdetail = $mysqli->query("SELECT Pick_up_Location, Destination, distance, time, id FROM newload where id < '445'") or die($mysqli->error);
// foreach ($unitdetail as $row) {

//     // $PUs = serialize(str_replace("'","''", $row['Pick_up_Location']));
//     // $des = serialize(str_replace("'","''", $row['Destination']));
//     // $dis = serialize(str_replace("'","''", $row['distance']));
//     // $tim = serialize(str_replace("'","''", $row['time']));

//     // $PUs = serialize(array(str_replace("'", "''", $row['Pick_up_Location'])));
//     // $des = serialize(array(str_replace("'", "''", $row['Destination'])));
//     // $dis = serialize(array(str_replace("'", "''", $row['distance'])));
//     // $tim = serialize(array(str_replace("'", "''", $row['time'])));

//     // $PUs = unserialize(serialize(unserialize(str_replace("'", "''", $row['Pick_up_Location']))[0]));
//     // $des = unserialize(serialize(unserialize(str_replace("'", "''", $row['Destination']))[0]));
//     // $dis = unserialize(serialize(unserialize(str_replace("'", "''", $row['distance']))[0]));
//     // $tim = unserialize(serialize(unserialize(str_replace("'", "''", $row['time']))[0]));
//     $id = $row['id'];


//     // $update = $mysqli->query("UPDATE newload SET Pick_up_Location='$PUs', Destination= '$des' , distance='$dis', time='$tim' where id='$id'") or die($mysqli->error);

//     // print("id:  <b>" . $row['id'] . "</b>  <b>Pickup Location:  </b>". $row['Pick_up_Location'] . "  <b>Destination:  </b>" . $row['Destination'] . "  <b>distance:  </b>" . $row['distance']  . "  <b>time:  </b>" . $row['time'] . "<br>" );

//     // print_r("id:  <b>" . $row['id'] . "</b>  <b>Pickup Location:  </b>" . $PUs . "  <b>Destination:  </b>" . $des . "  <b>distance:  </b>" . $dis  . "  <b>time:  </b>" . $tim . "<br>");

//     // print_r($PUs ) . " < br > ";
// }