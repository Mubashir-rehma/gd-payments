<?php

include './Assets/backendfiles/charts/headerCharts.php';
include './Assets/backendfiles/config.php';
include './Assets/backendfiles/notification.php';

$query = "SELECT * FROM truck_details where Status='on_Hold'";
$truck_hol = $mysqli->query($query) or die($mysqli->error);

?>


<!---------------------- Jquery & AJAX  -------------------->
<!--=================== Icons =================-->
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<script src="https://kit.fontawesome.com/b554f1d9a7.js" crossorigin="anonymous"></script>

<!--=================== Select2 Library =========================-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

<!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script> -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> -->
<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
</script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
    integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous">
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"
    integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous">
</script>

<!------------------ Data Table  ------------------------------->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>


<link rel="shortcut icon" href="./Assets/Images/WhatsApp Image 2022-05-16 at 2.20 1.png" type="image/x-icon">

<!--------------------- Custom CSS  -------------------->
<link rel="stylesheet" href="./Assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.0/Chart.bundle.min.js"></script>
<?php // $truck_hold = $mysqli->query("SELECT * From truck_details where Status = 'on_hold'") or die($mysqli->error); 
?>
<style>
.pin {
    position: relative;
    display: inline-block;
    height: 20px;
    min-width: 20px;
    padding: 0 10px 0 2px;
    margin-left: 20px;
    color: <?php echo "#fff"?>;
    font-size: 13px;
    /* font-family: Arial; */
    text-align: center;
    line-height: 34px;
    text-transform: uppercase;
    border-radius: 4px;
    /* box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.28); */
}

.pin-green {
    background-color: <?php echo "#74e1aa"?>;
}

/* the tip */

.pin::before {
    content: "";
    position: absolute;
    right: -5px;
    top: 2.5px;
    height: 16px;
    width: 14px;
    background-color: <?php echo "#74e1aa"?>;
    border-radius: 4px;
    z-index: -1;
    transform: rotate(45deg);
}

/* hide the left box-shadow */

.pin::after {
    content: "";
    position: absolute;
    right: 0;
    top: 2px;
    height: 15px;
    width: 2px;
    background-color: <?php echo "#74e1aa"?>;
}

nav li ul {
    margin-left: 15px;
    margin-top: 10px;
}

nav li ul li {
    cursor: pointer;
    border-radius: 5px;
    padding: 5px 6px !important;
}

nav li ul li:hover {
    background-color: var(--button);
}

.dis_tab td {
    font-size: 10px !important;
}
</style>

<nav class="main-menu" id="main-menu">

    <ul>

        <li>
            <a href="index.php">
                <img id="newload" src="" class="menu-icon" alt="">
                <span class="nav-text">GDS</span>
            </a>
        </li>

        <li>
            <a href="driveravailability.php">
                <img id="stats" src="" class="menu-icon" alt="">
                <span class="nav-text">GD Payment</span>
            </a>
        </li>

        
</nav>

<div class="mobile_menu">
    <div id="mobile_menu_btn"><img src="./Assets/Images/lightmenu.png" alt="menu" srcset=""></div>
</div>

<header>
    <ul>
        <div style="display: flex;">
            <a class="logo" href="#" style="margin: -7px 30px 0 0;"><img
                    src="./Assets/Images/WhatsApp Image 2022-05-16 at 2.20 1.png" alt="" srcset=""></a>
            <li>
                <h1 style="margin-top: -5px;">
                    <?php if (isset($page_title)) {
                        echo $page_title;
                    } else {
                        echo "Web Page";
                    }; ?>
                </h1>
            </li>
            <li style="margin-left: 50px;">
                <h1 style="margin-top: -5px;" class="chart_button">Today Stats</h1>
            </li>
        </div>

        <div id="container">
            <div class="content">
                <div class="achievement">
                    <div class="bar">
                        <div class="img-box"></div>
                        <div class="progress login"></div>
                        <p> <span class="login-counter"
                                id="loads_done"><?php echo round($userachievedgoal, 0);  ?></span> / <span
                                id="total_loads"><?php echo round($dailygoal, 0) ?> </span> Profit</p>
                    </div>
                </div>

            </div>
        </div>



        <li id="profile">

          
            <div class="notification_container">
                <div class="not_icon_con">
                    <img src="" alt="" class="notification_icon">
                    <span class="not_count">0</span>
                </div>

            
            </div>

            <i class="uil uil-moon change-theme" id="theme-button"></i>

            <div class="headerprofile">
                <div class="profiletoggle" onclick="profiletoggle()">
                    <img src="./Assets/Images/PngItem_1468479.png" alt="" width="25" height="25">
                    <!--<i class="fa-solid fa-circle-user" style="width: 55px;"></i> -->
                </div>

                <div class="profiledetails">
                    <div class="userdetail">
                        <img src="./Assets/images/PngItem_1468479.png" alt="" width="25" height="25">
                        <p>Hi,
                            <?php
                            if (isset($_SESSION['myLastName']) & !empty($_SESSION['myLastName'])) {
                                echo $_SESSION['myLastName'];
                            } else if (isset($_SESSION['myFirstName']) & !empty($_SESSION['myFirstName'])) {
                                echo $_SESSION['myFirstName'];
                            }
                            ?>
                        </p>
                    </div>
                    <div class="profileitems">
                        <div class="profileItem">
                            <i class="fa-solid fa-circle-user" style="margin-right: 10px;"></i>
                            <a href="profileview.php">Profile</a>
                        </div>
                        <div class="profileItem">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 10px;"></i>
                            <a href="logout.php">logout</a>
                        </div>
                    </div>

                </div>
            </div>
        </li>
    </ul>
</header>


<div class="not_alert_container">
</div>

<div class="container-chart">
    <div class="chart-container">
        <div class="continer-title dark">
            <h2>Today Stats</h2>
        </div>

        <div class="charts">
            <div class="chart-group">
                <div class="chart">
                    <div class="num-chart u_today_stats bold">
                        <h3>Total Loads</h3>
                        <p><?php echo $i  ?></p>
                    </div>

                    <div class="num-chart u_today_stats light">
                        <h3>Total Profit</h3>
                        <p>$ <?php echo $userachievedgoal ?></p>
                    </div>
                </div>
                <div class="chart">
                    <div class="num-chart u_today_stats light">
                        <h3>Pace</h3>
                        <p>$ <?php echo $current_user_pace;  ?></p>
                    </div>

                    <div class="num-chart u_today_stats bold">
                        <h3>Best Month</h3>
                        <p>$ 4742.74</p>
                    </div>
                </div>
            </div>

            <div class="single-chart" style="width: 78%;">
                <div class="chart-detail">
                    <h5>All Time Load Count</h5>
                    <h5>Total: $ <?php echo $c_LC_total ?></h5>
                </div>
                <canvas class="dis-chart" id="all_time_load_count"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-container">
        <div class="continer-title">
            <h2>Today Stats for team</h2>
        </div>

        <div class="charts">
            <div class="chart-group">
                <div class="chart">
                    <div class="num-chart bold">
                        <h3>Total Loads Today</h3>
                        <p><?php echo $t_TL ?></p>
                    </div>

                    <div class="num-chart light">
                        <h3>Total Profit</h3>
                        <p>$ <?php echo $t_TP ?></p>
                    </div>
                </div>
                <div class="chart">
                    <div class="num-chart light">
                        <h3>Goal Today</h3>
                        <p>$ <?php echo $team_target_profit_today ?></p>
                    </div>

                    <div class="num-chart bold">
                        <h3>Left to get the goal</h3>
                        <p>$ <?php echo $t_r_Tar_pro_today ?></p>
                    </div>
                </div>

            </div>

            <div class="single-chart">
                <!-- <div class="chart-detail">
                    <h5>Goal Left to achieve by team</h5>
                    <h5>Total: $ <?php // echo $t_r_Tar_pro_today 
                                    ?></h5>
                </div>
                <canvas class="dis-chart" id="cur_goal_left"></canvas> -->

                <div class="chart-detail">
                    <h5>Loads Today</h5>
                    <h5>Total: <?php echo $t_TL ?></h5>
                </div>
                <canvas class="dis-chart" id="loads_today"></canvas>
            </div>
        </div>

        <div class="charts">
            <div class="chart bg_chart">
                <!-- <div class="chart-detail">
                    <h5>Daily Goal Left to achieve by team</h5>
                    <h5>Total: $ 4742</h5>
                </div>
                <canvas class="dis-chart" id="daily_goal_left_to_achieve_by_team"></canvas> -->

                <div class="chart-detail">
                    <h5>Average Per day</h5>
                    <h5>Total: $ <?php echo $t_AP ?></h5>
                </div>
                <canvas class="dis-chart" id="avg_per_day"></canvas>
            </div>

            <div class="chart sm_chart">
                <!-- <div class="chart-detail">
                    <h5>Loads Today</h5>
                    <h5>Total: 8</h5>
                </div>
                <canvas class="dis-chart" id="loads_today"></canvas> -->

                <div class="chart-detail">
                    <h5>Profit Today</h5>
                    <h5>Total: $ <?php echo $t_TP ?></h5>
                </div>
                <canvas class="dis-chart" id="profit_today"></canvas>
            </div>
        </div>

        <div class="charts">
            <!-- <div class="chart sm_chart"> -->
            <!-- <div class="chart-detail">
                    <h5>Average Per day</h5>
                    <h5>Total: $ 4742</h5>
                </div>
                <canvas class="dis-chart" id="avg_per_day"></canvas> -->


            <!-- </div> -->

            <div class="chart bg_chart">
                <!-- <div class="chart-detail">
                    <h5>Comparsion Vs best Month</h5>
                    <h5>Total: 8</h5>
                </div>
                <canvas class="dis-chart" id="com_vs_best"></canvas> -->

                <div class="chart-detail">
                    <h5>Pace</h5>
                </div>
                <canvas class="dis-chart" id="pace"></canvas>
            </div>
        </div>

        <!-- <div class="charts"> -->
        <!-- <div class="chart bg_chart"> -->
        <!-- <div class="chart-detail">
                    <h5>Profit Today</h5>
                    <h5>Total: $ 4742</h5>
                </div>
                <canvas class="dis-chart" id="profit_today"></canvas> -->
        <!-- </div> -->

        <!-- <div class="chart sm_chart"> -->
        <!-- <div class="chart-detail">
                    <h5>Pace</h5>
                    <h5>Total: 8</h5>
                </div>
                <canvas class="dis-chart" id="pace"></canvas> -->
        <!-- </div> -->
        <!-- </div> -->
    </div>
</div>


<!-------------- Chart Js  -------------------->
<script src="./Assets/js/charts/config.js"></script>
<script src="./Assets/js/header.js"></script>
<script>
//Stop watch
var holdid = [
    <?php
        if (!empty($truck_hol)) {
            $i = 0;
            foreach ($truck_hol as $row) {
                $givenTime = $row['Holdtime'];

                // Convert the given time to a DateTime object
                $givenDateTime = new DateTime($givenTime);


                // Get the current time as a DateTime object
                $currentDateTime = new DateTime("now", new DateTimeZone('America/New_York'));
                // print($currentDateTime->format('m-d-y h:i A'));


                // Calculate the time difference
                $timeDifference = $givenDateTime->diff($currentDateTime);

                // Extract minutes and seconds from the time difference
                $hours = $timeDifference->h;
                $minutes = $timeDifference->i;
                $seconds = $timeDifference->s;
                $time = 14 - $minutes;
                $time = $hours > 0  && $time > 0 ? -$time : $time;
                $seconds = $hours > 0 || $time < 0 ? -$seconds : $seconds;

                $i++;
                if ($row['Status'] == 'on_Hold') {
                    echo '{id : "truckhold' . $row['truck_id'] . '", hours :' . -$hours . ', mins: ' . $time . ", sec : " . $seconds . "}, ";
                }
            }
        }
        ?>
];


var all_time_load_count = {
    type: 'line',
    data: {
        labels: <?php echo $i_LC_labels ?>,
        datasets: [{
            data: <?php echo $i_LC_data ?>,
            backgroundColor: 'rgba(254,206,0,0.4)',
            borderColor: 'rgba(254,206,0,1)',
            fill: true,
            tension: 0.5,
            opacity: 0.5,
            borderWidth: 1,
        }]
    },
    options: headeroption
};
var all_time_load_count_element = document.getElementById("all_time_load_count")
new Chart(all_time_load_count_element, all_time_load_count);

// Loads Today
var loads_today = {
    type: 'bar',
    data: {
        labels: <?php echo $t_DN_label ?>,
        datasets: [{
            data: <?php echo $t_LC_data ?>,
            backgroundColor: bg1,
        }]
    },
    options: headeroption
};
var loads_today_element = document.getElementById("loads_today")
new Chart(loads_today_element, loads_today);



// Average Per day
var avg_per_day = {
    type: 'bar',
    data: {
        labels: <?php echo $t_DN_label ?>,
        datasets: [{
            data: <?php echo $t_AP_data ?>,
            backgroundColor: bg1,
        }]
    },
    options: headeroption
};
var avg_per_day_element = document.getElementById("avg_per_day")
new Chart(avg_per_day_element, avg_per_day);


// Comparsion Vs Best Month
// var com_vs_best = {
//     type: 'bar',
//     data: {
//         labels: ['sam', 'mubashir', 'waqas', 'akhter', 'sulema'],
//         datasets: [{
//             data: [10, 5, 11, 7, 8],
//             backgroundColor: bg1,
//         }]
//     },
//     options: headeroption
// };
// var com_vs_best_element = document.getElementById("com_vs_best")
// new Chart(com_vs_best_element, com_vs_best);


// Profit Today
var profit_today = {
    type: 'bar',
    data: {
        labels: <?php echo $t_DN_label ?>,
        datasets: [{
            data: <?php echo $t_TP_data ?>,
            backgroundColor: bg1,
        }]
    },
    options: headeroption
};
var profit_today_element = document.getElementById("profit_today")
new Chart(profit_today_element, profit_today);


// Pace
var pace = {
    type: 'bar',
    data: {
        labels: <?php echo $t_pace_label ?>,
        datasets: [{
            data: <?php echo $t_pace_data ?>,
            backgroundColor: bg1,
        }]
    },
    options: headeroption
};
var pace_element = document.getElementById("pace")
new Chart(pace_element, pace);
</script>

<!----------------------- main js  -------------------------->
<script src="./Assets/js/main.js"></script>

<script>
var element = document.getElementsByClassName('chart_button')[0];

element.addEventListener("mouseover", function() {
    document.getElementsByClassName('container-chart')[0].style.display = "block";
});

element.addEventListener("mouseout", function() {
    document.getElementsByClassName('container-chart')[0].style.display = "none";
});


// Toggle Buton
function profiletoggle() {
    var x = document.getElementsByClassName("profiledetails")[0];
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}

// Mobile Menu Button
$("#mobile_menu_btn").on('click', function(e) {
    var menu = document.getElementById("main-menu")
    var profile = document.getElementById("profile")
    var mobile_menu_btn = document.getElementById("mobile_menu_btn")

    if (menu.style.display == "none") {
        $(this).parent()[0].style = "box-shadow: none;"
        profile.style.display = "flex"
        menu.style.display = "flex"
    } else {
        profile.style.display = "none"
        menu.style.display = "none"
    }

})


// Progress bar
let progress = document.querySelectorAll("button");

let i = 0;
let setSelection = document.querySelector("." + "login");
let getCounter = document.querySelector("." + "login" + "-counter").textContent;
let totals = document.getElementById("total_loads").textContent;;
let prcnt = (getCounter / totals) * 100 + "%";

setSelection.classList.add("running");
document.querySelector("#container").classList.add("running");

setTimeout(() => {
    setSelection.classList.remove("running");
    document.querySelector("#container").classList.add("running");
}, 1000);

setSelection.style.width = prcnt;
</script>