<?php
    session_start();

    include './config.php';
    include './notification.php';
    $uploadDir = "../uploads/vendor_attachments/";
    $creater = $_SESSION['myid'];
    $document_root = $_SERVER['DOCUMENT_ROOT'];


    if($_REQUEST['action_type'] == "new_vendor"){
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

        
        if(!empty($newid)){

            // Add Files
            if (!empty($_POST['ven_attachments'])) {

                $ven_attachments = $_POST['ven_attachments'];

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

            $query = "SELECT * from vendors";
            $vendors = $mysqli->query($query) or die($mysqli->error);
            while ($vendors_detail = $vendors->fetch_assoc()) :
                $html_content .= '<option value="' . $vendors_detail['vendor_id'] . ' " data-foo=" ' . $vendors_detail['vendor_name'] . ' "> ' . $vendors_detail['ven_company'] . ' </option>';
            endwhile; 


        } else {
            $msg = "Someting went Wrong!";
            $success = 0;
        }

        $data[] = [
            'msg' => $msg,
            'success' => $success,
            'newid' => $newid,
            'html_content' => $html_content
        ];

        echo json_encode($data);


    } else if($_REQUEST['action_type'] == "new_item"){
        $item_name = $mysqli->real_escape_string($_POST['item_name']);
        $account_type = $mysqli->real_escape_string($_POST['account_type']);
        $prefered_vendor = $mysqli->real_escape_string(serialize($_POST['prefered_vendor']));
        $item_notes = $mysqli->real_escape_string($_POST['item_notes']);
        $added_by = $creater;

        $query = "INSERT INTO items (item_name, item_Account_type, item_notes, prefered_vendor, added_by) VALUES ('$item_name','$account_type','$item_notes','$prefered_vendor','$added_by')";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Item has been Successfully Added!";
        $success = 1;

        $data[] = [
            'msg' => $msg,
            'success' => $success
            // 'newid' => $newid,
            // 'html_content' => $html_content
        ];

        echo json_encode($data);


    } else if ($_REQUEST['action_type'] == "new_purchase") {
        $pur_date = $mysqli->real_escape_string($_POST['pur_date']);
        $vendor = $_POST['vendor'];
        $total_amount = $_POST['total_amount'];
        $amount_paid = $_POST['amount_paid'];
        $payment_status = $mysqli->real_escape_string($_POST['payment_status']);
        $pur_items = serialize($_POST['pur_items']);
        $pur_notes = $mysqli->real_escape_string(serialize($_POST['pur_notes']));
        $added_by  = $creater;
        // print_r($_POST);



        $id = "";
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
        }


        $msg = "";
        $success = 0;
        $newid = "";
        $html_content = "";

        if (!empty($id)) {
            $query = "UPDATE purchases SET pur_date ='$pur_date', vendor ='$vendor', total_amount ='$total_amount', amount_paid ='$amount_paid', payment_status ='$payment_status', pur_items ='$pur_items', pur_notes ='$pur_notes', new_pur_added_by ='$added_by' WHERE pur_id='$id')";

            $mysqli->query($query) or die($mysqli->error);
            $newid = $id;

            $msg = "Purchase has been updated Successfully!";
            $success = 1;

        } else {
            $query = "INSERT INTO purchases (pur_date, vendor, total_amount, amount_paid, payment_status, pur_items, pur_notes, new_pur_added_by) VALUES ('$pur_date','$vendor','$total_amount','$amount_paid','$payment_status','$pur_items', '$pur_notes','$added_by')";

            $mysqli->query($query) or die($mysqli->error);
            $newid = $mysqli->insert_id;

            $msg = "Purchase has been Added Successfully!";
            $success = 1;
        }


        if (!empty($newid)) {

            // Add Files
            if (!empty($_POST['pur_attachments'])) {

                $pur_attachments = $_POST['pur_attachments'];

                foreach ($pur_attachments as $key => $value) {
                    $pur_attachments = json_decode($value, true);
                    // File upload path 
                    $fileName = $newid . '_' . basename($pur_attachments['name']);
                    $targetFilePath = '../uploads/Purchase_docs/' . $fileName;


                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["pur_attachments"]["tmp_name"][$key], $targetFilePath)) {
                        // File db insert 
                        $fileName = $mysqli->real_escape_string($fileName);
                        $query = "INSERT INTO pur_docs (file_name, pur_id) VALUES ('$fileName','$newid')";
                        $mysqli->query($query) or die($mysqli->error);
                    } else {
                        // $errorUpload .= $codFiles . ' | ';
                    }
                }
            }

            $query = "SELECT * from purchases";
            $purchases = $mysqli->query($query) or die($mysqli->error);

            
        } else {
            $msg = "Someting went Wrong!";
            $success = 0;
        }

        $data[] = [
            'msg' => $msg,
            'success' => $success,
            'newid' => $newid,
            'html_content' => $html_content
        ];

        echo json_encode($data);
        
    } else if($_REQUEST['action_type'] == "sale_payment_update"){
        $id = $_POST['id'];
        $payment_date = $_POST['payment_date'];
        $payment_method = $mysqli->real_escape_string($_POST['payment_method']);
        $paid_amount = $_POST['paid_amount'];
        $total_amount = $_POST['total_amount'];
        $payment_status = $mysqli->real_escape_string($_POST['payment_status']);
        $added_by  = $creater;
        $notes = $mysqli->real_escape_string($_POST['sale_pay_update_notes']);

        $query = "INSERT INTO sale_payment_updates (load_id, sale_payment_status, payment_method, amount_paid, total_amount, date_amount_received, added_by, notes) VALUES ('$id','$payment_status','$payment_method','$paid_amount','$total_amount','$payment_date','$added_by', '$notes')";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Broker Payment has been Successfully Added!";
        $success = 1;
        

        $data[] = [
            'msg' => $msg,
            'success' => $success,
            // 'rows' => $rows,
            // 'newid' => $newid,
            // 'html_content' => $html_content
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "pur_payment_update") {
        $id = $_POST['id'];
        $payment_date = $_POST['payment_date'];
        $payment_method = $mysqli->real_escape_string($_POST['payment_method']);
        $paid_amount = $_POST['paid_amount'];
        $total_amount = $_POST['total_amount'];
        $quick_pay = $_POST['quick_pay'];
        $payment_status = $mysqli->real_escape_string($_POST['payment_status']);
        $added_by  = $creater;

        $mailStatus = "";
        if ($_POST['payment_status'] == "3") {
            $mailStatus = sendMail($mysqli, $id, $mail, "Payment history");
        }

        $query = "INSERT INTO purchase_payment_updates (pur_load_id, payment_status, payment_method, amount_paid, total_amount, paid_on, added_by, quickpay) VALUES ('$id','$payment_status','$payment_method','$paid_amount','$total_amount','$payment_date','$added_by', '$quick_pay')";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Payment Status has been Successfully Updated!";
        $success = 1;

        $data[] = [
            'msg' => $msg,
            'success' => $success,
            'mailStatus' => $mailStatus,
            // 'newid' => $newid,
            // 'html_content' => $html_content
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "payment_setting") {
        $setting_added_for = $_POST['setting_added_for'];
        $setting_payment_method = $mysqli->real_escape_string($_POST['setting_payment_method']);
        $setting_payment_status = $_POST['setting_payment_status'];;
        $added_by  = $creater;

        $query = "INSERT INTO settings (setting_added_by, added_for, setting_payment_method, setting_payment_status) VALUES ('$added_by','$setting_added_for','$setting_payment_method','$setting_payment_status')";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Item has been Successfully Added!";
        $success = 1;

        $data[] = [
            'msg' => $msg,
            'success' => $success
            // 'newid' => $newid,
            // 'html_content' => $html_content
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "driver_payment_update"){
        $status = $_POST['status'];
        $load_id = $_POST['load_id'];
        $total_amount = $_POST['total_amount'];
        $date = date('Y/m/d h:i:s', time());

        $mailStatus = "";
        if($status == "3"){
            $mailStatus = sendMail($mysqli, $load_id, $mail, "Payment history");
        }

        $query = "INSERT INTO purchase_payment_updates (pur_load_id, payment_status, amount_paid, total_amount, paid_on, added_by) VALUES ('$load_id','$status','$total_amount','$total_amount','$date','$creater')";
        $stmt = $mysqli->query($query) or die($mysqli->error);

        // $msg = "Someting Went Wrong!";
        // $success = 0;
        // if($stmt){
            $msg = "Status has been Successfully Updated!";
            $success = 1;
        // }
        

        $data[] = [
            'msg' => $msg,
            'success' => $success,
            'mailStatus' => $mailStatus,
            // 'newid' => $newid,
            // 'html_content' => $html_content
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "sale_payment_status_update") {
        $status = $_POST['status'];
        $load_id = $_POST['load_id'];
        $total_amount = $_POST['total_amount'];
        $paid_amount = $_POST['total_amount'];
        $date = date('Y/m/d h:i:s', time());

        if ($status == "mark_paid") {
            $status = "Paid";
        }

        if($status == 7){
            $query = "INSERT INTO sale_payment_updates (load_id, sale_payment_status, amount_paid, total_amount, date_amount_received, added_by) VALUES ('$load_id','$status','$paid_amount','$total_amount','$date','$creater')";
        } else {
            $query = "INSERT INTO sale_payment_updates (load_id, sale_payment_status, total_amount, date_amount_received, added_by) VALUES ('$load_id','$status','$total_amount','$date','$creater')";
        }

        
        $mysqli->query($query) or die($mysqli->error);

        // $msg = "Someting Went Wrong!";
        // $success = 0;
        // if($stmt){
        $msg = "Status has been Successfully Added!";
        $success = 1;
        // }


        $data[] = [
            'msg' => $msg,
            'success' => $success
            // 'newid' => $newid,
            // 'html_content' => $html_content
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "delet_item") {
        $item_id = $_POST['item_id'];

        $query = "DELETE FROM items WHERE item_id='$item_id'";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Item Successfully Deleted!";
        $success = 1;

        $data[] = [
            "msg" => $msg,
            "success" => $success
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "delete_vendor") {
        $vendor_id = $_POST['vendor_id'];

        $query = "DELETE FROM vendors WHERE vendor_id='$vendor_id'";
        $stmt = $mysqli->query($query) or die($mysqli->error);

        if(!$stmt){
            $msg = "Something went wrong!";
            $success = 0;
        } else {
            $msg = "Vendor Successfully Deleted!";
            $success = 1;
        }
        

        $data[] = [
            "msg" => $msg,
            "success" => $success
        ];

        echo json_encode($data);
    } else if($_REQUEST['action_type'] == "delete_sale_payment_update"){
        $sale_payment_id = $_POST['sale_payment_id'];

        $query = "DELETE FROM sale_payment_updates WHERE sale_payment_id='$sale_payment_id'";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Record Successfully Deleted!";
        $success = 1;
        
        

        $data[] = [
            "msg" => $msg,
            "success" => $success
        ];

        echo json_encode($data);
    } else if($_REQUEST['action_type'] == "broker_accounting_info"){
        $broker_id_for_AP = $_POST['broker_id_for_AP'];
        $broker_accounting_email = $_POST['broker_accounting_email'];
        $accounting_contact_person = $mysqli->real_escape_string($_POST['accounting_contact_person']);
        $broker_notes = $mysqli->real_escape_string($_POST['broker_notes']);
        $broker_accounting_attachments = $_POST['broker_accounting_attachments'];

        $query = "UPDATE broker_details SET accounting_email='$broker_accounting_email',accounting_contact_person='$accounting_contact_person',accounting_notes='$broker_notes' WHERE broker_id='$broker_id_for_AP'";
        $stmt = $mysqli->query($query) or die($mysqli->error);
        $newid = $broker_id_for_AP;

        if($stmt){
            // Add Files
            if (!empty($broker_accounting_attachments)) {

                foreach ($broker_accounting_attachments as $key => $value) {
                    $broker_accounting_attachments = json_decode($value, true);
                    // File upload path 
                    $fileName = $newid . '_' . basename($broker_accounting_attachments['name']);
                    $targetFilePath = "../uploads/broker_attachments/" . $fileName;


                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["broker_accounting_attachments"]["tmp_name"][$key], $targetFilePath)) {
                        // File db insert 

                        $query = "INSERT INTO broker_attachments (file_name, broker_id, added_by) VALUES ('$fileName','$newid', '$creater')";
                        $mysqli->query($query) or die($mysqli->error);

                    } else {
                        // $errorUpload .= $codFiles . ' | ';
                    }
                }
            }
        }

        $success = 1;
        $msg = "Data successfully Added!";

        $data[] = [
            'msg' => $msg,
            'success' => $success,
        ];

        echo json_encode($data);

    } else if($_REQUEST['action_type'] == "delete_broker_attachment"){
        $id = $_POST['id'];

        $query = "SELECT `file_name` FROM `broker_attachments` WHERE broker_attachments_id='$id'";
        $file_name = $mysqli->query($query) or die($mysqli->error);
        $file_name = mysqli_fetch_array($file_name);


        $query = "DELETE FROM broker_attachments WHERE broker_attachments_id='$id'";
        $delete = $mysqli->query($query) or die($mysqli->error);
        if ($delete) {
            @unlink('../uploads/broker_attachments/' . $file_name['file_name']);
            $status = 'ok';
        }

        $success = 1;
        $msg = "File successfully Deleted!";

        $data[] = [
            'msg' => $msg,
            'success' => $success,
        ];

        echo json_encode($data);
    } else if($_REQUEST['action_type'] == "delete_driver_payment_update"){
        $pur_payment_id = $_POST['pur_payment_id'];

        $query = "DELETE FROM purchase_payment_updates WHERE pur_payment_id='$pur_payment_id'";
        $mysqli->query($query) or die($mysqli->error);

        $msg = "Record Successfully Deleted!";
        $success = 1;
        
        

        $data[] = [
            "msg" => $msg,
            "success" => $success
        ];

        echo json_encode($data);
    } else if ($_REQUEST['action_type'] == "driver_accounting_info"){
        $broker_id_for_AP = $_POST['broker_id_for_AP'];
        $driver_accounting_email = $_POST['driver_accounting_email'];
        $driver_full_name = $mysqli->real_escape_string($_POST['driver_full_name']);
        $driver_Address = $mysqli->real_escape_string($_POST['driver_Address']);
        $bank_name = $mysqli->real_escape_string($_POST['bank_name']);
        $routing_number = $mysqli->real_escape_string($_POST['routing_number']);
        $account_number = $mysqli->real_escape_string($_POST['account_number']);
        $ssn_ein_number = $mysqli->real_escape_string($_POST['ssn_ein_number']);
        $driver_accounting_notes = $mysqli->real_escape_string($_POST['driver_accounting_notes']);
        $driver_accounting_attachments = $_POST['driver_accounting_attachments'];

        $query = "UPDATE truck_details SET driver_full_name='$driver_full_name', driver_accounting_email='$driver_accounting_email',driver_Address='$driver_Address',bank_name='$bank_name',routing_number='$routing_number',account_number='$account_number',ssn_ein_number='$ssn_ein_number',driver_accounting_notes='$driver_accounting_notes' WHERE truck_id='$broker_id_for_AP'";
        $stmt = $mysqli->query($query) or die($mysqli->error);
        $newid = $broker_id_for_AP;

        if($stmt){
            // Add Files
            if (!empty($driver_accounting_attachments)) {

                foreach ($driver_accounting_attachments as $key => $value) {
                    $driver_accounting_attachments = json_decode($value, true);
                    // File upload path 
                    $fileName = $newid . '_' . basename($driver_accounting_attachments['name']);
                    $targetFilePath = "../uploads/Driver_attachments/" . $fileName;


                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    // if (in_array($fileType, $allowTypes)) {
                    // Upload file to server 
                    if (move_uploaded_file($_FILES["driver_accounting_attachments"]["tmp_name"][$key], $targetFilePath)) {
                        // File db insert 

                        $query = "INSERT INTO driver_accounting_attachments (file_name, truck_id, added_by) VALUES ('$fileName','$newid', '$creater')";
                        $mysqli->query($query) or die($mysqli->error);

                    } else {
                        // $errorUpload .= $codFiles . ' | ';
                    }
                }
            }
        }

        $success = 1;
        $msg = "Data successfully Added!";

        $data[] = [
            'msg' => $msg,
            'success' => $success,
        ];

        echo json_encode($data);
    } else if($_REQUEST['action_type'] == "delete_driver_attachment"){
        $id = $_POST['id'];

        $query = "SELECT `file_name` FROM `driver_accounting_attachments` WHERE driver_att_id='$id'";
        $file_name = $mysqli->query($query) or die($mysqli->error);
        $file_name = mysqli_fetch_array($file_name);


        $query = "DELETE FROM driver_accounting_attachments WHERE driver_att_id='$id'";
        $delete = $mysqli->query($query) or die($mysqli->error);
        if ($delete) {
            @unlink('../uploads/Driver_attachments/' . $file_name['file_name']);
            $status = 'ok';
        }

        $success = 1;
        $msg = "File successfully Deleted!";

        $data[] = [
            'msg' => $msg,
            'success' => $success,
        ];

        echo json_encode($data);
    } 




function sendMail($mysqli, $id, $mail, $subject){
    $loadquery = "SELECT *
        FROM newload n
        LEFT OUTER JOIN truck_details AS t
        ON t.truck_id = n.truck_Number
        LEFT OUTER JOIN
        (select * from purchase_payment_updates PPU left outer join (select setting_payment_method as DSPM, setting_payment_status as DSPS, settings_id from settings where added_for='Driver') as settings on settings.settings_id = PPU.payment_status where exists (select * from (select pur_load_id, max(pur_payment_id) as PPid
        from purchase_payment_updates ppP
        GROUP BY pur_load_id
        ) as PPP where PPU.pur_payment_id = PPP.PPid)) PPU on n.id = PPU.pur_load_id
        where id='$id' and PPU.pur_load_id = '$id'
        ORDER BY id DESC";

    $loaddata = $mysqli->query($loadquery) or die($mysqli->error);


    $date = new DateTime();
    $week = $date->format("W");
    $driver_email = "";
    $quickpay = "";
    $loadid = "";
    foreach ($loaddata as $l) {
        $driver_email =  $l['cpemail'];
        $quickpay = $l['quickpay'];
        $loadid = $l['id'];
    };

    $rows = "";

    $rows .= '<div style="text-align: center; max-width: 450px; display: flex; align-items: center; margin: auto; justify-content: center;">';
        $rows .= '<div>';
            $rows .= '<p style="text-align: center; font-weight: 600;">Contract history statement of</p>';
            $rows .= '<div style="display: flex;">';
                $rows .= '<div style="text-align: left;">';
                    $rows .= '<p style="font-weight: 600; font-size: 12px; margin: 0; width: 155px;">GTMM Transportation LLC</p>';
                    $rows .= '<a style=" font-size: 12px; margin: 0; color: black; text-decoration: none;" href="tel:+17042881045">704 288 1045</a>';
                $rows .= '</div>';
                $rows .= '<div style="margin-left: 130px;">';
                    $rows .= '<img style="width: 150px;" src="http://gtmmtrans.com/images/logo.png" alt="" srcset="">';
                    $rows .= '<p style="margin: 0;">Issued Date: ' . $date->format("m-d-Y")  . '</p>';
                $rows .= '</div>';
            $rows .= '</div>';
            $rows .= '<div style="display: flex;"><p style="text-align: left;">Week: ' . $week . '</p>';
            if($quickpay == 1){
                $rows .= '<p style="text-align: right; margin-left: 270px; color: red;"><span style="color: #3a3a3a; font-weight: 600;">Load No.' . $loadid . '</span> Quickpay</p>';
            } else {
                $rows .= '<p style="text-align: right; margin-left: 310px; color: red;"><span style="color: #3a3a3a; font-weight: 600;">Load No. '.  $loadid .'</span></p>';
            };
            $rows .= '</div>';
            $rows .= '<table style="padding: 10px; border: 1px solid #dadada; border-collapse: collapse;">';;
                $rows .= '<thead>';
                    $rows .= '<tr>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">#</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Drivers/ Trucks</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Pick Up</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Drop Off</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Paid On</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Dispatcher</th>';
                        $rows .= '<th style="padding: 5px; border: 1px solid #dadada; border-collapse: collapse;">Total Rate</th>';
                    $rows .= '</tr>';
                $rows .= '</thead>';
                $rows .= '<tbody>';
                    $i = "";
                    foreach ($loaddata as $row) {
                        $i++;

                        count((is_countable(unserialize($row['Pick_up_Location']))) ? unserialize($row['Pick_up_Location']) : []) > 0 ? $pickuplocation = unserialize($row['Pick_up_Location'])[0] : $pickuplocation = unserialize($row['Pick_up_Location']);
                        count((is_countable(unserialize($row['Destination']))) ? unserialize($row['Destination']) : []) > 0 ? $destination = unserialize($row['Destination'])[0] : $destination = unserialize($row['Destination']);

                        
                        
                        if($row['quickpay'] == 1){
                            $total_rate = ($row['Carier_Driver_Rate'] * 0.95);
                            $total_rate = $row['Carier_Driver_Rate'] . ' - 5% = $ ' . $total_rate;
                        } else {
                            $total_rate = $row['Carier_Driver_Rate'];
                        }

                        $rows .= '<tr>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $i . '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $row['truckDriver']  . '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $pickuplocation;
                                $rows .= '<br><span style="color: #8f8f8f; font-size: 12px;">';
                                    if (strtotime($row['pickupdate']) > 0) {
                                        $pickupdate = $row['pickupdate'];
                                        $forpickupdate = date("m-d-y", strtotime($pickupdate));
                                        $rows .= $forpickupdate . " ";
                                        $forpickuptime = date("h:i a", strtotime($pickupdate));
                                        $rows .= $forpickuptime;
                                    } else {
                                        $rows .= '';
                                    }
                                $rows .= '</span>';
                            $rows .= '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $destination;
                                $rows .= '<br><span style="color: #8f8f8f; font-size: 12px;">';
                                    if (strtotime($row['dropdate']) > 0) {
                                        $originalDate = $row['dropdate'];
                                        $newDate = date("m-d-y", strtotime($originalDate));
                                        $rows .= $newDate . " ";

                                        $forpickuptime = date("h:i a", strtotime($originalDate));
                                        $rows .= $forpickuptime;
                                    } else {
                                        $rows .= '';
                                    }
                                $rows .= '</span>';
                            $rows .= '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">';
                                if (strtotime($row['paid_on']) > 0) {
                                    $paid_on = $row['paid_on'];
                                    $rows .= $newDate = date("m-d-y", strtotime($paid_on));
                                } else {
                                    $rows .= '';
                                }
                            $rows .= '</td>';
                            $rows .= '<td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">' . $row['dispatcher']  . '</td>';
                           $rows .= ' <td style="padding: 5px; font-size: 12px; border: 1px solid #dadada; border-collapse: collapse;">$ ' . $total_rate  . '</td>';
                        $rows .= '</tr>';
                    } 
                $rows .= '</tbody>';
            $rows .= '</table>';
        $rows .= '</div>';
    $rows .= '</div>';

    $mailStatus = "";
    if(!empty($driver_email)){
        $mail->addAddress($driver_email);
        $mail->WordWrap = 50;
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    =  $rows;

        if (!$mail->send()) {
            $mailStatus = 'Email could not be sent.'  . ' Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $mailStatus = 'Email has been sent';
        }
    } else {
        $mailStatus = "Please add the driver email First!";
    }

    return $mailStatus;
}

?>