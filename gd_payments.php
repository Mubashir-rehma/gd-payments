<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="./Assets/Images/WhatsApp Image 2022-05-16 at 2.20 1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <script>
        $('a[tab-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('#tabs a[href="' + activeTab + '"]').tab('show');
        }
    </script>

    <title>GD Payments</title>
</head>

<?php
include './Assets/js/charts/charts.php';

use function GuzzleHttp\Psr7\str;

session_start();

$sessData = !empty($_SESSION['sessData']) ? $_SESSION['sessData'] : '';

// Get status message from session 
if (!empty($sessData['status']['msg'])) {
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Get posted data from session 
if (!empty($sessData['postData'])) {
    $postData = $sessData['postData'];
    unset($_SESSION['sessData']['postData']);
}



include './Assets/backendfiles/config.php';

$openstatus = $mysqli->query("SELECT count(status) FROM gdpays where status='partially_paid'") or die($mysqli->error);
$openstatusData = mysqli_fetch_assoc($openstatus);
$partially_paid = $openstatusData['count(status)'];

$postedstatus = $mysqli->query("SELECT count(status) FROM gdpays where status='Paid'") or die($mysqli->error);
$postedstatusData = mysqli_fetch_assoc($postedstatus);
$paid = $postedstatusData['count(status)'];

$matchedstatus = $mysqli->query("SELECT count(status) FROM gdpays where status='Payable'") or die($mysqli->error);
$matchedstatusData = mysqli_fetch_assoc($matchedstatus);
$payable = $matchedstatusData['count(status)'];

$query = 'SELECT SUM(TotalAmount) AS total_amount FROM gd_pay';
$result = $mysqli->query($query);

$query1 = 'SELECT SUM(total_paid) AS total_paid FROM gdpays';
$result1 = $mysqli->query($query1);
// if (!empty($_GET['id'])) {
//     $id = $_GET['id'];
//     $newloaddata = $mysqli->query("SELECT * FROM newload n 
//     LEFT OUTER JOIN truck_details AS t ON t.truck_id = n.truck_Number 
//     LEFT OUTER JOIN broker_details AS b ON b.broker_id = n.Broker 
//     LEFT OUTER JOIN (select *, max(callid) from newcheckcalls 
//     GROUP BY newloadID) C ON C.newloadID = n.id where n.id = '$id'
//     ORDER BY id DESC limit 1") or die($mysqli->error);

//     $newloaddata = mysqli_fetch_assoc($newloaddata);
// };


// $address = $mysqli->query("SELECT * FROM address") or die($mysqli->error);
// $dest = $mysqli->query("SELECT * FROM address") or die($mysqli->error);
// $truck_details = $mysqli->query("SELECT * FROM truck_details") or die($mysqli->error);
// $broker_details = $mysqli->query("SELECT * FROM broker_details GROUP BY broker_company") or die($mysqli->error);
// $users = $mysqli->query("SELECT user_name FROM users") or die($mysqli->error);

// if (!empty($_GET['callid'])) {
//     $callid = $_GET['callid'];
//     $checkcalls = $mysqli->query("SELECT * FROM newcheckcalls where callid=$callid") or die($mysqli->error);
//     $checkcall = mysqli_fetch_assoc($checkcalls);
// }


// // Pre-filled data 
// $newloadData = !empty($postData) ? $postData : $newloadData;

// // Define action 
// $actionLabel = !empty($_GET['id']) ? 'Update' : 'Add';

// $load_en_routestatus = $mysqli->query("SELECT count(status) FROM newload where status='load_en_route'") or die($mysqli->error);
// $load_en_routestatusData = mysqli_fetch_assoc($load_en_routestatus);
// $load_en_route = $load_en_routestatusData['count(status)'];

// $load_deliveredstatus = $mysqli->query("SELECT count(status) FROM newload where status='load_delivered'") or die($mysqli->error);
// $load_deliveredstatusData = mysqli_fetch_assoc($load_deliveredstatus);
// $load_delivered = $load_deliveredstatusData['count(status)'];

// $load_issuestatus = $mysqli->query("SELECT count(status) FROM newload where status='load_issue'") or die($mysqli->error);
// $load_issuestatusData = mysqli_fetch_assoc($load_issuestatus);
// $load_issue = $load_issuestatusData['count(status)'];

// $load_invoicedstatus = $mysqli->query("SELECT count(status) FROM newload where status='load_invoiced'") or die($mysqli->error);
// $load_invoicedstatusData = mysqli_fetch_assoc($load_invoicedstatus);
// $load_invoiced = $load_invoicedstatusData['count(status)'];

// $load_paidstatus = $mysqli->query("SELECT count(status) FROM newload where status='load_paid'") or die($mysqli->error);
// $load_paidstatusData = mysqli_fetch_assoc($load_paidstatus);
// $load_paid = $load_paidstatusData['count(status)'];

// $load_factored = $mysqli->query("SELECT count(status) FROM newload where status='load_Factored'") or die($mysqli->error);
// $load_factoredData = mysqli_fetch_assoc($load_factored);
// $load_factored = $load_factoredData['count(status)'];

// // Number of total loads delivered today
// $today = date('Y-m-d');
// $loads_delivered_todayQuery = $mysqli->query("SELECT count(status) FROM newload where DATE(delivery_date)='$today'") or die($mysqli->error);
// $loads_delivered_todayQueryData = mysqli_fetch_assoc($loads_delivered_todayQuery);
// $loads_delivered_today = $loads_delivered_todayQueryData['count(status)'];

// $disQuery = $mysqli->query("SELECT dispatcher,count(dispatcher)  AS count_me FROM newload WHERE dispatcher IS NOT NULL GROUP BY dispatcher ORDER BY COUNT(dispatcher) DESC") or die($mysqli->error);
// $disData = mysqli_fetch_array($disQuery);


// $status_bar_query = "select * from newload as n 
// left outer join 
// (select * from load_tracking T where exists (select * from (select load_id, max(tracking_id) as tracking_id
//     from load_tracking tt 
//     group by Load_pickup_location, load_Destination
//     ) as tt where T.tracking_id = tt.tracking_id)) T 
//     on n.id = T.load_id 
// LEFT OUTER JOIN truck_details AS t 
//     ON t.truck_id = n.truck_Number 
// LEFT OUTER JOIN broker_details AS b 
//     ON b.broker_id = n.Broker 
// LEFT OUTER JOIN 
//     (select * from newcheckcalls C where exists (select * from (select newloadID, max(callid) as callid
//     from newcheckcalls cc 
//     GROUP BY newloadID
//     ) as cc where C.callid = cc.callid)) C on n.id = C.newloadID 
// where n.status = 'load_en_route'
// order by n.id desc";
// $status_bar = $mysqli->query($status_bar_query) or die($mysqli->error);
// foreach($status_bar as $row){
//     print("  lID:   ". $row['id'] . "  TID:  " . $row['tracking_id'] . "  LPU:  " . $row['Pick_up_Location'] . "  TPU:   " . $row['Load_pickup_location'] . "  LDES:  " . $row['Destination'] . "   TDES:  " .$row['load_Destination'] . "  Ttotal DIS:   " . $row['total_distance'] . "  TC DIS:   " .$row['current_distace'] . "<br>");
// };

// function loadstatusbars($mysqli)
// {
//     $query = "select * from newload where status <> 'load_delivered' order by id desc";
//     $data = $mysqli->query($query) or die($mysqli->error);

//     foreach ($data as $row) {
//         $PU = unserialize($row['Pick_up_Location']);
//         $des = unserialize($row['Destination']);
//         $dis = unserialize($row['distance']);
//         $time = unserialize($row['time']);
//         $id = $row['id'];

//         $count = count(is_countable($PU) ? $PU : []);

//         print("count:   " .  $count  . "  /PU:  " . $row['Pick_up_Location'] . "  /des:  " . $row['Destination'] . "   /dis:  " . $row['distance'] . "  /time:  " . $row['time'] . "<br>");

//         for ($i = 0; $i < $count; $i++) {
//             $PU = $PU[$i];
//             $des = $des[$i];
//             $dis = $dis[$i];
//             $time = $time[$i];

//             $tquery = "select * from load_tracking where load_id='$id' and load_Destination='$des' and Load_pickup_location='$PU' order by tracking_id desc limit 1";
//             $tdata = $mysqli->query($tquery) or die($mysqli->error);

//             foreach ($tdata as $t) {
//                 // print("  lID:   " . $id . "  TID:  " . $t['tracking_id'] . "  LPU:  " . $PU . "  TPU:   " . $t['Load_pickup_location'] . "  LDES:  " . $des . "   TDES:  " . $t['load_Destination'] . "  Ttotal DIS:   " . $dis . "  TC DIS:   " . $t['current_distace'] . "   T CL:  " . $t['current_location'] . "<br>");
//             }
//         };
//     };
// }



function status($status, $value)
{
    echo $status == $value ? "selected" : '';
}

function floatvalue($val)
{
    $val = str_replace(",", ".", $val);
    $val = preg_replace('/\.(?=.*\.)/', '', $val);
    return floatval($val);
}
?>




<body style="background-color: var(--body);">


    <?php $page_title = "Load Board";
    include ('header.php'); ?>

    <!-- <div id="alertmsg_container">
        <div id="alert_msg" class="alert_msg"><span class="close_alrt_msg">x</span></div>
    </div> -->
    <div id="alert_msg" class="alert_msg"></div>

    <div class="loader" style="position: fixed;">
        <div class="loading">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>


    <!------------------- Chart Js  ----------------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.0/chart.js"></script>


    <div class="dispatchercharts" style="display: none;">
        <div class="dis-charts summary">
            <p>Dispatcher Summary</p>
            <canvas class="dis-chart" id="dis-summary"
                style="display: block;box-sizing: border-box;height: 150px !important;"></canvas>
        </div>
        <div class=" dis-charts detail">
            <p>Dispatcher Details</p>
            <canvas class="chart" id="disDetail"
                style="display: block;box-sizing: border-box;height: 150px !important;"></canvas>
        </div>
        <div class="dis-charts" style="width: 200px">
            <p>Dispatcher Summary</p>
            <canvas class="chart" id="disp-pie-summary" style="width: 150px !important;"></canvas>
        </div>
    </div>

    <div class="indexstats dispatchercharts">

        <div class="statusbar_Section" id="undelivered_load_bars">
            <div style="display: flex;justify-content: space-between;margin-right: 30px;margin-bottom: 30px;">
                <input onkeyup="search()" style="width: 72%;margin-right: 20px;" type="search" name="search_loadBars"
                    id="search_loadBars" placeholder="search...">
                <!-- <select name="search_loadBars_filter" style="width: 20%;">
                    <option value="load_num">Pro No.</option>
                    <option value="truck_num">Truck No.</option>
                    <option value="driver">Driver</option>
                    <option value="driver">Remaning Distance</option>
                    <option value="check_notes">Check Notes</option>
                </select> -->
            </div>
            <div class="truckNumber" id="truckedit">
                <div class="modal-content">
                    <!-- <span class="close">&times;</span> -->
                    <div class="modal-header newloadHeader">
                        <h2 class="modal-title">New GD Payments</h2>
                    </div>

                    <div class="modal-body">
                        <form id="driver_info_form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id"
                                value="<?php echo !empty($truckdetail['truck_id']) ? $truckdetail['truck_id'] : ''; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <p class="form-control-static">GD #:
                                            <?php echo !empty($static_values['gd']) ? $static_values['gd'] : '77'; ?>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <p class="form-control-static">Total Amount:
                                            <?php echo !empty($static_values['total_amount']) ? $static_values['total_amount'] : '7000'; ?>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <p class="form-control-static">Status:
                                            <?php echo !empty($static_values['status']) ? $static_values['status'] : 'Opened'; ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control" name="gddate" id="gddate"
                                            placeholder="123456789"
                                            value="<?php echo !empty($truckdetail['engineNumber']) ? $truckdetail['engineNumber'] : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="amount_paid">Amount Paid</label>
                                        <input type="text" class="form-control" name="amount_paid" id="amount_paid"
                                            placeholder="6700"
                                            value="<?php echo !empty($truckdetail['weight']) ? $truckdetail['weight'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="gdstatus">Status</label>
                                        <select class="form-control" name="gdpstatus" id="gdsstatus">
                                            <option value="partially_paid">Partailly Paid</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Payable">Payable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="formbuttons">
                                <button type="reset" value="Cancel" name="reset" class="cancel">Reset</button>
                                <button type="submit" value="Submit" name="truckstate" class="submit">Submit</button>

                            </div>
                        </form>

                    </div>
                </div>
            </div>


        </div>
        <div class="chartcontain" style="width: 35%;">
            <div class="amountcard">
                <div class="load_stats">
                    <div class="content" style="text-align: center;">
                        <p>Total Amount</p>
                        <h2 style="color:green"><?php if ($result) {
                            $row = $result->fetch_assoc();
                            $total_amount = $row['total_amount'];
                            echo "$ $total_amount"; // Output the total amount
                        } ?></h2>
                    </div>
                </div>
                <div class="load_stats">

                    <div class="content" style="text-align: center;">
                        <p>Total Paid</p>
                        <h2 style="color:red"><?php if ($result1) {
                            $row1 = $result1->fetch_assoc();
                            $total_paid = $row1['total_paid'];
                            echo "$ $total_paid"; // Output the total amount
                        } ?></h2>
                    </div>
                </div>
            </div>

            <div class="chart chart-shadow" style="margin-top: 20px;">
                <div class="prog-pie-chart">
                    <h3>Left to Get the Goal</h3>
                    <canvas class="chart" id="today-goal-left"></canvas>
                </div>

            </div>
        </div>

    </div>

    </div>

    <!-- Display status message -->
    <?php if (!empty($statusMsg)) { ?>
        <div class="col-xs-12" id="alert">
            <div class="alert alert-. $statusMsgType; ?>"><?php echo $statusMsg; ?></div>
        </div>
    <?php } ?>

    <div class="navbar tab-container">
        <ul class="menu tabs" style="padding: 0;">
            <li class="nav-item">
                <img id="issue" src="" alt="" srcset="">
                <a href="#paid" class="nav-link" class="nav-link"><span class="title">Paid</span>
                    <span>
                        <?php echo $paid ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="shipped" src="" alt="" srcset="">
                <a href="#partially_paid" class="nav-link" class="nav-link"><span class="title">Partially Paid</span>
                    <span>
                        <?php echo $partially_paid ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="route" src="" alt="">
                <a href="#payable" class="nav-link" class="nav-link"><span class="title">Payable</span>
                    <span>
                        <?php echo $payable ?>
                    </span>
                </a>

            </li>



        </ul>

        <!-- <li id="newloadformbtn">

            <button>+ New Load</button>
        </li> -->
    </div>
    <div class=outer-circle></div>

    <div class="tab-content">
        <div class="table tabcontent active" id="load_en_route" style=" display: block;">
            <table id="table4" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>GD No.</th>
                        <th>Bank Date</th>
                        <th>Amount</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard1">
                    <?php // tdata($load_en_routedata)  
                    ?>
                </tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_delivered">
            <table id="table5" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>GD No.</th>
                        <th>Bank Date</th>
                        <th>Amount</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard2"> <?php // tdata($load_delivereddata)  
                ?></tbody>

            </table>
        </div>

        <div class="table tabcontent" id="load_issue">
            <table id="table6" style="width: 100%;">
                <thead>
                    <tr style="background: none; text-align: center;width: 100%;">
                        <th>#</th>
                        <th>GD No.</th>
                        <th>Bank Date</th>
                        <th>Amount</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <div class="actions">
                            <th>Actions</th>
                        </div>

                    </tr>
                </thead>


                <tbody id="loadBoard3"><?php // tdata($load_issuedata)  
                ?></tbody>

            </table>
        </div>


    </div>







    <script src="./Assets/js/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.css">
    <script src="https://cdn.jsdelivr.net/gh/stefanpenner/es6-promise@master/dist/es6-promise.min.js"></script>
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=fetch"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>


        // var dispSummaryelement = document.getElementById("dis-summary")
        //         // disSummary.defaults.font.size = 8;
        //         var disSummary = new Chart(dispSummaryelement, config);

        var config3 = {
            type: 'doughnut',
            data: {
                labels: [ 'No label'
                    // <?php
                    // $labels = $mysqli->query("SELECT SUM(TotalAmount) AS total_amount FROM gd_pay") or die($mysqli->error);
                    // while ($row = mysqli_fetch_array($labels)) {
                    //     echo "'" . $row['TotalAmount'] . "', ";
                    //     $dispatcher = $row['TotalAmount'];
                    // }
                    // ?>
                ],
                datasets: [{
                    data: [ 88,5
                        // <?php

                        // $data = $mysqli->query("SELECT SUM(TotalAmount) AS total_amount FROM gd_pay") or die($mysqli->error);
                        // while ($row = mysqli_fetch_array($data)) {
                        //     echo $row['total_amount'] . ",";
                        // }

                        // ?>
                    ],
                    backgroundColor: [
                        "#F94144",
                        "#F3722C",
                    ],
                    borderWidth: 0.5,

                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                size: 8,

                            }
                        }
                    }
                },
                // legend: {
                //     display: false
                // },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        },
                        display: false,
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: "#aaaa",
                            font: {
                                size: 8
                            }
                        },
                        display: false,
                    },
                }
            }
        };
        var dispPieSummaryelement = document.getElementById("today-goal-left")
        // disSummary.defaults.font.size = 8;
        var disPieSummary = new Chart(dispPieSummaryelement, config3);

        // Search functionality
        function search() {
            // Declare variables
            var input, filter, ul, li, p, a, i, txtValue;
            input = document.getElementById('search_loadBars');
            filter = input.value.toUpperCase();
            ul = document.getElementById("undelivered_load_bars");
            li = ul.getElementsByClassName('undelivered_load_bars');
            filterindex = $("select[name='search_loadBars_filter'] option:selected").index()

            // Loop through all list items, and hide those who don't match the search query
            for (i = 0; i < li.length; i++) {
                p = li[i].getElementsByClassName("sa")[filterindex];

                txtValue = p.textContent || p.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }

            }
        }
    </script>


</body>

</html>