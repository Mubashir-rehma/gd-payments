<?php
include 'config.php';

$action_type = $_GET['action_type']; // Get the value of action_type from the URL
$id = $_GET['id']; 

echo $id;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gdbank = $_POST['gdbank'];
    $total_amount = $_POST['total_amount'];
    $gdno = $_POST['gdno'];
    $amount_paid = $_POST['amount_paid'];
    $status = $_POST['status'];

    // Prepare the SQL query
    $query = "INSERT INTO gd_pay (Gd_bankDate, GD_number, TotalAmount, PaidAmount, status) 
              VALUES ('$gdbank', '$gdno', '$total_amount', '$amount_paid', '$status')";

    // Execute the query
    $result = $mysqli->query($query);

    // Check if the query was successful
    if ($result) {
        // Redirect to gd_payments.php
        header("Location: ../../gd_payments.php");
        exit(); // Ensure no further code is executed
    } else {
        // Handle error if insertion fails
        die("Error: " . $mysqli->error);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Fetch data from the database
$query = "SELECT * FROM gd_pay";
$result = $mysqli->query($query);

// Check if there are any rows returned
if ($result->num_rows > 0) {
    $data = array();

    // Fetch each row and add it to the $data array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Return the data as JSON
    echo json_encode($data);
} else {
    // If no rows found, return an empty array
    echo json_encode(array());
}

}
$query = [];
$lf = "";
// Loads en route
if(isset($_REQUEST['opening'])){
    $queryq .= " where n.Status= 'opening' ORDER BY id DESC";
    $query = $mysqli->query($queryq) or die($mysqli->error);
} else if (isset($_REQUEST['posted'])){
    // Delivered Data
    $queryq .= " where n.Status= 'posted' ORDER BY id DESC";
    $query = $mysqli->query($queryq) or die($mysqli->error);

} else if (isset($_REQUEST['bs_matched'])){
    // Loads Issue
    $queryq .= " where n.Status= 'bs_matched' ORDER BY id DESC";
    $query = $mysqli->query($queryq) or die($mysqli->error);
    

}else if  ($_REQUEST["action_type"] == 'edit_gd' ) {
    $query = "SELECT * FROM gd_pay";
    $result = $mysqli->query($query);
    
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        $data = array();
    
        // Fetch each row and add it to the $data array
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    
        // Return the data as JSON
        echo json_encode($data);
    } else {
        // If no rows found, return an empty array
        echo json_encode(array());
    }
    
}
?>