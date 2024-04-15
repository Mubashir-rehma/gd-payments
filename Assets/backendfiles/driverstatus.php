<?php
session_start();
// include "access.php";
// access('ADMIN');
// access('DISPATCHER');

include './config.php';

// $update = false;
$creater = $_SESSION['myid'];
$now = new DateTime("now", new DateTimeZone('America/New_York'));
$now = $now->format('Y-m-d H:i:s');

if (isset($_POST['truckstateupdate'])) {
    $id = $_POST['id'];
    $truckstatus = $_POST['truckstatus'];
    $city = $_POST['city'];
    $truckNumber = $_POST['truckNumber'];
    $engineNumber = $_POST['engineNumber'];
    $Vehicle_dims = $_POST['Vehicle_dims'];
    $weight = $_POST['weight'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $truckOwner = $_POST['truckOwner'];
    $registeredIn = $_POST['registeredIn'];
    $truckDriver = $_POST['truckDriver'];
    $cpPhone = $_POST['cpPhone'];
    $cpemail = $_POST['cpemail'];

    $truckNumbernotes = $_POST['truckNumbernotes'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $insurance_issuer = $mysqli->real_escape_string($_POST['insurance_issuer']);
    $insurance_start_date = $_POST['insurance_start_date'];
    $insurance_end_date = $_POST['insurance_end_date'];

    $mysqli->query("UPDATE truck_details SET Status='$truckstatus', city='$city', truckNumber='$truckNumber', engineNumber='$engineNumber', Vehicle_dims='$Vehicle_dims', weight='$weight', make='$make', model='$model', year='$year', truckOwner='$truckOwner', registeredIn='$registeredIn', truckDriver='$truckDriver', cpPhone='$cpPhone', cpemail='$cpemail', arrival_date='$arrival_date',truckNumbernotes='$truckNumbernotes', lat='$lat', lng='$lng', insurance_issuer='$insurance_issuer', insurance_start_date='$insurance_start_date', insurance_end_date='$insurance_end_date' where truck_id='$id'") or die($mysqli->error);


    $_SESSION['message'] = "Status has been Updated!";
    $_SESSION['msg_type'] = "success";

    header("location: /Logistics CRM Redesign/driveravailability.php");
} else if ($_REQUEST['action_type'] == 'truckstate') {
    $id = $_POST['id'];
    $truckstatus = $mysqli->real_escape_string($_POST['truckstatus']);
    $city = $mysqli->real_escape_string($_POST['city']);
    $truckNumber = $mysqli->real_escape_string($_POST['truckNumber']);
    $engineNumber = $mysqli->real_escape_string($_POST['engineNumber']);
    $Vehicle_dims = $mysqli->real_escape_string($_POST['Vehicle_dims']);
    $weight = $mysqli->real_escape_string($_POST['weight']);
    $make = $mysqli->real_escape_string($_POST['make']);
    $model = $mysqli->real_escape_string($_POST['model']);
    $year = $_POST['year'];
    $truckOwner = $mysqli->real_escape_string($_POST['truckOwner']);
    $registeredIn = $mysqli->real_escape_string($_POST['registeredIn']);
    $truckDriver = $mysqli->real_escape_string($_POST['truckDriver']);
    $cpPhone = $_POST['cpPhone'];
    $cpemail = $mysqli->real_escape_string($_POST['cpemail']);
    //$arrival_date = $_POST['arrival_date'];
    $truckNumbernotes = $mysqli->real_escape_string($_POST['truckNumbernotes']);
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $insurance_issuer = $mysqli->real_escape_string($_POST['insurance_issuer']);
    $insurance_start_date = $_POST['insurance_start_date'];
    if (isset($_POST['truckstatus']) && $_POST['truckstatus'] === "available_on_") {
        $drivertime = $_POST['drivertime'];
    }    
    //$drivertime = $_POST['drivertime'];
    $insurance_end_date = $_POST['insurance_end_date'];
    $home_state = $_POST['home_state'];
    $c = serialize([$creater]);
    $n = serialize([$now]);



   if (!empty($id)) {
        $query = "SELECT * from truck_details where truck_id='$id'";
        $data = $mysqli->query($query) or die($mysqli->error);
        $cr = [];
        $no = [];
        foreach ($data as $d) {
            $cra = unserialize($d['t_last_updated_by']);
            $cra[] =  $creater;
            $cr[] = $cra;
            $noa = unserialize($d['t_lat_updated_on']);
            $noa[] = $now;
            $no[] = $noa;
            $hstatus= $d['Status'];
            
        }
        $cr = array_merge(...$cr);
        $no = array_merge(...$no);
        $c = serialize($cr);
        $n = serialize($no);

        if ($_POST['truckstatus'] == "available_on_" && ($drivertime !== "")) { 
            $mysqli->query("UPDATE truck_details SET Status='$truckstatus', city='$city', truckNumber='$truckNumber', engineNumber='$engineNumber', Vehicle_dims='$Vehicle_dims', weight='$weight', make='$make', model='$model', year='$year', truckOwner='$truckOwner', registeredIn='$registeredIn', truckDriver='$truckDriver', cpPhone='$cpPhone', cpemail='$cpemail',truckNumbernotes='$truckNumbernotes', lat='$lat', lng='$lng', insurance_issuer='$insurance_issuer', insurance_start_date='$insurance_start_date', insurance_end_date='$insurance_end_date', driver_availability= '$drivertime' , home_state='$home_state', t_last_updated_by = '$c', t_lat_updated_on = '$n' where truck_id='$id'") or die($mysqli->error);
            $newid = $id;
            // print("available_on_");
        } else if($_POST['truckstatus'] !== "available_on_"){
            // print("not available_on_");
            $mysqli->query("UPDATE truck_details SET Status='$truckstatus', city='$city', truckNumber='$truckNumber', engineNumber='$engineNumber', Vehicle_dims='$Vehicle_dims', weight='$weight', make='$make', model='$model', year='$year', truckOwner='$truckOwner', registeredIn='$registeredIn', truckDriver='$truckDriver', cpPhone='$cpPhone', cpemail='$cpemail',truckNumbernotes='$truckNumbernotes', lat='$lat', lng='$lng', insurance_issuer='$insurance_issuer', insurance_start_date='$insurance_start_date', insurance_end_date='$insurance_end_date', home_state='$home_state', t_last_updated_by = '$c', t_lat_updated_on = '$n' where truck_id='$id'") or die($mysqli->error);
            $newid = $id;
        }
    } 

    // if ($_POST['truckstatus'] == "available_on_" && !empty($drivertime)) {
    //     $mysqli->query("INSERT INTO truck_details (truckNumber, engineNumber, Vehicle_dims, weight, make, model, year, truckOwner, registeredIn, truckDriver, cpPhone, cpemail, city, truckNumbernotes, Status, arrival_date, lat, lng, insurance_issuer, insurance_start_date, insurance_end_date, driver_availability, home_state, t_last_updated_by, t_lat_updated_on) VALUES ('$truckNumber', '$engineNumber', '$Vehicle_dims', '$weight', '$make', '$model', '$year', '$truckOwner', '$registeredIn', '$truckDriver', '$cpPhone', '$cpemail', '$city', '$truckNumbernotes', '$truckstatus', '$arrival_date', '$lat', '$lng', '$insurance_issuer', '$insurance_start_date', '$insurance_end_date', '$drivertime', '$home_state', '$c', '$n')") or die($mysqli->error);
    //     $newid = $mysqli->insert_id;

    // }   

   else if ($_POST['truckstatus'] == "available_on_" && ($drivertime !== "")) {
    $mysqli->query("INSERT INTO truck_details (truckNumber, engineNumber, Vehicle_dims, weight, make, model, year, truckOwner, registeredIn, truckDriver, cpPhone, cpemail, city, truckNumbernotes, Status, arrival_date, lat, lng, insurance_issuer, insurance_start_date, insurance_end_date, home_state, t_last_updated_by, t_lat_updated_on) VALUES ('$truckNumber', '$engineNumber', '$Vehicle_dims', '$weight', '$make', '$model', '$year', '$truckOwner', '$registeredIn', '$truckDriver', '$cpPhone', '$cpemail', '$city', '$truckNumbernotes', '$truckstatus', '$arrival_date', '$lat', '$lng', '$insurance_issuer', '$insurance_start_date', '$insurance_end_date', '$home_state', '$c','$n')") or die($mysqli->error);
    $newid = $mysqli->insert_id;
   }else if($_POST['truckstatus'] !== "available_on_"){
    // print("not available_on_");
    $mysqli->query("INSERT INTO truck_details (truckNumber, engineNumber, Vehicle_dims, weight, make, model, year, truckOwner, registeredIn, truckDriver, cpPhone, cpemail, city, truckNumbernotes, Status, arrival_date, lat, lng, insurance_issuer, insurance_start_date, insurance_end_date, home_state, t_last_updated_by, t_lat_updated_on) VALUES ('$truckNumber', '$engineNumber', '$Vehicle_dims', '$weight', '$make', '$model', '$year', '$truckOwner', '$registeredIn', '$truckDriver', '$cpPhone', '$cpemail', '$city', '$truckNumbernotes', '$truckstatus', '$arrival_date', '$lat', '$lng', '$insurance_issuer', '$insurance_start_date', '$insurance_end_date', '$home_state', '$c','$n')") or die($mysqli->error);
    $newid = $mysqli->insert_id;
}

    function add_driver_attach($driver_attachments, $mysqli, $newid, $creater, $doctype, $inputname)
    {
        if (!empty($driver_attachments)) {
            // $fileNotes = $_POST['file_notes'];
            // $filetags = $_POST['filetags'];

            $i = 0;
            foreach ($driver_attachments as $key => $val) {
                // File upload path 
                $fileName = $newid . '_' . basename($driver_attachments[$key]);
                $targetFilePath = '../uploads/Driver_attachments/Insurance_attachments/' . $fileName;

                // Check whether file type is valid 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                // if (in_array($fileType, $allowTypes)) {
                // Upload file to server 
                if (move_uploaded_file($_FILES[$inputname]["tmp_name"][$key], $targetFilePath)) {
                    // File db insert 
                    $query = "INSERT INTO driver_insurance_attachments (file_name, driver_id, uploaded_by, doc_type) VALUES ('$fileName','$newid', $creater, '$doctype')";
                    $mysqli->query($query) or die($mysqli->error);
                } else {
                    // $errorUpload .= $codFiles[$key] . ' | ';
                }

                $i++;
            }
        }
    }




    if (!empty($newid)) {

        // Add Files
        $driver_attachments = array_filter($_FILES['driver_ide_attachment']['name']);
        add_driver_attach($driver_attachments, $mysqli, $newid, $creater, "identity_doc", "driver_ide_attachment");
        $driver_attachments = array_filter($_FILES['driver_ins_attachment']['name']);
        add_driver_attach($driver_attachments, $mysqli, $newid, $creater, "insurance_doc", "driver_ins_attachment");
        $driver_attachments = array_filter($_FILES['driver_dl_attachment']['name']);
        add_driver_attach($driver_attachments, $mysqli, $newid, $creater, "driving_license", "driver_dl_attachment");
        $driver_attachments = array_filter($_FILES['driver_vanpics_attachment']['name']);
        add_driver_attach($driver_attachments, $mysqli, $newid, $creater, "driver_van_pics", "driver_vanpics_attachment");




        // if (!empty($driver_attachments)) {
        //     // $fileNotes = $_POST['file_notes'];
        //     // $filetags = $_POST['filetags'];

        //     $i = 0;
        //     foreach ($driver_attachments as $key => $val) {
        //         // File upload path 
        //         $fileName = $newid . '_' . basename($driver_attachments[$key]);
        //         $targetFilePath = '../uploads/Driver_attachments/Insurance_attachments/' . $fileName;
        //         $fileNote = $mysqli->real_escape_string($fileNotes[$i]);
        //         $filetag = $mysqli->real_escape_string($filetags[$i]);

        //         // Check whether file type is valid 
        //         $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        //         // if (in_array($fileType, $allowTypes)) {
        //         // Upload file to server 
        //         if (move_uploaded_file($_FILES["driver_attachment"]["tmp_name"][$key], $targetFilePath)) {
        //             // File db insert 
        //             $query = "INSERT INTO driver_insurance_attachments (file_name, driver_id, uploaded_by, fileNotes, file_tags) VALUES ('$fileName','$newid', $creater, '$fileNote', '$filetag')";
        //             $mysqli->query($query) or die($mysqli->error);
        //         } else {
        //             $errorUpload .= $codFiles[$key] . ' | ';
        //         }

        //         $i++;
        //     }
        // }
    }

    echo json_encode(["success" => 1, "msg" => "Data successfully Added!"]);

    // header("location: /Logistics CRM Redesign/driveravailability.php");
} else if ($_REQUEST['action_type'] == 'submitFile') {
    // print($_POST['truckstate']);
    // print_r($_POST);
    $driver_attachments = $_POST['driver_attachment'];


    // if(!empty($newid)){

    // Add Files
    if (!empty($driver_attachments)) {

        foreach ($driver_attachments as $key => $value) {
            // print_r ($driver_attachments);

            // $driver_attachments = json_decode($value, true);
            print_r($driver_attachments);
            if (!empty($driver_attachments['name'])) {
                // File upload path 
                $fileName = $newid . '_' . basename($driver_attachments['name']);
                echo $fileName;
                $targetFilePath = '../uploads/Driver_attachments/Insurance_attachments/' . $fileName;


                // Check whether file type is valid 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                // if (in_array($fileType, $allowTypes)) {
                // Upload file to server 
                if (move_uploaded_file($_FILES["driver_attachments"]["tmp_name"][$key], $targetFilePath)) {
                    // File db insert 

                    $query = "INSERT INTO driver_insurance_attachments (file_name, driver_id, uploaded_by) VALUES ('$fileName','$newid', $creater)";
                    $mysqli->query($query) or die($mysqli->error);
                } else {
                    // $errorUpload .= $codFiles . ' | ';
                }
            }
        }
    }
    // }


} elseif (isset($_POST['distance'])) {
    $distance = $_POST['distance'];
    $uniquecitycity = $mysqli->real_escape_string($_POST['city']);

    $mysqli->query("UPDATE truck_details SET distance = '$distance' WHERE city = '$uniquecitycity' AND (Status = 'available' OR Status = 'on_Hold')") or die($mysqli->error);

    $_SESSION['message'] = "Status has been Updated!";
    $_SESSION['msg_type'] = "success";

    // header("location: /Logistics CRM Redesign/driveravailability.php");
} elseif (($_REQUEST['action_type'] == 'hold') && !empty($_POST['truck_id'])) {
    $id = $_POST['truck_id'];
    $last_Status = $_POST['status'];
    // $redirect = $_POST['redirect'];
    $username = $_POST['user_id'];
    // $truckstatus = $_POST['truckstatus'];
    $query = "SELECT * from truck_details Where truck_id = $id";
    $data = $mysqli->query($query) or die($mysqli->error);
    foreach ($data as $i){
    $status_updating_user =$i['status_updating_user'];
    if($_SESSION['myid'] == $status_updating_user || empty($status_updating_user)){
    $ht = "INSERT INTO hold_time_status (user_id, status, truck_id, timestamp) VALUES ('$creater', 'on_Hold', '$id', '$now')";
    $mysqli->query($ht) or die($mysqli->error);

    $mysqli->query("UPDATE truck_details SET Status='on_Hold', last_Status='$last_Status', status_updating_user='$creater', last_updated_on='$now', Holdtime='$now' where truck_id='$id'") or die($mysqli->error);
    $record_id = $mysqli->insert_id;

    //Status changes 

    // $query = "SELECT * from truck_details where truck_id='$id'";
    // $data = $mysqli->query($query) or die($mysqli->error);
    // foreach ($data as $i){
        
    //     $hstatus= $i['Status'];
    // }
    // $ht = "INSERT INTO hold_time_status (user_id, status, truck_id) VALUES ('$creater', '$hstatus', '$id')";
    // $mysqli->query($ht) or die($mysqli->error);
    $_SESSION['message'] = "Status has been Updated!";
    $_SESSION['msg_type'] = "success";

    $data = [
        "success" => 1,
        "id" => $record_id
    ];
    }
    else {
        $data = [
            "success" => 0,
            "msg" => "You are not authenticated to Update or un hold the truck!",
            
        ];
    }
}
    echo json_encode($data);

    // header("location: /Logistics CRM Redesign/$redirect");
} elseif (($_REQUEST['action_type'] == 'removehold') && !empty($_POST['truck_id'])) {
   
    $id = $_POST['truck_id'];
    $last_Status = $_POST['last_status'];
    // $redirect = $_POST['redirect'];
    $username = $_POST['user_id'];

      $query = "SELECT * from truck_details WHERE truck_id= $id";
    $data = $mysqli->query($query) or die($mysqli->error);
    foreach ($data as $i){
    $status_updating_user =$i['status_updating_user'];
    // $truckstatus = $_POST['truckstatus'];

    if($_SESSION['myid'] == $status_updating_user){
        $mysqli->query("UPDATE truck_details SET Status='$last_Status', last_Status='on_Hold', status_updating_user='', last_updated_on='$now' where truck_id='$id'") or die($mysqli->error);
        $record_id = $mysqli->insert_id;
        $ht = "INSERT INTO hold_time_status (user_id, status, truck_id, timestamp) VALUES ('$creater', '$last_Status', '$id', '$now')";
        $mysqli->query($ht) or die($mysqli->error);


        $_SESSION['message'] = "Status has been Updated!";
        $_SESSION['msg_type'] = "success";

        $data = [
            "success" => 1,
            "id" => $record_id
        ];
    } else {
        $data = [
            "success" => 0,
            "msg" => "You are not authenticated to Update or un hold the truck!",
            
        ];
    }
    }
    

    echo json_encode($data);

    // header("location: /Logistics CRM Redesign/$redirect");
} elseif (isset($_POST['truckNoCancel'])) {
    header("location: /Logistics CRM Redesign/driveravailability.php");
} else if ($_REQUEST['action_type'] == 'update_arrivaldate') {
    $truck_id = $_POST['truck_Number'];
    $arrivaldate = $_POST['arrivaldate'];

    $data = [];
    if (!empty($truck_id) && !empty($arrivaldate)) {
        $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', Status='not_Available'  where truck_id='$truck_id'") or die($mysqli->error);
    }
    // else{

    // }
} else if ($_REQUEST['action_type'] == 'delete_insurance_file') {
    $id = $_POST['id'];

    $query = "DELETE FROM `driver_insurance_attachments` WHERE id=$id";
    $mysqli->query($query) or die($mysqli->error);

    echo json_encode(["success" => 1, "msg" => "File successfully Deleted!"]);
} else if ($_REQUEST['action_type'] == 'delete_driver') {
    $id = $_POST['id'];

    $query = "DELETE FROM `truck_details` WHERE truck_id=$id";
    $mysqli->query($query) or die($mysqli->error);

    echo json_encode(["success" => 1, "msg" => "Record successfully Deleted!"]);
} else if ($_REQUEST['action_type'] == 'driver_truck' && $_SESSION['holdaccess'] == 1) {
    // $id = $_POST['id'];
    $uq = "UPDATE truck_details t left outer join newload as n on t.truck_id = n.truck_Number 
    SET t.last_status = t.Status where n.status != 'load_en_route'";
    $mysqli->query($uq) or die($mysqli->error);
    $tuq = "UPDATE truck_details t
    LEFT OUTER JOIN newload AS n ON t.truck_id = n.truck_Number
    SET t.Status = 'not_Available'
    WHERE t.status = 'available' OR t.status = 'available_locally'";
    $mysqli->query($tuq) or die($mysqli->error);
    echo json_encode(["success" => 1, "msg" => "Record successfully Updated!"]);
}
