<?php

    session_start();

    include './config.php';
    $user = $_SESSION['myid'];

    if(isset($_POST['addlabel'])){
        $label = $_POST['label'];
        $bg = $_POST['bg'];
        $tc = $_POST['tc'];

        $query = "INSERT INTO labels (label_name, background_color, text_color, added_by) VALUES ('$label', '$bg', '$tc', '$user')";
        $mysqli->query($query) or die($mysqli->error);

        $data = [
            "success" => 1,
            "msg" => "Label Added Successfully"
        ];
        json_encode($data);
    }