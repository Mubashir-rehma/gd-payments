<?php


function add_notification($mysqli, $msg_type, $msg, $msg_type_ref_id, $creater = "system")
{
    $trackers_query = "select * from users where usertype='tracker'";
    $trackers = $mysqli->query($trackers_query) or die($mysqli->error);

    foreach ($trackers as $t) {
        $t_id = $t['id'];
        $not_query = "insert into notifications (creater, receiver, msg_type, msg, msg_type_ref_id) values ('$creater', '$t_id', '$msg_type', '$msg', '$msg_type_ref_id')";

        $mysqli->query($not_query) or die($mysqli->error);
    }
}

function tracking2_hour_reminder($mysqli)
{
    $query = "SELECT * 
    FROM newload n 
    LEFT OUTER JOIN truck_details AS t 
    ON t.truck_id = n.truck_Number
    LEFT OUTER JOIN 
    (select * from load_tracking T where exists (select * from (select load_id, max(tracking_id) as tracking_id
    from load_tracking lt 
    GROUP BY load_id
    ) as lt where T.tracking_id = lt.tracking_id)) T on n.id = T.load_id 
    where n.status='load_en_route'
    ORDER BY id DESC";

    $data = $mysqli->query($query) or die($mysqli->error);

    foreach($data as $row){
        $trackingtimestamp = strtotime($row['timestamp']);
        $id = $row['id'];
        $tracking_id = $row['tracking_id'];

        // getting current date 
        $cDate = strtotime(date('Y-m-d H:i:s'));

        // Getting the value of old date + 24 hours
        $oldDate = $trackingtimestamp + 7200; // 7200 seconds in 2 hrs

        if ($oldDate <= $cDate) {
            // print("old time:  yes  " . "  load id: " . $id . "   tracking ID:   " . $tracking_id. "  timestamp:  " . $trackingtimestamp . "  nottime:  " . $oldDate . "<br><br>");

            $msg_type = "driver";
            $msg = "Get load Update from driver.";
            $creater = "system";

            add_notification($mysqli, $msg_type, $msg, $id, $creater);
        } 
    }
}

function get_notifications($mysqli, $notification_type){
    $user = $_SESSION['myid'];
    $usertype = $_SESSION['myusertype'];

    $query = "select * from notifications n
    LEFT OUTER JOIN newload l 
    on l.id = n.msg_type_ref_id
     LEFT OUTER JOIN truck_details AS t 
    ON t.truck_id = l.truck_Number
    where receiver = '$user' or creater ='$user' ";

    if(!empty($notification_type) && $notification_type != "null"){
        $notification_type = $_GET['notification_type'];
        // $query .= " where ";
        $query .= " and msg_type='$notification_type' ";
    }
    if($usertype != "tracker"){
        $query .= " GROUP BY created_at";
    }

    $query .= " order by n.not_id desc limit 100";

    $notification = $mysqli->query($query) or die($mysqli->error);
    $total_unread_notifications = 0;


    $html_content = '';
    if($notification->num_rows == 0){
        $html_content .= '<div style="text-align: center;color: green;">No Notifications</div>';
    } else {
        foreach ($notification as $row) {
            $timeago = time_elapsed_string($row['created_at']);

            if ($row['msg_type'] == "load") {
                $text = "Load Status Update";
                $img = "./Assets/Images/load status Update.png";
                !empty($row['id']) ? $action = $row['id'] : $action =  $row['msg_type_ref_id'];
                $actionlink = "index.php?action_type=viewdispetcher&id=" . $row['id'];
                $actionext = $action;
                $otherinfo = "";
            } else {
                $text = "Get the update from driver";
                $img = "./Assets/Images/Driver update.png";
                $actionlink = "tel:+1" . $row['cpPhone'];
                $actionext = $row['cpPhone'];
                $otherinfo = $row['truckNumber'];
            }

            if ($row['read_status'] == "0") {
                $flag = "flag";
                $total_unread_notifications++;
            } else {
                $flag = '';
            };

            $html_content .= '<div class="tab_content">';
                $html_content .= '<img src="' .  $img . '" alt="">';
                $html_content .= '<div class="content">';
                    $html_content .= '<p class="light">' . $text  . '</p>';
                    $html_content .= '<p>' . $row['msg']  . '</p>';
                    $html_content .= '<a href="' . $actionlink . '">' . $actionext . '</a><span class="light">' . $otherinfo . '</span>';
                    $html_content .= '<p class="not_time">' . $timeago . '</p>';
                $html_content .= '</div>';
                $html_content .= '<div class="' . $flag . '"  data-notification_id="' . $row['not_id'] . '"></div>';
            $html_content .= '</div>';
        }
    }
    
    $total_unread_notifications == 100 ? $total_unread_notifications = '100+' : $total_unread_notifications = $total_unread_notifications;
    $data [] = [
        "total_not" => $total_unread_notifications,
        "html_content" => $html_content,
    ];

    echo json_encode($data);
}

function notification_alert($mysqli)
{
    $user = $_SESSION['myid'];

    $query = "select * from notifications n
    LEFT OUTER JOIN newload l 
    on l.id = n.msg_type_ref_id
     LEFT OUTER JOIN truck_details AS t 
    ON t.truck_id = l.truck_Number
    where n.read_status = '0' and receiver = '$user' ";

    $query .= " order by n.not_id desc ";
    $query .= " limit 1";

    $notification = $mysqli->query($query) or die($mysqli->error);

    $data = [];

    if ($notification->num_rows > 0) {
        foreach ($notification as $row) {
            $timeago = time_elapsed_string($row['created_at']);
            
            $load_id = $row['id'];
            if ($row['msg_type'] == "load") {
                $text = "Load Status Update";
                $img = "./Assets/Images/load status Update.png";
                !empty($row['id']) ? $action = $row['id'] : $action =  $row['msg_type_ref_id'];
                $actionlink = 'index.php?action_type=viewdispetcher&id='.$row['id'];
                $actionext = "Pro No. " .$action;
                $otherinfo = "";
                $load_id = $row['id'];
            } else {
                $text = "Get the update from driver";
                $img = "./Assets/Images/Driver update.png";
                $actionlink = "tel:+1" . $row['cpPhone'];
                $actionext = $row['cpPhone'];
                $otherinfo = $row['truckNumber'];
            }

            if ($row['read_status'] == "0") {
                $flag = "flag";
            } else {
                $flag = '';
            };

            $data[] = [
                "text" => $text,
                "msg" => $row['msg'],
                "action_link" => $actionlink,
                "action_text" => $actionext,
                "time_ago" => $timeago,
                "not_id" => $row['not_id'],
                "load_id" => $load_id,
                "result" => true
            ];
        }
    }else {
        $data[] = [
            "result" => false
        ];
    }

    echo json_encode($data);
}


function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function not_read($mysqli, $id){
    $query = "UPDATE notifications SET read_status='1' WHERE not_id = '$id'";

    $mysqli->query($query) or die($mysqli->error);
}

?>