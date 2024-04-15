<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- <link rel="shortcut icon" href="./Assets/Images/WhatsApp Image 2022-05-16 at 2.20 1.png" type="image/x-icon"> -->
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

$gd = $mysqli->query("SELECT * FROM gd_pay") or die($mysqli->error);
$gds = mysqli_fetch_array($gd);

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


    <?php $page_title = "GD Payments";
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


    <div class="indexstats dispatchercharts">

        <div class="statusbar_Section" id="undelivered_load_bars">
        <div class="autocomplete" style="display: flex;justify-content: space-between;margin-right: 0rem;margin-bottom: 1.875rem;">
            <!-- <select class="js-example-basic-single" name="state">
                <option value="AL">Alabama</option>
                <option value="WY">Wyoming</option>
                </select> -->
                <input id="myInput" type="text" name="myCountry" placeholder="Add GD number here" style="width: 100%;margin-right: 0rem;">
            </div>
            <div class="truckNumber" id="truckedit">
                <div class="modal-content">
                    <!-- <span class="close">&times;</span> -->
                    <div class="modal-header newloadHeader">
                        <h2 class="modal-title">New GD Payments</h2>
                    </div>

                    <div class="modal-body">
                        <form id="driver_info_form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="gdid"
                                value="<?php echo !empty($truckdetail['truck_id']) ? $truckdetail['truck_id'] : ''; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <p class="form-control-static">GD #:
                                            <span id="gdnoText"> <?php echo !empty($static_values['gd']) ? $static_values['gd'] : '77'; ?></span>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <p class="form-control-static">Total Amount:
                                            <span id="totalAmounttext"><?php echo !empty($static_values['total_amount']) ? $static_values['total_amount'] : '7000'; ?></span>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <p class="form-control-static">Status:
                                            <span id="Statustext"><?php echo !empty($static_values['status']) ? $static_values['status'] : 'Opened'; ?></span>
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
                <div class="load_stats" style="margin-left: 0;">
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

var data = [
                <?php while($row = mysqli_fetch_array($gd)){
                    echo "'" .$row['GD_number']. "', ";
                } ?>
            ]

            
            function autocomplete(inp, arr) {
                /*the autocomplete function takes two arguments,
                the text field element and an array of possible autocompleted values:*/
                var currentFocus;
                /*execute a function when someone writes in the text field:*/
                inp.addEventListener("input", function(e) {
                    var a, b, i, val = this.value;
                    /*close any already open lists of autocompleted values*/
                    closeAllLists();
                    if (!val) {
                        return false;
                    }
                    // console.log(val)
                    currentFocus = -1;
                    /*create a DIV element that will contain the items (values):*/
                    a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    /*append the DIV element as a child of the autocomplete container:*/
                    this.parentNode.appendChild(a);
                    /*for each item in the array...*/
                    for (i = 0; i < arr.length; i++) {
                        /*check if the item starts with the same letters as the text field value:*/
                        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                            /*create a DIV element for each matching element:*/
                            b = document.createElement("DIV");
                            /*make the matching letters bold:*/
                            b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                            b.innerHTML += arr[i].substr(val.length);
                            /*insert a input field that will hold the current array item's value:*/
                            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                            /*execute a function when someone clicks on the item value (DIV element):*/
                            b.addEventListener("click", function(e) {
                                /*insert the value for the autocomplete text field:*/
                                val = this.getElementsByTagName("input")[0].value
                                inp.value = val;
                                $.ajax({
                                    type: 'get',
                                    url: './Assets/backendfiles/gd_pay.php?record='+ val, // Replace 'your_php_script.php' with the path to your PHP script
                                    data: {gd: val},
                                    success: function(response) {
                                        var data =JSON.parse(response)
                                        console.log(data)
                                        $("#gdnoText").html(data.GD_number)
                                        // $("#amount_paid").html(data.PaidAmount)
                                        $("#totalAmounttext").html(data.TotalAmount)
                                        $("#Statustext").html(data.Status)
                                        $("#gdid").val(data.id)
                                        // Handle the response here, if needed
                                        // window.location.href = 'gd_payments.php';
                                        // Optionally, you can display a success message or perform other actions
                                    },
                                    error: function(xhr, status, error) {
                                        // Handle errors here, if any
                                        console.error(xhr.responseText); // Log the error message to the console
                                        // Optionally, you can display an error message or perform other actions
                                    }
                                });

                                console.log(val)
                                /*close the list of autocompleted values,
                                (or any other open lists of autocompleted values:*/
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    }
                });
                /*execute a function presses a key on the keyboard:*/
                inp.addEventListener("keydown", function(e) {
                    var x = document.getElementById(this.id + "autocomplete-list");
                    if (x) x = x.getElementsByTagName("div");
                    if (e.keyCode == 40) {
                        /*If the arrow DOWN key is pressed,
                        increase the currentFocus variable:*/
                        currentFocus++;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 38) { //up
                        /*If the arrow UP key is pressed,
                        decrease the currentFocus variable:*/
                        currentFocus--;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 13) {
                        /*If the ENTER key is pressed, prevent the form from being submitted,*/
                        e.preventDefault();
                        if (currentFocus > -1) {
                            /*and simulate a click on the "active" item:*/
                            if (x) x[currentFocus].click();
                        }
                    }
                });

                function addActive(x) {
                    /*a function to classify an item as "active":*/
                    if (!x) return false;
                    /*start by removing the "active" class on all items:*/
                    removeActive(x);
                    if (currentFocus >= x.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = (x.length - 1);
                    /*add class "autocomplete-active":*/
                    x[currentFocus].classList.add("autocomplete-active");
                }

                function removeActive(x) {
                    /*a function to remove the "active" class from all autocomplete items:*/
                    for (var i = 0; i < x.length; i++) {
                        x[i].classList.remove("autocomplete-active");
                    }
                }

                function closeAllLists(elmnt) {
                    /*close all autocomplete lists in the document,
                    except the one passed as an argument:*/
                    var x = document.getElementsByClassName("autocomplete-items");
                    for (var i = 0; i < x.length; i++) {
                        if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                        }
                    }
                }
                /*execute a function when someone clicks in the document:*/
                document.addEventListener("click", function(e) {
                    // console.log(e.target)
                    closeAllLists(e.target);
                });
            }

            autocomplete(document.getElementById("myInput"), data);

            $("#myInput").on("change", function(e){
                console.log($(this).val())
            })


        // var dispSummaryelement = document.getElementById("dis-summary")
        //         // disSummary.defaults.font.size = 8;
        //         var disSummary = new Chart(dispSummaryelement, config);

        // var config3 = {
        //     type: 'doughnut',
        //     data: {
        //         labels: [ 'No label'
        //             // <?php
        //             // $labels = $mysqli->query("SELECT SUM(TotalAmount) AS total_amount FROM gd_pay") or die($mysqli->error);
        //             // while ($row = mysqli_fetch_array($labels)) {
        //             //     echo "'" . $row['TotalAmount'] . "', ";
        //             //     $dispatcher = $row['TotalAmount'];
        //             // }
        //             // ?>
        //         ],
        //         datasets: [{
        //             data: [ 88,5
        //                 // <?php

        //                 // $data = $mysqli->query("SELECT SUM(TotalAmount) AS total_amount FROM gd_pay") or die($mysqli->error);
        //                 // while ($row = mysqli_fetch_array($data)) {
        //                 //     echo $row['total_amount'] . ",";
        //                 // }

        //                 // ?>
        //             ],
        //             backgroundColor: [
        //                 "#F94144",
        //                 "#F3722C",
        //             ],
        //             borderWidth: 0.5,

        //         }]
        //     },
        //     options: {
        //         responsive: false,
        //         plugins: {
        //             legend: {
        //                 display: false,
        //                 labels: {

        //                     // This more specific font property overrides the global property
        //                     font: {
        //                         size: 8,

        //                     }
        //                 }
        //             }
        //         },
        //         // legend: {
        //         //     display: false
        //         // },
        //         scales: {
        //             x: {
        //                 grid: {
        //                     display: false,
        //                 },
        //                 ticks: {
        //                     color: "#aaaa",
        //                     font: {
        //                         size: 8
        //                     }
        //                 },
        //                 display: false,
        //             },
        //             y: {
        //                 grid: {
        //                     display: false
        //                 },
        //                 ticks: {
        //                     color: "#aaaa",
        //                     font: {
        //                         size: 8
        //                     }
        //                 },
        //                 display: false,
        //             },
        //         }
        //     }
        // };
        // var dispPieSummaryelement = document.getElementById("today-goal-left")
        // // disSummary.defaults.font.size = 8;
        // var disPieSummary = new Chart(dispPieSummaryelement, config3);

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