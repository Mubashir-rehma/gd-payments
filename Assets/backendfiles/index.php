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


if (($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
    
    // Include your database connection here

    $id = $_GET['id'];

    // Prepare and execute the SQL query to delete the record
    $sql = "DELETE FROM gd_pay WHERE id = $id";

    if ($mysqli->query($sql) === TRUE) {
        // Check if any rows were affected
        if ($mysqli->affected_rows > 0) {
            // Deletion successful
            $response = array(
                'success' => true,
                'msg' => "Record with ID $id deleted successfully."
            );
        } else {
            // No rows affected, meaning no matching record found
            $response = array(
                'success' => false,
                'msg' => "No record found with ID $id."
            );
        }
    } else {
        // Error in executing the query
        $response = array(
            'success' => false,
            'msg' => "Error deleting record: " . $mysqli->error
        );
    }

    // Close the database connection
    $mysqli->close();

    // Send the response as JSON
    echo json_encode(array($response));
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

    $gd = $_GET['storegdp'];
    // $gd = $_GET['gd'];
    $status = $_POST['gdpstatus'];
    $total_amount = $_POST['amount_paid'];
    $date = $_POST['gddate'];

    // Insert the record into gdpays
    $insertQuery = "INSERT INTO gdpays (total_paid, status, date, gid) VALUES ('$total_amount', '$status', '$date', '$gd')";
    $insertResult = $mysqli->query($insertQuery);

    if ($gd) {
        $updateQuery = "UPDATE gdpays 
        SET total_paid = '$total_amount', 
            status = '$status', 
            date = '$date' 
        WHERE id = $gd";

        // Execute the query
        $result = $mysqli->query($updateQuery);
        
    } else {
        $insertQuery = "INSERT INTO gdpays (total_paid, status, date, gid) VALUES ('$total_amount', '$status', '$date', '$gd')";
        $insertResult = $mysqli->query($insertQuery);
    }


    // if ($insertResult) {
    //     // Insertion successful
    //     echo "Record inserted into gdpays successfully.";
    // } else {
    //     // Insertion failed
    //     echo "Error: " . $mysqli->error;
    // }
}
