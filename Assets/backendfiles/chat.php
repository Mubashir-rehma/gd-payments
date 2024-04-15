<?php
    session_start();

    include './config.php';
    $creater = $_SESSION['myid'];
    $now = new DateTime("now", new DateTimeZone('America/New_York'));
    $now = $now->format('Y-m-d H:i:s');


    if(isset($_POST['msgtype']) && $_POST['msgtype'] == "new_msg"){
        $out_id = $creater;
        $in_id = $_POST['incomning_id'];
        $msg = $mysqli->real_escape_string($_POST['writemsg']);

        $msg_id = "";
        if(!empty($msg)){
            $query = "INSERT into messages (`incoming_msg_id`, `outgoing_msg_id`, `msg`) VALUES ('$in_id', '$out_id', '$msg')";
            $insert = $mysqli->query($query) or die($mysqli->error);
            $msg_id = $mysqli->insert_id;
        }

        $chat_attachments = array_filter($_FILES['attach_docs_i']['name']);
        $output = "";
        if (!empty($chat_attachments)) {

            $i = 0;
            foreach ($chat_attachments as $key => $val) {
                // File upload path 
                $fileName = $chat_attachments[$key];
                $targetFilePath = '../uploads/chat_docs/' . $fileName;

                // Check whether file type is valid 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                // if (in_array($fileType, $allowTypes)) {
                // Upload file to server 
                if (move_uploaded_file($_FILES["attach_docs_i"]["tmp_name"][$key], $targetFilePath)) {
                    // File db insert 
                    $query = "INSERT INTO messages (`incoming_msg_id`, `outgoing_msg_id`, `file_name`) VALUES ('$in_id', '$out_id', '$fileName')";
                    $mysqli->query($query) or die($mysqli->error);
                    $id = $mysqli->insert_id;

                    $fsz = null;
                    if(is_file("./Assets/uploads/chat_docs/".$fileName)){
                        $fsz = formatBytes(filesize("./Assets/uploads/chat_docs/".$fileName));
                    }
                    
                    $file = $chat_attachments[$key];
                    $forpickupdate = date("H:i", strtotime($now));

                    $explode = explode(".", $file);
                    $extension = $explode[sizeof($explode)-1]; //return "txt", "zip" or whatever

                    $mime = mime_content_type($targetFilePath);
                    if(strstr($mime, "video/")){
                        // this code for video
                        $ft = '<video src="./Assets/uploads/chat_docs/'.$fileName .'" controls></video>';
                    }else if(strstr($mime, "image/")){
                        $ft = '<img src="./Assets/uploads/chat_docs/'.$fileName .'" alt="" srcset="">';
                    } else if(in_array($extension, ["pdf"])){
                        $ft = '<img src="./Assets/Images/pdf file.svg" alt="" srcset="">';
                    } else if(in_array($extension, ["xlsx", "csv", "xlsm"])){
                        $ft = '<img src="./Assets/Images/excel.svg" alt="" srcset="">';
                    } else {
                        $ft = '<img src="./Assets/Images/document.svg" alt="" srcset="">';
                    }

                    $output .= '<div class="file_container msgs msg_sent" data-msg_id="'. $id .'" title="'.$fileName .'">
                        <div class="file">' .$ft .'</div>
                        <div class="file-info">
                            <div class="file_name">
                                <a style="font-size: 12px; color: #3a3a3a;" href="./Assets/uploads/chat_docs/'.$fileName.'" target="_blank" rel="noopener noreferrer" class="file_name">'.$fileName.'</a>
                                <p style="color: #979797; font-size: 10px; font-weight: 400; font-size: 10px !important;" class="file_other_info">'.$fsz .' on '.$forpickupdate .'</p>
                            </div>
                            <div class="file_action">
                                <a href="./Assets/uploads/chat_docs/'.$file.'" download>
                                    <img src="https://img.icons8.com/ios/50/null/installing-updates--v1.png" style="width: 20px; cursor: pointer; margin-right: 10px;"/>
                                </a>
                               
                            </div>
                        </div>

                        <div class="msg_actions">
                            <div class="open_actions"><i class="fa-solid fa-chevron-down"></i></div>
                            <div class="actions_con">
                                <ul>
                                    <li style="color: red; font-size: 15px; margin: 7px 10px 7px 0; cursor: pointer; "  class="delete_msg" data-id="'.$id.'"><i class="uil uil-trash-alt"></i> Delete</li>
                                </ul>
                            </div>
                        </div>
                    </div>';


                } else {
                    // $errorUpload .= $codFiles[$key] . ' | ';
                }

                $i++;
            }
        }

        if($msg_id){
            $query = "UPDATE users set last_msg_time = '$now' where id in ($creater,  $in_id)";
            $mysqli->query($query) or die($mysqli->error);
        };

        $data[] = [
            "output" => $output,
            "msg_id" => $msg_id
        ];
        echo json_encode($data);
    } else if(isset($_POST['delete_msg'])){
        $id = $_POST['id'];

        $mysqli->query("DELETE FROM `messages` WHERE msg_id=$id") or die($mysqli->error);
    };


    function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow)); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
?>