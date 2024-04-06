<!DOCTYPE html>
<html lang="en">

<head> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="./Assets/Images/WhatsApp Image 2022-05-16 at 2.20 1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <script>
        $('a[tab-toggle="tab"]').on('shown.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('#tabs a[href="' + activeTab + '"]').tab('show');
        }
    </script>

    <title>GD Payments</title>
</head>

<?php

use function GuzzleHttp\Psr7\str;

session_start();
include_once "./Assets/backendfiles/access.php";
access('ALL');
// access('ACCOUNTANT');

if (isset($_POST['read'])) {
    if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
    } else {
        if (isset($_POST['read'])) {
            header('location: index.php');
            session_destroy();
        }
    }
}



$postData = $newloadData = array();

// Get session data 
$sessData = !empty($_SESSION['sessData']) ? $_SESSION['sessData'] : '';

// Get status message from session 
if (!empty($sessData['status']['msg'])) {
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Get posted data from session 
if (!empty($sessData['postData'])) {
    $postData = $sessData['postData'];
    unset($_SESSION['sessData']['postData']);
}

// Get new Load data 
if (!empty($_GET['id'])) {
    // Include and initialize DB class 
    require_once './Assets/backendfiles/DB.class.php';
    $db = new DB();

    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'conFiles';
    $newloadData = $db->getRows($conditions);
}

// Get BOL Files 
if (!empty($_GET['id'])) {
    // Include and initialize DB class 
    require_once './Assets/backendfiles/DB.class.php';
    $db = new DB();

    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'bolFiles';
    $bolFiles = $db->getRows($conditions);
}

// Get POD Files 
if (!empty($_GET['id'])) {
    // Include and initialize DB class 
    require_once './Assets/backendfiles/DB.class.php';
    $db = new DB();

    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'podFiles';
    $podFiles = $db->getRows($conditions);
}

// Get Pickup Files 
if (!empty($_GET['id'])) {
    // Include and initialize DB class 
    require_once './Assets/backendfiles/DB.class.php';
    $db = new DB();

    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'pickup_files';
    $pickup_files = $db->getRows($conditions);
}

require_once './Assets/backendfiles/DB.class.php';
$database = new DB();
// $newloaddata = $database->getRows();

include './Assets/backendfiles/config.php';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $newloaddata = $mysqli->query("SELECT * FROM newload n 
    LEFT OUTER JOIN truck_details AS t ON t.truck_id = n.truck_Number 
    LEFT OUTER JOIN broker_details AS b ON b.broker_id = n.Broker 
    LEFT OUTER JOIN (select *, max(callid) from newcheckcalls 
    GROUP BY newloadID) C ON C.newloadID = n.id where n.id = '$id'
    ORDER BY id DESC limit 1") or die($mysqli->error);

    $newloaddata = mysqli_fetch_assoc($newloaddata);
};

// function loadQuery($status)
// {
//     return "SELECT * 
//     FROM newload n 
//     LEFT OUTER JOIN truck_details AS t 
//     ON t.truck_id = n.truck_Number 
//     LEFT OUTER JOIN broker_details AS b 
//     ON b.broker_id = n.Broker 
//     LEFT OUTER JOIN 
//     (select * from newcheckcalls C where exists (select * from (select newloadID, max(callid) as callid
//     from newcheckcalls cc 
//     GROUP BY newloadID
//     ) as cc where C.callid = cc.callid)) C on n.id = C.newloadID 
//     LEFT OUTER JOIN 
//     (select * from pod_files P where exists (select * from (select pod_newload_id, max(pod_id) as pod_id
//     from pod_files pp 
//     GROUP BY pod_newload_id
//     ) as pp where P.pod_id = pp.pod_id)) AS P
//     ON n.id = P.pod_newload_id
//     LEFT OUTER JOIN 
//     (select * from bol_files B where exists (select * from (select bol_newload_id, max(bol_id) as bol_id
//     from bol_files bb 
//     GROUP BY bol_newload_id
//     ) as bb where B.bol_id = bb.bol_id)) AS B
//     ON n.id = B.bol_newload_id
//     where n.status='$status'
//     ORDER BY id DESC";
// }

// // Loads en route
// $load_en_routedata = $mysqli->query(loadQuery('load_en_route')) or die($mysqli->error);

// // Delivered Data
// $load_delivereddata = $mysqli->query(loadQuery('load_delivered')) or die($mysqli->error);

// // Loads Issue
// $load_issuedata = $mysqli->query(loadQuery('load_issue')) or die($mysqli->error);


// // Loads Invoiced
// $load_invoiceddata = $mysqli->query(loadQuery('load_invoiced')) or die($mysqli->error);

// Loads Paid
// $load_paiddata = $mysqli->query(loadQuery('load_paid')) or die($mysqli->error);
 
$address = $mysqli->query("SELECT * FROM address") or die($mysqli->error);
$dest = $mysqli->query("SELECT * FROM address") or die($mysqli->error);
$truck_details = $mysqli->query("SELECT * FROM truck_details") or die($mysqli->error);
$broker_details = $mysqli->query("SELECT * FROM broker_details GROUP BY broker_company") or die($mysqli->error);
$users = $mysqli->query("SELECT user_name FROM users") or die($mysqli->error);

if (!empty($_GET['callid'])) {
    $callid = $_GET['callid'];
    $checkcalls = $mysqli->query("SELECT * FROM newcheckcalls where callid=$callid") or die($mysqli->error);
    $checkcall = mysqli_fetch_assoc($checkcalls);
}


// Pre-filled data 
$newloadData = !empty($postData) ? $postData : $newloadData;

// Define action 
$actionLabel = !empty($_GET['id']) ? 'Update' : 'Add';

$load_en_routestatus = $mysqli->query("SELECT count(status) FROM newload where status='load_en_route'") or die($mysqli->error);
$load_en_routestatusData = mysqli_fetch_assoc($load_en_routestatus);
$load_en_route = $load_en_routestatusData['count(status)'];

$load_deliveredstatus = $mysqli->query("SELECT count(status) FROM newload where status='load_delivered'") or die($mysqli->error);
$load_deliveredstatusData = mysqli_fetch_assoc($load_deliveredstatus);
$load_delivered = $load_deliveredstatusData['count(status)'];

$load_issuestatus = $mysqli->query("SELECT count(status) FROM newload where status='load_issue'") or die($mysqli->error);
$load_issuestatusData = mysqli_fetch_assoc($load_issuestatus);
$load_issue = $load_issuestatusData['count(status)'];

$load_invoicedstatus = $mysqli->query("SELECT count(status) FROM newload where status='load_invoiced'") or die($mysqli->error);
$load_invoicedstatusData = mysqli_fetch_assoc($load_invoicedstatus);
$load_invoiced = $load_invoicedstatusData['count(status)'];

$load_paidstatus = $mysqli->query("SELECT count(status) FROM newload where status='load_paid'") or die($mysqli->error);
$load_paidstatusData = mysqli_fetch_assoc($load_paidstatus);
$load_paid = $load_paidstatusData['count(status)'];

$load_factored = $mysqli->query("SELECT count(status) FROM newload where status='load_Factored'") or die($mysqli->error);
$load_factoredData = mysqli_fetch_assoc($load_factored);
$load_factored = $load_factoredData['count(status)'];

// Number of total loads delivered today
$today = date('Y-m-d');
$loads_delivered_todayQuery = $mysqli->query("SELECT count(status) FROM newload where DATE(delivery_date)='$today'") or die($mysqli->error);
$loads_delivered_todayQueryData = mysqli_fetch_assoc($loads_delivered_todayQuery);
$loads_delivered_today = $loads_delivered_todayQueryData['count(status)'];

$disQuery = $mysqli->query("SELECT dispatcher,count(dispatcher)  AS count_me FROM newload WHERE dispatcher IS NOT NULL GROUP BY dispatcher ORDER BY COUNT(dispatcher) DESC") or die($mysqli->error);
$disData = mysqli_fetch_array($disQuery);


$status_bar_query = "select * from newload as n 
left outer join 
(select * from load_tracking T where exists (select * from (select load_id, max(tracking_id) as tracking_id
    from load_tracking tt 
    group by Load_pickup_location, load_Destination
    ) as tt where T.tracking_id = tt.tracking_id)) T 
    on n.id = T.load_id 
LEFT OUTER JOIN truck_details AS t 
    ON t.truck_id = n.truck_Number 
LEFT OUTER JOIN broker_details AS b 
    ON b.broker_id = n.Broker 
LEFT OUTER JOIN 
    (select * from newcheckcalls C where exists (select * from (select newloadID, max(callid) as callid
    from newcheckcalls cc 
    GROUP BY newloadID
    ) as cc where C.callid = cc.callid)) C on n.id = C.newloadID 
where n.status = 'load_en_route'
order by n.id desc";
$status_bar = $mysqli->query($status_bar_query) or die($mysqli->error);
// foreach($status_bar as $row){
//     print("  lID:   ". $row['id'] . "  TID:  " . $row['tracking_id'] . "  LPU:  " . $row['Pick_up_Location'] . "  TPU:   " . $row['Load_pickup_location'] . "  LDES:  " . $row['Destination'] . "   TDES:  " .$row['load_Destination'] . "  Ttotal DIS:   " . $row['total_distance'] . "  TC DIS:   " .$row['current_distace'] . "<br>");
// };

function loadstatusbars($mysqli)
{
    $query = "select * from newload where status <> 'load_delivered' order by id desc";
    $data = $mysqli->query($query) or die($mysqli->error);

    foreach ($data as $row) {
        $PU = unserialize($row['Pick_up_Location']);
        $des = unserialize($row['Destination']);
        $dis = unserialize($row['distance']);
        $time = unserialize($row['time']);
        $id = $row['id'];

        $count = count(is_countable($PU) ? $PU : []);

        print("count:   " .  $count  . "  /PU:  " . $row['Pick_up_Location'] . "  /des:  " . $row['Destination'] . "   /dis:  " . $row['distance'] . "  /time:  " . $row['time'] . "<br>");

        for ($i = 0; $i < $count; $i++) {
            $PU = $PU[$i];
            $des = $des[$i];
            $dis = $dis[$i];
            $time = $time[$i];

            $tquery = "select * from load_tracking where load_id='$id' and load_Destination='$des' and Load_pickup_location='$PU' order by tracking_id desc limit 1";
            $tdata = $mysqli->query($tquery) or die($mysqli->error);

            foreach ($tdata as $t) {
                // print("  lID:   " . $id . "  TID:  " . $t['tracking_id'] . "  LPU:  " . $PU . "  TPU:   " . $t['Load_pickup_location'] . "  LDES:  " . $des . "   TDES:  " . $t['load_Destination'] . "  Ttotal DIS:   " . $dis . "  TC DIS:   " . $t['current_distace'] . "   T CL:  " . $t['current_location'] . "<br>");
            }
        };
    };
}

// loadstatusbars($mysqli);


// function tdata($query)
// {
//     if (!empty($query)) {
//         $i = 0;
//         foreach ($query as $row) {
//             $i++;

//             // $pickuplocation = unserialize($row['Pick_up_Location'])[0];
//             $PU_count = count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []);
//             count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []) > 0 ? $pickuplocation = stripslashes(unserialize($row['Pick_up_Location'])[0]) : $pickuplocation = stripslashes(unserialize($row['Pick_up_Location']));
//             count((is_countable(unserialize($row['Destination']))) ? unserialize($row['Destination']) : []) > 0 ? $destination = stripslashes(unserialize($row['Destination'])[0]) : $destination = stripslashes(unserialize($row['Destination']));

//             $PU_count > 1 ? $ml_info = '<span title="Multiple Locations added for the load" style="width: 15px;height: 15px;background: #ebeb57;padding: 3px 9px; border-radius: 50%;font-size: 10px;margin-left: 5px;cursor: help;">!</span>' : $ml_info =  '';

//             $podfile = "";
//             if (!empty($row['pod_newload_id'])) {
//                 $podfile = 'style="background-color: var(--pod-attached);"';
//             } else if (!empty($row['bol_newload_id'])) {
//                 $podfile = 'style="background-color: var(--bol-attached);"';
//             }

//             echo '<tr ' . $podfile . ' >';

//             echo '<td>' . $i . '</td>';
//             echo '<td>';
//             echo '<a style="cursor: pointer;" data-action_type="dispatcher" class="load_dispatcher_form" data-load_id="' . $row['id'] . '"> JBA <br>' . $row['id'] . '</a><br>';
//             echo '<span style="color: var(--light-font);">' . $row['Ref_No'] . '</span>';

//             echo '</td>';
//             echo '<td>' . $row['dispatcher'] . '</td>';
//             echo '<td style="min-width: 220px;">';
//             echo '<div>';
//             echo '<div class="truckImg">';
//             echo '<img src="./Assets/Images/Business Logo.png" width="30px" />';
//             echo '</div>';

//             echo '<div class="additionalcontent" style="width: 150px;">';
//             echo '<p style="margin-bottom: 0; float: left; margin-right: 7px;">' . $row['broker_company'] . '</p><span style="color: var(--light-font);">' . $row['brokerState'] . '</span> <br>';

//             echo '<span style="color: var(--light-font);">$ ' . $row['Customer_Rate'] . '</span><br>';

//             echo '<a href="tel:' . $row['brokerphone'] . '">' . $row['brokerphone'] . '</a>';
//             echo '<a style="overflow-wrap: anywhere;" href=" mailto:' . $row['brokeremail'] . '" target="_blank" rel="noopener noreferrer">' . $row['brokeremail'] . '</a>';
//             echo '</div>';
//             echo '</div>';

//             echo '</td>';
//             echo '<td style="min-width: 290px;">';

//             echo '<div>';
//             echo '<div class="truckImg">';
//             echo ' <img src="./Assets/Images/truck.png" width="30px" />';
//             echo '</div>';

//             echo '<div class="additionalcontent" style="width: 150px;">';
//             echo '<p style="margin-bottom: 0; float: left; margin-right: 7px;">' . $row['truckNumber'] . '</p>';
//             echo '<span style="color: var(--light-font);">' . $row['truckDriver'] . '</span> <br>';
//             echo '<span style="color: var(--light-font);">$' . $row['Carier_Driver_Rate'] . '</span><br>';
//             echo '<a href="tel:+1' . $row['cpPhone'] . '">' . $row['cpPhone'] . '</a>';
//             echo '<a href="mailto:' . $row['cpemail'] . '" target="_blank" rel="noopener noreferrer">' . $row['cpemail'] . '</a>';
//             echo '</div>';
//             echo '</div>';

//             echo '</td>';

//             echo '<td>';
//             echo '<div style="width: 265px;">';
//             echo '<div class="origin"></div>';
//             echo '<div class="additionalcontent" style="width: 245px;"><a href="https://www.google.com/maps/place/' . $pickuplocation  . '" target="_blank" rel="noopener noreferrer">' . $pickuplocation . '</a>';
//             echo $ml_info;
//             '</div>';
//             echo '</div>';
//             echo '<div style="width: 265px;">';
//             echo '<div class="destinationbox"></div>';
//             echo '<div class="additionalcontent" style="width: 245px;"><a href="https://www.google.com/maps/place/' . $destination . '" target="_blank" rel="noopener noreferrer">' . $destination . '</a></div>';
//             echo '</div>';
//             echo '</td>';

//             echo '<td style="font-size: 10px;">';
//             echo '<p style="margin-bottom: 10px;">';
//             if (strtotime($row['pickupdate']) > 0) {
//                 $pickupdate = $row['pickupdate'];
//                 $forpickupdate = date("m-d-y", strtotime($pickupdate));
//                 echo $forpickupdate . " ";
//                 $forpickuptime = date("h:i a", strtotime($pickupdate));
//                 echo $forpickuptime;
//             } else {
//                 echo '';
//             }
//             echo '</p>';
//             echo '<p style="margin-bottom: 0;">';
//             if (strtotime($row['dropdate']) > 0) {
//                 $originalDate = $row['dropdate'];
//                 $newDate = date("m-d-y", strtotime($originalDate));
//                 echo $newDate . " ";

//                 $forpickuptime = date("h:i a", strtotime($originalDate));
//                 echo $forpickuptime;
//             } else {
//                 echo '';
//             }
//             echo '</p>';
//             echo '</td>';

//             echo '<td>' . $row['newchecknotes'] . '</td>';
//             echo '<td style="display: flex; justify-content: center;">';

//             echo '<li>';
//             echo '<div style="margin: 10px 15px 0px" onclick="newcheckcallsbtn()">';

//             echo '<a style="color: var(--font); font-size: 15px; margin: 7px 10px 10px 0; cursor: pointer; " href="index.php?action_type=newcall&id=' . $row['id'] . '">';
//             echo '<img class="checkcall" src="" width="15px" height="15px" />';
//             echo '</a>';
//             echo '</div>';

//             echo '</li>';

//             echo '<div class="btn-group btn-group-rounded">';
//             echo '<button type="button" class="btn btn-default btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius:3px; background: none; border: none; outline: none; text-align:center;">';
//             echo '<i class="uil uil-ellipsis-h"></i>';
//             echo '</button>';
//             echo ' <ul class="dropdown-menu">';
//             echo '<li>';
//             echo '<a style="color: green; font-size: 15px; margin: 7px 10px 10px 0; cursor: pointer;" class="get_load_form" data-action_type="edit_load" data-load_id="' . $row['id'] . '">';
//             echo '<i class="uil uil-pen"></i>Edit';
//             echo ' </a>';
//             echo '</li>';



//             echo '<li>';
//             echo '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" href="index.php?action_type=dispatcher&id=' . $row['id'] . '">';
//             echo '<i class="uil uil-minus-path"></i>Dispatch Load';
//             echo '</a>';
//             echo '</li>';

//             echo '<li>';
//             echo '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font); " class="load_action" data-action_type="load_en_route" data-load_id="' . $row['id'] . '">';
//             echo '<i class="fa-solid fa-route"></i>Loads en Route';
//             echo '</a>';
//             echo '</li>';

//             echo '<li>';
//             echo '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_delivered" data-load_id="' . $row['id'] . '">';
//             echo '<i class="fa-solid fa-truck-ramp-box"></i>Loads Delivered';
//             echo '</a>';
//             echo '</li>';

//             echo '<li>';
//             echo '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_issue" data-load_id="' . $row['id'] . '">';
//             echo '<i class="fa-solid fa-triangle-exclamation"></i>Loads Issue';
//             echo '</a>';
//             echo '</li>';

//             echo '<li>';
//             echo '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_invoiced" data-load_id="' . $row['id'] . '">';
//             echo '<i class="fa-solid fa-file-invoice-dollar"></i>Loads Invoiced';
//             echo '</a>';
//             echo '</li>';

//             echo '<li>';
//             echo '<a style="font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; color: var(--font);" class="load_action" data-action_type="load_paid" data-load_id="' . $row['id'] . '">';
//             echo '<i class="fa-solid fa-money-check-dollar"></i>Loads Paid';
//             echo '</a>';
//             echo '</li>';

//             echo '<li>';
//             echo '<a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " class="load_action" data-action_type="delete" data-load_id="' . $row['id'] . '">';
//             echo '<i class="uil uil-trash-alt"></i>Delete';
//             echo '</a>';
//             echo '</li>';




//             echo '</ul>';
//             echo '</div>';
//             echo '</td>';

//             echo '</tr>';
//         }
//     } else {
//         echo '<tr>';
//         echo '<td colspan="9">No Data found...</td>';
//         echo '</tr>';
//     }
// }

function status($status, $value)
{
    echo $status == $value ? "selected" : '';
}

function floatvalue($val)
{
    $val = str_replace(",", ".", $val);
    $val = preg_replace('/\.(?=.*\.)/', '', $val);
    return floatval($val);
}
?>




<body style="background-color: var(--body);">


    <?php $page_title = "Load Board";
    include('header.php'); ?>

    <!-- <div id="alertmsg_container">
        <div id="alert_msg" class="alert_msg"><span class="close_alrt_msg">x</span></div>
    </div> -->
    <div id="alert_msg" class="alert_msg"></div>

    <div class="loader" style="position: fixed;">
        <div class="loading">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>


    <!------------------- Chart Js  ----------------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.0/chart.js"></script>


    <div class="dispatchercharts" style="display: none;">
        <div class="dis-charts summary">
            <p>Dispatcher Summary</p>
            <canvas class="dis-chart" id="dis-summary" style="display: block;box-sizing: border-box;height: 150px !important;"></canvas>
        </div>
        <div class=" dis-charts detail">
            <p>Dispatcher Details</p>
            <canvas class="chart" id="disDetail" style="display: block;box-sizing: border-box;height: 150px !important;"></canvas>
        </div>
        <div class="dis-charts" style="width: 200px">
            <p>Dispatcher Summary</p>
            <canvas class="chart" id="disp-pie-summary" style="width: 150px !important;"></canvas>
        </div>
    </div>

    <div class="indexstats dispatchercharts">
        <div class="load_stats">
            <img src="./Assets/Images/delivery.png" />
            <div class="content" style="text-align: center;">
                <p>Loads Delivered Today</p>
                <h2><?php echo $loads_delivered_today; ?></h2>
            </div>
        </div>

        <div class="statusbar_Section" id="undelivered_load_bars">
            <div style="display: flex;justify-content: space-between;margin-right: 30px;margin-bottom: 30px;">
                <input onkeyup="search()" style="width: 100%;margin-right: 20px;" type="search" name="search_loadBars" id="search_loadBars" placeholder="search...">
                <!-- <select name="search_loadBars_filter" style="width: 20%;">
                    <option value="load_num">Pro No.</option>
                    <option value="truck_num">Truck No.</option>
                    <option value="driver">Driver</option>
                    <option value="driver">Remaning Distance</option>
                    <option value="check_notes">Check Notes</option>
                </select> -->
            </div>
            <?php
            foreach ($status_bar as $row) {
                // print(" lID: " . $row['id'] . " TID: " . $row['tracking_id'] . " LPU: " . $row['Pick_up_Location'] . " TPU: " . $row['Load_pickup_location'] . " LDES: " . $row['Destination'] . " TDES: " . $row['load_Destination'] . " Ttotal DIS: " . $row['total_distance'] . " TC DIS: " . $row['current_distace'] . "<br>");

                $lid = $row['id'];
                $td = substr($row['total_distance'], 0, -3);
                $cd = substr($row['current_distace'], 0, -3);
                $td = floatvalue($td);
                $cd = floatvalue($cd);
                $status = $row['status'];

                // $barprcnt = 0;
                // $statusbar_style = "background-color: var(--button)";
                // $barstarprcnt = 0;
                // if (trim($td) <> 0 || $td <> null) {
                //     $barprcnt = round(($cd / $td) * 100);
                //     if ($barprcnt > 100) {
                //         $barprcnt = 100;
                //     } else {
                //         $barprcnt = $barprcnt;
                //     };
                //     if ($barprcnt == "" || $barprcnt == null || $barprcnt == 0) {
                //         $barstarprcnt = $barprcnt;
                //     } else {
                //         $barstarprcnt = $barprcnt - 2;
                //     };
                // }

                // if ($barprcnt < 50) {
                //     $statusbar_style = "background-color: var(--button); width: $barprcnt%;";
                //     $statusstar_style = "background-color: var(--button); margin-left: $barstarprcnt%;";
                // } else if ($barprcnt < 70) {
                //     $statusbar_style = "background-color: blue; width: $barprcnt%;";
                //     $statusstar_style = "background-color: blue; margin-left: $barstarprcnt%;";
                // } else {
                //     $statusbar_style = "background-color: green; width: $barprcnt%;";
                //     $statusstar_style =  "background-color: green; margin-left: $barstarprcnt%;";
                // }
                $dispatchedClass = '';
                $onsiteClass = '';
                $enrouteClass = '';
                $ondeliverSiteClass = '';
                $IssueClass = '';
                $deliveredClass = '';
                if ($status == "Dispatched") {
                    $dispatchedClass = 'active_step';
                } else if ($status == "Driver on site") {
                    $dispatchedClass = 'step_performed';
                    $onsiteClass = 'active_step';
                } else if ($status == "Driver Enroute") {
                    $dispatchedClass = 'step_performed';
                    $onsiteClass = 'step_performed';
                    $enrouteClass = 'active_step';
                } else if ($status == "Driver on site delivery") {
                    $dispatchedClass = 'step_performed';
                    $onsiteClass = 'step_performed';
                    $enrouteClass = 'step_performed';
                    $ondeliverSiteClass = 'active_step';
                } else if ($status == "Issue") {
                    $dispatchedClass = 'issue';
                    $onsiteClass = 'issue';
                    $enrouteClass = 'issue';
                    $ondeliverSiteClass = 'issue';
                    $IssueClass = 'issue';
                } else if ($status == "Delivered") {
                    $dispatchedClass = 'step_performed';
                    $onsiteClass = 'step_performed';
                    $enrouteClass = 'step_performed';
                    $ondeliverSiteClass = 'step_performed';
                    $IssueClass = 'step_performed';
                    $deliveredClass = 'active_step';
                }



            ?>
                <div class="status_bar_container undelivered_load_bars">
                    <p class="load_id sa"><?php echo $row['id']; ?></p>
                    <!-- <div class="status_bar">
                        <div class="status_bar_bg" style="background-color: white;"></div>
                        <div class="active_status_bar" style="<?php // echo $statusbar_style;  ?>"></div>
                        <div class="status_star" style="<?php // echo $statusstar_style;  ?>"><img src="https://img.icons8.com/ios-glyphs/30/ffffff/star--v1.png" /></div>
                        <div style="float: left; padding-left: 20px;  margin-top: -22px; display: flex;">
                            <p class="truck_no sa" style="margin-right: 15px; color: var(--light-font);"><?php // echo $row['truckNumber'];  ?></p>
                            <p class="truck_driver sa" style="color: var(--light-font);"><?php // echo $row['truckDriver'];  ?></p>
                        </div>
                        <p style=" color: var(--light-font);margin-top: -22px;float: right;margin-right: 20px;" class="rem_dis sa"><?php // echo $row['current_distace'];  ?></p>
                    </div>
                    <p style=" color: var(--light-font)" class="checknotes sa"><?php // echo $row['newchecknotes'];  ?></p> -->

                    <div class="progress_bar2">
                        <li class="csstriangle start <?php echo  $dispatchedClass ?>">
                            <p> 1 <span> Dispatched</span></p>
                        </li>
                        <li class="csstriangle  <?php echo  $onsiteClass ?>">
                            <p> 2 <span> Driver on Site</span></p>
                        </li>
                        <li class="csstriangle  <?php echo  $enrouteClass ?>">
                            <p> 3 <span> Enroute</span></p>
                        </li>
                        <li class="csstriangle  <?php echo  $ondeliverSiteClass ?>">
                            <p> 4 <span> On delivery Site</span></p>
                        </li>
                        <li class="csstriangle   <?php echo  $IssueClass ?>">
                            <p> 5 <span> Issue</span></p>
                        </li>
                        <li class="csstriangle end  <?php echo  $deliveredClass ?>">
                            <p> 6 <span> Delivered</span></p>
                        </li>
                    </div>
                </div>

            <?php }; ?>
        </div>
    </div>

    <!-- <div style="margin-left: 80px; margin-top: 80px">
        <?php if (!empty($newcheckcall)) {
            $i = 0;
            foreach ($newcheckcall as $row) {
                $i++;
        ?>

                <div><span>. $row['id']. '</span> <span>. $row['callid']. '</span> <span>. $row['newloadID']. '</span></div>

            <?php  }
        } else { ?>
            <tr>
                <td colspan="6">No Data found...</td>
            </tr>
        <?php } ?>
    </div> -->


    <div id="map" style="display: none;"></div>

    <!-- Display status message -->
    <?php if (!empty($statusMsg)) { ?>
        <div class="col-xs-12" id="alert">
            <div class="alert alert-. $statusMsgType; ?>"><?php echo $statusMsg; ?></div>
        </div>
    <?php } ?>

    <div class="navbar tab-container">
        <ul class="menu tabs" style="padding: 0;">
            <li class="nav-item">
                <img id="issue" src="" alt="" srcset="">
                <a href="#load_issue" class="nav-link" class="nav-link"><span class="title">Posted</span>
                    <span>
                        <?php echo $load_issue;  ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="shipped" src="" alt="" srcset="">
                <a href="#load_delivered" class="nav-link" class="nav-link"><span class="title"> BS Matched</span>
                    <span>
                        <?php echo $load_delivered;  ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="route" src="" alt="">
                <a href="#load_en_route" class="nav-link" class="nav-link"><span class="title">Opening</span>
                    <span>
                        <?php echo $load_en_route;  ?>
                    </span>
                </a>

            </li>

            

        </ul>

        <li id="newloadformbtn">

            <button>+ New Load</button>
        </li>
    </div>
    <div class=outer-circle></div>

    <div class="tab-content">
        <div class="table tabcontent active" id="load_en_route" style=" display: block;">
            <table id="table1" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>Pro No.</th>
                        <th>Dispatcher</th>
                        <th>Broker</th>
                        <th>Truck No.</th>
                        <th>Origin/Destination</th>
                        <th width=80px; style="width: 396px;font-size: 10px;">Pickup / Drop off Date & Time</th>
                        <th>Updates</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard1">
                    <?php // tdata($load_en_routedata)  ?>
                </tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_delivered">
            <table id="table2" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>Pro No.</th>
                        <th>Dispatcher</th>
                        <th>Broker</th>
                        <th>Truck No.</th>
                        <th>Origin/Destination</th>
                        <th width=80px; style="width: 396px;font-size: 10px;">Pickup / Drop off Date & Time</th>
                        <th>Updates</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard2"> <?php // tdata($load_delivereddata)  ?></tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_issue">
            <table id="table3" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>Pro No.</th>
                        <th>Dispatcher</th>
                        <th>Broker</th>
                        <th>Truck No.</th>
                        <th>Origin/Destination</th>
                        <th width=80px; style="width: 396px;font-size: 10px;">Pickup / Drop off Date & Time</th>
                        <th>Updates</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard3"><?php // tdata($load_issuedata)  ?></tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_invoiced">
            <table id="table4" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>Pro No.</th>
                        <th>Dispatcher</th>
                        <th>Broker</th>
                        <th>Truck No.</th>
                        <th>Origin/Destination</th>
                        <th width=80px; style="width: 396px;font-size: 10px;">Pickup / Drop off Date & Time</th>
                        <th>Updates</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard4"><?php // tdata($load_invoiceddata)  ?></tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_paid">
            <table id="table5" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>Pro No.</th>
                        <th>Dispatcher</th>
                        <th>Broker</th>
                        <th>Truck No.</th>
                        <th>Origin/Destination</th>
                        <th width=80px; style="width: 396px;font-size: 10px;">Pickup / Drop off Date & Time</th>
                        <th>Updates</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard5"><?php // tdata($load_paiddata)  ?></tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_factored">
            <table id="table6" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>Pro No.</th>
                        <th>Dispatcher</th>
                        <th>Broker</th>
                        <th>Truck No.</th>
                        <th>Origin/Destination</th>
                        <th width=80px; style="width: 396px;font-size: 10px;">Pickup / Drop off Date & Time</th>
                        <th>Updates</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard6"><?php // tdata($load_paiddata)  ?></tbody>

            </table>
        </div>
    </div>



    <?php if (!empty($_GET['id']) && ((!empty($_GET['action_type'] == "checkcalledit")) || !empty($_GET['action_type'] == "newcall"))) :
        $checkcalledit_mode = 'style="display: block;"';
    else : $checkcalledit_mode = 'style="display: none;"';
    endif;  ?>
    <div class="newcheckcalls modal" id="newcheckcalls" <?php echo $checkcalledit_mode ?>>
        <div class="newcheckcalls modal-content">
            <span class="close" style="opacity: 0.8;" onclick="newcheckcallsclose()">&times;</span>
            <div class="newloadHeader">
                <h2>New Check Calls for Dispatch # <?php echo !empty($newloaddata['id']) ? $newloaddata['id'] : ''; ?></h2>
            </div>

            <div class="form">
                <form action="./Assets/backendfiles/postAction.php" method="post">
                    <input type="hidden" name="newloadID" value="<?php echo !empty($newloaddata['id']) ? $newloaddata['id'] : ''; ?>">
                    <input type="hidden" name="newcallid" value="<?php echo !empty($checkcall['callid']) ? $checkcall['callid'] : ''; ?>">
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerphone">Check Points</label>
                            <input type="text" name="checkpoints" id="" placeholder="ABC, DEF, GHI" value="<?php echo !empty($checkcall['checkpoints']) ? $checkcall['checkpoints'] : ''; ?>">
                        </div>

                    </div>
                    <div class="textareainputbox">
                        <label for="notesprivate">Notes</label>
                        <textarea name="newchecknotes" id="notesprivate" cols="120" rows="5" placeholder="Enter notes here..."><?php echo !empty($checkcall['newchecknotes']) ? $checkcall['newchecknotes'] : ''; ?></textarea>
                    </div>


                    <div class="formbuttons">
                        <span value="Cancel" name="brokercancel" class="cancel" onclick="newcheckcallsclose()">Cancel</span>

                        <?php if (!empty($_GET['id']) && !empty($_GET['action_type'] == "checkcalledit")) : ?>
                            <button type="submit" value="Submit" name="newcallupdate" class="submit">Update</button>

                        <?php else : ?>
                            <button type="submit" value="Submit" name="newcallsubmit" class="submit">Submit</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <?php if (!empty($_GET['id']) && !empty($_GET['action_type'] == "editload")) :
        $editload_mode = 'style="display: block;"';

        $PU_count = count((is_countable(unserialize($newloaddata['Pick_up_Location']))) ? unserialize($newloaddata['Pick_up_Location']) : []);
        $PU_location = unserialize($newloaddata['Pick_up_Location']);
        $Destination = unserialize($newloaddata['Destination']);
        $start_lat = unserialize($newloaddata['start_lat']);
        $start_lng = unserialize($newloaddata['start_lng']);
        $end_lat = unserialize($newloaddata['end_lat']);
        $end_lng = unserialize($newloaddata['end_lng']);
        $distance = unserialize($newloaddata['distance']);
        $time = unserialize($newloaddata['time']);
        $PU_count < 1 ? $PU_count = 1 : $PU_count = $PU_count;
        $PU_count >= 1 ? $PU1st_loc = $PU_location[0] : $PU1st_loc = $PU_location;
        $PU_count >= 1 ? $PU1st_des = $Destination[0] : $PU1st_des = $Destination;
        $PU_count >= 1 ? $nl1st_sl = $start_lat[0] : $nl1st_sl = $start_lat;
        $PU_count >= 1 ? $nl1st_slung = $start_lng[0] : $nl1st_slung = $start_lng;
        $PU_count >= 1 ? $nl1st_endlat = $end_lat[0] : $nl1st_endlat = $end_lat;
        $PU_count >= 1 ? $nl1st_elng = $end_lng[0] : $nl1st_elng = $end_lng;
        $PU_count >= 1 ? $nl1st_dis = $distance[0] : $nl1st_dis = $distance;
        $PU_count >= 1 ? $nl1st_dur = $time[0] : $nl1st_dur = $time;

    else : $editload_mode = 'style="display: none;"';
    endif;  ?>
    <div class="newLoad modal" id="newloadform" <?php echo $editload_mode ?>>

        <div class="modal-content">
            <span class="close" style="opacity: 0.8;" onclick="closemodal()">&times;</span>
            <div class="newloadHeader">
                <h2><?php echo $actionLabel; ?> Load</h2>
            </div>
            <div class="form">
                <!-- // action="./Assets/backendfiles/postAction.php" -->
                <form method="post" id="new_load_form" class="new_load_form" enctype="multipart/form-data">

                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="broker">Brokerage</label>
                            <div class="select">
                                <select required class="form-control select2 broker" style="width: 100%;" name="broker" id="broker">
                                    <?php while ($broker_detail = $broker_details->fetch_assoc()) : ?>

                                        <option value="<?php echo $broker_detail['broker_id']; ?>" data-foo="<?php echo $broker_detail['brokercity'] . ' , ' . $broker_detail['brokerState']; ?>"><?php echo $broker_detail['broker_company']; ?></option>

                                    <?php endwhile; ?>
                                </select>
                                <div class="addmore"><button type="reset" id="brokerDetailsbtn" class="brokerDetailsbtn">+</button></div>
                            </div>

                        </div>

                        <div class="inputbox">
                            <label for="truckNo">Broker Agent</label>
                            <select name="brokeragent" id="brokeragent"></select>
                        </div>



                    </div>
                    <div class="field_wrapper">

                        <div class=" inputgroup">
                            <div class="inputbox">
                                <span class="msg"></span>
                                <label for="pickupLocation">Pick Up Location</label>
                                <input required class="start" id="start" type="text" placeholder="Pick Up Location" name="pick_up_Location[]" value="<?php echo !empty($newloaddata['Pick_up_Location']) ? $PU1st_loc : ''; ?>" />
                                <span class="pu_blank_msg"></span>
                                <input type="text" name="start_lat[]" class="start_lat" hidden value="<?php echo !empty($newloaddata['start_lat']) ? $nl1st_sl : ''; ?>">
                                <input type="text" name="start_lng[]" class="start_lng" hidden value="<?php echo !empty($newloaddata['start_lng']) ? $nl1st_slung : ''; ?>">
                            </div>

                            <div class="inputbox">
                                <label for="destination">Destination</label>
                                <div class="select">
                                    <input required class="end" style="width: 100%;" id="end" type="text" placeholder="Destination" name="destination[]" value="<?php echo !empty($newloaddata['Destination']) ? $PU1st_des : ''; ?>" />
                                    <!-- <div class="addmore"><button type="reset" id="calculate"><i class="uil uil-map"></i></button></div> -->
                                    <input type="text" name="end_lat[]" class="end_lat" hidden value="<?php echo !empty($newloaddata['end_lat']) ? $nl1st_endlat : ''; ?>">
                                    <input type="text" name="end_lng[]" class="end_lng" hidden value="<?php echo !empty($newloaddata['end_lng']) ? $nl1st_elng : ''; ?>">
                                    <input type="text" name="distance[]" class="distance" hidden value="<?php echo !empty($newloaddata['distance']) ? $nl1st_dis : ''; ?>">
                                    <input type="text" name="duration[]" class="duration" hidden value="<?php echo !empty($newloaddata['time']) ? $nl1st_dur : ''; ?>">
                                    <a href="javascript:void(0);" class="add_button" title="Add field">+</a>
                                </div>
                            </div>
                        </div>

                        <?php
                        if (!empty($_GET['id']) && !empty($_GET['action_type'] == "editload") && $PU_count > 1) :
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
                        ?>

                                <div class=" inputgroup">
                                    <div class="inputbox">
                                        <span class="msg"></span>
                                        <input class="start" id="start" type="text" placeholder="Pick Up Location" name="pick_up_Location[]" value="<?php echo !empty($newloaddata['Pick_up_Location']) ? $PU_loc : ''; ?>" />
                                        <span class="pu_blank_msg"></span>
                                        <input type="text" name="start_lat[]" class="start_lat" hidden value="<?php echo !empty($newloaddata['start_lat']) ? $nl_sl : ''; ?>">
                                        <input type="text" name="start_lng[]" class="start_lng" hidden value="<?php echo !empty($newloaddata['start_lng']) ? $nl_slung : ''; ?>">
                                    </div>

                                    <div class="inputbox" style="flex-direction: row;">'
                                        <div style="width: 100%;">
                                            <input style="width: 100%;" type="text" name="destination[]" class="end pac-target-input" placeholder="Drop off" value="<?php echo !empty($newloaddata['Destination']) ? $PU_des : ''; ?>" />
                                            <input type="text" name="end_lat[]" class="end_lat" hidden value="<?php echo !empty($newloaddata['end_lat']) ? $nl_endlat : ''; ?>">
                                            <input type="text" name="end_lng[]" class="end_lng" hidden value="<?php echo !empty($newloaddata['end_lng']) ? $nl_elng : ''; ?>">
                                            <input type="text" name="distance[]" class="distance" hidden value="<?php echo !empty($newloaddata['distance']) ? $nl_dis : ''; ?>">
                                            <input type="text" name="duration[]" class="duration" hidden value="<?php echo !empty($newloaddata['time']) ? $nl_dur : ''; ?>">
                                        </div>
                                        <a href="javascript:void(0);" class="remove_button">-</a>
                                    </div>
                                </div>

                        <?php };
                        endif; ?>
                    </div>

                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="truckNo">Truck Number</label>
                            <div class="select">
                                <select class="form-control select2 truck_Number" style="width: 90%;" name="truck_number" id="truck_Number">

                                    <?php
                                    $truck_details = $mysqli->query("SELECT * FROM truck_details") or die($mysqli->error);
                                    while ($truck_detai = $truck_details->fetch_assoc()) : ?>

                                        <option value="<?php echo $truck_detai['truck_id']; ?>" data-foo="<?php echo $truck_detai['truckDriver']; ?>"><?php echo $truck_detai['truckNumber']; ?></option>

                                    <?php endwhile; ?>

                                    <!-- <option selected="selected" data-foo="We dare to defend our rights">Alabama</option> -->

                                </select>
                                <div class="addmore"><button type="reset" id="truckNumberbtn" class="truckNumberbtn">+</button></div>
                            </div>
                        </div>
                        <div class="inputbox">
                            <label for="refNo">Reference Number</label>
                            <input required type="text" name="ref_num" value="<?php echo !empty($newloaddata['Ref_No']) ? $newloaddata['Ref_No'] : ''; ?>" placeholder="ref-123">
                        </div>

                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="customerrate">Customer Rate</label>
                            <input required type="number" name="customer_rate" id="" value="<?php echo !empty($newloaddata['Customer_Rate']) ? $newloaddata['Customer_Rate'] : ''; ?>" placeholder="10.54">
                        </div>
                        <div class="inputbox">
                            <label for="carierrate">Carier/Driver Rate</label>
                            <input required type="number" name="carier_rate" id="" value="<?php echo !empty($newloaddata['Carier_Driver_Rate']) ? $newloaddata['Carier_Driver_Rate'] : ''; ?>" placeholder="10.23">
                        </div>

                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="trucktype">Truck Type</label>
                            <input type="text" name="truck_type" id="" value="<?php echo !empty($newloaddata['Truck_type']) ? $newloaddata['Truck_type'] : ''; ?>" placeholder="Truck">
                        </div>
                        <div class="inputbox">
                            <label for="comodity">Comodity</label>
                            <input type="text" name="comodity" id="" value="<?php echo !empty($newloaddata['Comodity']) ? $newloaddata['Comodity'] : ''; ?>" placeholder="Please enter comodity">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="subgroup">
                            <div class="inputbox">
                                <label for="loadtype">Load Type</label>
                                <label for="">
                                    <input type="radio" name="loadtype" id="" value="Full Load" <?php if (!empty($newloaddata['load_Type']) && $newloaddata['load_Type'] == 'Full Load') {
                                                                                                    echo "checked";
                                                                                                } else {
                                                                                                    echo "checked";
                                                                                                }; ?>>Full load
                                    <input type="radio" name="loadtype" id="" value="LTL" <?php if (!empty($newloaddata['load_Type']) && $newloaddata['load_Type'] == "LTL") {
                                                                                                echo "checked";
                                                                                            }; ?>>LTL
                                </label>


                            </div>
                        </div>
                        <div class="subgroup">
                            <div class="inputbox">
                                <label for="plattes">Pallets</label>
                                <input type="number" name="plattes" id="" value="<?php echo !empty($newloaddata['Pallets']) ? $newloaddata['Pallets'] : ''; ?>" placeholder="10">
                            </div>
                            <div class="inputbox">
                                <label for="weight">Weight</label>
                                <input type="number" name="weight" id="" value="<?php echo !empty($newloaddata['Weight']) ? $newloaddata['Weight'] : ''; ?>" placeholder="10">
                            </div>
                        </div>

                    </div>

                    <div class="inputgroup">
                        <div class="subgroup">
                            <div class="inputbox">

                                <label for="loadtype">Pick Up Date</label>

                                <input required type="datetime-local" name="pickupdate" id="" value="<?php echo  !empty($newloaddata['pickupdate']) ? substr(date('c', strtotime($newloaddata['pickupdate'])), 0, 16) : ''; ?>">
                            </div>
                            <div class="inputbox">
                                <label for="ratecon">Drop of Date</label>

                                <input required type="datetime-local" name="dropdate" id="" value="<?php echo !empty($newloaddata['dropdate']) ? substr(date('c', strtotime($newloaddata['dropdate'])), 0, 16) : ''; ?>">
                            </div>
                        </div>
                        <?php //($_SESSION['myusertype'] == "admin") ? $eidtDis = 'style="display: flex;"' : $eidtDis = 'style="display: none;"'; 
                        ?>
                        <div class="inputbox">
                            <label for="dispatcher">Dispatcher</label>
                            <input type="text" name="dispatcher" value="<?php echo !empty($newloaddata['dispatcher']) ? $newloaddata['dispatcher'] : $_SESSION['myusername']; ?>" placeholder="Dispatcher">
                        </div>

                    </div>

                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="trucktype">Add Labels</label>
                            <select class="select2" name="labels[]" id="" multiple>
                                <?php  
                                    $labels = "SELECT * from labels";
                                    $labels = $mysqli->query($labels) or die($mysqli->error);

                                    foreach($labels as $label){
                                
                                ?>
                                <option value="<?php echo $label['label_id'] ?>" ><?php echo $label['label_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="pod">Attach CON</label>
                            <input type="file" name="rate_con_files[]" id="" multiple>
                        </div>
                        <div class="inputbox">
                            <label for="bol">Attach BOL</label>
                            <input type="file" name="bol_files[]" id="" multiple>
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="pod">Attach POD</label>
                            <input type="file" name="pod_files[]" id="" multiple>
                        </div>
                        <div class="inputbox">
                            <label for="pod">Attach Pickup Documents</label>
                            <input type="file" name="pickup_docs[]" id="" multiple>
                        </div>
                    </div>
                    <div class="textareainputbox">
                        <label for="notesprivate">Notes Private</label>
                        <textarea name="notesprivate" id="notesprivate" cols="160" rows="5" placeholder="Enter your private notes here..."><?php echo !empty($newloaddata['Notes_Private']) ? $newloaddata['Notes_Private'] : ''; ?></textarea>
                    </div>

                    <div id="info-panel" style="display: none;">
                        <div id="info">
                            <span style="color: red;">RED</span><br />
                            <span style="color: blue;">BLUE</span><br />
                            <span style="color: green;">GREEN</span>
                        </div>
                        <div id="routes-value">
                            <input id="red" type="text" /><br />
                            <input id="blue" type="text" /><br />
                            <!-- <input id="green" type="text" name="distance" /><br /> -->
                            <!-- <input type="text" id="time" name="time"> -->
                        </div>
                    </div>

                    <?php if (!empty($_GET['id'])) : ?>
                        <div class="User_Files">
                            <div class="rateConFiles">
                                <h2>Rate CON Files</h2>

                                <div id="rate_con_files_con">
                                    <?php if (!empty($newloadData['rate_con_files'])) { ?>

                                        <?php foreach ($newloadData['rate_con_files'] as $fileRow) { ?>

                                            <div id="con<?php echo $fileRow['id']; ?>">

                                                <?php
                                                $file = $fileRow['fileName'];

                                                $fileName = ltrim($file, $_GET['id'] . '_');

                                                ?>

                                                <a href="Assets/uploads/cod_Files/<?php echo $fileRow['fileName']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $fileName; ?></a>

                                                <a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deleteCon('<?php echo $fileRow['id']; ?>')"><i class="uil uil-trash-alt"></i></a> <br>
                                            </div>

                                        <?php } ?>
                                    <?php } else { ?>
                                        <p style="color: red;">No Files available</p>
                                    <?php } ?>
                                </div>

                            </div>


                            <div class="bolFiles">
                                <h2>BOL Files</h2>

                                <?php if (!empty($bolFiles['bol_files'])) { ?>
                                    <?php foreach ($bolFiles['bol_files'] as $fileRow) { ?>

                                        <div id="bol<?php echo $fileRow['bol_id']; ?>">

                                            <?php
                                            $file = $fileRow['fileName'];

                                            $fileName = ltrim($file, $_GET['id'] . '_');

                                            ?>

                                            <a href="Assets/uploads/cod_Files/<?php echo $fileRow['fileName']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $fileName; ?></a>
                                            <a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deletebol('<?php echo $fileRow['bol_id']; ?>')"><i class="uil uil-trash-alt"></i></a> <br>
                                        </div>

                                    <?php } ?>
                                <?php } else { ?>
                                    <p style="color: red;">No Files available.</p>
                                <?php } ?>

                            </div>

                            <div class="podFiles">
                                <h2>POD Files</h2>

                                <?php if (!empty($podFiles['pod_files'])) { ?>
                                    <?php foreach ($podFiles['pod_files'] as $fileRow) { ?>

                                        <div id="pod<?php echo $fileRow['pod_id']; ?>">

                                            <?php
                                            $file = $fileRow['fileName'];

                                            $fileName = ltrim($file, $_GET['id'] . '_');

                                            ?>

                                            <a href="Assets/uploads/cod_Files/<?php echo $fileRow['fileName']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $fileName; ?></a>
                                            <a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deletepod('<?php echo $fileRow['pod_id']; ?>')"><i class="uil uil-trash-alt"></i></a> <br>
                                        </div>

                                    <?php } ?>
                                <?php } else { ?>
                                    <p style="color: red;">No Files available.</p>
                                <?php } ?>

                            </div>

                            <div class="pickup_files">
                                <h2>Pickup Documents</h2>

                                <?php if (!empty($pickup_files['pickup_files'])) { ?>
                                    <?php foreach ($pickup_files['pickup_files'] as $fileRow) { ?>

                                        <div id="pickup<?php echo $fileRow['pickup_file_id']; ?>">

                                            <?php
                                            $file = $fileRow['file_name'];

                                            $fileName = ltrim($file, $_GET['id'] . '_');

                                            ?>

                                            <a href="Assets/uploads/cod_Files/<?php echo $fileRow['file_name']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $fileName; ?></a>
                                            <a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; " href="javascript:void(0);" onclick="deletepcikupfile('<?php echo $fileRow['pickup_file_id']; ?>')"><i class="uil uil-trash-alt"></i></a> <br>
                                        </div>

                                    <?php } ?>
                                <?php } else { ?>
                                    <p style="color: red;">No Files available.</p>
                                <?php } ?>

                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="formbuttons">
                        <button type="submit" value="Cancel" name="cancel" class="cancel" onclick="closemodal()">Cancel</button>

                        <?php if (!empty($_GET['id'])) : ?>
                            <button value="Submit" id="newloadbtn" name="submit" class="submit newloadbtn">Update</button>

                        <?php else : ?>
                            <button value="Submit" id="newloadbtn" name="submit" class="submit newloadbtn">Submit</button>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="id" value="<?php echo !empty($newloadData['id']) ? $newloadData['id'] : ''; ?>">
                </form>
            </div>
        </div>
    </div>

    <div class="brokerdetails modal" id="brokerDetails" style="display: none;">
        <div class="brokerform modal-content">
            <span class="close" style="opacity: 0.8;" onclick="brokerDetailsclose()">&times;</span>
            <div class="newloadHeader">
                <h2>Broker Details</h2>
            </div>

            <div class="form">
                <form id="broker_form">
                    <div class="inputgroup">
                        <div class="inputbox autocomplete" style="width: 100%;">
                            <label for="brokerName">Broker Company</label>
                            <input type="text" name="brokercompany" id="brokercompany" placeholder="Please enter agent Company..">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerName">Broker Agent Name</label>
                            <input type="text" name="brokerName" id="brokerName" placeholder="Please enter agent Name..">
                            <span id='brokerNameerror' style='color: red;'></span>
                        </div>
                        <div class="inputbox">
                            <label for="brokeremail">Email</label>
                            <input type="email" name="brokeremail" id="" placeholder="agent@gmail.com">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerphone">Phone</label>
                            <input type="tel" name="brokerphone" id="" placeholder="+x-xxx-xxx-xxx">
                        </div>
                        <div class="inputbox">
                            <label for="brokerAddress">Address</label>
                            <input type="text" name="brokerAddress" id="" placeholder="Please enter agent address here..">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokercity">City</label>
                            <input type="text" name="brokercity" id="" placeholder="city">
                        </div>
                        <div class="inputbox">
                            <label for="brokerState">State</label>
                            <input type="text" name="brokerState" id="" placeholder="State">
                        </div>
                    </div>
                    <div class="textareainputbox">
                        <label for="notesprivate">Notes</label>
                        <textarea name="brokernotes" id="notesprivate" cols="120" rows="5" placeholder="Enter your private notes here..."></textarea>
                    </div>

                    <div class="formbuttons">
                        <button type="reset" value="Cancel" name="brokercancel" class="cancel" onclick="brokerDetailsclose()">Cancel</button>

                        <button value="Submit" name="brokersubmit" id="brokersubmit" class="submit">Submit</button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    <div class="address modal" id="address" style="display: none;">
        <div class="address modal-content">
            <span class="close" style="opacity: 0.8;" onclick="addressclose()">&times;</span>
            <div class="newloadHeader">
                <h2>New Address</h2>
            </div>

            <div class="form">
                <form action="./Assets/backendfiles/postAction.php" method="post">
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerName">Address</label>
                            <input type="text" name="address" id="" placeholder="Address...">
                        </div>
                        <div class="inputbox">
                            <label for="brokeremail">Street</label>
                            <input type="text" name="street" id="" placeholder="Street xx">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerphone">City</label>
                            <input type="tel" name="city" id="" placeholder="New York">
                        </div>
                        <div class="inputbox">
                            <label for="brokerAddress">State</label>
                            <input type="text" name="state" id="" placeholder="New York">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokercity">Zip</label>
                            <input type="text" name="zip" id="" placeholder="xxxxx">
                        </div>
                        <div class="inputbox">
                            <label for="brokerState">Country</label>
                            <input type="text" name="country" id="" placeholder="USA">
                        </div>
                    </div>
                    <div class="textareainputbox">
                        <label for="notesprivate">Notes</label>
                        <textarea name="addressnotes" id="notesprivate" cols="120" rows="5" placeholder="Enter you Private notes here..."></textarea>
                    </div>

                    <div class="formbuttons">
                        <button type="reset" value="Cancel" name="addresscancel" class="cancel" onclick="addressclose()">Cancel</button>

                        <button type="submit" value="Submit" name="addressform" class="submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="truckNumber modal" id="truckNumber" style="display: none;">
        <div class="truckNumber modal-content">
            <span class="close truck_close" style="opacity: 0.8;">&times;</span>
            <div class="newloadHeader">
                <h2>Truck Details</h2>
            </div>

            <div class="form">
                <form action="./Assets/backendfiles/postAction.php" method="post">
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerName">Plate Number</label>
                            <input type="text" name="truckNumber" id="" placeholder="ABC - 123">
                        </div>
                        <div class="inputbox">
                            <label for="brokeremail">Vin Number</label>
                            <input type="text" name="engineNumber" id="" placeholder="ABC - 123">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerName">Make</label>
                            <input type="text" name="make" id="" placeholder="NISSAN">
                        </div>
                        <div class="inputbox">
                            <label for="brokeremail">Model</label>
                            <input type="text" name="model" id="" placeholder="ABC - 123">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerName">Year</label>
                            <input type="month" name="year" id="" placeholder="2014">
                        </div>
                        <div class="inputbox">
                            <label for="brokercity">Contact Person Email</label>
                            <input type="text" name="cpemail" id="" placeholder="Email of Truck Driver/CP">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokerphone">Truck owner</label>
                            <input type="tel" name="truckOwner" id="" placeholder="Name of Truck owner">
                        </div>
                        <div class="inputbox">
                            <label for="brokerAddress">Truck regestred In</label>
                            <input type="text" name="registeredIn" id="" placeholder="New York">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="brokercity">Truck Driver</label>
                            <input type="text" name="truckDriver" id="" placeholder="Name of Truck Driver">
                        </div>
                        <div class="inputbox">
                            <label for="brokerState">Contact Person Phone</label>
                            <input type="text" name="cpPhone" id="" placeholder="+x-xxx-xxx-xxx">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">

                        </div>
                        <div class="inputbox">
                            <label for="brokerState">Contact Person Address</label>
                            <input type="text" name="cpAddress" id="" placeholder="Address...">
                        </div>
                    </div>
                    <div class="textareainputbox">
                        <label for="notesprivate">Notes</label>
                        <textarea name="truckNumbernotes" id="notesprivate" cols="120" rows="5" placeholder="Enter you Private notes here..."></textarea>
                    </div>

                    <div class="formbuttons">
                        <button type="reset" value="Cancel" name="truckNoCancel" class="cancel truck_close">Cancel</button>

                        <button type="submit" value="Submit" name="truckNo" class="submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <?php if (!empty($_GET['id']) && !empty($_GET['action_type'] == "dispatcher")) :
        $dispatcher_mode = 'style="display: block;"';
    else : $dispatcher_mode = 'style="display: none;"';
    endif; ?>
    <div class="dispetchForm modal" id="dispetchForm" <?php echo $dispatcher_mode ?>>
        <div class="modal-content">
            <span class="close" style="opacity: 0.8;" onclick="dispetchFormclose()">&times;</span>
            <div class="newloadHeader">
                <h2>Dispetch Load</h2>
            </div>
            <div class="form">
                <form method="post" action="./Assets/backendfiles/postAction.php" enctype="multipart/form-data">
                    <input type="hidden" name="newloadID" value="<?php echo !empty($newloaddata['id']) ? $newloaddata['id'] : ''; ?>">
                    <div class="inputgroup">

                        <div class="inputbox">
                            <label for="truckNo">Truck</label>
                            <!-- <div class="select"> -->
                            <input type="text" name="truck_number" id="" value="<?php echo !empty($newloaddata['truckNumber']) ? $newloaddata['truckNumber'] : ''; ?>">
                            <!-- </div> -->
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="customerrate">Driver</label>
                            <input type="text" name="driver" id="" value="<?php $trucknumber = !empty($newloaddata['truckNumber']) ? $newloaddata['truckNumber'] : '';

                                                                            if (!empty($trucknumber)) {
                                                                                $truck_details = $mysqli->query("SELECT * FROM truck_details where truckNumber=$trucknumber") or die($mysqli->error);

                                                                                while ($truck_detail = $truck_details->fetch_assoc()) {
                                                                                    echo $truck_detail['truckDriver'];
                                                                                }
                                                                            } ?>" placeholder="Driver">
                        </div>
                    </div>

                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="carierrate">Booked With</label>
                            <input type="text" name="Broker" id="" value="<?php echo !empty($newloaddata['brokerName']) ? $newloaddata['brokerName'] : ''; ?>" placeholder="10.23">
                        </div>
                    </div>
                    <div class="inputgroup">
                        <div class="inputbox">
                            <label for="loadtype">Load Type</label>
                            <label for="">
                                <input type="radio" name="loadtype" id="" value="Full Load" checked>Full load
                                <input type="radio" name="loadtype" id="" value="LTL">LTL
                            </label>


                        </div>
                    </div>
                    <div class="textareainputbox">
                        <label for="notesprivate">Notes Public</label>
                        <textarea style="width: 105%;" name="notesprivate" id="notesprivate" cols="55" rows="5" placeholder="Enter your private notes here..."><?php echo !empty($newloaddata['Notes_Private']) ? $newloaddata['Notes_Private'] : ''; ?></textarea>
                    </div>

                    <div class="assignedusers">
                        <label for="assignedusers">User</label>
                        <?php
                        $id = !empty($newloaddata['id']) ? $newloaddata['id'] : '1';
                        $newcheckcalls = $mysqli->query("SELECT * FROM newcheckcalls where newloadID=$id") or die($mysqli->error);
                        // if (!empty($newcheckcalls->fetch_assoc())) {
                        while ($newcheckcall = $newcheckcalls->fetch_assoc()) :

                        ?>
                            <div class="userdetails">
                                <div class="username">
                                    <p style="text-align: center;"><?php echo $newcheckcall['user']; ?></p>
                                </div>
                                <div class="assigndetails" style="display: flex;">
                                    <div class="content">
                                        <p style="margin: 0;">
                                            <?php
                                            $uploadtime = $newcheckcall['UploadedOn'];
                                            $foruploadtime = date("m-d-y", strtotime($uploadtime));
                                            echo $foruploadtime;
                                            ?>
                                        </p>
                                        <p>
                                            <?php
                                            $uploadtime = $newcheckcall['UploadedOn'];
                                            $foruploadtime = date("h:m", strtotime($uploadtime));
                                            echo $foruploadtime;
                                            ?>
                                        </p>
                                    </div>
                                    <a style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; margin-left: 20px;" href="./Assets/backendfiles/postAction.php?action_type=checkcalldelete&id=<?php echo $newcheckcall['callid']; ?>" onclick="return confirm('Are you sure to delete data?')?true:false;">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>

                                </div>

                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="inputgroup">

                        <div class="inputbox">
                            <div class="select">

                                <select class="form-control select2" style="width: 60%;" name="user">

                                    <?php while ($user = $users->fetch_assoc()) : ?>
                                        <option data-foo=" "><?php echo $user['user_name']; ?></option>

                                    <?php endwhile; ?>


                                </select>
                                <div class="addmore"><button type="submit" name="assignUser" style="font-weight: 400; font-size: 15px; padding: 3px 10px;">Assign User</button></div>


                            </div>
                        </div>
                    </div>

                    <div class="formbuttons">
                        <button type="submit" value="Cancel" name="cancel" class="cancel" onclick="dispetchFormclose()">Cancel</button>
                        <button type="submit" value="Submit" name="submitdisp" class="submit">Submit</button>
                    </div>

                    <input type="hidden" name="id" value="<?php echo !empty($newloaddata['id']) ? $newloaddata['id'] : ''; ?>">
                </form>

                <div class="summary">
                    <div class="title">
                        <h2>Totals</h2>
                    </div>
                    <div class="weightdetails">
                        <div class="details">
                            <p>Weight</p>
                            <p><?php echo !empty($newloaddata['Weight']) ? $newloaddata['Weight'] : ''; ?></p>
                        </div>
                        <div class="details">
                            <p>Pallets</p>
                            <p><?php echo !empty($newloaddata['Pallets']) ? $newloaddata['Pallets'] : ''; ?></p>
                        </div>
                        <div class="details">
                            <p>Pcs</p>
                            <p></p>
                        </div>
                        <div class="details">
                            <p>Length</p>
                            <p></p>
                        </div>
                    </div>
                    <div class="ratedetails">
                        <div class="details">
                            <p>Rate</p>
                            <p>$ <?php echo !empty($newloaddata['Customer_Rate']) ? $newloaddata['Customer_Rate'] : ''; ?></p>
                        </div>
                        <div class="details">
                            <p>carrier Pay</p>
                            <p>$ <?php echo !empty($newloaddata['Carier_Driver_Rate']) ? $newloaddata['Carier_Driver_Rate'] : ''; ?></p>
                        </div>
                    </div>
                    <div class="miledetails">
                        <div class="details">
                            <p>Total Miles</p>
                            <p></p>
                        </div>
                        <div class="details">
                            <p>Total Time</p>
                            <p></p>
                        </div>
                        <div class="details">
                            <p>Rate/Mile</p>
                            <p></p>
                        </div>
                        <div class="details">
                            <p>Carrier pay/Mile</p>
                            <p></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script src="./Assets/js/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.css">
    <script src="https://cdn.jsdelivr.net/gh/stefanpenner/es6-promise@master/dist/es6-promise.min.js"></script>
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=fetch"></script>
    <script>
        // Search functionality
        function search() {
            // Declare variables
            var input, filter, ul, li, p, a, i, txtValue;
            input = document.getElementById('search_loadBars');
            filter = input.value.toUpperCase();
            ul = document.getElementById("undelivered_load_bars");
            li = ul.getElementsByClassName('undelivered_load_bars');
            filterindex = $("select[name='search_loadBars_filter'] option:selected").index()

            // Loop through all list items, and hide those who don't match the search query
            for (i = 0; i < li.length; i++) {
                p = li[i].getElementsByClassName("sa")[filterindex];

                txtValue = p.textContent || p.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }

            }
        }


        $(document).ready(function() {

            // Have the previously selected tab open
            var activeTab = sessionStorage.activeTab;
            $(".tab-content").fadeIn(1000);
            if (activeTab) {
                $('.tab-content ' + activeTab).show().siblings().hide();
                // also make sure you your active class to the corresponding tab menu here
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").parent().addClass('active').siblings().removeClass('active');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').addClass("active_span").parent().parent().siblings().children().children().removeClass('active_span');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').children().addClass("active_span").parent().parent().parent().siblings().children().children().children().removeClass('active_span');
            } else {
                activeTab = "#Dashboard";
                $('.tab-content ' + activeTab).show().siblings().hide();
                // also make sure you your active class to the corresponding tab menu here
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").parent().addClass('active').siblings().removeClass('active');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').addClass("active_span").parent().parent().siblings().children().children().removeClass('active_span');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').children().addClass("active_span").parent().parent().parent().siblings().children().children().children().removeClass('active_span');
            }

            // Enable, disable and switch tabs on click
            $('.navbar .menu li a').on('click', function(e) {
                var currentAttrValue = $(this).attr('href');

                // Show/Hide Tabs
                $('.tab-content ' + currentAttrValue).fadeIn(2000).siblings().hide();
                sessionStorage.activeTab = currentAttrValue;

                // Change/remove current tab to active
                $(this).parent('li').addClass('active').siblings().removeClass('active');
                $('.navbar .menu li a span').removeClass('active_span').removeClass('active_span2');
                $(this).children().addClass('active_span');
                e.preventDefault();
            });


            // Data Table
            // Create date inputs
            minDate = new DateTime($('#min'), {
                format: 'MM-D-YY'
            });
            maxDate = new DateTime($('#max'), {
                format: 'MMMM Do YYYY'
            });
            table1_dt = $('#table1').DataTable({
                "bStateSave": true,
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'show'
                ]
            });
            $('#table2').dataTable({
                "bStateSave": true,
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $('#table3').dataTable({
                "bStateSave": true,
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $('#table4').dataTable({
                "bStateSave": true,
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $('#table5').dataTable({
                "bStateSave": true,
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });


        // Toggle Buton
        function profiletoggle() {
            var x = document.getElementsByClassName("profiledetails")[0];
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }

        // Select 2 function
        $(function() {
            $(".select2").select2({
                matcher: matchCustom,
                templateResult: formatCustom
            });
        })

        function stringMatch(term, candidate) {
            return candidate && candidate.toLowerCase().indexOf(term.toLowerCase()) >= 0;
        }

        function matchCustom(params, data) {
            // If there are no search terms, return all of the data
            if ($.trim(params.term) === '') {
                return data;
            }
            // Do not display the item if there is no 'text' property
            if (typeof data.text === 'undefined') {
                return null;
            }
            // Match text of option
            if (stringMatch(params.term, data.text)) {
                return data;
            }
            // Match attribute "data-foo" of option
            if (stringMatch(params.term, $(data.element).attr('data-foo'))) {
                return data;
            }
            // Return `null` if the term should not be displayed
            return null;
        }

        function formatCustom(state) {
            return $(
                '<div><div>' + state.text + '</div><div class="foo">' +
                $(state.element).attr('data-foo') +
                '</div></div>'
            );
        }

        // Delete CON Files from record
        function deleteCon(id, fileType) {
            var result = confirm("Are you sure to delete?");
            if (result) {
                $.post("./Assets/backendfiles/postAction.php", {
                    action_type: 'rateCon_delete',
                    id: id
                }, function(resp) {
                    if (resp == 'ok') {
                        $('#con' + id).remove();
                        alert('The File has been removed from the DataBase');
                    } else {
                        alert('Some problem occurred, please try again.');
                    }
                });
            }
        }

        // Delete BOL Files from record
        function deletebol(id, fileType) {
            var result = confirm("Are you sure to delete?");
            if (result) {
                $.post("./Assets/backendfiles/postAction.php", {
                    action_type: 'bolFile_delete',
                    id: id
                }, function(resp) {
                    if (resp == 'ok') {
                        $('#bol' + id).remove();
                        alert('The File has been removed from the DataBase');
                    } else {
                        alert('Some problem occurred, please try again.');
                    }
                });
            }
        }

        // Delete POD Files from record
        function deletepod(id, fileType) {
            var result = confirm("Are you sure to delete?");
            if (result) {
                $.post("./Assets/backendfiles/postAction.php", {
                    action_type: 'podFile_delete',
                    id: id
                }, function(resp) {
                    if (resp == 'ok') {
                        $('#pod' + id).remove();
                        alert('The File has been removed from the DataBase');
                    } else {
                        alert('Some problem occurred, please try again.');
                    }
                });
            }
        }

        // Delete Pickup Files from record
        function deletepcikupfile(id, fileType) {
            var result = confirm("Are you sure to delete?");
            if (result) {
                $.post("./Assets/backendfiles/postAction.php", {
                    action_type: 'pickupFile_delete',
                    id: id
                }, function(resp) {
                    if (resp == 'ok') {
                        $('#pod' + id).remove();
                        alert('The File has been removed from the DataBase');
                    } else {
                        alert('Some problem occurred, please try again.');
                    }
                });
            }
        }

        // Time for allert messgae
        $("#alert").show().delay(5000).queue(function(n) {
            $(this).hide();
            n();
        });

        function opentable(evt, tablestatus) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tablestatus).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // select the already added option
        function selector(id, value) {
            select = document.getElementById(id);
            for (i = 0; i < select.options.length; i++) {
                if (select.options[i].innerHTML == value) {
                    select.selectedIndex = i;
                }
            }

        }

        selector("broker", "<?php echo !empty($newloaddata['brokerName']) ? $newloaddata['brokerName'] : ''; ?>")
        selector("truck_Number", "<?php echo !empty($newloaddata['truckNumber']) ? $newloaddata['truckNumber'] : ''; ?>")


        // Modal Box
        var modal = document.getElementById("newloadform");
        var btn = document.getElementById("newloadformbtn");
        var span = document.getElementsByClassName("close")[0];
        btn.onclick = function() {
            modal.style.display = "block";
        }

        function closemodal() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Broker details
        var brokerDetails = document.getElementById("brokerDetails");
        var brokerDetailsbtn = document.getElementsByclass("brokerDetailsbtn");
        var span = document.getElementsByClassName("close")[0];
        brokerDetailsbtn.onclick = function() {
            brokerDetails.style.display = "block";
        }

        function brokerDetailsclose() {
            brokerDetails.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == brokerDetails) {
                brokerDetails.style.display = "none";
            }
        }

        // Dispetcher details
        var dispetcher = document.getElementById("dispetcher");
        // var dispetcherbtn = document.getElementById("dispetcherbtn");
        var span = document.getElementsByClassName("close")[0];
        // dispetcherbtn.onclick = function() {
        //     dispetcher.style.display = "block";
        // }

        function closedispetcher() {
            dispetcher.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == dispetcher) {
                dispetcher.style.display = "none";
            }
        }

        // new check calls details
        var newcheckcalls = document.getElementById("newcheckcalls");
        // var newcheckcallsbtn = document.getElementById("newcheckcallsbtn");
        var span = document.getElementsByClassName("close")[0];

        function newcheckcallsbtn() {
            newcheckcalls.style.display = "block";
        }

        function newcheckcallsclose() {
            newcheckcalls.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == newcheckcalls) {
                newcheckcalls.style.display = "none";
            }
        }

        // Truck details
        var truckNumber = document.getElementById("truckNumber");
        var truckNumberbtn = document.getElementById("truckNumberbtn");
        var span = document.getElementsByClassName("close")[0];
        truckNumberbtn.onclick = function() {
            truckNumber.style.display = "block";
        }

        function truckNumberclose() {
            truckNumber.style.display = "none";
        }

        function mconwc(event) {
            window.onclick
            if (event.target == truckNumber) {
                truckNumber.style.display = "none";
            }
        }
    </script>


    <!---------- Charts Js  -------------------->
    <script>
        // Despatcher Summary
        var config = {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    $labels = $mysqli->query("SELECT dispatcher,count(dispatcher)  AS count_me FROM newload  WHERE dispatcher IS NOT NULL GROUP BY dispatcher ORDER BY COUNT(dispatcher) DESC") or die($mysqli->error);
                    while ($row = mysqli_fetch_array($labels)) {
                        echo "'" . $row['dispatcher'] . "', ";
                        $dispatcher = $row['dispatcher'];
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php

                        function disploadcounts($dispName)
                        {
                            $disloadcounts = [];

                            include './Assets/backendfiles/config.php';

                            $data = $mysqli->query("SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, COUNT(*) AS amount, dispatcher AS D
                                                                FROM newload
                                                                where dispatcher='$dispName'
                                                                GROUP BY unique_date
                                                                ORDER BY unique_date DESC
                                                                ") or die($mysqli->error);

                            while ($row = mysqli_fetch_array($data)) {
                                // echo "'" . $row['amount'] . "', ";
                                $amount = $row['amount'];
                                array_push($disloadcounts, $amount);
                            }

                            return json_encode($disloadcounts);
                        };



                        $data = $mysqli->query("SELECT dispatcher,count(dispatcher)  AS count_me 
                                                            FROM newload  
                                                            
                                                            WHERE dispatcher IS NOT NULL 
                                                            GROUP BY dispatcher 
                                                            ORDER BY COUNT(dispatcher) DESC
                                                            
                                                            ") or die($mysqli->error);
                        while ($row = mysqli_fetch_array($data)) {
                            echo "{x: '" . $row['dispatcher'] . "', y:" . $row['count_me'] . ", doneperday: " . disploadcounts($row['dispatcher']) . ", title: '" . $row['dispatcher'] . "'},";
                        }

                        ?>
                    ],
                    backgroundColor: bg1,
                }]
            },
            options: {
                // maintainAspectRatio: false,
                responsive: false,
                plugins: {
                    legend: {
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                size: 8,

                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        }
                    },
                }
            }
        };
        var dispSummaryelement = document.getElementById("dis-summary")
        // disSummary.defaults.font.size = 8;
        var disSummary = new Chart(dispSummaryelement, config);

        var config3 = {
            type: 'doughnut',
            data: {
                labels: [
                    <?php
                    $labels = $mysqli->query("SELECT dispatcher,count(dispatcher)  AS count_me FROM newload  WHERE dispatcher IS NOT NULL GROUP BY dispatcher ORDER BY COUNT(dispatcher) DESC") or die($mysqli->error);
                    while ($row = mysqli_fetch_array($labels)) {
                        echo "'" . $row['dispatcher'] . "', ";
                        $dispatcher = $row['dispatcher'];
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php

                        $data = $mysqli->query("SELECT dispatcher,count(dispatcher)  AS count_me 
                                                            FROM newload  
                                                            
                                                            WHERE dispatcher IS NOT NULL 
                                                            GROUP BY dispatcher 
                                                            ORDER BY COUNT(dispatcher) DESC
                                                            
                                                            ") or die($mysqli->error);
                        while ($row = mysqli_fetch_array($data)) {
                            echo $row['count_me'] . ",";
                        }

                        ?>
                    ],
                    backgroundColor: [
                        "#F94144",
                        "#F3722C",
                        "#F8961E",
                        "#F9844A",
                        "#F9C74F",
                        "#90BE6D",
                        "#43AA8B",
                        "#4D908E",
                        "#577590",
                        "#277DA1",
                        "#001219",
                        "#005F73",
                        "#0A9396",
                        "#94D2BD",
                        "#E9D8A6",
                        "#EE9B00",
                        "#CA6702",
                        "#BB3E03",
                        "#AE2012",
                        "#9B2226",
                    ],
                    borderWidth: 0.5,

                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                size: 8,

                            }
                        }
                    }
                },
                // legend: {
                //     display: false
                // },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        },
                        display: false,
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        },
                        display: false,
                    },
                }
            }
        };
        var dispPieSummaryelement = document.getElementById("disp-pie-summary")
        // disSummary.defaults.font.size = 8;
        var disPieSummary = new Chart(dispPieSummaryelement, config3);

        // Despatcher Summary
        var config2 = {
            type: 'line',
            data: {
                labels: [
                    <?php
                    // $uniquedisloaddate = [];

                    $labels = $mysqli->query("SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, COUNT(*) AS amount, dispatcher AS D
                                                            FROM newload
                                                            where dispatcher='Thomas'
                                                            GROUP BY unique_date
                                                            ORDER BY unique_date DESC
                                                            ") or die($mysqli->error);

                    while ($row = mysqli_fetch_array($labels)) {
                        echo "'" . $row['unique_date'] . "', ";
                        // $uniquedate = $row['unique_date'];
                        // array_push($uniquedate);
                    }
                    ?>
                ],
                datasets: [{
                    label: "Dispatcher Details",
                    data: [
                        <?php

                        $disloadcounts = [];

                        $data = $mysqli->query("SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, COUNT(*) AS amount, dispatcher AS D
                                                                FROM newload
                                                                where dispatcher='Thomas'
                                                                GROUP BY unique_date
                                                                ORDER BY unique_date DESC
                                                                ") or die($mysqli->error);

                        while ($row = mysqli_fetch_array($data)) {
                            echo "'" . $row['amount'] . "', ";
                            // $amount = $row['amount'];
                            // array_push($disloadcounts, $amount);
                        }

                        ?>
                    ],
                    backgroundColor: [
                        "rgba(254, 206, 0, 0.2)",

                    ],
                    borderColor: 'rgb(254, 206, 0)',
                    borderWidth: 1,
                    fill: true,
                    tension: 0.5,
                    fillOpacity: 0.5,


                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        // display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                size: 8
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 0
                    }
                },
                tooltips: {
                    // enabled: false
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        }
                    },
                    // yAxes: [{
                    //     // ticks: {
                    //     //     beginAtZero: true
                    //     // }
                    // }],
                    // xAxes: [{
                    //     ticks: {
                    //         fontSize: 8
                    //     }
                    // }]
                }
            }
        };
        var dispDetailelement = document.getElementById("disDetail").getContext("2d");
        var disDetail = new Chart(dispDetailelement, config2);

        function clickhandler(click) {
            const points = disSummary.getElementsAtEventForMode(click, 'nearest', {
                intersect: true
            }, true);
            if (points.length) {

                disDetail.config.data.datasets[0].data = points[0].element.$context.raw.doneperday;
                disDetail.config.data.datasets[0].label = points[0].element.$context.raw.title;
                disDetail.update();
            }
        }

        dispSummaryelement.onclick = clickhandler;
    </script>

    <!-- Map -->
    <script>
        var map;
        var color = ["red", "blue", "green"];
        var directions = [];
        var shortest = [];
        var fastest = [];

        //--------- Further Code in the Index.js File --------- // 

        // function initMap() {
        //     map = new google.maps.Map(document.getElementById("map"), {
        //         center: {
        //             lat: -33.8688,
        //             lng: 151.2195
        //         },
        //         zoom: 13
        //     });

        //     // var startInput = document.getElementsByClassName("start");
        //     var endInput = document.getElementsByClassName("end");
        //     var startValue, endValue;
        // console.log(startInput)

        // for (i = 0; i < startInput.length; i++) {
        // var startAutocomplete = new google.maps.places.Autocomplete(startInput[i]);
        // }
        // for (i = 0; i < endInput.length; i++) {
        //     var endAutocomplete = new google.maps.places.Autocomplete(endInput[i]);
        // }

        // var endAutocomplete = new google.maps.places.Autocomplete(endInput[0]);

        // startAutocomplete.addListener("place_changed", function() {
        //     startValue = startAutocomplete.getPlace().formatted_address;
        // });

        // endAutocomplete.addListener("place_changed", function() {
        //     var place = endAutocomplete.getPlace()
        //     // document.getElementById("lat").value = place.geometry.location.lat()
        //     // document.getElementById("lng").value = place.geometry.location.lng()
        //     endValue = place.formatted_address;
        // });

        // var markers = [];
        // var directionsService = new google.maps.DirectionsService();
        // var directionsRenderer = new google.maps.DirectionsRenderer();

        //     document
        //         .getElementById("end")
        //         .addEventListener("change", function() {
        //             setTimeout(function() {
        //                 console.log("I am the third log after 5 seconds");

        //                 for (var i = 0; i < directions.length; i++) {
        //                     directions[i].setMap(null);
        //                 }
        //                 directions = [];
        //                 shortest = [];
        //                 fastest = [];
        //                 for (var i = 0; i < color.length; i++) {
        //                     document.getElementById(color[i]).value = "";
        //                 }
        //                 showAlternativeRoutes(
        //                     directionsService,
        //                     directionsRenderer,
        //                     startValue,
        //                     endValue
        //                 );
        //             }, 2000);
        //         });
        // }



        // function showAlternativeRoutes(
        //     directionsService,
        //     directionsRenderer,
        //     startValue,
        //     endValue
        // ) {
        //     console.log("calculated!");
        //     directionsService.route({
        //             origin: startValue,
        //             destination: endValue,
        //             travelMode: "DRIVING",
        //             provideRouteAlternatives: true
        //         },
        //         function(response, status) {
        //             if (status === "OK") {
        //                 console.log(response);
        //                 for (var i = 0; i < response.routes.length; i++) {
        //                     shortest.push(response.routes[i].legs[0].distance.value);
        //                     fastest.push(response.routes[i].legs[0].duration.value);
        //                 }
        //                 shortest.sort(function(a, b) {
        //                     return a - b;
        //                 });
        //                 fastest.sort(function(a, b) {
        //                     return a - b;
        //                 });
        //                 console.log(shortest);

        //                 for (var i = 0; i < response.routes.length; i++) {
        //                     var dr = new google.maps.DirectionsRenderer();
        //                     directions.push(dr);
        //                     dr.setOptions({
        //                         map: map,
        //                         directions: response,
        //                         routeIndex: i,
        //                         polylineOptions: {
        //                             strokeColor: color[i],
        //                             strokeOpacity: 0.5
        //                         }
        //                     });

        //                     if (
        //                         shortest[0] == response.routes[i].legs[0].distance.value &&
        //                         fastest[0] == response.routes[i].legs[0].duration.value
        //                     ) {
        //                         document.getElementById(color[i]).value =
        //                             response.routes[i].legs[0].distance.text +
        //                             " - " +
        //                             response.routes[i].legs[0].duration.text +
        //                             "(fastest and shortest)";
        //                     } else if (
        //                         fastest[0] == response.routes[i].legs[0].duration.value
        //                     ) {
        //                         document.getElementById(color[i]).value =
        //                             response.routes[i].legs[0].distance.text +
        //                             " - " +
        //                             response.routes[i].legs[0].duration.text +
        //                             "(fastest)";
        //                     } else if (
        //                         shortest[0] == response.routes[i].legs[0].distance.value
        //                     ) {
        //                         document.getElementById(color[i]).value =
        //                             response.routes[i].legs[0].distance.text; //+
        //                         //" - " +
        //                         //response.routes[i].legs[0].duration.text +
        //                         //"(shortest)";
        //                     } else {
        //                         document.getElementById(color[i]).value =
        //                             response.routes[i].legs[0].distance.text;
        //                         document.getElementById('time').value = response.routes[i].legs[0].duration.text;
        //                     }
        //                 }
        //                 map.setZoom(11);
        //             } else {
        //                 window.alert("Directions request failed due to " + status);
        //             }
        //         }
        //     );
        // }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZ7-l7KyYlEaTI3Yxv9Ar5pPcQa8usipY&libraries=places&callback=initMap" async defer></script>
</body>

</html>