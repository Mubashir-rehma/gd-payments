<?php
session_start();

include './config.php';
require_once './DB.class.php';
$db = new DB();
$uploadDir = "../uploads/cod_Files/";
$redirectURL = '../../index.php';
$statusMsg = $errorMsg = '';
$sessData = array();
$statusType = 'danger';


if (($_REQUEST['action_type'] == "broker_submit")) {
    $id = $_POST['id'];
    $broker_company = $mysqli->real_escape_string($_POST['brokercompany']);
    $brokerName = $mysqli->real_escape_string($_POST['brokerName']);
    $brokeremail = $mysqli->real_escape_string($_POST['brokeremail']);
    $brokerphone = $mysqli->real_escape_string($_POST['brokerphone']);
    $brokerAddress = $mysqli->real_escape_string($_POST['brokerAddress']);
    $brokercity = $mysqli->real_escape_string($_POST['brokercity']);
    $brokernotes = $mysqli->real_escape_string($_POST['brokernotes']);
    $brokerState = $mysqli->real_escape_string($_POST['brokerState']);
    $user = $mysqli->real_escape_string($_SESSION['myusername']);

    $msg = "";

    if(!empty($id)){
        $mysqli->query("UPDATE broker_details SET broker_company='$broker_company',brokerName='$brokerName',brokeremail='$brokeremail',brokerphone='$brokerphone',brokerAddress='$brokerAddress',brokercity='$brokercity',brokerState='$brokerState',brokernotes='$brokernotes' WHERE broker_id='$id'") or die($mysqli->error);

        $msg = "Broker Updated Successfully!";
    } else {
        $mysqli->query("INSERT INTO broker_details (broker_company, brokerName, brokeremail, brokerphone, brokerAddress, brokercity, brokernotes, brokerState, created_by) VALUES('$broker_company', '$brokerName', '$brokeremail','$brokerphone', '$brokerAddress', '$brokercity', '$brokernotes', '$brokerState', '$user')") or die($mysqli->error);
        
        $msg = 'Broker Added Successfully!';
    }
    

    $brokers = $mysqli->query(loadQuery($_SESSION['myusername']));
    $dispatcher = $_SESSION['myusername'];
    $newbrokers = $mysqli->query("select * from broker_details where created_by = '$dispatcher'") or die($mysqli->error);

    $html_content = tableData($newbrokers, $brokers);

    $data[] = [
        'success' => 1,
        'msg' => $msg,
        'rows' => $html_content
    ];

    echo json_encode($data);
} else if (($_REQUEST['action_type'] == 'brokercompany_list')) {
    $brokerid = $mysqli->query("select broker_id, broker_company from broker_details") or die($mysqli->error);

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
} else if($_REQUEST['action_type'] == 'broker_delete'){
    $id = $_GET['id'];
    $dispatcher = $_SESSION['myusername'];

    $delete = $mysqli->query("DELETE FROM broker_details WHERE broker_id='$id'") or die($mysqli->error);

    $brokers = $mysqli->query(loadQuery($dispatcher));
    $newbrokers = $mysqli->query("select * from broker_details where created_by = '$dispatcher'") or die($mysqli->error);

    $html_content = tableData($newbrokers, $brokers);

    $data[] = [
        'success' => 1,
        'msg' => 'Broker Deleted Successfully!',
        'rows' => $html_content
    ];

    echo json_encode($data);

} else if ($_REQUEST['action_type'] == 'broker_edit') {
    $id = $_GET['id'];


    $broker = $mysqli->query("select * from broker_details where broker_id = '$id'") or die($mysqli->error);

    $data = [];
    foreach($broker as $row){
        $broker_id = $row['broker_id'];
        $broker_company = $row['broker_company'];
        $brokerName = $row['brokerName'];
        $brokeremail = $row['brokeremail'];
        $brokerphone = $row['brokerphone'];
        $brokerAddress = $row['brokerAddress'];
        $brokercity = $row['brokercity'];
        $brokerState = $row['brokerState'];
        $brokernotes = $row['brokernotes'];

        $data[] = [
            'success' => 1,
            'broker_id' => $broker_id,
            'broker_company' => $broker_company,
            'brokerName' => $brokerName,
            'brokeremail' => $brokeremail,
            'brokerphone' => $brokerphone,
            'brokerAddress' => $brokerAddress,
            'brokercity' => $brokercity,
            'brokerState' => $brokerState,
            'brokernotes' => $brokernotes,
        ];
    };

    echo json_encode($data);
} else if($_REQUEST['action_type'] == 'view_load_history'){
    $id = $_GET['id'];
    $dispatcher = $_SESSION['myusername'];

    $load_history = $mysqli->query(loadhistoryQuery($dispatcher, $id)) or die($mysqli->error);

    $rows = tdata($load_history);

    $data = [
        "success" => 1,
        "rows" => $rows
    ];

    echo json_encode($data);
}


function tableData($q1, $q2){
    $rows = '';
    $i = 0;
    foreach($q1 as $row){
        !empty($row['brokerState']) ? $state = " , " : $state =  "";
        $i++;
        $rows .= '<tr>';
            $rows .= '<td> '. $i . ' </td>';

            $rows .= '<td>' . $row['broker_company'] .' </td>';

            $rows .= '<td> ' .$row['brokerName'] . '</td>';

            $rows .= '<td style="width: 200px;">';
                $rows .= '<a href="mailto: '.$row['brokeremail'] .'"> '.$row['brokeremail'] .'</a><br>';
                $rows .= '<a href="tel:+1 '. $row['brokerphone'] .'"> '.$row['brokerphone'] .'</a>';
            $rows .= '</td>';

            $rows .= '<td style="text-align: left;">';
                $rows .= '<a href="https://www.google.com/maps/place/ '. $row['brokerAddress'] .'" target="_blank" "rel="noopener noreferrer"> '. $row['brokerAddress'] .'</a>';
            $rows .= '</td>';

            $rows .= '<td> ' .$row['brokercity'] . $state . $row['brokerState']  . '</td>';

            $rows .= '<td> ' . $row['brokernotes'] . '</td>';

            $rows .= '<td style="display: flex;">';
            $rows .= '<a href="#" data-broker_id="' . $row['broker_id'] . '" data-action_type="broker_edit" class="broker_edit eidt" ><i class="uil uil-pen"></i></a>';
            $rows .= '<a href="#" data-broker_id="' . $row['broker_id'] . '" data-action_type="broker_delete" class="broker_delete delete" ><i class="uil uil-trash-alt"></i></a>';
            $rows .= '</td>';
        $rows .= '</tr>';
    }

    foreach ($q2 as $ro) {
        !empty($ro['brokerState']) ? $state = " , " : $state =  "";
        $i++;
        $rows .= '<tr>';
        $rows .= '<td> ' . $i . ' </td>';

        $rows .= '<td><a href="#" data-broker_id=" ' . $ro['Broker'] . '" data-action_type="view_load_history" class="view_load_history"> ' . $ro['broker_company'] . ' </a></td>';

        $rows .= '<td> ' . $ro['brokerName'] . '</td>';

        $rows .= '<td style="width: 200px;">';
        $rows .= '<a href="mailto: ' . $ro['brokeremail'] . '"> ' . $ro['brokeremail'] . '</a><br>';
        $rows .= '<a href="tel:+1 ' . $ro['brokerphone'] . '"> ' . $ro['brokerphone'] . '</a>';
        $rows .= '</td>';

        $rows .= '<td style="text-align: left;">';
        $rows .= '<a href="https://www.google.com/maps/place/ ' . $ro['brokerAddress'] . '" target="_blank" "rel="noopener noreferrer"> ' . $ro['brokerAddress'] . '</a>';
        $rows .= '</td>';

        $rows .= '<td> ' . $ro['brokercity'] . $state . $ro['brokerState']  . '</td>';

        $rows .= '<td> ' . $ro['brokernotes'] . '</td>';

        $rows .= '<td style="display: flex;">';
        $rows .= '<a href="#" data-broker_id="' . $ro['broker_id'] . '" data-action_type="broker_edit" class="broker_edit edit" ><i class="uil uil-pen"></i></a>';
        $rows .= '<a href="#" data-broker_id="' . $ro['broker_id'] . '" data-action_type="broker_delete" class="broker_delete delete" ><i class="uil uil-trash-alt"></i></a>';
        $rows .= '</td>';
        $rows .= '</tr>';
    }
     
    return $rows;
}

function loadQuery($dispatcher)
{
    $query = "SELECT * 
        FROM newload n 
        LEFT OUTER JOIN broker_details AS b 
        ON b.broker_id = n.Broker";

    if ($_SESSION['myusertype'] == "admin") {
        $query .= " group by Broker ";
    } else {
        $query .= " where n.dispatcher='$dispatcher' group by Broker ";
    }
    $query .= " ORDER BY id DESC ";

    return $query;
}

function tdata($query)
{
    $rows = "";
    if (!empty($query)) {
        $i = 0;
        foreach ($query as $row) {
            $i++;

            $PU_count = count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []);
            count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []) > 0 ? $pickuplocation = unserialize($row['Pick_up_Location'])[0] : $pickuplocation = unserialize($row['Pick_up_Location']);
            count((is_countable(unserialize($row['Destination']))) ? unserialize($row['Destination']) : []) > 0 ? $destination = unserialize($row['Destination'])[0] : $destination = unserialize($row['Destination']);

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
            $rows .= 'JBA <br>' . $row['id'] . '<br>';
            $rows .= '<span style="color: var(--light-font);">' . $row['Ref_No'] . '</span>';

            $rows .= '</td>';
            $rows .= '<td style="min-width: 220px;">';
            $rows .= '<div>';
            $rows .= '<div class="truckImg">';
            $rows .= '<img src="./Assets/Images/Business Logo.png" width="30px" />';
            $rows .= '</div>';

            $rows .= '<span style="color: var(--light-font);">$ ' . $row['Customer_Rate'] . '</span><br>';
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
                $forpickupdate = date("m-d-y", strtotime($pickupdate));
                $rows .= $forpickupdate . " ";
                $forpickuptime = date("h:i a", strtotime($pickupdate));
                $rows .= $forpickuptime;
            } else {
                $rows .= '';
            }
            $rows .= '</p>';
            $rows .= '<p style="margin-bottom: 0;">';
            if (strtotime($row['dropdate']) > 0) {
                $originalDate = $row['dropdate'];
                $newDate = date("m-d-y", strtotime($originalDate));
                $rows .= $newDate . " ";

                $forpickuptime = date("h:i a", strtotime($originalDate));
                $rows .= $forpickuptime;
            } else {
                $rows .= '';
            }
            $rows .= '</p>';
            $rows .= '</td>';

            $rows .= '<td>' . $row['newchecknotes'] . '</td>';

            $rows .= '</tr>';
        }
    } else {
        $rows .= '<tr>';
        $rows .= '<td colspan="7">No Data found...</td>';
        $rows .= '</tr>';
    }

    return $rows;
}

function loadhistoryQuery($dispatcher, $id)
{
    $query = "SELECT * 
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
    ON n.id = B.bol_newload_id";

    if($_SESSION['myusertype'] == "admin"){
        $query .= " where n.Broker = '$id' ";
    } else{
        $query .= " where n.dispatcher='$dispatcher' and n.Broker = '$id' ";
    }
    $query .= " ORDER BY id DESC limit 5 ";

    return $query;
}

?>