<?php
session_start();

include './config.php';
include './notification.php';
require_once './DB.class.php';
$db = new DB();
$uploadDir = "../uploads/cod_Files/";
$redirectURL = '../../index.php';
$statusMsg = $errorMsg = '';
$sessData = array();
$statusType = 'danger';
$creater = $_SESSION['myid'];


if (($_REQUEST['action_type'] == "broker_submit")) {
    $broker_company = $mysqli->real_escape_string($_POST['brokercompany']);
    $brokerName = $mysqli->real_escape_string($_POST['brokerName']);
    $brokeremail = $mysqli->real_escape_string($_POST['brokeremail']);
    $brokerphone = $mysqli->real_escape_string($_POST['brokerphone']);
    $brokerAddress = $mysqli->real_escape_string($_POST['brokerAddress']);
    $brokercity = $mysqli->real_escape_string($_POST['brokercity']);
    $brokernotes = $mysqli->real_escape_string($_POST['brokernotes']);
    $brokerState = $mysqli->real_escape_string($_POST['brokerState']);
    $user = $mysqli->real_escape_string($_SESSION['myusername']);

    $mysqli->query("INSERT INTO broker_details (broker_company, brokerName, brokeremail, brokerphone, brokerAddress, brokercity, brokernotes, brokerState, created_by) VALUES('$broker_company', '$brokerName', '$brokeremail','$brokerphone', '$brokerAddress', '$brokercity', '$brokernotes', '$brokerState', '$user')") or die($mysqli->error);
    $broker_details = $mysqli->query("SELECT * FROM broker_details") or die($mysqli->error);

    $html_content = '';
    while ($broker_detail = $broker_details->fetch_assoc()) : 
        $html_content .= '<option value="' . $broker_detail['broker_id'] . ' " data-foo=" ' . $broker_detail['brokerName'] .' ,    ' . $broker_detail['brokercity'] . ' , ' . $broker_detail['brokerState']  .' "> '. $broker_detail['broker_company'] .' </option>';
    endwhile; 

    $data[] = [
        'success' => 1,
        'msg' => 'Broker Added Successfully!',
        'rows' => $html_content
    ];

    echo json_encode($data);

} else if (($_REQUEST['action_type'] == 'loadtracking')) {
    $id = $_GET['id'];
    $status = $_POST['status'];
    $current_loc = $_POST['current_loc'];
    $pickup_loc = $_POST['pickup_loc'];
    $destination = $_POST['destination'];
    $user = $_SESSION['myFirstName'] . " " . $_SESSION['myLastName'];


    $data = [];
    $html_content = '';

    try {
        $mysqli->query("INSERT INTO load_tracking (load_id, Created_By, status, current_location, Load_pickup_location, load_Destination, timestamp) VALUES('$id', '$user', '$status', '$current_loc', '$pickup_loc', '$destination', '$now')") or die($mysqli->error);

        if($status == "Delivered"){
            add_notification($mysqli, "load", "The load has been delivered", $_GET['id'], $creater);
        }

        $load_tracking = "select DISTINCT Load_pickup_location, load_Destination from load_tracking where load_id='$id'";

        $load_traacking_data = $mysqli->query($load_tracking) or die($mysqli->error);

        // foreach($load_traacking_data as $row){
        //     print("PU:  " . $row['Load_pickup_location'] . "  Des:   " . $row['load_Destination'] . "<br>");
        // };


        foreach ($load_traacking_data as $p) {
            $pickup = $p['Load_pickup_location'];
            $des = $p['load_Destination'];

            $html_content .= `<div style=" background: #e1e1e1; padding: 10px;  border-radius: 5px; margin-bottom: 13px;">
                <span title="Pick Up Location">  $pickup  </span> :
                <span title="Drop Off location"> $des </span>
            </div>`;
            // '<tr title="Pick Up & Drop off locations">
            //     <td style="background-color: #e7e7e7;" colspan="6">
            //         <span title="Pick Up Location">' .  $pickup . ' </span> :
            //         <span title="Drop Off location">' .  $des . '</span>
            //     </td>
            // </tr>';

            $load_tracking_query = "select * from load_tracking where (load_id='$id' and Load_pickup_location='$pickup' and load_Destination='$des') group by tracking_id ORDER BY tracking_id desc";
            $load_traacking_data = $mysqli->query($load_tracking_query) or die($mysqli->error);

            foreach ($load_traacking_data as $row) {
                $tracking_id = $row['tracking_id'];
                $uploadtime = $row['timestamp'];
                $uploaddate = date('m-d-y', strtotime($uploadtime));
                $uploadtime = date('h:m A', strtotime($uploadtime));
                $Created_By = $row['Created_By'];
                $status = $row['status'];
                $current_location = $row['current_location'];
                $current_distace = $row['current_distace'];
                $total_distance = $row['total_distance'];
                $duration = $row['duration'];
                !empty($current_distace) ? $cd = $current_distace . " / " : $cd = '';

                $info = !empty($current_location) ? "Location was updated to <span style='color: red;'>  $current_location </span>" : "Status was updated to <span style='color: red;'>  $status </span>" ;

                $html_content .=  "<div class='track_info'>$info by <span style='color: red;'> $Created_By </span> on <p class='light'> $uploaddate $uploadtime (EST)</p></div>";
                // '<tr>
                //     <td title="Date & time when the status was updated">' .
                //     $uploaddate
                //         . '<p class="light">' . $uploadtime . '</p>
                //     </td>
                //     <td title="Tracker who updated the status">' . $Created_By . '</td>
                //     <td title="Load status for the location">' . $status . ' </td>
                //     <td title="Current Location of the driver">' . $current_location . '</td>
                //     <td title="Estimated Distance and time left for the load to be delivered.">
                //         ' . $cd . '<span class="ligh">' .  $total_distance . '</span>
                //         <p class="light">' . $duration . '</p>
                //     </td>
                //     <td><a style="color: red; cursor: pointer;" href="#" data-tracker_id="' . $tracking_id . '" class="delete_tracker_record"> <i class="uil uil-trash-alt"></i></a></td>
                // </tr>';
            }
        }


        $data[] = [
            'success' => 1,
            'msg' => "Status for load location has been successfully added!",
            'user' => "$user",
            'rows' => "$html_content"
        ];
    } catch (Exception $e) {

        $data[] = [
            'success' => 0,
            'msg' => "Something went wrong!",
        ];
    }

    echo json_encode($data);
} else if (($_REQUEST['action_type'] == 'currentlocUpdate')) {
    $id = $_GET['id'];
    $data = json_decode($_POST['data']);
    $current_loc = $mysqli->real_escape_string($data->current_loc);
    $lat = $mysqli->real_escape_string($data->lat);
    $lng = $mysqli->real_escape_string($data->lng);
    $pickup_loc = $data->pickup;
    $destination = $data->destination;
    $distance = $data->distance;
    $duration = $data->duration;
    $originaDistance = $data->originaDistance;
    $truck_no = $data->truck_no;
    $user = $_SESSION['myFirstName'] . " " . $_SESSION['myLastName'];

    $PU_count = count((is_countable($pickup_loc)) ? ($pickup_loc) : []);

    $data = [];
    $html_content = '';

    try {
        $truckquery = "UPDATE truck_details SET city='$current_loc',lat='$lat',lng='$lng' WHERE truck_id='$truck_no'";
        $mysqli->query($truckquery) or die($mysqli->error);


        $PU_count < 1 ? $PU_count = 1 : $PU_count = $PU_count;
        for ($i = 0; $i < $PU_count; $i++) {
            $pickup = $mysqli->real_escape_string($pickup_loc[$i]);
            $des = $mysqli->real_escape_string($destination[$i]);
            $dis = $mysqli->real_escape_string($distance[$i]);
            $dur = $mysqli->real_escape_string($duration[$i]);
            $Od = $mysqli->real_escape_string($originaDistance[$i]);

            if(
                $distance[$i] == "0" || $distance[$i] == "0 mi" || 
                $distance[$i] == "0 мил." ||
                $distance[$i] == "1 feet" || $duration[$i] == "0 min" || 
                $duration[$i] == "0" || $duration[$i] == ""
            ){
                add_notification($mysqli, "load", "The load has reached to its destination.", $id, $creater);
            }


            $mysqli->query("INSERT INTO load_tracking (load_id, Created_By, current_location, lat, lng, Load_pickup_location, load_Destination, current_distace, total_distance, duration, timestamp) VALUES('$id', '$user', '$current_loc', '$lat', '$lng', '$pickup', '$des', '$dis', '$Od', '$dur', '$now')") or die($mysqli->error);
        }

        for ($i = 0; $i < $PU_count; $i++) {
            $pickup = $pickup_loc[$i];
            $des = $destination[$i];
            $dis = $distance[$i];
            $dur = $duration[$i];
            $Od = $originaDistance[$i];

            $html_content .= `<div style=" background: #e1e1e1; padding: 10px;  border-radius: 5px; margin-bottom: 13px;">
                <span title="Pick Up Location">  $pickup  </span> :
                <span title="Drop Off location"> $des </span>
            </div>`;
                // '<tr title="Pick Up & Drop off locations">
                //     <td style="background-color: #e7e7e7;" colspan="6">
                //         <span title="Pick Up Location">' .  $pickup . ' </span> :
                //         <span title="Drop Off location">' .  $des . '</span>
                //     </td>
                // </tr>';

            $load_tracking_query = "select * from load_tracking where (load_id='$id' and Load_pickup_location='$pickup' and load_Destination='$des') group by tracking_id ORDER BY tracking_id desc";
            $load_traacking_data = $mysqli->query($load_tracking_query) or die($mysqli->error);

            foreach ($load_traacking_data as $row) {
                $tracking_id = $row['tracking_id'];
                $uploadtime = $row['timestamp'];
                $uploaddate = date('m-d-y', strtotime($uploadtime));
                $uploadtime = date('h:i a', strtotime($uploadtime));
                $Created_By = $row['Created_By'];
                $status = $row['status'];
                $current_location = $row['current_location'];
                $current_distace = $row['current_distace'];
                $total_distance = $row['total_distance'];
                $duration = $row['duration'];
                !empty($current_distace) ? $cd = $current_distace . " / " : $cd = '';

                $info = !empty($current_location) ? "Location was updated to <span style='color: red;'>  $current_location </span>" : "Status was updated to <span style='color: red;'>  $status </span>" ;

                $html_content .=  "<div class='track_info'>$info by <span style='color: red;'> $Created_By </span> on <p class='light'> $uploaddate $uploadtime (EST)</p></div>";
                
            //     '<tr>
            //     <td title="Date & time when the status was updated">' .
            //     $uploaddate
            //         . '<p class="light">' . $uploadtime . '</p>
            //     </td>
            //     <td title="Tracker who updated the status">' . $Created_By . '</td>
            //     <td title="Load status for the location">' . $status . ' </td>
            //     <td title="Current Location of the driver">' . $current_location . '</td>
            //     <td title="Estimated Distance and time left for the load to be delivered.">
            //         ' . $cd . '<span class="ligh">' .  $total_distance . '</span>
            //         <p class="light">' . $duration . '</p>
            //     </td>
            //     <td><a style="color: red; cursor: pointer;" href="#" data-tracker_id="' . $tracking_id . '" class="delete_tracker_record"> <i class="uil uil-trash-alt"></i></a></td>
            // </tr>';
            }
        }

        // print($html_content);


        $data[] = [
            'success' => 1,
            'msg' => "Status for load location has been successfully added!",
            'user' => "$user",
            'rows' => "$html_content"
        ];
    } catch (Exception $e) {

        $data[] = [
            'success' => 0,
            'msg' => "Something went wrong!",
        ];
    }

    echo json_encode($data);
} else if (($_REQUEST['action_type'] == 'deleteTrackingRecord')) {
    $id = $_POST['id'];

    $data = [];

    try {
        $query = "DELETE FROM load_tracking` WHERE tracking_id='$id'";
        $mysqli->query($query) or die($mysqli->error);

        $data[] = [
            'success' => 1,
            'msg' => "Tracking Record has been successfully deleted!",
        ];
    } catch (Exception $e) {

        $data[] = [
            'success' => 0,
            'msg' => "Something went wrong!",
        ];
    }

    echo json_encode($data);
} else if (($_REQUEST['action_type'] == 'brokercompany_list')) {
    $brokerid = $mysqli->query("select DISTINCT broker_company from broker_details") or die($mysqli->error);

    $data = [];

    try {
        $brokers = array();
        foreach ($brokerid as $row) {
            $broker = $row['broker_company'];
            array_push($brokers, $broker);
        };
        $data[] = [
            'success' => 1,
            'brokers' => $brokers,
        ];
    } catch (Exception $e) {
        $data[] = [
            'success' => 0,
            'msg' => 'Something went wrong',
            'error' => $e->getMessage()
        ];
    };

    echo json_encode($data);
} else if (($_REQUEST['action_type'] == 'broker_agentList')) {
    $brokercompany = $_GET['brokerage'];
    $brokerid = $mysqli->query("SELECT broker_id, brokerName from broker_details WHERE broker_company='$brokercompany'") or die($mysqli->error);

    $data = [];

    foreach($brokerid as $b){
        $id = $b['broker_id'];
        $bag = $b['brokerName'];
        $html = "<option value='$id'>$bag</option>";

        array_push($data, $html);
    };

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'addnewload')) {

    // Get submitted data 
    $broker = $_POST['broker'];
    $pick_up_Location = array();
    foreach($_POST['pick_up_Location'] as &$PU){array_push($pick_up_Location, addslashes($PU));}
    $pick_up_Location = serialize($pick_up_Location);
    $start_lat = serialize($_POST['start_lat']);
    $start_lng = serialize($_POST['start_lng']);
    $destination = array();
    foreach($_POST['destination'] as &$DES){array_push($destination, addslashes($DES));}
    $destination = serialize($destination);
    $end_lat = serialize($_POST['end_lat']);
    $end_lng = serialize($_POST['end_lng']);
    $truck_number = $_POST['truck_number'];
    $ref_num = $_POST['ref_num'];
    $customer_rate = $_POST['customer_rate'];
    $carier_rate = $_POST['carier_rate'];
    $truck_type = $_POST['truck_type'];
    $comodity = $_POST['comodity'];
    $plattes = $_POST['plattes'];
    $weight = $_POST['weight'];
    $loadtype = $_POST['loadtype'];
    $dispatcher = $_POST['dispatcher'];
    $pickupdate = $_POST['pickupdate'];
    $dropdate = $_POST['dropdate'];
    $notesprivate = $mysqli->real_escape_string($_POST['notesprivate']);
    $distance = serialize($_POST['distance']);
    $time = serialize($_POST['duration']);
    $id = $_POST['id'];
    $brokeragent = $_POST['brokeragent'];
    $lastdestinationpoint = $mysqli->real_escape_string($_POST['destination'][count($_POST['destination']) - 1]);
    $last_end_lat = $_POST['end_lat'][count($_POST['end_lat']) - 1];
    $last_end_lng = $_POST['end_lng'][count($_POST['end_lng']) - 1];
    // $labels = serialize($_POST['labels']);


    // Submitted user data 
    $newload = array(
        'Broker' => $brokeragent,
        'Pick_up_Location' => $pick_up_Location,
        'start_lat' => $start_lat,
        'start_lng' => $start_lng,
        'Destination' => $destination,
        'end_lat' => $end_lat,
        'end_lng' => $end_lng,
        'truck_Number' => $truck_number,
        'Ref_No' => $ref_num,
        'Customer_Rate' => $customer_rate,
        'Carier_Driver_Rate' => $carier_rate,
        'Truck_type' => $truck_type,
        'Comodity' => $comodity,
        'Pallets' => $plattes,
        'Weight' => $weight,
        'load_Type' => $loadtype,
        'dispatcher' => $dispatcher,
        'pickupdate' => $pickupdate,
        'dropdate' => $dropdate,
        'Notes_Private' => $notesprivate,
        'distance' => $distance,
        'time' => $time,
        'brokeragent' => $brokeragent,
    );

    // Store submitted data into session 
    $sessData['postData'] = $newload;
    $sessData['postData']['id'] = $id;

    // ID query string 
    $idStr = !empty($id) ? '?id=' . $id : '';

    if (empty($ref_num)) {
        $error = '<br/>Ref. Number Must be entered.';
    }

    $success = 0;

    if (!empty($error)) {
        $statusMsg = 'Please fill all the mandatory fields.' . $error;
        $success = 0;
    } else {
        if (!empty($id)) {
            // Update data 
            $condition = array('id' => $id);
            $update = $db->update($newload, $condition);

            $newloadID = $id;
            
            $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', city='$lastdestinationpoint', Status='available_on_', lat='$last_end_lat', lng='$last_end_lng'  where truck_id='$truck_number'") or die($mysqli->error);

            $statusMsg = 'Load has been Updated successfully!';
            $success = 1;

            add_notification($mysqli, "load", "The load has been updated", $newloadID, $creater);
        } else {
            // Insert data 
            $insert = $db->insert($newload);
            $newloadID = $insert;
            // print($newloadID);

            $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', city='$lastdestinationpoint', Status='available_on_', lat='$last_end_lat', lng='$last_end_lng'  where truck_id='$truck_number'") or die($mysqli->error);

            $statusMsg = 'Load has been Added successfully!';
            $success = 1;

            add_notification($mysqli, "load", "A new load is added", $newloadID, $creater);
        }

        

        $codFiles = array_filter($_FILES['rate_con_files']['name']);
        $bolFiles = array_filter($_FILES['bol_files']['name']);
        $podFiles = array_filter($_FILES['pod_files']['name']);
        $pickup_files = array_filter($_FILES['pickup_docs']['name']);

        if (!empty($newloadID)) {

            // Add POD Files
            if (!empty($podFiles)) {
                foreach ($podFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($podFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["pod_files"]["tmp_name"][$key], $targetFilePath)) {
                        // File db insert 
                        $FileData = array(
                            'pod_newload_id' => $newloadID,
                            'fileName' => $fileName
                        );
                        $insert = $db->insetpodFile($FileData);
                    } else {
                        $errorUpload .= $codFiles[$key] . ' | ';
                    }
                }
            }

            // Add Cod Files
            if (!empty($codFiles)) {
                foreach ($codFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($codFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["rate_con_files"]["tmp_name"][$key], $targetFilePath)) {
                        // Image db insert 
                        $FileData = array(
                            'newload_id' => $newloadID,
                            'fileName' => $fileName
                        );
                        $insert = $db->insetrateconFile($FileData);
                    } else {
                        $errorUpload .= $codFiles[$key] . ' | ';
                    }
                }
            }

            // Add Bol Files
            if (!empty($bolFiles)) {
                foreach ($bolFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($bolFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["bol_files"]["tmp_name"][$key], $targetFilePath)) {
                        // Image db insert 
                        $FileData = array(
                            'bol_newload_id' => $newloadID,
                            'fileName' => $fileName
                        );
                        $insert = $db->insetbolFile($FileData);
                    } else {
                        $errorUpload .= $codFiles[$key] . ' | ';
                    }
                }
            }

            // Add Pickup Files
            if (!empty($pickup_files)) {
                foreach ($pickup_files as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($pickup_files[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["pickup_docs"]["tmp_name"][$key], $targetFilePath)) {
                        // Image db insert 
                        $FileData = array(
                            'pickup_newload_id' => $newloadID,
                            'file_name' => $fileName
                        );
                        $insert = $db->insetpickupfile($FileData);
                    } else {
                        $errorUpload .= $pickup_files[$key] . ' | ';
                    }
                }
            }

        } else {
            $statusMsg = 'Someting went wrong, please try again!';
            $success = 0;
        }
    }

    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $html_content = tdata($load_en_routedata);

    $data[] = [
        'success' => $success,
        'msg' => $statusMsg,
        // 'rows' => $html_content
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
    // Previous image files 
    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'conFiles';
    $conditions['return_type'] = 'podFiles';
    $conditions['return_type'] = 'bolFiles';
    $prevData = $db->getRows($conditions);

    // Delete New Load data 
    $condition = array('id' => $_GET['id']);
    $delete = $db->delete($condition);

    add_notification($mysqli, "load", "The load has been Deleted", $_GET['id']);

    if ($delete) {
        // Delete rate_con_files data 
        $condition = array('newload_id' => $_GET['id']);
        $delete = $db->deleteConFile($condition);
        $delete = $db->deletebolFile($condition);
        $delete = $db->deletepodFile($condition);
        $delete = $db->deletepcikupfile($condition);

        // Remove CON files from server 
        if (!empty($prevData['rate_con_files'])) {
            foreach ($prevData['rate_con_files'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        // Remove POD files from server 
        if (!empty($prevData['pod_files'])) {
            foreach ($prevData['pod_files'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        // Remove BOL files from server 
        if (!empty($prevData['bol_files'])) {
            foreach ($prevData['bol_files'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        // Remove Pickup files from server 
        if (!empty($prevData['pickup_docs'])) {
            foreach ($prevData['pickup_docs'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        $success = 1;
        $msg  = 'Record has been deleted successfully.';
    } else {
        $success = 0;
        $msg  = 'Some problem occurred, please try again.';
    }

    // // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'load_en_route') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_en_route' WHERE id=$id") or die($mysqli->error);


    $success = 1;
    $msg  = 'Record has been Updated successfully.';


    // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'load_delivered') && !empty($_GET['id'])) {

    $id = $_GET['id'];
    $date = new DateTime("now", new DateTimeZone('America/New_York'));
    $date = $date->format('Y-m-d H:i:s');

    add_notification($mysqli, "load", "The load has been Delivered", $_GET['id']);


    $mysqli->query("UPDATE newload SET status='load_delivered', delivery_date='$date' WHERE id=$id") or die($mysqli->error);


    $success = 1;
    $msg  = 'Record has been Updated successfully.';


    // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'load_issue') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_issue' WHERE id=$id") or die($mysqli->error);


    $success = 1;
    $msg  = 'Record has been Updated successfully.';


    // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'load_invoiced') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_invoiced' WHERE id=$id") or die($mysqli->error);


    $success = 1;
    $msg  = 'Record has been Updated successfully.';


    // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'load_paid') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_paid' WHERE id=$id") or die($mysqli->error);


    $success = 1;
    $msg  = 'Record has been Updated successfully.';


    // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} elseif (($_REQUEST['action_type'] == 'load_Factored') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_Factored' WHERE id=$id") or die($mysqli->error);


    $success = 1;
    $msg  = 'Record has been Updated successfully.';


    // Data for the loads
    // $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);
    // $load_en_route = tdata($load_en_routedata);
    // $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);
    // $load_delivered = tdata($load_delivereddata);
    // $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);
    // $load_issue = tdata($load_issuedata);
    // $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);
    // $load_invoiced = tdata($load_invoiceddata);
    // $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
    // $load_paid = tdata($load_paiddata);

    // Status message 
    $data[] = [
        "success" => $success,
        "msg" => $msg,
        // "load_en_route" => $load_en_route,
        // "load_delivered" => $load_delivered,
        // "load_issue" => $load_issue,
        // "load_invoiced" => $load_invoiced,
        // "load_paid" => $load_paid,
    ];

    echo json_encode($data);
} else if(($_REQUEST['action_type'] == 'edit_load') && !empty($_GET['id'])){
    $id = $_GET['id'];

    echo loadform($mysqli,$id, $db);
} else if (($_REQUEST['action_type'] == 'load_rating') && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $delivery = $_POST['load_delivery_star'];
    $communication = $_POST['load_communication_star'];
    $comments = $mysqli->real_escape_string($_POST['load_comments']);
    $uploaded_at = date("Y-m-d H:i:s");

    $query = "UPDATE newload SET load_delivery='$delivery', load_communication='$communication',load_rating_comments='$comments', load_rating_uploaded_on='$uploaded_at', load_rating_addedBy='$creater' WHERE id='$id'";
    $execution = $mysqli->query($query) or die($mysqli->error);

    if($execution){
        add_notification($mysqli, "load", "Load has been rated.", $id, $creater);

        $data[] = [
            "success" => 1,
            "msg" => "Rating has been added successfully!",
            "rows" => loadRating($mysqli, $id)
        ];
    } else {
        $data[] = [
            "success" => 0,
            "msg" => "Something went wrong! Please try again."
        ];
    }


    

    echo json_encode($data);
} else if (($_REQUEST['action_type'] == 'driver_rating') && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $delivery = $_POST['driver_delivery_star'];
    $driving = $_POST['driver_driving_star'];
    $communication = $_POST['driver_communication_star'];
    $comments = $mysqli->real_escape_string($_POST['load_driver_comments']);
    $uploaded_at = date("Y-m-d H:i:s");

    $query = "UPDATE newload SET driver_delivery='$delivery', driver_driving='$driving', driver_communication='$communication', driver_rating_comments='$comments', driver_rating_uploaded_on='$uploaded_at', driver_rating_addedBy = '$creater' WHERE id='$id'";
    $execution = $mysqli->query($query) or die($mysqli->error);

    if ($execution) {
        add_notification($mysqli, "driver", "Driver has been rated.", $id, $creater);

        $data[] = [
            "success" => 1,
            "msg" => "Rating has been added successfully!",
            "rows" => driverRating($mysqli, $id)
        ];
    } else {
        $data[] = [
            "success" => 0,
            "msg" => "Something went wrong! Please try again.",
            
        ];
    }
    echo json_encode($data);
} 


function tdata($query)
{
    $rows = '';
    if (!empty($query)) {
        $i = 0;
        foreach ($query as $row) {
            $i++;

            // print($row['id'] . "<br>");

            // $pickuplocation = unserialize($row['Pick_up_Location'])[0];
            $PU_count = count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []);
            count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []) > 0 ? $pickuplocation = stripslashes(unserialize($row['Pick_up_Location'])[0]) : $pickuplocation = stripslashes(unserialize($row['Pick_up_Location']));
            count((is_countable(unserialize($row['Destination']))) ? unserialize($row['Destination']) : []) > 0 ? $destination = stripslashes(unserialize($row['Destination'])[0]) : $destination = stripslashes(unserialize($row['Destination']));

            $PU_count > 1 ? $ml_info = '<span title="Multiple Locations added for the load" style="width: 15px;height: 15px;background: #ebeb57;padding: 3px 9px; border-radius: 50%;font-size: 10px;margin-left: 5px;cursor: help;">!</span>' : $ml_info =  '';

            $podfile = "";
            if (!empty($row['pod_newload_id'])) {
                $podfile = 'style="background-color: var(--pod-attached);"';
            } else if (!empty($row['bol_newload_id'])) {
                $podfile = 'style="background-color: var(--bol-attached);"';
            }

            $rows .= '<tr ' . $podfile . ' >';

            $rows .= '<td>' . $i . '</td>';
            $rows .= '<td>';
            $rows .= '<a style="cursor: pointer;" data-action_type="dispatcher" class="load_dispatcher_form" data-load_id="' . $row['id'] . '"> JBA <br>' . $row['id'] . '</a><br>';
            $rows .= '<span style="color: var(--light-font);">' . $row['Ref_No'] . '</span>';

            $rows .= '</td>';
            $rows .= '<td>' . $row['dispatcher'] . '</td>';
            $rows .= '<td style="min-width: 220px;">';
            $rows .= '<div>';
            $rows .= '<div class="truckImg">';
            $rows .= '<img src="./Assets/Images/Business Logo.png" width="30px" />';
            $rows .= '</div>';

            $rows .= '<div class="additionalcontent" style="width: 150px;">';
            $rows .= '<p style="margin-bottom: 0; float: left; margin-right: 7px;">' . $row['broker_company'] . '</p><span style="color: var(--light-font);">' . $row['brokerState'] . '</span> <br>';

            $rows .= '<span style="color: var(--light-font);">$ ' . $row['Customer_Rate'] . '</span><br>';

            $rows .= '<a href="tel:' . $row['brokerphone'] . '">' . $row['brokerphone'] . '</a>';
            $rows .= '<a style="overflow-wrap: anywhere;" href=" mailto:' . $row['brokeremail'] . '" target="_blank" rel="noopener noreferrer">' . $row['brokeremail'] . '</a>';
            $rows .= '</div>';
            $rows .= '</div>';

            $rows .= '</td>';
            $rows .= '<td style="min-width: 290px;">';

            $rows .= '<div>';
            $rows .= '<div class="truckImg">';
            $rows .= ' <img src="./Assets/Images/truck.png" width="30px" />';
            $rows .= '</div>';

            $rows .= '<div class="additionalcontent" style="width: 150px;">';
            $rows .= '<p style="margin-bottom: 0; float: left; margin-right: 7px;">' . $row['truckNumber'] . '</p>';
            $rows .= '<span style="color: var(--light-font);">' . $row['truckDriver'] . '</span> <br>';
            $rows .= '<span style="color: var(--light-font);">$' . $row['Carier_Driver_Rate'] . '</span><br>';
            $rows .= '<a href="tel:+1' . $row['cpPhone'] . '">' . $row['cpPhone'] . '</a>';
            $rows .= '<a href="mailto:' . $row['cpemail'] . '" target="_blank" rel="noopener noreferrer">' . $row['cpemail'] . '</a>';
            $rows .= '</div>';
            $rows .= '</div>';

            $rows .= '</td>';

            $rows .= '<td>';
            $rows .= '<div style="width: 265px;">';
            $rows .= '<div class="origin"></div>';
            $rows .= '<div class="additionalcontent" style="width: 245px;"><a href="https://www.google.com/maps/place/' . $pickuplocation  . '" target="_blank" rel="noopener noreferrer">' . $pickuplocation . '</a>';
            $rows .= $ml_info;
            '</div>';
            $rows .= '</div>';
            $rows .= '<div style="width: 265px;">';
            $rows .= '<div class="destinationbox"></div>';
            $rows .= '<div class="additionalcontent" style="width: 245px;"><a href="https://www.google.com/maps/place/' . $destination . '" target="_blank" rel="noopener noreferrer">' . $destination . '</a></div>';
            $rows .= '</div>';
            $rows .= '</td>';

            $rows .= '<td style="font-size: 10px;">';
            $rows .= '<p style="margin-bottom: 10px;">';
            if (strtotime($row['pickupdate']) > 0) {
                $pickupdate = $row['pickupdate'];
                $rows .= date("m-d-y", strtotime($pickupdate));
                 $rows .= " ";
                $rows .= date("h:i a", strtotime($pickupdate));
            } else {
                $rows = '';
            }
            $rows .= '</p>';
            $rows .= '<p style="margin-bottom: 0;">';
            if (strtotime($row['dropdate']) > 0) {
                $originalDate = $row['dropdate'];
                $rows .= date("m-d-y", strtotime($originalDate));
                $rows .= " ";

                $rows .= date("h:i a", strtotime($originalDate));
            } else {
                $rows = '';
            }
            $rows .= '</p>';
            $rows .= '</td>';

            $rows .= '<td>' . $row['newchecknotes'] . '</td>';
            $rows .= '<td style="display: flex; justify-content: center;">';

            $rows .= '<li>';
            $rows .= '<div style="margin: 10px 15px 0px" onclick="newcheckcallsbtn()">';

            $rows .= '<a style="color: var(--font); font-size: 15px; margin: 7px 10px 10px 0; cursor: pointer; " href="index.php?action_type=newcall&id=' . $row['id'] . '">';
            $rows .= '<img class="checkcall" src="" width="15px" height="15px" />';
            $rows .= '</a>';
            $rows .= '</div>';

            $rows .= '</li>';

            $rows .= '<div class="btn-group btn-group-rounded">';
            $rows .= '<button type="button" class="btn btn-default btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius:3px; background: none; border: none; outline: none; text-align:center;">';
            $rows .= '<i class="uil uil-ellipsis-h"></i>';
            $rows .= '</button>';
            $rows .= ' <ul class="dropdown-menu">';
            $rows .= '<li>';
            $rows .= '<a style="color: green; font-size: 15px; margin: 7px 10px 10px 0; cursor: pointer;" class="get_load_form" data-action_type="edit_load" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="uil uil-pen"></i>Edit';
            $rows .= ' </a>';
            $rows .= '</li>';



            $rows .= '<li>';
            $rows .= '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" href="index.php?action_type=dispatcher&id=' . $row['id'] . '">';
            $rows .= '<i class="uil uil-minus-path"></i>Dispatch Load';
            $rows .= '</a>';
            $rows .= '</li>';

            $rows .= '<li>';
            $rows .= '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font); " class="load_action" data-action_type="load_en_route" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="fa-solid fa-route"></i>Loads en Route';
            $rows .= '</a>';
            $rows .= '</li>';

            $rows .= '<li>';
            $rows .= '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_delivered" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="fa-solid fa-truck-ramp-box"></i>Loads Delivered';
            $rows .= '</a>';
            $rows .= '</li>';

            $rows .= '<li>';
            $rows .= '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_issue" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="fa-solid fa-triangle-exclamation"></i>Loads Issue';
            $rows .= '</a>';
            $rows .= '</li>';

            $rows .= '<li>';
            $rows .= '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_invoiced" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="fa-solid fa-file-invoice-dollar"></i>Loads Invoiced';
            $rows .= '</a>';
            $rows .= '</li>';

            $rows .= '<li>';
            $rows .= '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_paid" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="fa-solid fa-money-check-dollar"></i>Loads Paid';
            $rows .= '</a>';
            $rows .= '</li>';

            $rows .= '<li>';
            $rows .= '<a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " class="load_action" data-action_type="delete" data-load_id="' . $row['id'] . '">';
            $rows .= '<i class="uil uil-trash-alt"></i>Delete';
            $rows .= '</a>';
            $rows .= '</li>';




            $rows .= '</ul>';
            $rows .= '</div>';
            $rows .= '</td>';

            $rows .= '</tr>';
        }
    } else {
        $rows = '<tr>';
        $rows .= '<td colspan="9">No Data found...</td>';
        $rows .= '</tr>';
    }

    return $rows;
}

function loadQuery($status)
{
    return "SELECT * 
    FROM newload n 
    LEFT OUTER JOIN truck_details AS t 
    ON t.truck_id = n.truck_Number 
    LEFT OUTER JOIN broker_details AS b 
    ON b.broker_id = n.Broker 
    LEFT OUTER JOIN 
    (select * from newcheckcalls C where exists (select * from (select newloadID, max(callid) as callid
    from newcheckcalls cc 
    GROUP BY newloadID
    ) as cc where C.callid = cc.callid)) C on n.id = C.newloadID 
    LEFT OUTER JOIN 
    (select * from pod_files P where exists (select * from (select pod_newload_id, max(pod_id) as pod_id
    from pod_files pp 
    GROUP BY pod_newload_id
    ) as pp where P.pod_id = pp.pod_id)) AS P
    ON n.id = P.pod_newload_id
    LEFT OUTER JOIN 
    (select * from bol_files B where exists (select * from (select bol_newload_id, max(bol_id) as bol_id
    from bol_files bb 
    GROUP BY bol_newload_id
    ) as bb where B.bol_id = bb.bol_id)) AS B
    ON n.id = B.bol_newload_id
    where n.status='$status'
    ORDER BY id DESC";
}


function loadform($mysqli, $id, $db){

    $id = $id;

    $conditions['where'] = array(
        'id' => $id,
    );
    $conditions['return_type'] = 'conFiles';
    $newloadData = $db->getRows($conditions);

    $newloaddata = $mysqli->query("SELECT * FROM newload n 
    LEFT OUTER JOIN truck_details AS t ON t.truck_id = n.truck_Number 
    LEFT OUTER JOIN broker_details AS b ON b.broker_id = n.Broker 
    LEFT OUTER JOIN (select *, max(callid) from newcheckcalls 
    GROUP BY newloadID) C ON C.newloadID = n.id where n.id = '$id'
    ORDER BY id DESC limit 1") or die($mysqli->error);

    $broker_details = $mysqli->query("SELECT * FROM broker_details GROUP BY broker_company") or die($mysqli->error);
    $newloaddata = mysqli_fetch_assoc($newloaddata);

    // Get BOL Files 
    $conditions['where'] = array(
        'id' => $id,
    );
    $conditions['return_type'] = 'bolFiles';
    $bolFiles = $db->getRows($conditions);
    

    // Get POD Files 
    $conditions['where'] = array(
        'id' => $id,
    );
    $conditions['return_type'] = 'podFiles';
    $podFiles = $db->getRows($conditions);
    

    // Get Pickup Files 
    $conditions['where'] = array(
        'id' => $id,
    );
    $conditions['return_type'] = 'pickup_files';
    $pickup_files = $db->getRows($conditions);
    


    $PU_count = count((is_countable(unserialize($newloaddata['Pick_up_Location']))) ? unserialize($newloaddata['Pick_up_Location']) : []);
    $PU_location = unserialize($newloaddata['Pick_up_Location']);
    $Destination = unserialize($newloaddata['Destination']);
    $start_lat = unserialize($newloaddata['start_lat']);
    $start_lng = unserialize($newloaddata['start_lng']);
    $end_lat = unserialize($newloaddata['end_lat']);
    $end_lng = unserialize($newloaddata['end_lng']);
    $distance = unserialize($newloaddata['distance']);
    $time = unserialize($newloaddata['time']);
    $labelsaar = unserialize($newloaddata['labels']);
    $PU_count < 1 ? $PU_count = 1 : $PU_count = $PU_count;
    $PU_count >= 1 ? $PU1st_loc = $PU_location[0] : $PU1st_loc = $PU_location;
    $PU_count >= 1 ? $PU1st_des = $Destination[0] : $PU1st_des = $Destination;
    $PU_count >= 1 ? $nl1st_sl = $start_lat[0] : $nl1st_sl = $start_lat;
    $PU_count >= 1 ? $nl1st_slung = $start_lng[0] : $nl1st_slung = $start_lng;
    $PU_count >= 1 ? $nl1st_endlat = $end_lat[0] : $nl1st_endlat = $end_lat;
    $PU_count >= 1 ? $nl1st_elng = $end_lng[0] : $nl1st_elng = $end_lng;
    $PU_count >= 1 ? $nl1st_dis = $distance[0] : $nl1st_dis = $distance;
    $PU_count >= 1 ? $nl1st_dur = $time[0] : $nl1st_dur = $time;

    $row = '';

    $row .= '<div class="newLoad modal newloadedit_modal" style="display:block;" >';

        $row .='<div class="modal-content">';
            $row .='<span class="load_edit_close close" style="opacity: 0.8;" onclick="closemodal()">&times;</span>';
            $row .='<div class="newloadHeader">';
                $row .='<h2>Update Load</h2>';
            $row .='</div>';
            $row .='<div class="form">';
                $row .= '<form method="post" id="new_load_form" class="new_load_form" enctype="multipart/form-data">';

                    $row .='<div class="inputgroup">';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="broker">Brokerage</label>';
                            $row .= '<div class="select">';
                                $row .= '<select required class="form-control select2 broker" style="width: 100%;" name="broker" id="broker">';
                                    while ($broker_detail = $broker_details->fetch_assoc()) : 

                                        ($broker_detail['broker_company'] == $newloaddata['broker_company']) ? $checked = "selected=selected" :  $checked = '';

                                        $row .= '<option value="'. $broker_detail['broker_id'] .'" data-foo="'. $broker_detail['brokercity'] . ' , ' . $broker_detail['brokerState'] . '" '. $checked .' >'. $broker_detail['broker_company'] .' </option>';

                                    endwhile; 
                                $row .= '</select>';
                                $row .= '<div class="addmore"><button type="reset" id="brokerDetailsbtn"  class="brokerDetailsbtn">+</button></div>';
                            $row .= '</div>';

                        $row .= '</div>';

                        
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="truckNo">Broker Agent</label>';
                            $row .= '<select  name="brokeragent" id="brokeragent">';
                                $brokercompany = $newloaddata['broker_company'];
                                $bid = $newloaddata['Broker'];
                                $brokerid = $mysqli->query("SELECT broker_id, brokerName from broker_details WHERE broker_company='$brokercompany'") or die($mysqli->error);

                                foreach($brokerid as $b){
                                    $id = $b['broker_id'];
                                    $bag = $b['brokerName'];
                                    ($id == $bid) ? $checked = "selected=selected" :  $checked = '';
                                    $row .= '<option value="'. $id .'" '. $checked .' > '. $bag .'</option>';
                                }
                            $row .= '</select>';
                                    
                        $row .= '</div>';



                    $row .='</div>';
                    $row .='<div class="field_wrapper">';

                        $row .= '<div class=" inputgroup">';

                            !empty($newloaddata['Pick_up_Location']) ? $PU1st_loc = $PU1st_loc : $PU1st_loc = '';
                            !empty($newloaddata['start_lat']) ? $nl1st_sl = $nl1st_sl : $nl1st_sl = '';
                            !empty($newloaddata['start_lng']) ? $nl1st_slung = $nl1st_slung : $nl1st_slung = '';
                            $row .= '<div class="inputbox">';
                                $row .= '<span class="msg"></span>';
                                $row .= '<label for="pickupLocation">Pick Up Location</label>';
                                $row .= '<input required class="start" id="start" type="text" placeholder="Pick Up Location" name="pick_up_Location[]" value="'. $PU1st_loc .'" />';
                                $row .= '<span class="pu_blank_msg"></span>';
                                $row .= '<input type="text" name="start_lat[]" class="start_lat" hidden value="'. $nl1st_sl .'">';
                                $row .= '<input type="text" name="start_lng[]" class="start_lng" hidden value="'. $nl1st_slung .'">';
                            $row .= '</div>';


                            !empty($newloaddata['Destination']) ? $PU1st_des = $PU1st_des : $PU1st_des = '';
                            !empty($newloaddata['end_lat']) ? $nl1st_endlat = $nl1st_endlat : $nl1st_endlat = '';
                            !empty($newloaddata['end_lng']) ? $nl1st_elng = $nl1st_elng : $nl1st_elng = '';
                            !empty($newloaddata['distance']) ? $nl1st_dis = $nl1st_dis : $nl1st_dis = '';
                            !empty($newloaddata['time']) ? $nl1st_dur = $nl1st_dur : $nl1st_dur = '';
                            $row .= '<div class="inputbox">';
                                $row .= '<label for="destination">Destination</label>';
                                $row .= '<div class="select">';
                                    $row .= '<input required class="end" style="width: 100%;" id="end" type="text" placeholder="Destination" name="destination[]" value="'. $PU1st_des .'" />';
                                    $row .= '<input type="text" name="end_lat[]" class="end_lat" hidden value="'. $nl1st_endlat .'">';
                                    $row .= '<input type="text" name="end_lng[]" class="end_lng" hidden value="'. $nl1st_elng .'">';
                                    $row .= '<input type="text" name="distance[]" class="distance" hidden value="'. $nl1st_dis .'">';
                                    $row .= '<input type="text" name="duration[]" class="duration" hidden value="'. $nl1st_dur .'">';
                                    $row .= '<a href="javascript:void(0);" class="add_button" title="Add field">+</a>';
                                $row .= '</div>';
                            $row .= '</div>';
                        $row .= '</div>';

                        
                        
                        for ($i = 1; $i < $PU_count; $i++) {
                            $PU_count < 1 ? $PU_count = 1 : $PU_count = $PU_count;
                            $PU_count > 0 ? $PU_loc = $PU_location[$i] : $PU_loc = $PU_location;
                            $PU_count > 0 ? $PU_des = $Destination[$i] : $PU_des = $Destination;
                            $PU_count > 0 ? $nl_sl = $start_lat[$i] : $nl_sl = $start_lat;
                            $PU_count > 0 ? $nl_slung = $start_lng[$i] : $nl_slung = $start_lng;
                            $PU_count > 0 ? $nl_endlat = $end_lat[$i] : $nl_endlat = $end_lat;
                            $PU_count > 0 ? $nl_elng = $end_lng[$i] : $nl_elng = $end_lng;
                            $PU_count > 0 ? $nl_dis = $distance[$i] : $nl_dis = $distance;
                            $PU_count > 0 ? $nl_dur = $time[$i] : $nl_dur = $time;
                    

                            $row .= '<div class=" inputgroup">';

                                !empty($newloaddata['Pick_up_Location']) ? $PU_loc = $PU_loc : $PU_loc = '';
                                !empty($newloaddata['start_lat']) ? $nl_sl = $nl_sl : $nl_sl = '';
                                !empty($newloaddata['start_lng']) ? $nl_slung = $nl_slung : $nl_slung = '';
                                $row .= '<div class="inputbox">';
                                    $row .= '<span class="msg"></span>';
                                    $row .= '<input class="start" id="start" type="text" placeholder="Pick Up Location" name="pick_up_Location[]" value="'. $PU_loc .'" />';
                                    $row .= '<span class="pu_blank_msg"></span>';
                                    $row .= '<input type="text" name="start_lat[]" class="start_lat" hidden value="'. $nl_sl .'">';
                                    $row .= '<input type="text" name="start_lng[]" class="start_lng" hidden value="'. $nl_slung .'">';
                                $row .= '</div>';

                                !empty($newloaddata['Destination']) ? $PU_des = $PU_des : $PU_des = '';
                                !empty($newloaddata['end_lat']) ? $nl_endlat = $nl_endlat : $nl_endlat = '';
                                !empty($newloaddata['end_lng']) ? $nl_elng = $nl_elng : $nl_elng = '';
                                !empty($newloaddata['distance']) ? $nl_dis = $nl_dis : $nl_dis = '';
                                !empty($newloaddata['time']) ? $nl_dur = $nl_dur : $nl_dur = '';
                                $row .= '<div class="inputbox" style="flex-direction: row;">';
                                    $row .= '<div style="width: 100%;">';
                                        $row .= '<input style="width: 100%;" type="text" name="destination[]" class="end pac-target-input" placeholder="Drop off" value="'. $PU_des .' " />';
                                        $row .= '<input type="text" name="end_lat[]" class="end_lat" hidden value="'. $nl_endlat .'">';
                                        $row .= '<input type="text" name="end_lng[]" class="end_lng" hidden value="'. $nl_elng .'">';
                                        $row .= '<input type="text" name="distance[]" class="distance" hidden value="'. $nl_dis .'">';
                                        $row .= '<input type="text" name="duration[]" class="duration" hidden value="'. $nl_dur .'">';
                                    $row .= '</div>';
                                    $row .= '<a href="javascript:void(0);" class="remove_button">-</a>';
                                $row .= '</div>';
                            $row .= '</div>';
                        }

                    $row .='</div>';

                    $row .='<div class="inputgroup">';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="truckNo">Truck Number</label>';
                            $row .= '<div class="select">';
                                $row .= '<select class="form-control select2 truck_Number" style="width: 90%;" name="truck_number" id="truck_Number">';

                                    
                                    $truck_details = $mysqli->query("SELECT * FROM truck_details") or die($mysqli->error);
                                    while ($truck_detai = $truck_details->fetch_assoc()) : 
                                        ($truck_detai['truck_id'] == $newloaddata['truck_Number']) ? $checked = "selected=selected" :  $checked = '';

                                        $row .= '<option value="'. $truck_detai['truck_id'].'" data-foo="'. $truck_detai['truckDriver'].' " '. $checked .' >'. $truck_detai['truckNumber'] .' </option>';

                                    endwhile; 

                                $row .= '</select>';
                                $row .= '<div class="addmore"><button type="reset" id="truckNumberbtn" class="truckNumberbtn" >+</button></div>';
                            $row .= '</div>';
                        $row .= '</div>';

                        !empty($newloaddata['Ref_No']) ? $ref_no = $newloaddata['Ref_No'] : $ref_no = '';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="refNo">Reference Number</label>';
                            $row .= '<input required type="text" name="ref_num" value="'. $ref_no .'" placeholder="ref-123">';
                        $row .= '</div>';

                    $row .='</div>';
                    $row .='<div class="inputgroup">';

                        !empty($newloaddata['Customer_Rate']) ? $customer_rate =  $newloaddata['Customer_Rate'] : $customer_rate =  '';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="customerrate">Customer Rate</label>';
                            $row .= '<input required type="number" name="customer_rate" id="" value="'. $newloaddata['Customer_Rate'] .'" placeholder="10.54">';
                        $row .= '</div>';

                        !empty($newloaddata['Carier_Driver_Rate']) ? $Carier_Driver_Rate = $newloaddata['Carier_Driver_Rate'] : $Carier_Driver_Rate = '';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="carierrate">Carier/Driver Rate</label>';
                            $row .= '<input required type="number" name="carier_rate" id="" value="'. $newloaddata['Carier_Driver_Rate'] .'" placeholder="10.23">';
                        $row .= '</div>';

                    $row .='</div>';
                    $row .='<div class="inputgroup">';

                        !empty($newloaddata['Truck_type']) ? $Truck_Type = $newloaddata['Truck_type'] : $Truck_Type = '';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="trucktype">Truck Type</label>';
                            $row .= '<input type="text" name="truck_type" id="" value="'. $Truck_Type .'" placeholder="Truck">';
                        $row .= '</div>';

                        !empty($newloaddata['Comodity']) ? $Comodityd = $newloaddata['Comodity'] : $Comodityd = '';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="comodity">Comodity</label>';
                            $row .= '<input type="text" name="comodity" id="" value="'. $Comodityd .'" placeholder="Please enter comodity">';
                        $row .= '</div>';


                    $row .='</div>';
                    $row .='<div class="inputgroup">';
                        $row .= '<div class="subgroup">';
                            $row .= '<div class="inputbox">';

                                (!empty($newloaddata['load_Type']) && $newloaddata['load_Type'] == 'Full Load') ? $flchecked = "checked" : $flchecked = "";
                                (!empty($newloaddata['load_Type']) && $newloaddata['load_Type'] == "LTL") ? $ltlchecked = "checked" : $ltlchecked = "";
                                $row .= '<label for="loadtype">Load Type</label>';
                                $row .= '<label for="">';
                                    $row .= '<input type="radio" name="loadtype" id="" value="Full Load" '. $flchecked .'>Full load';
                                    $row .= '<input type="radio" name="loadtype" id="" value="LTL" '. $ltlchecked .' >LTL';
                                $row .= '</label>';


                            $row .= '</div>';
                        $row .= '</div>';
                        $row .= '<div class="subgroup">';

                            !empty($newloaddata['Pallets']) ? $Palletsd = $newloaddata['Pallets'] : $Palletsd = '';
                            $row .= '<div class="inputbox">';
                                $row .= '<label for="plattes">Pallets</label>';
                                $row .= '<input type="number" name="plattes" id="" value="'. $Palletsd .'" placeholder="10">';
                            $row .= '</div>';

                            !empty($newloaddata['Weight']) ? $Weightd = $newloaddata['Weight'] : $Weightd = '';
                            $row .= '<div class="inputbox">';
                                $row .= '<label for="weight">Weight</label>';
                                $row .= '<input type="number" name="weight" id="" value="'. $Weightd .'" placeholder="10">';
                            $row .= '</div>';
                        $row .= '</div>';

                    $row .='</div>';

                    $row .='<div class="inputgroup">';
                        $row .= '<div class="subgroup">';

                            !empty($newloaddata['pickupdate']) ? $pickupdate = substr(date('c', strtotime($newloaddata['pickupdate'])), 0, 16) : $pickupdate = '';
                            $row .= '<div class="inputbox">';
                                $row .= '<label for="loadtype">Pick Up Date</label>';
                                $row .= '<input required type="datetime-local" name="pickupdate" id="" value="'. substr(date('c', strtotime($newloaddata['pickupdate'])), 0, 16)  .'">';
                            $row .= '</div>';

                            !empty($newloaddata['dropdate']) ? $dropdate = substr(date('c', strtotime($newloaddata['dropdate'])), 0, 16) : $dropdate = '';
                            $row .= '<div class="inputbox">';
                                $row .= '<label for="ratecon">Drop of Date</label>';
                                $row .= '<input required type="datetime-local" name="dropdate" id="" value="'. substr(date('c', strtotime($newloaddata['dropdate'])), 0, 16) .'">';
                            $row .= '</div>';
                        $row .= '</div>';
                        //($_SESSION['myusertype'] == "admin") ? $eidtDis = 'style="display: flex;"' : $eidtDis = 'style="display: none;"'; 
                        
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="dispatcher">Dispatcher</label>';
                            $row .= '<input type="text" name="dispatcher" value="'. $newloaddata['dispatcher'] .'" placeholder="Dispatcher">';
                        $row .= '</div>';

                    $row .='</div>';

                    $row .= '<div class="inputgroup">';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="trucktype">Add Labels</label>';
                            $row .= '<select class="select2" name="labels[]" id="" multiple>';
                                $labels = "SELECT * from labels";
                                $labels = $mysqli->query($labels) or die($mysqli->error);

                                foreach($labels as $label){
                                    (!empty($labelsaar) && in_array($label['label_id'], $labelsaar)) ? $selected = "selected" : $selected = "";
                                    $row .= '<option value="' . $label['label_id'] .'" '. $selected .'> '. $label['label_name'] .' </option>';
                                }
                            $row .= '</select>';
                        $row .= '</div>';
                    $row .= '</div>';

                    $row .='<div class="inputgroup">';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="pod">Attach CON</label>';
                            $row .= '<input type="file" name="rate_con_files[]" id="" multiple>';
                        $row .= '</div>';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="bol">Attach BOL</label>';
                            $row .= '<input type="file" name="bol_files[]" id="" multiple>';
                        $row .= '</div>';
                    $row .='</div>';
                    $row .='<div class="inputgroup">';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="pod">Attach POD</label>';
                            $row .= '<input type="file" name="pod_files[]" id="" multiple>';
                        $row .= '</div>';
                        $row .= '<div class="inputbox">';
                            $row .= '<label for="pod">Attach Pickup Documents</label>';
                            $row .= '<input type="file" name="pickup_docs[]" id="" multiple>';
                        $row .= '</div>';
                    $row .='</div>';
                    $row .='<div class="textareainputbox">';
                        $row .= '<label for="notesprivate">Notes Private</label>';
                        $row .= '<textarea name="notesprivate" id="notesprivate" cols="160" rows="5" placeholder="Enter your private notes here...">'.$newloaddata['Notes_Private'].'</textarea>';
                    $row .='</div>';

                    
                    $row .='<div class="User_Files">';
                        $row .= '<div class="rateConFiles">';
                            $row .= '<h2>Rate CON Files</h2>';

                            $row .= '<div id="rate_con_files_con">';
                                if (!empty($newloadData['rate_con_files'])) { 

                                    foreach ($newloadData['rate_con_files'] as $fileRow) { 

                                        $row .= '<div id="con'.$fileRow['id'].'">';
                                            $file = $fileRow['fileName'];
                                            $fileName = ltrim($file, $id . '_');

                                            $row .= '<a href="Assets/uploads/cod_Files/'.$fileRow['fileName'].'" target="_blank" rel="noopener noreferrer">'. $fileName .' </a>';
                                            $row .= '<a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deleteCon(\''.$fileRow['id'].'\')"><i class="uil uil-trash-alt"></i></a> <br>';
                                        $row .= '</div>';

                                    } 
                                } else { 
                                    $row .= '<p style="color: red;">No Files available</p>';
                                } 
                            $row .= '</div>';

                        $row .= '</div>';


                        $row .= '<div class="bolFiles">';
                            $row .= '<h2>BOL Files</h2>';

                            if (!empty($bolFiles['bol_files'])) { 
                                foreach ($bolFiles['bol_files'] as $fileRow) { 

                                    $row .= '<div id="bol'. $fileRow['bol_id'].'">';
                                        $file = $fileRow['fileName'];
                                        $fileName = ltrim($file, $id . '_');

                                        $row .= '<a href="Assets/uploads/cod_Files/'. $fileRow['fileName'].' " target="_blank" rel="noopener noreferrer">'. $fileName.' </a>';
                                        $row .= '<a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deletebol(\''.$fileRow['bol_id'].'\')"><i class="uil uil-trash-alt"></i></a> <br>';
                                    $row .= '</div>';

                                } 
                            } else { 
                                $row .= '<p style="color: red;">No Files available.</p>';
                            } 

                        $row .= '</div>';

                        $row .= '<div class="podFiles">';
                            $row .= '<h2>POD Files</h2>';

                            if (!empty($podFiles['pod_files'])) { 
                                foreach ($podFiles['pod_files'] as $fileRow) { 

                                    $row .= '<div id="pod'.$fileRow['pod_id'].'">';
                                        $file = $fileRow['fileName'];
                                        $fileName = ltrim($file, $id . '_');

                                        $row .= '<a href="Assets/uploads/cod_Files/'.$fileRow['fileName'] .'" target="_blank" rel="noopener noreferrer">'. $fileName.' </a>';
                                        $row .= '<a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deletepod(\''.$fileRow['pod_id'].'\')"><i class="uil uil-trash-alt"></i></a> <br>';
                                    $row .= '</div>';

                                } 
                            } else { 
                                $row .= '<p style="color: red;">No Files available.</p>';
                            } 

                        $row .= '</div>';

                        $row .= '<div class="pickup_files">';
                            $row .= '<h2>Pickup Documents</h2>';

                            if (!empty($pickup_files['pickup_files'])) { 
                                foreach ($pickup_files['pickup_files'] as $fileRow) { 

                                    $row .= '<div id="pickup'. $fileRow['pickup_file_id'] .'">';
                                        $file = $fileRow['file_name'];
                                        $fileName = ltrim($file, $id . '_');

                                        $row .= '<a href="Assets/uploads/cod_Files/'. $fileRow['file_name'].' " target="_blank" rel="noopener noreferrer">'. $fileName.' </a>';
                                        $row .= '<a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deletepcikupfile(\''.$fileRow['pickup_file_id'].'\')"><i class="uil uil-trash-alt"></i></a> <br>';
                                    $row .= '</div>';

                                } 
                            } else { 
                                $row .= '<p style="color: red;">No Files available.</p>';
                            } 

                        $row .= '</div>';
                    $row .='</div>';
                    

                    $row .='<div class="formbuttons">';
                        $row .= '<button class="load_edit_close cancel">Cancel</button>';

                        $row .= '<button value="Submit" id="newloadbtn" class="newloadbtn" data-loadtype="loadUpdate" name="submit" class="submit">Update</button>';
                    $row .='</div>';

                    !empty($newloaddata['id']) ?  $id = $newloaddata['id'] :  $id = '';
                    $row .= '<input type="hidden" name="id" value="'. $id .'">';
                $row .='</form>';
            $row .='</div>';
        $row .='</div>';
    $row .='</div>';

    return $row;
}

function loadRating($mysqli, $id){

    $id = $id;

    $newloaddata = $mysqli->query("SELECT * FROM newload n 
    LEFT OUTER JOIN truck_details AS t ON t.truck_id = n.truck_Number 
    LEFT OUTER JOIN broker_details AS b ON b.broker_id = n.Broker 
    LEFT OUTER JOIN (select *, max(callid) from newcheckcalls 
    GROUP BY newloadID) C ON C.newloadID = n.id where n.id = '$id'
    ORDER BY id DESC limit 1") or die($mysqli->error);
    $newloaddata = mysqli_fetch_assoc($newloaddata);
    
    $rows = '';
    $rows .= '<div>';
        $rows .= '<p>Delivery</p>';
        $rows .= '<div class="rating" style="display: flex;">';
            $newloaddata['load_delivery'] >= 1 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .'" title="Rate 1"></i></li>';

            $newloaddata['load_delivery'] >= 2 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 2"></i></li>';

            $newloaddata['load_delivery'] >= 3 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 3"></i></li>';

            $newloaddata['load_delivery'] >= 4 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 4"></i></li>';

            $newloaddata['load_delivery'] >= 5 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 5"></i></li>';
        $rows .= '</div>';
    $rows .= '</div>';

    $rows .= '<div>';
        $rows .= '<p>Communication</p>';
        $rows .= '<div class="rating" style="display: flex;">';

        $newloaddata['load_communication'] >= 1 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .'" title="Rate 1"></i></li>';

            $newloaddata['load_communication'] >= 2 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 2"></i></li>';

            $newloaddata['load_communication'] >= 3 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 3"></i></li>';

            $newloaddata['load_communication'] >= 4 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 4"></i></li>';

            $newloaddata['load_communication'] >= 5 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: ' . $rated .';" title="Rate 5"></i></li>';
        $rows .= '</div>';
    $rows .= '</div>';


    $rows .= '<div class="input">';
        $rows .= '<label for="comments">Comments</label>';
        $rows .= '<p>' . $newloaddata['load_rating_comments'] .' </p>';
    $rows .= '</div>';

    return $rows;
}


function driverRating($mysqli, $id)
{

    $id = $id;

    $newloaddata = $mysqli->query("SELECT * FROM newload n 
    LEFT OUTER JOIN truck_details AS t ON t.truck_id = n.truck_Number 
    LEFT OUTER JOIN broker_details AS b ON b.broker_id = n.Broker 
    LEFT OUTER JOIN (select *, max(callid) from newcheckcalls 
    GROUP BY newloadID) C ON C.newloadID = n.id where n.id = '$id'
    ORDER BY id DESC limit 1") or die($mysqli->error);
    $newloaddata = mysqli_fetch_assoc($newloaddata);


    $rows = '';
    $rows .='<div>';
        $rows .= '<p>Delivery</p>';
        $rows .= '<div class="rating" style="display: flex;">';

            $newloaddata['driver_delivery'] >= 1 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' " title="Rate 1"></i></li>';

            $newloaddata['driver_delivery'] >= 2 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 2"></i></li>';

            $newloaddata['driver_delivery'] >= 3 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 3"></i></li>';

            $newloaddata['driver_delivery'] >= 4 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 4"></i></li>';

            $newloaddata['driver_delivery'] >= 5 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 5"></i></li>';
        $rows .= '</div>';
    $rows .= '</div>';

    $rows .='<div>';
        $rows .= '<p>Driving</p>';
        $rows .= '<div class="rating" style="display: flex;">';

            $newloaddata['driver_driving'] >= 1 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' " title="Rate 1"></i></li>';

            $newloaddata['driver_driving'] >= 2 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 2"></i></li>';

            $newloaddata['driver_driving'] >= 3 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 3"></i></li>';

            $newloaddata['driver_driving'] >= 4 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 4"></i></li>';

            $newloaddata['driver_driving'] >= 5 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 5"></i></li>';
        $rows .= '</div>';
    $rows .= '</div>';

    $rows .='<div>';
        $rows .= '<p>Communication</p>';
        $rows .= '<div class="rating" style="display: flex;">';

            $newloaddata['driver_communication'] >= 1 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' " title="Rate 1"></i></li>';

            $newloaddata['driver_communication'] >= 2 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 2"></i></li>';

            $newloaddata['driver_communication'] >= 3 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 3"></i></li>';

            $newloaddata['driver_communication'] >= 4 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 4"></i></li>';

            $newloaddata['driver_communication'] >= 5 ? $rated = "var(--rating-stars)" : $rated = "#dadada";
            $rows .= '<li><i class="fa fa-star" style="color: '. $rated .' ;" title="Rate 5"></i></li>';
        $rows .= '</div>';
    $rows .= '</div>';

    $rows .= '<div class="input">';
        $rows .= '<label for="comments">Comments</label>';
        $rows .= '<p> '. $newloaddata['driver_rating_comments'] .' </p>';
    $rows .= '</div>';

    return $rows;
}
?>