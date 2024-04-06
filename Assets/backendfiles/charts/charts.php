<?php

include '../config.php';

if (($_REQUEST['action_type'] == "team_load_count_dis_filter")) {
    $dispatcher = $_POST['dispatcher'];

    $query = "SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, 
    SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    COUNT(*) AS amount, dispatcher AS D
    FROM newload
    where dispatcher='$dispatcher'
    GROUP BY unique_date
    ORDER BY unique_date DESC";

    if(!empty($_GET['startDate'])){
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];

        $query = "SELECT  DISTINCT (DATE(timeStamp)) AS unique_date, 
            SUM(Customer_Rate) as toal_Customer_Rate, 
            sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
            COUNT(*) AS amount, dispatcher AS D
            FROM newload
            where dispatcher='$dispatcher'
            and DATE(created)>='$startDate' and  DATE(created)<='$endDate'
            GROUP BY unique_date
            ORDER BY unique_date DESC";
    }
    
    $mysql = $mysqli->query($query) or die($mysqli->error);

    $data = [];
    $i_TL = 0;
    $i_TP = 0;
    $i_AP = 0;
    $i_APL = 0;
    $i = 1;
    foreach ($mysql as $row){
        $i_LC_labels = $row['unique_date'];
        $i_LC_data = $row['amount'];
        $i_TL += $row['amount'];
        $CR = $row['toal_Customer_Rate'];
        $DR = $row['total_Carier_Driver_Rate'];
        $OTR = $CR * 0.024;
        $i_TP_data = round(($CR - $DR - $OTR), 0);
        $i_AP_data = round(($CR - $DR - $OTR) / 22, 0);
        $i_APL_data = round(($CR - $DR - $OTR) / $i_TL, 0);
        $i_TP += ($CR - $DR - $OTR);
        $i_AP += (($CR - $DR - $OTR) / 22) / $i++;
        $i_APL += (($CR - $DR - $OTR) / $i_TL) / $i++;

        $data[] = [
            'TL' => round($i_TL), 
            'TP' => round($i_TP), 
            'AP' => round($i_AP), 
            'APL' => round($i_APL), 
            'label' => $i_LC_labels, 
            'LC_data' => $i_LC_data,
            'TP_data' => $i_TP_data,
            'AP_data' => $i_AP_data,
            'APL_data' => $i_APL_data
        ];

        $i_TL = 0;
        $i_TP = 0;
        $i_AP = 0;
        $i_APL = 0;
    }
    echo json_encode($data);


} elseif(($_REQUEST['action_type'] == "time_base_TF")){
    $dispatcher = $_POST['dispatcher'];


    $dis_time_base_query = "SELECT
    EXTRACT(MONTH FROM created) as month, 
    EXTRACT(YEAR FROM created) as year, 
    SUM(Customer_Rate) as toal_Customer_Rate, 
    sum(Carier_Driver_Rate) as total_Carier_Driver_Rate, 
    count(dispatcher) as total_loads ,
    dispatcher
    FROM newload
    Where dispatcher='$dispatcher'
    GROUP BY month, year, dispatcher
    ORDER BY year DESC, month ASC";
    $dis_time_base = $mysqli->query($dis_time_base_query) or die($mysqli->$error);

    $data = [];
    foreach ($dis_time_base as $row) {
        $tl = $row['total_loads'];
        $customer = $row['toal_Customer_Rate'];
        $driver = $row['total_Carier_Driver_Rate'];
        $OTR = $row['toal_Customer_Rate'] * 0.024;
        $profit = round(($customer - $driver - $OTR), 0);
        $APL = round($profit / $tl, 0);
        $Adpm = round($profit / 22, 0);

        $data[] = [
            'month' => date("F", mktime(0, 0, 0, $row['month'], 10)),
            'TL' => $row['total_loads'],
            'TP' => $profit,
            'AP' => $APL,
            'ADP' => $Adpm,
        ];
    }
    echo json_encode($data);
}


?>