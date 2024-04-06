<?php

include './Assets/backendfiles/config.php';

$email = $_SESSION['myusername'];
$today = date('Y-m-d');
$userloadsquery = $mysqli->query("SELECT * FROM newload where created_by='$email' and DATE(created) = '$today'") or die($mysqli->error);

// Total Loads done by the current user
$user_total_loads_today = $userloadsquery->num_rows;

// Total Profit achieved by the current user
$i = 0;
$user_total_profit_today = 0;
foreach ($userloadsquery as $row) {
    $i++;
    $user_total_profit_today += $row['Customer_Rate'] - $row['Carier_Driver_Rate'] - ($row['Customer_Rate'] * 0.024);
    // $userachievedgoal++;
}

// Goal Target set for the user
$user_goal = $mysqli->query("SELECT * from goals where user='$email' ORDER BY id DESC limit 1") or die($mysqli->error);
$goalarr = mysqli_fetch_assoc($user_goal);

if ($goalarr['timeline'] == 'monthly') {
    $dailygoal = round($goalarr['goal'] / 22, 0);
} elseif ($row['timeline'] == 'weekly') {
    $dailygoal += round($row['goal'] / 5, 0);
} elseif ($row['timeline'] == 'daily') {
    $dailygoal += round($row['goal'], 0);
}

// Goal Target set for the Team
$query = "SELECT * from goals where goal_status = 'Active' group by user order by id";
// $query = "SELECT  *
//     FROM (SELECT id, goal, timeline, goal_status, user,
//     ROW_NUMBER() OVER (PARTITION BY user ORDER BY id desc) AS RowNumber
//     FROM   goals WHERE  goal_status = 'Active') AS a
//     WHERE   a.RowNumber = 1";
$team_goal = $mysqli->query($query) or die($mysqli->error);
$g = 0;
$team_target_profit_today = 0;
foreach ($team_goal as $row) {
    if ($row['timeline'] == 'monthly') {
        $team_target_profit_today += round($row['goal'] / 22, 0);
    } elseif ($row['timeline'] == 'weekly') {
        $team_target_profit_today += round($row['goal'] / 5, 0);
    } elseif ($row['timeline'] == 'daily') {
        $team_target_profit_today += round($row['goal'], 0);
    }
}


// Goal Left 
$goal_left_to_achieve_query = $query;
// $goal_left_to_achieve_query = "SELECT  *
//     FROM (SELECT id, goal, timeline, goal_status, user,
//     ROW_NUMBER() OVER (PARTITION BY user ORDER BY id desc) AS RowNumber
//     FROM   goals WHERE  goal_status = 'Active') AS a
//     WHERE   a.RowNumber = 1";

$goal_left_to_achieve = $mysqli->query($goal_left_to_achieve_query) or die($mysqli->error);
$team_remaining_target_profit_today = 0;
$gl_label = [];
$gl_data = [];
$gl_data2 = [];
foreach ($goal_left_to_achieve as $row) {
    $gl_label[] = $row['user'];

    $email = $row['user'];
    $today = date('Y-m-d');
    $userloadsquery = $mysqli->query("SELECT * FROM newload where created_by='$email' and DATE(created) = '$today'") or die($mysqli->error);

    $i = 0;
    $team_total_profit_today = 0;
    foreach ($userloadsquery as $load_row) {
        $i++;
        $team_total_profit_today += $load_row['Customer_Rate'] - $load_row['Carier_Driver_Rate'] - ($load_row['Customer_Rate'] * 0.024);
    }
    $gl_data[] = $team_total_profit_today;

    // Total Profit achieved by the current user
    if ($row['timeline'] == 'monthly') {
        $team_remaining_target_profit_today = round($row['goal'] / 22, 0) - $team_total_profit_today;
    } elseif ($row['timeline'] == 'weekly') {
        $team_remaining_target_profit_today = round($row['goal'] / 5, 0) - $team_total_profit_today;
    } elseif ($row['timeline'] == 'daily') {
        $team_remaining_target_profit_today = round($row['goal'], 0) - $team_total_profit_today;
    }
    $gl_data2[] = $team_remaining_target_profit_today;
    $team_remaining_target_profit_today = 0;
}

// Total Load count Query
$total_load_count = "SELECT dispatcher,count(dispatcher)  AS count_me 
FROM newload  WHERE dispatcher IS NOT NULL 
GROUP BY dispatcher 
ORDER BY COUNT(dispatcher) DESC";


$team_overall_stats_query = "SELECT SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    count(dispatcher) as total_loads ,dispatcher
    FROM newload
    GROUP BY dispatcher
    ORDER BY dispatcher";
$dis_data = $mysqli->query($team_overall_stats_query) or die($mysqli->$error);

$team_total_profit = 0;
$team_avg_profit = 0;
$team_total_loads = 0;
$i = 0;
foreach ($dis_data as $row) {
    $i++;
    $customer = $row['toal_Customer_Rate'];
    $driver = $row['total_Carier_Driver_Rate'];
    $OTR = $row['toal_Customer_Rate'] * 0.024;
    $total_loads = $row['total_loads'];

    $team_total_profit += $customer - $driver - $OTR;
    $team_avg_profit += (($customer - $driver - $OTR) / $total_loads) / $i++;
    $team_total_loads += $total_loads;
}


// Time base Query
$dis_time_base_query = "SELECT
    EXTRACT(MONTH FROM created) as month, 
    EXTRACT(YEAR FROM created) as year, 
    SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    count(dispatcher) as total_loads ,
    dispatcher
    FROM newload
    Where dispatcher='Thomas'
    GROUP BY month, year, dispatcher
    ORDER BY year DESC, month ASC";
$dis_time_base = $mysqli->query($dis_time_base_query) or die($mysqli->$error);


// Dispatcher List
$dis_list_query = "SELECT
    SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    count(dispatcher) as total_loads ,
    dispatcher
FROM newload
GROUP BY dispatcher";
// $dis_list = $mysqli->query($dis_list_query) or die($mysqli->$error);
// foreach ($dis_list as $dis) {
//     print( $dis['dispatcher'] . ",  ");
// }

// Individual Load Count
$i_query = "SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, 
    SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    COUNT(*) AS amount, dispatcher AS D
    FROM newload
    where dispatcher='Thomas' 
    GROUP BY unique_date
    ORDER BY unique_date DESC";

// Individual Load Count
$i_LC = $mysqli->query($i_query) or die($mysqli->error);
$i_LC_labels = [];
$i_LC_data = [];
$i_TP_data = [];
$i_AP_data = [];
$i_APL_data = [];
$i_TL = 0;
$i_TP = 0;
$i_AP = 0;
$i_APL = 0;
$x = 1;
foreach ($i_LC as $i) {
    $x++;
    $i_LC_labels[] = $i['unique_date'];
    $i_LC_data[] = $i['amount'];
    $i_TL += $i['amount'];
    $CR = $i['toal_Customer_Rate'];
    $DR = $i['total_Carier_Driver_Rate'];
    $OTR = $CR * 0.024;
    $i_TP_data[] = ($CR - $DR - $OTR);
    $i_AP_data[] = ($CR - $DR - $OTR) / 22;
    $i_APL_data[] = ($CR - $DR - $OTR) / $i_TL;
    $i_TP += ($CR - $DR - $OTR);
    $i_AP += (($CR - $DR - $OTR) / 22) / $x++;
    $i_APL += (($CR - $DR - $OTR) / $i_TL) / $x++;
}
$i_LC_labels = json_encode($i_LC_labels);
$i_LC_data = json_encode($i_LC_data);
$i_TL = round($i_TL);
$i_TP = round($i_TP);
$i_AP = round($i_AP);
$i_APL = round($i_APL);


?>