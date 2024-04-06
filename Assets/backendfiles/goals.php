<?php

include './config.php';

if (isset($_POST['newgoal'])) {
    $user = $_POST['user'];
    $timeline = $_POST['timeline'];
    $profit_goal = $_POST['profit_goal'];

    $mysqli->query("INSERT INTO goals(user, timeline, goal) VALUES ('$user','$timeline','$profit_goal')");

    echo "Goal Successfully Added";
    header("location: ../../goals.php");
} elseif (($_REQUEST['action_type'] == "newgoal")) {
    $user = $_POST['user'];
    $timeline = $_POST['timeline'];
    $profit_goal = $_POST['profit_goal'];

    $mysqli->query("INSERT INTO goals(user, timeline, goal) VALUES ('$user','$timeline','$profit_goal')");

    echo "success : Goal Successfully Added for the user";
} elseif (($_REQUEST['action_type'] == "editgoal")) {
    $id = $_GET['editid'];
    $user = $_POST['user'];
    $timeline = $_POST['timeline'];
    $profit_goal = $_POST['profit_goal'];
    $last_modified = date_default_timezone_get();

    $mysqli->query("UPDATE goals SET id='$id',user='$user',timeline='$timeline',goal='$profit_goal',last_modified='$last_modified' WHERE id='$id'");

    echo "success : Goal Successfully Updated for the user";
} elseif (($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $mysqli->query("DELETE FROM goals where id = '$id'");

    header("location: ../../goals.php");
}



?>