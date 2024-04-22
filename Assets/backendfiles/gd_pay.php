<?php
include 'config.php';

// $action_type = $_GET['action_type']; // Get the value of action_type from the URL
// $id = $_GET['id'];

// echo $id;

if ($_SERVER["REQUEST_METHOD"] == "POST") if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gd = $_GET['uid'];

    $gdbank = $_POST['gdbank'];
    $total_amount = $_POST['total_amount'];
    $gdno = $_POST['gdno'];
    $amount_paid = $_POST['amount_paid'];
    $status = $_POST['status'];

    if ($gd) {
        $updateQuery = "UPDATE gd_pay 
        SET 
            Gd_bankDate = '$gdbank',
            GD_number = '$gdno', 
            TotalAmount = '$total_amount', 
            PaidAmount = '$amount_paid', 
            Status = '$status'
        WHERE 
            id = '$gd'
        ";

        // Execute the query
        $result = $mysqli->query($updateQuery);
    } else {
        // Prepare the SQL query
        $query = "INSERT INTO gd_pay (Gd_bankDate, GD_number, TotalAmount, PaidAmount, Status) 
 VALUES ('$gdbank', '$gdno', '$total_amount', '$amount_paid', '$status')";

        // Execute the query
        $result = $mysqli->query($query);
    }


    // Check if the query was successful
    if ($result) {
        // Redirect to gd_payments.php
        header("Location: ../../gd_payments.php");
        exit(); // Ensure no further code is executed
    } else {
        // Handle error if insertion fails
        die("Error: " . $mysqli->error);
    }
} elseif (isset($_REQUEST['REQUEST_METHOD']) && $_SERVER["REQUEST_METHOD"] == "GET") {
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
} elseif (isset($_REQUEST['action_type']) && ($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
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
}

// $query = [];
// $lf = "";
// Loads en route
if (isset($_REQUEST['opening'])) {
    $queryq .= " where n.Status= 'opening' ORDER BY id DESC";
    $query = $mysqli->query($queryq) or die($mysqli->error);
} else if (isset($_REQUEST['posted'])) {
    // Delivered Data
    $queryq .= " where n.Status= 'posted' ORDER BY id DESC";
    $query = $mysqli->query($queryq) or die($mysqli->error);
} else if (isset($_REQUEST['bs_matched'])) {
    // Loads Issue
    $queryq .= " where n.Status= 'bs_matched' ORDER BY id DESC";
    $query = $mysqli->query($queryq) or die($mysqli->error);
} else if (isset($_REQUEST['action_type']) && $_REQUEST["action_type"] == 'edit_gd') {
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
} else if (isset($_REQUEST['record']) && $_REQUEST["record"]) {
    $gd = $_GET['gd'];
    // echo $gd;
    $query = "SELECT * FROM gd_pay where GD_number = '$gd' limit 1";
    $result = $mysqli->query($query)->fetch_assoc();

    // print_r($result);

    echo json_encode($result);
} else if (isset($_REQUEST['gdrecord']) && $_REQUEST["gdrecord"]) {
    $gd = $_GET['gd'];
    // echo $gd;
    $query = "SELECT * FROM gd_pay where id = '$gd' limit 1";
    $result = $mysqli->query($query)->fetch_assoc();

    // print_r($result);

    echo json_encode($result);
} else if (isset($_REQUEST['gdpayrecord']) && $_REQUEST["gdpayrecord"]) {
    $gd = $_GET['gd'];
    // echo $gd;
    $query = "SELECT gdpays.id AS gdpays_id, gdpays.*, gd_pay.* 
    FROM gdpays 
    JOIN gd_pay ON gdpays.gid = gd_pay.id  where gdpays.id = '$gd' limit 1";
    $result = $mysqli->query($query)->fetch_assoc();

    // print_r($result);

    echo json_encode($result);
} elseif (isset($_REQUEST['gdpayupdate']) && $_REQUEST["gdpayupdate"]) {
    $gd = $_GET['gd'];
    $status = $_POST['gdpstatus']; // Make sure these fields are sent in your AJAX request

    $total_amount = $_POST['amount_paid']; // Make sure these fields are sent in your AJAX request
    $date = $_POST['gddate']; // Make sure these fields are sent in your AJAX request

    $updateQuery = "UPDATE gdpays 
                SET total_paid = '$total_amount', 
                    status = '$status', 
                    date = '$date' 
                WHERE id = $id";

    // Execute the query
    $result = $mysqli->query($updateQuery);
} elseif (isset($_GET['action_type']) && !empty($_GET['action_type'])) {
    $gd = $_GET['id'];
    $gdbank = $_POST['gdbank'];
    $total_amount = $_POST['total_amount'];
    $gdno = $_POST['gdno'];
    $amount_paid = $_POST['amount_paid'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE gd_pay 
    SET 
        Gd_bankDate = '$gdbank', 
        TotalAmount = '$total_amount', 
        PaidAmount = '$amount_paid', 
        status = '$status'
    WHERE 
        id = '$gd'
    ";

    // Execute the query
    $result = $mysqli->query($updateQuery);
}
