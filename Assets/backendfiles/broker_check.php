<?php
session_start();

include './config.php';

if (($_REQUEST['action_type'] == 'checkbrokername')) {
    $username = $_GET['broker_name'];
    $query = "select brokerName from logisticscrm.broker_details where brokerName ='$username' limit 1";
    $usercheck = $mysqli->query($query);

    if ($usercheck->num_rows > 0) {
        echo "Broker Name Already exists. Please try some other Broker Name";
        die;
    } else {
        echo "success";
    }
} else if (($_REQUEST['action_type'] == 'addCheckCall')) {
    $id = $_GET['id'];
    $checkpoints = $_POST['checkpoints'];
    $newchecknotes = $_POST['check_notes'];
    $user = $_SESSION['myFirstName'] . " " . $_SESSION['myLastName'];

    $data = [];

    try{
        $mysqli->query("INSERT INTO newcheckcalls (newloadID, user, checkpoints, newchecknotes, UploadedOn) VALUES('$id', '$user', '$checkpoints', '$newchecknotes', '$now')") or die($mysqli->error);

        $data[] = [
            'success' => 1,
            'msg' => "Data for check call has been successfully added!",
            'user' => "$user"
        ];
    }catch(Exception $e){

        $data[] = [
            'success' => 0,
            'msg' => "Something went wrong!",
        ];

    }

    echo json_encode($data);
}

?>

