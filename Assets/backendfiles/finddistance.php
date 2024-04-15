<?php

include './config.php';

$query = "SELECT * from truck_details where lat IS NOT NULL";
$data = $mysqli->query($query) or die($mysqli->error);

$i = 0;

foreach($data as $d){
    if(!empty($d['lat'])){
        $distance = distance(40.1017582, -88.2753144, $d['lat'], $d['lng']);
        $id = $d['truck_id'];
        $loc = $d['city'];
        if($distance <= 300){
            echo "Location found within range: id : $id,    : Location : $loc ,   : distance : $distance <br><br>";
            $i++;
        }
    };
};
if($i == 0){
    echo "No Locations found within Range please either expend radius or search another location";
};
echo $i;
// lat 41.5868353
// lng -93.6249593
function distance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $km = $miles * 1.609344;
    return $miles;
}

// print_r($data);