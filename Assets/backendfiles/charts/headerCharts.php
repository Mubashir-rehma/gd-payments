<?php

include './Assets/backendfiles/config.php';
include './Assets/backendfiles/charts/public_class.php';

$username = $_SESSION['myusername'];
empty($username) ? $username = "" : $username = $username;
$today = date('Y-m-d');
$first_of_current_month = date('Y-01-m');
$userloadsquery = $mysqli->query("SELECT * FROM newload where created_by='$username' and DATE(created) = '$today'") or die($mysqli->error);
$userloadsarr = mysqli_fetch_assoc($userloadsquery);

// Goal Achieved
$i = 0;
$userachievedgoal = 0;
foreach ($userloadsquery as $row) {
    $i++;
    $userachievedgoal += $row['Customer_Rate'] - $row['Carier_Driver_Rate'] - ($row['Customer_Rate'] * 0.024);
}

// Goal Target set
$goal = $mysqli->query("SELECT * from goals where user='$username' ORDER BY id DESC limit 1") or die($mysqli->error);
$goalarr = mysqli_fetch_assoc($goal);

$dailygoal = 0;
if ($goalarr['timeline'] == 'monthly') {
    $dailygoal = round($goalarr['goal'] / 22, 0);
} elseif ($goalarr['timeline'] == 'weekly') {
    $dailygoal += round($goalarr['goal'] / 5, 0);
} elseif ($goalarr['timeline'] == 'daily') {
    $dailygoal += round($goalarr['goal'], 0);
}

// Current month calculations
$team_overall_stats_query = "SELECT SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    count(dispatcher) as total_loads ,dispatcher
    FROM newload
    where DATE(created)>='$first_of_current_month' and  DATE(created)<='$today'
    GROUP BY dispatcher
    ORDER BY dispatcher";
$dis_data = $mysqli->query($team_overall_stats_query) or die($mysqli->$error);
$current_user_pace = 0;
$t_pace_label = [];
$t_pace_data = [];
foreach ($dis_data as $row) {
    if ($row['dispatcher'] == $username) {
        $CR = $row['toal_Customer_Rate'];
        $DR = $row['total_Carier_Driver_Rate'];
        $OTR = $CR * 0.024;
        $profit = $CR - $DR - $OTR;
        $workingdays = getWorkingDays($first_of_current_month, $today, '');
        $AVG_Profit = $profit / $workingdays;
        $current_user_pace += round($AVG_Profit * 22, 0);
    }

    $t_pace_label[] = $row['dispatcher'];
    $CR = $row['toal_Customer_Rate'];
    $DR = $row['total_Carier_Driver_Rate'];
    $OTR = $CR * 0.024;
    $profit = $CR - $DR - $OTR;
    $workingdays = getWorkingDays($first_of_current_month, $today, '');
    $AVG_Profit = $profit / $workingdays;
    $t_pace_data[] = round($AVG_Profit * 22, 0);
}
$t_pace_label = json_encode($t_pace_label);
$t_pace_data = json_encode($t_pace_data);

// Current User Load Count
$i_query = "SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, 
    COUNT(*) AS amount, dispatcher AS D
    FROM newload
    where dispatcher='$username' 
    GROUP BY unique_date
    ORDER BY unique_date DESC";
// Current User Load Count
$i_LC = $mysqli->query($i_query) or die($mysqli->error);
$i_LC_labels = [];
$i_LC_data = [];
$c_LC_total = 0;
foreach ($i_LC as $iLC) {
    $i_LC_labels[] = $iLC['unique_date'];
    $i_LC_data[] = $iLC['amount'];
    $c_LC_total += $iLC['amount'];
}
$i_LC_labels = json_encode($i_LC_labels);
$i_LC_data = json_encode($i_LC_data);


// Team Load Count
$t_query = "SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, 
    SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    COUNT(*) AS amount, dispatcher AS D
    FROM newload
    where  DATE(created)='$today'
    GROUP BY unique_date
    ORDER BY unique_date DESC";
// Team Load Count
$t_LC = $mysqli->query($t_query) or die($mysqli->error);
$t_LC_labels = [];
$t_DN_label = [];
$t_LC_data = [];
$t_TP_data = [];
$t_AP_data = [];
$t_APL_data = [];
$t_TL = 0;
$t_TP = 0;
$t_AP = 0;
$t_APL = 0;
$x = 1;
foreach ($t_LC as $tLC) {
    $x++;
    $t_LC_labels[] = $tLC['unique_date'];
    $t_DN_label[] = $tLC['D'];
    $t_LC_data[] = $tLC['amount'];
    $t_TL += $tLC['amount'];
    $CR = $tLC['toal_Customer_Rate'];
    $DR = $tLC['total_Carier_Driver_Rate'];
    $OTR = $CR * 0.024;
    $t_TP_data[] = ($CR - $DR - $OTR);
    $t_AP_data[] = ($CR - $DR - $OTR) / 22;
    $t_APL_data[] = ($CR - $DR - $OTR) / $t_TL;
    $t_TP += ($CR - $DR - $OTR);
    $t_AP += (($CR - $DR - $OTR) / 22) / $x++;
    $t_APL += (($CR - $DR - $OTR) / $t_TL) / $x++;
}
$t_LC_labels = json_encode($t_LC_labels);
$t_DN_label = json_encode($t_DN_label);
$t_LC_data = json_encode($t_LC_data);
$t_AP_data = json_encode($t_AP_data);
$t_TP_data = json_encode($t_TP_data);
$t_TL = round($t_TL);
$t_TP = round($t_TP);
$t_AP = round($t_AP);
$t_APL = round($t_APL);


// Goal Today 
$goal_left_to_achieve_query = "SELECT * from goals where goal_status = 'Active' group by user order by id";
// $goal_left_to_achieve_query = "SELECT  *
//     FROM (SELECT id, goal, timeline, goal_status, user,
//     ROW_NUMBER() OVER (PARTITION BY user ORDER BY id desc) AS RowNumber
//     FROM   goals WHERE  goal_status = 'Active') AS a
//     WHERE   a.RowNumber = 1";

$goal_left_to_achieve = $mysqli->query($goal_left_to_achieve_query) or die($mysqli->error);
$team_remaining_target_profit_today = 0;
$t_r_Tar_pro_today = 0;
$team_target_profit_today = 0;
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

    // Total Target profit today
    if ($row['timeline'] == 'monthly') {
        $team_target_profit_today += round($row['goal'] / 22, 0);
    } elseif ($row['timeline'] == 'weekly') {
        $team_target_profit_today += round($row['goal'] / 5, 0);
    } elseif ($row['timeline'] == 'daily') {
        $team_target_profit_today += round($row['goal'], 0);
    }

    // Total Profit achieved by the current user
    // For Num chart
    if ($row['timeline'] == 'monthly') {
        $t_r_Tar_pro_today += round($row['goal'] / 22, 0) - $team_total_profit_today;
    } elseif ($row['timeline'] == 'weekly') {
        $t_r_Tar_pro_today += round($row['goal'] / 5, 0) - $team_total_profit_today;
    } elseif ($row['timeline'] == 'daily') {
        $t_r_Tar_pro_today += round($row['goal'], 0) - $team_total_profit_today;
    }

    // For bar chart
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


?>