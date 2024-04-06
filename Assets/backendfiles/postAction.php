<?php
// Start session 
session_start();
require __DIR__ . './vendor/autoload.php';

// Include and initialize DB class 
require_once './DB.class.php';
$db = new DB();

include './config.php';
include './notification.php';

use PHPMailer\PHPMailer\PHPMailer;

require_once './phpmailer/Exception.php';
require_once './phpmailer/PHPMailer.php';
require_once './phpmailer/SMTP.php';


// $client = new \Google_Client();
// $client->setApplicationName('Google Sheets and PHP');
// $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
// $client->setAccessType('offline');
// $client->setAuthConfig(__DIR__ . './googlesheetapi.json');
// $service = new \Google\Service\Sheets($client);

// $spreadsheetId = "1VvfO4vYXeHHKUDhDXhcGfBFEP31MbW4GskgnyTYzE4g"; //It is present in your URL

// $range = "Sheet1";
// $values = [["This", "is", "a", "test"],];
// $body = new Google\Service\Sheets\ValueRange([
//     'values' => $values
// ]);
// $params = [
//     'valueInputOption' => 'RAW'
// ];
// $insert = [
//     "insertDataOption" => "INSERT_ROWS"
// ];
// $result = $service->spreadsheets_values->append(
//     $spreadsheetId,
//     $range,
//     $body,
//     $params
// );

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->CharSet    = 'UTF-8';
$mail->SMTPDebug  = 0;
$mail->SMTPSecure = "tls";
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'domainbird03@gmail.com'; // Gmail address which you want to use as SMTP server
$mail->Password = 'domainbird@123'; // Gmail address Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = '587';

$mail->setFrom('domainbird03@gmail.com'); // Gmail address which you used as SMTP server
$mail->addAddress('accounting@gtmmtransportation.com'); // Email address where you want to receive emails (you can use any of your gmail address including the gmail address which you used as SMTP server)

$mail->isHTML(true);

// File upload path 
$uploadDir = "../uploads/cod_Files/";

// Allow file formats 
$allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc');

// Set default redirect url 
$redirectURL = '../../index.php';

$statusMsg = $errorMsg = '';
$sessData = array();
$statusType = 'danger';


if (isset($_POST['submit'])) {
    
    // Set redirect url 
    $redirectURL = '../../index.php';
    $to = "accounting@gtmmtransportation.com"; // this is your Email address
    $from = "accounting@gtmmtransportation.com"; // this is the sender's Email address

    // Get submitted data 
    $broker = $_POST['broker'];
    $pick_up_Location = serialize($_POST['pick_up_Location']);
    $start_lat = serialize($_POST['start_lat']);
    $start_lng = serialize($_POST['start_lng']);
    $destination = serialize($_POST['destination']);
    $end_lat = serialize($_POST['end_lat']);
    $end_lng = serialize($_POST['end_lng']);
    $truck_number = $_POST['truck_number'];
    $ref_num = $_POST['ref_num'];
    $customer_rate = $_POST['customer_rate'];
    $carier_rate = $_POST['carier_rate'];
    $truck_type = $_POST['truck_type'];
    $comodity = $_POST['comodity'];
    $plattes = $_POST['plattes'];
    $weight = $_POST['weight'];
    $loadtype = $_POST['loadtype'];
    $dispatcher = $_POST['dispatcher'];
    $pickupdate = $_POST['pickupdate'];
    $dropdate = $_POST['dropdate'];
    $notesprivate = $_POST['notesprivate'];
    $distance = serialize($_POST['distance']);
    $time = serialize($_POST['duration']);
    $id = $_POST['id'];
    $brokeragent = $_POST['brokeragent'];
    // $lat = $_POST['lat'];
    // // $lng = $_POST['lng'];

    // Submitted user data 
    $newload = array(
        'Broker' => $broker,
        'Pick_up_Location' => $pick_up_Location,
        'start_lat' => $start_lat,
        'start_lng' => $start_lng,
        'Destination' => $destination,
        'end_lat' => $end_lat,
        'end_lng' => $end_lng,
        'truck_Number' => $truck_number,
        'Ref_No' => $ref_num,
        'Customer_Rate' => $customer_rate,
        'Carier_Driver_Rate' => $carier_rate,
        'Truck_type' => $truck_type,
        'Comodity' => $comodity,
        'Pallets' => $plattes,
        'Weight' => $weight,
        'load_Type' => $loadtype,
        'dispatcher' => $dispatcher,
        'pickupdate' => $pickupdate,
        'dropdate' => $dropdate,
        'Notes_Private' => $notesprivate,
        'distance' => $distance,
        'time' => $time,
        'brokeragent' => $brokeragent,
        // 'lat' => $lat,
        // 'lng' => $lng,
    );

    // Store submitted data into session 
    $sessData['postData'] = $newload;
    $sessData['postData']['id'] = $id;

    // ID query string 
    $idStr = !empty($id) ? '?id=' . $id : '';

    if (empty($ref_num)) {
        $error = '<br/>Ref. Number Must be entered.';
    }

    if (!empty($error)) {
        $statusMsg = 'Please fill all the mandatory fields.' . $error;
    } else {
        if (!empty($id)) {
            // Update data 
            $condition = array('id' => $id);
            $update = $db->update($newload, $condition);

            // $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', city='$destination', Status='not_Available'  where truckNumber='$truck_number'") or die($mysqli->error);

            $newloadID = $id;
        } else {
            // Insert data 
            $insert = $db->insert($newload);
            // $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', city='$destination', Status='not_Available' where truckNumber='$truck_number'") or die($mysqli->error);

            // $mail->Subject = "New Load Submission: ". $ref_num. "";
            // $mail->Body = "<strong>Ref No. : </strong>" .$ref_num . 
            //     "<br>" . "<strong>Broker : </strong>". $broker. 
            //     "<br>". "<strong>Pick Up Location : </strong>". $pick_up_Location .
            //     "<br>" . "<strong>Pick Up Date : </strong>" . $pickupdate .
            //     "<br>" . "<strong>Destination : </strong>" . $destination .
            //     "<br>" . "<strong>Drop Date : </strong>" . $dropdate .
            //     "<br>" . "<strong>Truck No. : </strong>" . $truck_number .
            //     "<br>" . "<strong>Customer Rate : </strong>" . $customer_rate .
            //     "<br>" . "<strong>Carrier/Driver Rate : </strong>" . $carier_rate .
            //     "<br>" . "<strong>Truck Type : </strong>" . $truck_type .
            //     "<br>" . "<strong>Comodity : </strong>" . $comodity .
            //     "<br>" . "<strong>Plattes : </strong>" . $plattes .
            //     "<br>" . "<strong>Weight : </strong>" . $weight .
            //     "<br>" . "<strong>Load Type : </strong>" . $loadtype .
            //     "<br>" . "<strong>Dispatcher : </strong>" . $dispatcher .
            //     "<br>" . "<strong>Notes : </strong>" . $notesprivate .
            //     "<br>" . "<strong>Broker Agent : </strong>" . $brokeragent . 
            //     "<br><br> Please Invoice the broker on OTR as well make sure to add the reocord in the google sheet. <br><br> Thank you.";

            // // $headers = "From:" . $from;
            // // mail($to, $subject, $message, $headers);
            
            // $mail->send();

            $newloadID = $insert;
        }

        $codFiles = array_filter($_FILES['rate_con_files']['name']);
        $bolFiles = array_filter($_FILES['bol_files']['name']);
        $podFiles = array_filter($_FILES['pod_files']['name']);
        $pickup_files = array_filter($_FILES['pickup_docs']['name']);

        if (!empty($newloadID)) {

            // Add POD Files
            if (!empty($podFiles)) {
                foreach ($podFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($podFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["pod_files"]["tmp_name"][$key], $targetFilePath)) {
                            // File db insert 
                            $FileData = array(
                                'pod_newload_id' => $newloadID,
                                'fileName' => $fileName
                            );
                            $insert = $db->insetpodFile($FileData);
                        } else {
                            $errorUpload .= $codFiles[$key] . ' | ';
                        }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }

                $errorUpload = !empty($errorUpload) ? 'Upload Error in POD Files: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
            }

            // Add Cod Files
            if (!empty($codFiles)) {
                foreach ($codFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($codFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["rate_con_files"]["tmp_name"][$key], $targetFilePath)) {
                            // Image db insert 
                            $FileData = array(
                                'newload_id' => $newloadID,
                                'fileName' => $fileName
                            );
                            $insert = $db->insetrateconFile($FileData);
                        } else {
                            $errorUpload .= $codFiles[$key] . ' | ';
                        }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }

                $errorUpload = !empty($errorUpload) ? 'Upload Error in Rate CON: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
            }

            // Add Bol Files
            if (!empty($bolFiles)) {
                foreach ($bolFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($bolFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["bol_files"]["tmp_name"][$key], $targetFilePath)) {
                            // Image db insert 
                            $FileData = array(
                                'bol_newload_id' => $newloadID,
                                'fileName' => $fileName
                            );
                            $insert = $db->insetbolFile($FileData);
                        } else {
                            $errorUpload .= $codFiles[$key] . ' | ';
                        }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }

                $errorUpload = !empty($errorUpload) ? 'Upload Error in Bol Files: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
            }

            // Add Pickup Files
            if (!empty($pickup_files)) {
                foreach ($pickup_files as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($pickup_files[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["pickup_docs"]["tmp_name"][$key], $targetFilePath)) {
                        // Image db insert 
                        $FileData = array(
                            'pickup_newload_id' => $newloadID,
                            'file_name' => $fileName
                        );
                        $insert = $db->insetpickupfile($FileData);
                    } else {
                        $errorUpload .= $pickup_files[$key] . ' | ';
                    }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }
            }



            $statusType = 'success';
            $statusMsg = 'rate_con_files has been uploaded successfully.' . $errorMsg;
            $sessData['postData'] = '';

            $redirectURL = '../../index.php';
        } else {
            $statusMsg = 'Some problem occurred, please try again.';
            // Set redirect url 
            $redirectURL .= $idStr;
        }
    }

    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;

} elseif (($_REQUEST['action_type'] == 'addnewload')) {

    // Get submitted data 
    $broker = $_POST['broker'];
    $pick_up_Location = serialize($_POST['pick_up_Location']);
    $start_lat = serialize($_POST['start_lat']);
    $start_lng = serialize($_POST['start_lng']);
    $destination = serialize($_POST['destination']);
    $end_lat = serialize($_POST['end_lat']);
    $end_lng = serialize($_POST['end_lng']);
    $truck_number = $_POST['truck_number'];
    $ref_num = $_POST['ref_num'];
    $customer_rate = $_POST['customer_rate'];
    $carier_rate = $_POST['carier_rate'];
    $truck_type = $_POST['truck_type'];
    $comodity = $_POST['comodity'];
    $plattes = $_POST['plattes'];
    $weight = $_POST['weight'];
    $loadtype = $_POST['loadtype'];
    $dispatcher = $_POST['dispatcher'];
    $pickupdate = $_POST['pickupdate'];
    $dropdate = $_POST['dropdate'];
    $notesprivate = $_POST['notesprivate'];
    $distance = serialize($_POST['distance']);
    $time = serialize($_POST['duration']);
    $id = $_POST['id'];
    $brokeragent = $_POST['brokeragent'];
    // $lat = $_POST['lat'];
    // // $lng = $_POST['lng'];

    // Submitted user data 
    $newload = array(
        'Broker' => $broker,
        'Pick_up_Location' => $pick_up_Location,
        'start_lat' => $start_lat,
        'start_lng' => $start_lng,
        'Destination' => $destination,
        'end_lat' => $end_lat,
        'end_lng' => $end_lng,
        'truck_Number' => $truck_number,
        'Ref_No' => $ref_num,
        'Customer_Rate' => $customer_rate,
        'Carier_Driver_Rate' => $carier_rate,
        'Truck_type' => $truck_type,
        'Comodity' => $comodity,
        'Pallets' => $plattes,
        'Weight' => $weight,
        'load_Type' => $loadtype,
        'dispatcher' => $dispatcher,
        'pickupdate' => $pickupdate,
        'dropdate' => $dropdate,
        'Notes_Private' => $notesprivate,
        'distance' => $distance,
        'time' => $time,
        'brokeragent' => $brokeragent,
        // 'lat' => $lat,
        // 'lng' => $lng,
    );

    // Store submitted data into session 
    $sessData['postData'] = $newload;
    $sessData['postData']['id'] = $id;

    // ID query string 
    $idStr = !empty($id) ? '?id=' . $id : '';

    if (empty($ref_num)) {
        $error = '<br/>Ref. Number Must be entered.';
    }

    if (!empty($error)) {
        $statusMsg = 'Please fill all the mandatory fields.' . $error;
    } else {
        if (!empty($id)) {
            // Update data 
            $condition = array('id' => $id);
            $update = $db->update($newload, $condition);

            // $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', city='$destination', Status='not_Available'  where truckNumber='$truck_number'") or die($mysqli->error);

            $newloadID = $id;
        } else {
            // Insert data 
            $insert = $db->insert($newload);
            // $mysqli->query("UPDATE truck_details SET arrival_date='$dropdate', city='$destination', Status='not_Available' where truckNumber='$truck_number'") or die($mysqli->error);

            // $mail->Subject = "New Load Submission: ". $ref_num. "";
            // $mail->Body = "<strong>Ref No. : </strong>" .$ref_num . 
            //     "<br>" . "<strong>Broker : </strong>". $broker. 
            //     "<br>". "<strong>Pick Up Location : </strong>". $pick_up_Location .
            //     "<br>" . "<strong>Pick Up Date : </strong>" . $pickupdate .
            //     "<br>" . "<strong>Destination : </strong>" . $destination .
            //     "<br>" . "<strong>Drop Date : </strong>" . $dropdate .
            //     "<br>" . "<strong>Truck No. : </strong>" . $truck_number .
            //     "<br>" . "<strong>Customer Rate : </strong>" . $customer_rate .
            //     "<br>" . "<strong>Carrier/Driver Rate : </strong>" . $carier_rate .
            //     "<br>" . "<strong>Truck Type : </strong>" . $truck_type .
            //     "<br>" . "<strong>Comodity : </strong>" . $comodity .
            //     "<br>" . "<strong>Plattes : </strong>" . $plattes .
            //     "<br>" . "<strong>Weight : </strong>" . $weight .
            //     "<br>" . "<strong>Load Type : </strong>" . $loadtype .
            //     "<br>" . "<strong>Dispatcher : </strong>" . $dispatcher .
            //     "<br>" . "<strong>Notes : </strong>" . $notesprivate .
            //     "<br>" . "<strong>Broker Agent : </strong>" . $brokeragent . 
            //     "<br><br> Please Invoice the broker on OTR as well make sure to add the reocord in the google sheet. <br><br> Thank you.";

            // // $headers = "From:" . $from;
            // // mail($to, $subject, $message, $headers);

            // $mail->send();

            $newloadID = $insert;
        }

        $codFiles = array_filter($_FILES['rate_con_files']['name']);
        $bolFiles = array_filter($_FILES['bol_files']['name']);
        $podFiles = array_filter($_FILES['pod_files']['name']);

        if (!empty($newloadID)) {

            // Add POD Files
            if (!empty($podFiles)) {
                foreach ($podFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($podFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["pod_files"]["tmp_name"][$key], $targetFilePath)) {
                            // File db insert 
                            $FileData = array(
                                'pod_newload_id' => $newloadID,
                                'fileName' => $fileName
                            );
                            $insert = $db->insetpodFile($FileData);
                        } else {
                            $errorUpload .= $codFiles[$key] . ' | ';
                        }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }

                $errorUpload = !empty($errorUpload) ? 'Upload Error in POD Files: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
            }

            // Add Cod Files
            if (!empty($codFiles)) {
                foreach ($codFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($codFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["rate_con_files"]["tmp_name"][$key], $targetFilePath)) {
                            // Image db insert 
                            $FileData = array(
                                'newload_id' => $newloadID,
                                'fileName' => $fileName
                            );
                            $insert = $db->insetrateconFile($FileData);
                        } else {
                            $errorUpload .= $codFiles[$key] . ' | ';
                        }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }

                $errorUpload = !empty($errorUpload) ? 'Upload Error in Rate CON: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
            }

            // Add Bol Files
            if (!empty($bolFiles)) {
                foreach ($bolFiles as $key => $val) {
                    // File upload path 
                    $fileName = $newloadID . '_' . basename($bolFiles[$key]);
                    $targetFilePath = $uploadDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["bol_files"]["tmp_name"][$key], $targetFilePath)) {
                            // Image db insert 
                            $FileData = array(
                                'bol_newload_id' => $newloadID,
                                'fileName' => $fileName
                            );
                            $insert = $db->insetbolFile($FileData);
                        } else {
                            $errorUpload .= $codFiles[$key] . ' | ';
                        }
                    // } else {
                    //     $errorUploadType .= $codFiles[$key] . ' | ';
                    // }
                }

                $errorUpload = !empty($errorUpload) ? 'Upload Error in Bol Files: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
            }



            $statusType = 'success';
            $statusMsg = 'rate_con_files has been uploaded successfully.' . $errorMsg;
            $sessData['postData'] = '';

            $redirectURL = '../../index.php';
        } else {
            $statusMsg = 'Some problem occurred, please try again.';
            // Set redirect url 
            $redirectURL .= $idStr;
        }
    }

    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'block') && !empty($_GET['id'])) {
    // Update data 
    $newload = array('status' => 0);
    $condition = array('id' => $_GET['id']);
    $update = $db->update($newload, $condition);
    if ($update) {
        $statusType = 'success';
        $statusMsg  = 'New Load data has been blocked successfully.';
    } else {
        $statusMsg  = 'Some problem occurred, please try again.';
    }

    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'unblock') && !empty($_GET['id'])) {
    // Update data 
    $newload = array('status' => 1);
    $condition = array('id' => $_GET['id']);
    $update = $db->update($newload, $condition);
    if ($update) {
        $statusType = 'success';
        $statusMsg  = 'New Load data has been activated successfully.';
    } else {
        $statusMsg  = 'Some problem occurred, please try again.';
    }

    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
    // Previous image files 
    $conditions['where'] = array(
        'id' => $_GET['id'],
    );
    $conditions['return_type'] = 'conFiles';
    $conditions['return_type'] = 'podFiles';
    $conditions['return_type'] = 'bolFiles';
    $prevData = $db->getRows($conditions);

    // Delete New Load data 
    $condition = array('id' => $_GET['id']);
    $delete = $db->delete($condition);

    add_notification($mysqli, "load", "The load has been Deleted", $_GET['id']);

    if ($delete) {
        // Delete rate_con_files data 
        $condition = array('newload_id' => $_GET['id']);
        $delete = $db->deleteConFile($condition);
        $delete = $db->deletebolFile($condition);
        $delete = $db->deletepodFile($condition);
        $delete = $db->deletepcikupfile($condition);

        // Remove CON files from server 
        if (!empty($prevData['rate_con_files'])) {
            foreach ($prevData['rate_con_files'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        // Remove POD files from server 
        if (!empty($prevData['pod_files'])) {
            foreach ($prevData['pod_files'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        // Remove BOL files from server 
        if (!empty($prevData['bol_files'])) {
            foreach ($prevData['bol_files'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        // Remove Pickup files from server 
        if (!empty($prevData['pickup_docs'])) {
            foreach ($prevData['pickup_docs'] as $file) {
                @unlink($uploadDir . $file['fileName']);
            }
        }

        $statusType = 'success';
        $statusMsg  = 'Record has been deleted successfully.';
    } else {
        $statusMsg  = 'Some problem occurred, please try again.';
    }

    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_POST['action_type'] == 'rateCon_delete') && !empty($_POST['id'])) {
    // Previous File data 
    $prevData = $db->getConFileRow($_POST['id']);

    // Delete Files data 
    $condition = array('id' => $_POST['id']);
    $delete = $db->deleteConFile($condition);
    if ($delete) {
        @unlink($uploadDir . $prevData['fileName']);
        $status = 'ok';
    } else {
        $status  = 'err';
    }
    echo $status;
    die;
} elseif (($_POST['action_type'] == 'podFile_delete') && !empty($_POST['id'])) {
    // Previous File data 
    $prevData = $db->getPodFile($_POST['id']);

    // Delete Files data 
    $condition = array('pod_id' => $_POST['id']);
    $delete = $db->deletepodFile($condition);
    if ($delete) {
        @unlink($uploadDir . $prevData['fileName']);
        $status = 'ok';
    } else {
        $status  = 'err';
    }
    echo $status;
    die;
} elseif (($_POST['action_type'] == 'bolFile_delete') && !empty($_POST['id'])) {
    // Previous File data 
    $prevData = $db->getIgetBolFilemgRow($_POST['id']);

    // Delete Files data 
    $condition = array('bol_id' => $_POST['id']);
    $delete = $db->deletebolFile($condition);
    if ($delete) {
        @unlink($uploadDir . $prevData['fileName']);
        $status = 'ok';
    } else {
        $status  = 'err';
    }
    echo $status;
    die;
} elseif (($_POST['action_type'] == 'pickupFile_delete') && !empty($_POST['id'])) {
    // Previous File data 
    $prevData = $db->getpickupfilerow($_POST['id']);

    // Delete Files data 
    $condition = array('pickup_file_id' => $_POST['id']);
    $delete = $db->deletepcikupfile($condition);
    if ($delete) {
        @unlink($uploadDir . $prevData['file_name']);
        $status = 'ok';
    } else {
        $status  = 'err';
    }
    echo $status;
    die;
} elseif (($_REQUEST['action_type'] == 'checkcalldelete') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("DELETE FROM newcheckcalls WHERE callid=$id") or die($mysqli->error);


    $statusType = 'success';
    $statusMsg  = 'Record has been deleted successfully.';


    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'load_en_route') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_en_route' WHERE id=$id") or die($mysqli->error);


    $statusType = 'success';
    $statusMsg  = 'Record has been deleted successfully.';


    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'load_delivered') && !empty($_GET['id'])) {

    $id = $_GET['id'];
    $date = new DateTime("now", new DateTimeZone('America/New_York'));
    $date = $date->format('Y-m-d H:i:s');

    add_notification($mysqli, "load", "The load has been Delivered", $_GET['id']);


    $mysqli->query("UPDATE newload SET status='load_delivered', delivery_date='$date' WHERE id=$id") or die($mysqli->error);


    $statusType = 'success';
    $statusMsg  = 'Record has been deleted successfully.';


    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'load_issue') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_issue' WHERE id=$id") or die($mysqli->error);


    $statusType = 'success';
    $statusMsg  = 'Record has been deleted successfully.';


    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'load_invoiced') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_invoiced' WHERE id=$id") or die($mysqli->error);


    $statusType = 'success';
    $statusMsg  = 'Record has been deleted successfully.';


    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'load_paid') && !empty($_GET['id'])) {

    $id = $_GET['id'];


    $mysqli->query("UPDATE newload SET status='load_paid' WHERE id=$id") or die($mysqli->error);


    $statusType = 'success';
    $statusMsg  = 'Record has been deleted successfully.';


    // Status message 
    $sessData['status']['type'] = $statusType;
    $sessData['status']['msg']  = $statusMsg;
} elseif (($_REQUEST['action_type'] == 'checkbrokername')) {
    $username = $_GET['broker_name'];
    $query = "select brokerName from logisticscrm.broker_details where brokerName ='$username' limit 1";
    $usercheck = $mysqli->query($query);

    if ($usercheck->num_rows > 0) {
        echo "Username Already exists. Please try some other username";

        die;
    } else {
        echo "success";
    }
}


if (isset($_POST['cancel'])) {
    header("location: ../../index.php");
}

if (isset($_POST['brokersubmit'])) {
    $broker_company = $_POST['brokercompany'];
    $brokerName = $_POST['brokerName'];
    $brokeremail = $_POST['brokeremail'];
    $brokerphone = $_POST['brokerphone'];
    $brokerAddress = $_POST['brokerAddress'];
    $brokercity = $_POST['brokercity'];
    $brokernotes = $_POST['brokernotes'];

    $mysqli->query("INSERT INTO broker_details (broker_company, brokerName, brokeremail, brokerphone, brokerAddress, brokercity, brokernotes) VALUES('$broker_company', '$brokerName', '$brokeremail','$brokerphone', '$brokerAddress', '$brokercity', '$brokernotes')") or die($mysqli->error);

    // $_SESSION['message'] = "Record has been saved!";
    // $_SESSION['msg_type'] = "success";

    $data = [];
    $data[] = [
        'success' => 1,
        'msg' => 'Broker Added Successfully!'
    ];

    echo json_encode($data);

    // header("location: ../../index.php");
}

if (isset($_POST['addressform'])) {
    $address = $_POST['address'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $country = $_POST['country'];
    $addressnotes = $_POST['addressnotes'];

    $mysqli->query("INSERT INTO address (address, street, city, state, zip, country, addressnotes) VALUES('$address', '$street','$city', '$state', '$zip', '$country', '$addressnotes')") or die($mysqli->error);

    $_SESSION['message'] = "Record has been saved!";
    $_SESSION['msg_type'] = "success";

    header("location: ../../index.php");
}

if (isset($_POST['truckNo'])) {
    $truckNumber = $_POST['truckNumber'];
    $engineNumber = $_POST['engineNumber'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $truckOwner = $_POST['truckOwner'];
    $registeredIn = $_POST['registeredIn'];
    $truckDriver = $_POST['truckDriver'];
    $cpPhone = $_POST['cpPhone'];
    $cpemail = $_POST['cpemail'];
    $cpAddress = $_POST['cpAddress'];
    $truckNumbernotes = $_POST['truckNumbernotes'];

    $mysqli->query("INSERT INTO truck_details (truckNumber, engineNumber, make, model, year, truckOwner, registeredIn, truckDriver, cpPhone, cpemail, cpAddress, truckNumbernotes) VALUES('$truckNumber', '$engineNumber', '$make', '$model', '$year', '$truckOwner', '$registeredIn', '$truckDriver', '$cpPhone', '$cpemail', '$cpAddress', '$truckNumbernotes')") or die($mysqli->error);

    $_SESSION['message'] = "Record has been saved!";
    $_SESSION['msg_type'] = "success";

    header("location: ../../index.php");
}

if (isset($_POST['newcallsubmit'])) {
    $id = $_POST['newloadID'];
    $checkpoints = $_POST['checkpoints'];
    $newchecknotes = $_POST['newchecknotes'];
    $user = $_SESSION['myFirstName'] . " " . $_SESSION['myLastName'];


    $mysqli->query("INSERT INTO newcheckcalls (newloadID, user, checkpoints, newchecknotes) VALUES('$id', '$user', '$checkpoints', '$newchecknotes')") or die($mysqli->error);

    $_SESSION['message'] = "Record has been saved!";
    $_SESSION['msg_type'] = "success";

    header("location: ../../index.php?action_type=viewdispetcher&id=$id");
}

if (isset($_POST['assignUser'])) {
    $id = $_POST['newloadID'];
    $AssignedBy = $_SESSION['myFirstName'] . " " . $_SESSION['myLastName'];
    $user = $_POST['user'];



    $mysqli->query("INSERT INTO newcheckcalls (newloadID, user, AssignedBy) VALUES('$id', '$user', '$AssignedBy')") or die($mysqli->error);

    $_SESSION['message'] = "Record has been saved!";
    $_SESSION['msg_type'] = "success";

    header("location: ../../index.php?action_type=viewdispetcher&id=$id");
}

if (isset($_POST['newcallupdate'])) {
    $id = $_POST['newloadID'];
    $callid = $_POST['newcallid'];
    $checkpoints = $_POST['checkpoints'];
    $newchecknotes = $_POST['newchecknotes'];
    $user = $_SESSION['myFirstName'] . " " . $_SESSION['myLastName'];



    $mysqli->query("UPDATE newcheckcalls SET newloadID='$id', user='$user', checkpoints='$checkpoints', newchecknotes='$newchecknotes' WHERE callid='$callid'") or die($mysqli->error);

    $_SESSION['message'] = "Record has been Updated!";
    $_SESSION['msg_type'] = "success";

    header("location: ../../index.php?action_type=viewdispetcher&id=$id");
}

// Store status into the session 
$_SESSION['sessData'] = $sessData;

// Redirect the user 
header("Location: " . $redirectURL);
exit();
?>

<script>
    function deleteConFile(id) {
        var result = confirm("Are you sure to delete?");
        if (result) {
            $.post("postAction.php", {
                action_type: "rateCon_delete",
                id: id
            }, function(resp) {
                if (resp == 'ok') {
                    $('#imgb_' + id).remove();
                    alert('The File has been removed from the records.');
                } else {
                    alert('Some problem occurred, please try again.');
                }
            });
        }
    }
</script>