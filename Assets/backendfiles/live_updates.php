<?php
session_start();
include './config.php';
$creater = $_SESSION['myid'];


if ($_REQUEST['action_type'] == "live_update") {
    //$val = $mysqli->real_escape_string($_POST['val']);
    $val = $_POST['val'];
    $col = $_POST['col'];
    $load_id = $_POST['load_id'];

    // var_dump($val);

    if ($col !== "assigned_to") {
        $query = "UPDATE newload SET $col='$val' where id='$load_id'";
        $mysqli->query($query) or die($mysqli->error);

        $q = "INSERT into live_update_changes (load_id, user_id, column_name, changes) Values('$load_id', '$creater', '$col', '$val')";
        $mysqli->query($q) or die($mysqli->error);

    } else if ($col == "assigned_to") {
        if(isset($val)){
            $uv = implode(", ", $val);
            $aq= "select * from load_changes_assigned where load_id=$load_id && user_id IN ($uv)";
            $ad= $mysqli->query($aq) or die($mysqli->error);
            // Check if the query was successful
            if ($ad) {
                // Fetch all rows into an associative array
                $sqlData = $ad->fetch_all(MYSQLI_ASSOC);
                $ad->free(); // Free the result set
            } else {
                // Handle the query error
                // echo "Error executing the query: " . $mysqli->error;
            }

            // Convert the SQL data into an array of values to search in (assuming you want to search in a specific column, e.g., 'name')
            $columnToSearch = 'user_id';
            $dataArray = array_column($sqlData, $columnToSearch);

            foreach ($val as $v) {             
                // $qa = "select * from load_changes_assigned where load_id=$load_id && user_id=$v";
                // $d = $mysqli->query($qa) or die($mysqli->error);
                // foreach($d as $dd){
                //         //print_r($dd);
                // }
                if((!in_array($v, $dataArray))){
                    $assigned = "INSERT into load_changes_assigned (load_id, user_id) VALUES('$load_id', '$v')";
                    $mysqli->query($assigned) or die($mysqli->error);
                }
                // else if(($d->field_count > $v)) {
                //     $querydel = "DELETE FROM load_changes_assigned where load_id = $load_id AND user_id=$v";
                //     $mysqli->query($querydel) or die($mysqli->error);
                // }

                $q = "INSERT into live_update_changes (load_id, user_id, column_name, changes) Values('$load_id', '$creater', '$col', '$v')";
                $mysqli->query($q) or die($mysqli->error);
            }
        } else {
            $querydel = "DELETE FROM load_changes_assigned where load_id = $load_id";
            $mysqli->query($querydel) or die($mysqli->error);
        }

    }


    $data = [
        "success" => 1,
        "msg" => "Data Updated Successfully"
    ];

    echo json_encode($data);
} else if($_REQUEST['action_type'] == "delete_assigned_users"){
    $val = $_POST['val'];
    $col = $_POST['col'];
    $load_id = $_POST['load_id'];
    $v = implode(", ", $val);

    $querydel = "DELETE FROM load_changes_assigned where load_id = $load_id AND user_id NOT IN($v)";
    $mysqli->query($querydel) or die($mysqli->error);
}



else if ($_REQUEST['action_type'] == "notes_update") {
    $id = $_POST['load_id'];
    $newchecknotes = $mysqli->real_escape_string($_POST['val']);


    $mysqli->query("INSERT INTO newcheckcalls (newloadID, newchecknotes) VALUES('$id', '$newchecknotes')") or die($mysqli->error);

    $data = [
        "success" => 1,
        "msg" => "Data Updated Successfully"
    ];

    echo json_encode($data);
} else if ($_REQUEST['action_type'] == "fetch_updates") {
    $load_id = $_POST['load_id'];
    $col = $_POST['col'];

    $q = "SELECT * from live_update_changes l inner join users u on l.user_id = u.id where load_id = $load_id and column_name LIKE '$col%'";
    $d = $mysqli->query($q) or die($mysql->error);
    $html = '';


    foreach ($d as $dd) {
        $time = new DateTime($dd['timestamp']);
        $et = new DateTimeZone('America/New_York');
        $time->setTimezone($et);
        $time = $time->format('m-d-y h:i A');

        $html .= 'LUB: <span style="color: var(--light-font)">' . $dd['user_name'] . ' on ' . $time . '</span><br>';
    }

    $data = [
        "success" => 1,
        "data" => $html
    ];

    echo json_encode($data);
}
