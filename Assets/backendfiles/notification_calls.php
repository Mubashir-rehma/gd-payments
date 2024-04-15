<?php
session_start();
include './config.php';
include './notification.php';

if (!empty($_REQUEST['notification_type'])) {
    $notification_type = $_GET['notification_type'];
    get_notifications($mysqli, $notification_type);

} else if (!empty($_REQUEST['notification_read'])) {
    $id = $_GET['notification_read'];
    // not_read($mysqli, $id);

    $query = "UPDATE notifications SET read_status='1' WHERE not_id = '$id'";

    $mysqli->query($query) or die($mysqli->error);

} else if(!empty($_REQUEST['notification_alert'])){
    notification_alert($mysqli);
    tracking2_hour_reminder($mysqli);
};


?>