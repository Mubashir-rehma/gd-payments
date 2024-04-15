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
        $('a[tab-toggle="tab"]').on('shown.bs.tab', function(e) {
            var activeTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', activeTab);
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

// Count for partially paid status
$partially_paid_query = $mysqli->query("SELECT COUNT(*) AS partially_count
    FROM gdpays
    JOIN gd_pay ON gdpays.id = gd_pay.gpid
    WHERE gdpays.status = 'partially_paid';
") or die($mysqli->error);
$partially_paid_data = mysqli_fetch_assoc($partially_paid_query);
$partially_paid = $partially_paid_data['partially_count'];

// Count for paid status
$paid_query = $mysqli->query("SELECT COUNT(*) AS paid_count
    FROM gdpays
    JOIN gd_pay ON gdpays.id = gd_pay.gpid
    WHERE gdpays.status = 'paid';
") or die($mysqli->error);
$paid_data = mysqli_fetch_assoc($paid_query);
$paid = $paid_data['paid_count'];

// Count for payable status
$payable_query = $mysqli->query("SELECT COUNT(*) AS payable_count
    FROM gdpays
    JOIN gd_pay ON gdpays.id = gd_pay.gpid
    WHERE gdpays.status = 'Payable';
") or die($mysqli->error);
$payable_data = mysqli_fetch_assoc($payable_query);
$payable = $payable_data['payable_count'];


$query = 'SELECT SUM(TotalAmount) AS total_amount FROM gd_pay';
$result = $mysqli->query($query);

$query1 = 'SELECT SUM(total_paid) AS total_paid FROM gdpays';
$result1 = $mysqli->query($query1);



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
    include('header.php'); ?>

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
            <canvas class="dis-chart" id="dis-summary" style="display: block;box-sizing: border-box;height: 150px !important;"></canvas>
        </div>
        <div class=" dis-charts detail">
            <p>Dispatcher Details</p>
            <canvas class="chart" id="disDetail" style="display: block;box-sizing: border-box;height: 150px !important;"></canvas>
        </div>
        <div class="dis-charts" style="width: 200px">
            <p>Dispatcher Summary</p>
            <canvas class="chart" id="disp-pie-summary" style="width: 150px !important;"></canvas>
        </div>
    </div>

    <div class="indexstats dispatchercharts">

        <div class="statusbar_Section" id="undelivered_load_bars">
            <div style="display: flex;justify-content: space-between;margin-right: 30px;margin-bottom: 30px;">
                <input onkeyup="search()" style="width: 72%;margin-right: 20px;" type="search" name="search_loadBars" id="search_loadBars" placeholder="search...">
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
                            <input type="hidden" name="id" value="<?php echo !empty($truckdetail['truck_id']) ? $truckdetail['truck_id'] : ''; ?>">

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
                                        <input type="date" class="form-control" name="gddate" id="gddate" placeholder="123456789" value="<?php echo !empty($truckdetail['engineNumber']) ? $truckdetail['engineNumber'] : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="amount_paid">Amount Paid</label>
                                        <input type="text" class="form-control" name="amount_paid" id="amount_paid" placeholder="6700" value="<?php echo !empty($truckdetail['weight']) ? $truckdetail['weight'] : ''; ?>">
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
                <a href="#Paid" class="nav-link" class="nav-link"><span class="title">Paid</span>
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
                <a href="#Payable" class="nav-link" class="nav-link"><span class="title">Payable</span>
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
        <div class="table tabcontent active" id="payable" style=" display: block;">
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

        <div class="table tabcontent" id="partially_paid">
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

        <div class="table tabcontent" id="paid">
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
        $(document).ready(function() {
            // Have the previously selected tab open
            var activeTab = sessionStorage.activeTab;
          
            $(".tab-content").fadeIn(1000);
            if (activeTab) {
                $('.tab-content ' + activeTab).show().siblings().hide();
                // also make sure you your active class to the corresponding tab menu here
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").parent().addClass('active').siblings().removeClass('active');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').addClass("active_span").parent().parent().siblings().children().children().removeClass('active_span');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').children().addClass("active_span").parent().parent().parent().siblings().children().children().children().removeClass('active_span');
            } else {
                activeTab = "#Dashboard";
                $('.tab-content ' + activeTab).show().siblings().hide();
                // also make sure you your active class to the corresponding tab menu here
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").parent().addClass('active').siblings().removeClass('active');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').addClass("active_span").parent().parent().siblings().children().children().removeClass('active_span');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').children().addClass("active_span").parent().parent().parent().siblings().children().children().children().removeClass('active_span');
            }

            // Enable, disable and switch tabs on click
            $('.navbar .menu li a').on('click', function(e) {
                var currentAttrValue = $(this).attr('href');
                console.log("currentAttrValue", currentAttrValue)
                // Show/Hide Tabs
                $('.tab-content ' + currentAttrValue).fadeIn(2000).siblings().hide();
                sessionStorage.activeTab = currentAttrValue;

                // Change/remove current tab to active
                $(this).parent('li').addClass('active').siblings().removeClass('active');
                $('.navbar .menu li a span').removeClass('active_span').removeClass('active_span2');
                $(this).children().addClass('active_span');
                e.preventDefault();
            });

        });
        // var dispSummaryelement = document.getElementById("dis-summary")
        //         // disSummary.defaults.font.size = 8;
        //         var disSummary = new Chart(dispSummaryelement, config);

        var config3 = {
            type: 'doughnut',
            data: {
                labels: ['No label'
                    // <?php
                        // $labels = $mysqli->query("SELECT SUM(TotalAmount) AS total_amount FROM gd_pay") or die($mysqli->error);
                        // while ($row = mysqli_fetch_array($labels)) {
                        //     echo "'" . $row['TotalAmount'] . "', ";
                        //     $dispatcher = $row['TotalAmount'];
                        // }
                        // 
                        ?>
                ],
                datasets: [{
                    data: [88, 5
                        // <?php

                            // $data = $mysqli->query("SELECT SUM(TotalAmount) AS total_amount FROM gd_pay") or die($mysqli->error);
                            // while ($row = mysqli_fetch_array($data)) {
                            //     echo $row['total_amount'] . ",";
                            // }

                            // 
                            ?>
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