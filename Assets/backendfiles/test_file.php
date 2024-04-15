<?php
session_start();

include './config.php';
include './notification.php';
$uploadDir = "../uploads/vendor_attachments/";
$creater = $_SESSION['myid'];
$document_root = $_SERVER['DOCUMENT_ROOT'];

// if($_REQUEST['action_type'] == "new_vendor"){
    $vendor_name = $mysqli->real_escape_string($_POST['vendor_name']);
    $ven_company = $mysqli->real_escape_string($_POST['ven_company']);
    $ven_tel_num = $_POST['ven_tel_num'];
    $ven_email = $_POST['ven_email'];
    $ven_Address = $mysqli->real_escape_string($_POST['ven_Address']);
    $ven_items = $mysqli->real_escape_string(serialize($_POST['ven_items']));
    $ven_status = $_POST['ven_status'];
    $ven_notesprivate = $mysqli->real_escape_string($_POST['ven_notesprivate']);
    $added_by  = $creater;



    $vendor_id = "";
    if(!empty($_POST['vendor_id'])){
        $vendor_id = $_POST['vendor_id'];
    }
    

    $msg = "";
    $success = 0;
    $newid = "";
    $html_content = "";

    if(!empty($vendor_id)){
        $query = "UPDATE vendors SET vendor_name ='$vendor_name', ven_company ='$ven_company', ven_tel_num ='$ven_tel_num', ven_email ='$ven_email', ven_Address ='$ven_Address', ven_items ='$ven_items', ven_status ='$ven_status', ven_notesprivate ='$ven_notesprivate', added_by ='$added_by' WHERE vendor_id='$vendor_id')";

        $mysqli->query($query) or die($mysqli->error);
        $newid = $vendor_id;

        $msg = "Vendor has been updated Successfully!";
        $success = 1;
    } else {
        $query = "INSERT INTO vendors (vendor_name, ven_company, ven_tel_num, ven_email, ven_Address, ven_items, ven_status, ven_notesprivate, added_by) VALUES ('$vendor_name','$ven_company','$ven_tel_num','$ven_email','$ven_Address','$ven_items','$ven_status','$ven_notesprivate','$added_by')";

        $mysqli->query($query) or die ($mysqli->error);
        $newid = $mysqli->insert_id;

        $msg = "Vendor has been Added Successfully!";
        $success = 1;
    }

    
    // if(!empty($newid)){
        print_r($_POST);
        // Add Files
        if (!empty($_POST['ven_attachments'])) {

            $ven_attachments = $_POST['ven_attachments'];
            print_r($ven_attachments);

            foreach ($ven_attachments as $key => $value) {
                $ven_attachments = json_decode($value, true);
                // File upload path 
                $fileName = $newid . '_' . basename($ven_attachments['name']);
                $targetFilePath = $uploadDir . $fileName;


                // Check whether file type is valid 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                // if (in_array($fileType, $allowTypes)) {
                // Upload file to server 
                if (move_uploaded_file($_FILES["ven_attachments"]["tmp_name"][$key], $targetFilePath)) {
                    // File db insert 

                    $query = "INSERT INTO vendor_attachments (ven_file_name, ven_id) VALUES ('$fileName','$newid')";
                    $mysqli->query($query) or die($mysqli->error);

                } else {
                    // $errorUpload .= $codFiles . ' | ';
                }
            }
        }

        // $query = "SELECT * from vendors";
        // $vendors = $mysqli->query($query) or die($mysqli->error);
        // while ($vendors_detail = $vendors->fetch_assoc()) :
        //     $html_content .= '<option value="' . $vendors_detail['vendor_id'] . ' " data-foo=" ' . $vendors_detail['vendor_name'] . ' "> ' . $vendors_detail['ven_company'] . ' </option>';
        // endwhile; 


    // } else {
    //     $msg = "Someting went Wrong!";
    //     $success = 0;
    // }

    $data[] = [
        'msg' => $msg,
        'success' => $success,
        // 'newid' => $newid,
        // 'html_content' => $html_content
    ];

    echo json_encode($data);


// }