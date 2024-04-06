<?php
session_start();
// include "header.php";
include './config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $sql = "SELECT * FROM calendar";
    $result = $mysqli->query($sql);

    // Initialize an array to hold calendar events
    $events = array();

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Extract category values from all objects
        $categories = array_column($events, 'events');

        // Get unique category values
        $uniqueCategories = array_unique($categories);

        $eventsByCategory = array();

        // Loop through the array of events
        foreach ($result as $event) {
            // Get the category of the current event
            $category = $event['events'];

            // Check if the category already exists in $eventsByCategory
            if (!isset($eventsByCategory[$category])) {
                // If the category doesn't exist, create an empty array for it
                $eventsByCategory[$category] = array();
            }

            // Add the current event to the array for its category
            $eventsByCategory[$category][] = $event;
        }

        $azCalendarEvents = array();

        // Initialize a counter for category IDs
        $categoryID = 1;

        $backgroundColors = ["#bff2f2", "#e0e4f4", "#ffd5cc", "#d2e0ff", "#bfdeff", "#d5c2f3", "#d5c2f3", "#bff2f2", "#e0e4f4", "#ffd5cc", "#d2e0ff", "#bfdeff", "#d5c2f3", "#d5c2f3"];

        // Loop through each category and its events
        foreach ($eventsByCategory as $category => $result) {
            // Initialize an array to hold events for the current category
            $categoryEvents = array();

            // Loop through each event in the current category
            foreach ($result as $row) {
                // print($categoryID);

                // $backgroundColor = $backgroundColors[$colorIndex];

                // Add event data to the array
                $categoryEvents[] = array(
                    "id" => $row["id"],
                    "start" => $row["start_date"],
                    "end" => $row["end_date"],
                    "title" => $row["title"],
                    "description" => $row["description"],
                    "category" => $row["events"], // Change "Category" to "category" if needed
                    "backgroundColor" => $backgroundColors[$categoryID], // Example background color
                    "borderColor" => "#00cccc" // Example border color
                );
                // Increment the color index and reset it if it exceeds the array length
                // $colorIndex++;
                // if ($colorIndex >= count($backgroundColors)) {
                //     $colorIndex = 0;
                // }
            }

            // Add the category object to the final data structure
            $azCalendarEvents[] = array(
                'id' => $categoryID,
                'title' => $category, // Category name also added as title
                'events' => $categoryEvents,
                "backgroundColor" => $backgroundColors[$categoryID], // Example background color
                "borderColor" => "#00cccc" // Example border color
            );

            // Increment the category ID counter
            $categoryID++;
        }

        // print_r($azCalendarEvents);
        // Fetch data row by row
        // while ($row = $result->fetch_assoc()) {
        //     // Format the fetched data into the desired structure
        //     $event = array(
        //         "id" => $row["id"],
        //         "start" => $row["start_date"],
        //         "end" => $row["end_date"],
        //         "title" => $row["title"],
        //         "description" => $row["description"],
        //         "category" => $row["events"], // Change "Category" to "category" if needed
        //         "backgroundColor" => "#bff2f2", // Example background color
        //         "borderColor" => "#00cccc" // Example border color
        //     );
        //     // Push the formatted event to the events array
        //     $events[] = $event;
        // }
    }

    // Close the database connection
    $mysqli->close();

    // Encode the events array as JSON
    $calendar_events = json_encode($azCalendarEvents);

    // Check if JSON encoding was successful
    if ($calendar_events === false) {
        // Handle JSON encoding error
        die(json_encode(array('error' => 'JSON encoding error')));
    }

    // print_r($calendar_events);
    // Set the correct content type header
    // header('Content-Type: application/json');


    // Echo the JSON data
    echo $calendar_events;
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form has been submitted
    // Assign values from $_POST
    $title = $_POST['title'];
    $start_date = date('Y-m-d', strtotime($_POST['start_date'])); // Format start date
    $start_time = $_POST['start_time']; // Retrieve start time
    $end_date = date('Y-m-d', strtotime($_POST['end_date'])); // Format end date
    $end_time = $_POST['end_time']; // Retrieve end time
    $description = $_POST['description'];
    $event_name = $_POST['event_name'];

    // Concatenate start_date and start_time
    $start_datetime = date('Y-m-d H:i:s', strtotime("$start_date $start_time"));
    // Concatenate end_date and end_time
    $end_datetime = date('Y-m-d H:i:s', strtotime("$end_date $end_time"));

    // Prepare the SQL query
    $query = "INSERT INTO calendar (title, start_date, end_date, description, events) 
              VALUES ('$title', '$start_datetime', '$end_datetime', '$description', '$event_name')";

    // Execute the query
    $mysqli->query($query) or die($mysqli->error);

    // Unset the variables
    unset($title);
    unset($start_date);
    unset($start_time);
    unset($end_date);
    unset($end_time);
    unset($description);
    unset($event_name);
} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Sanitize the received ID to prevent SQL injection
    // print_r($_GET['id']);
    $id = $_GET['id'];


    // Prepare the SQL query to delete the record
    $query = "DELETE FROM calendar WHERE id = '$id'";

    // Execute the query
    $result = $mysqli->query($query);

    // Check if the deletion was successful
    if ($result) {
        // Deletion successful
        echo json_encode(array("status" => "success", "message" => "Record deleted successfully"));
    } else {
        // Deletion failed
        echo json_encode(array("status" => "error", "message" => "Failed to delete record"));
    }
} else {
    // ID not received
    echo json_encode(array("status" => "error", "message" => "ID not received"));
}
