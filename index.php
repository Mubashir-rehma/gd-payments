<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <script>
        $('a[tab-toggle="tab"]').on('shown.bs.tab', function(e) {
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
// include './Assets/js/charts/charts.php';

use function GuzzleHttp\Psr7\str;

session_start();

include './Assets/backendfiles/config.php';

$openstatus = $mysqli->query("SELECT count(Status) FROM gd_pay where Status='opening'") or die($mysqli->error);
$openstatusData = mysqli_fetch_assoc($openstatus);
$open = $openstatusData['count(Status)'];

$postedstatus = $mysqli->query("SELECT count(Status) FROM gd_pay where Status='posted'") or die($mysqli->error);
$postedstatusData = mysqli_fetch_assoc($postedstatus);
$posted = $postedstatusData['count(Status)'];

$matchedstatus = $mysqli->query("SELECT count(Status) FROM gd_pay where Status='bs_matched'") or die($mysqli->error);
$matchedstatusData = mysqli_fetch_assoc($matchedstatus);
$matched = $matchedstatusData['count(Status)'];

$gd = $mysqli->query("SELECT * FROM gd_pay") or die($mysqli->error);
$gds = mysqli_fetch_array($gd);

if (isset($_GET['id'])) {
    // Retrieve the ID from the URL
    $loadID = $_GET['id'];
    $query = "SELECT * FROM gd_pay WHERE id = $loadID";
    $result = $mysqli->query($query);
    print_r($result);
}
$query = 'SELECT SUM(TotalAmount) AS total_amount FROM gd_pay';
$result = $mysqli->query($query);
$query1 = 'SELECT SUM(total_paid) AS total_paid FROM gdpays';
$result1 = $mysqli->query($query1);
?>




<body style="background-color: var(--body);">


    <?php $page_title = "GDs";
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

    <div class="indexstats dispatchercharts">

        <div class="statusbar_Section" id="undelivered_load_bars">

            <div class="autocomplete" style="display: flex;justify-content: space-between;margin-right: 0rem;margin-bottom: 1.875rem;">
                <!-- <select class="js-example-basic-single" name="state">
                <option value="AL">Alabama</option>
                <option value="WY">Wyoming</option>
                </select> -->
                <input id="myInput" type="text" name="myCountry" placeholder="Add GD number here" style="width: 100%;margin-right: 0rem;">
            </div>

            <div style="display: flex;justify-content: space-between;margin-right: 1.875rem;margin-bottom: 1.875rem;">
                <input type="none " onkeyup="search()" style="width: 72%;margin-right: 1.25rem; display: none" type="search" name="search_loadBars" id="search_loadBars" placeholder="search...">
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
                        <h2 class="modal-title">New GD</h2>
                    </div>

                    <div class="modal-body">
                        <form id="driver_info_form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="GDbank">GD Bank Date</label>
                                        <input type="date" class="form-control" name="gdbank" id="gdbank" placeholder="08/04/2024">
                                    </div>

                                    <div class="form-group">
                                        <label for="totalamount">Total Amount</label>
                                        <input type="text" class="form-control" name="total_amount" id="total_amount" placeholder="4454">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="GD#">GD #</label>
                                        <input type="text" class="form-control" name="gdno" id="gdno" placeholder="123456789">
                                    </div>

                                    <div class="form-group">
                                        <label for="amountpaid">Amount Paid</label>
                                        <input type="text" class="form-control" name="amount_paid" id="amount_paid" placeholder="6700">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="truckstatus">Status</label>
                                        <select class="form-control" name="status" id="status">

                                            <option value="opening">Opening</option>
                                            <option value="bs_matched">BS Matched</option>
                                            <option value="posted">Posted</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="formbuttons">
                                <button type="reset" value="Cancel" name="reset" class="cancel">Reset</button>
                                <button type="submit" value="Submit" name="payment" class="submit">+ Payment</button>
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

            <div class="chart chart-shadow" style="margin-top: 1.25rem;">
                <div class="prog-pie-chart">
                    <h3>Left to Get the Goal</h3>
                    <canvas class="chart" id="today_goal_left"></canvas>
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
                <a href="#posted" class="nav-link" class="nav-link"><span class="title">Posted</span>
                    <span>
                        <?php echo $posted ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="shipped" src="" alt="" srcset="">
                <a href="#bs_matched" class="nav-link" class="nav-link"><span class="title"> BS Matched</span>
                    <span>
                        <?php echo $matched ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="route" src="" alt="">
                <a href="#opening" class="nav-link" class="nav-link"><span class="title">Opening</span>
                    <span>
                        <?php echo $open ?>
                    </span>
                </a>

            </li>



        </ul>

    </div>
    <div class=outer-circle></div>

    <div class="tab-content">
        <div class="table tabcontent active" id="opening" style=" display: block;">
            <table id="table1" style="width: 100%;">
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

        <div class="table tabcontent" id="posted">
            <table id="table2" style="width: 100%;">
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

        <div class="table tabcontent" id="bs_matched">
            <table id="table3" style="width: 100%;">
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

        <?php
        $query = 'SELECT SUM(TotalAmount) AS total_amount FROM gd_pay';
        $result = $mysqli->query($query);
        $totalAmount = $result->fetch_assoc()['total_amount'];

        $query1 = 'SELECT SUM(total_paid) AS total_paid FROM gdpays';
        $result1 = $mysqli->query($query1);
        $totalPaid = $result1->fetch_assoc()['total_paid'];

        $remainingAmount = $totalAmount - $totalPaid;

        ?>

    </div>


    <script src="./Assets/js/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.css"> -->
    <script src="https://cdn.jsdelivr.net/gh/stefanpenner/es6-promise@master/dist/es6-promise.min.js"></script>
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=fetch"></script>
    <script>
        $(document).ready(function() {

            // Have the previously selected tab open
            var activeTab = sessionStorage.activeTab;
            $(".tab-content").fadeIn(1000);
            if (activeTab) {
                console.log("active tab", activeTab)
                $('.tab-content ' + activeTab).show().siblings().hide();
                // also make sure you your active class to the corresponding tab menu here
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").parent().addClass('active').siblings(); // NaNpxoveClass('active');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').addClass("active_span").parent().parent().siblings().children().children(); // NaNpxoveClass('active_span');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').children().addClass("active_span").parent().parent().parent().siblings().children().children().children(); // NaNpxoveClass('active_span');
            } else {
                activeTab = "#Dashboard";
                $('.tab-content ' + activeTab).show().siblings().hide();
                // also make sure you your active class to the corresponding tab menu here
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").parent().addClass('active').siblings(); // NaNpxoveClass('active');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').addClass("active_span").parent().parent().siblings().children().children(); // NaNpxoveClass('active_span');
                $(".menu li a[href=" + "\"" + activeTab + "\"" + "]").children('span').children().addClass("active_span").parent().parent().parent().siblings().children().children().children(); // NaNpxoveClass('active_span');
            }

            // Enable, disable and switch tabs on click
            $('.navbar .menu li a').on('click', function(e) {
                var currentAttrValue = $(this).attr('href');
                console.log("currentAttrValue", currentAttrValue)
                // Show/Hide Tabs
                $('.tab-content ' + currentAttrValue).fadeIn(2000).siblings().hide();
                sessionStorage.activeTab = currentAttrValue;

                // Change/remove current tab to active
                $(this).parent('li').addClass('active').siblings(); // NaNpxoveClass('active');
                $('.navbar .menu li a span'); // NaNpxoveClass('active_span'); // NaNpxoveClass('active_span2');
                $(this).children().addClass('active_span');
                e.preventDefault();
            });


            var totalAmount = <?php echo $totalAmount; ?>;
            var remainingAmount = <?php echo $remainingAmount; ?>;

            var today_goal_left = {
                type: 'doughnut',
                data: {
                    labels: ['Total Amount', 'Amount left to the Goal'],
                    datasets: [{
                        data: [totalAmount, remainingAmount],
                        backgroundColor: [
                            "#fece00",
                            // "ffff00",
                            "#ffe200"
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
                                font: {
                                    size: 8,
                                }
                            }
                        }
                    },
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

            var dispPieSummaryelement = document.getElementById("today_goal_left");
            new Chart(dispPieSummaryelement, today_goal_left);

            var data = [
                <?php while ($row = mysqli_fetch_array($gd)) {
                    echo "'" . $row['GD_number'] . "', ";
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
                                    url: './Assets/backendfiles/gd_pay.php?record=' + val, // Replace 'your_php_script.php' with the path to your PHP script
                                    data: {
                                        gd: val
                                    },
                                    success: function(response) {
                                        var data = JSON.parse(response)
                                        console.log(data)
                                        $("#gdno").val(data.GD_number)
                                        $("#amount_paid").val(data.PaidAmount)
                                        $("#total_amount").val(data.TotalAmount)
                                        $("#status").val(data.Status)
                                        $("#gdbank").val(data.Gd_bankDate)
                                        $("#id").val(data.id)
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

            $("#myInput").on("change", function(e) {
                console.log($(this).val())
            })

        });

        $("#loadBoard1, #loadBoard2, #loadBoard3").on("click", ".driver_info_form", function(e) {
            var loadID = $(this).data("load_id");
            console.log("loadID index:", loadID)
            $.ajax({
                type: "get", // or "GET" depending on your backend implementation
                url: './Assets/backendfiles/gd_pay.php?gdrecord=' + loadID, // Replace 'your_php_script.php' with the path to your PHP script
                data: {
                    gd: loadID // Sending loadID as the 'record' parameter
                },
                success: function(response) {
                    console.log("Response:", response)
                    var data = JSON.parse(response);
                    console.log(data);
                    $("#gdno").val(data.GD_number);
                    $("#amount_paid").val(data.PaidAmount);
                    $("#total_amount").val(data.TotalAmount);
                    $("#status").val(data.Status);
                    $("#gdbank").val(data.Gd_bankDate);
                    $("#id").val(data.id);
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
        });



        // Search functionality
        function search() {
            var input, filter, ul, li, p, i, txtValue;
            input = document.getElementById('search_loadBars');
            console.log("input", input)
            filter = input.value.toUpperCase();
            console.log("filter", filter)

            getGds(filter);
            console.log("getGds", getGds(filter))

            ul = document.getElementById("undelivered_load_bars");
            console.log("ul", ul)
            li = ul.getElementsByClassName('undelivered_load_bars');
            console.log("li", li)

            for (i = 0; i < li.length; i++) {
                li[i].style.display = "block"; // Show all items initially
                console.log("li", li[i])
            }







            try {
                const response = $.ajax({
                    url: './Assets/backendfiles/gd_pay.php',
                    type: 'GET',
                    dataType: 'json'
                });
                return response;
            } catch (error) {
                console.error('Request failed:', error);
                return null;
            }
        }

        $(document).ready(function() {
            $('#driver_info_form').submit(function(event) {
                // Prevent the default form submission
                event.preventDefault();

                var formId = $('#id').val();
                console.log("Form ID:", formId);


                // Serialize the form data
                var formData = $(this).serialize();
                // Send the form data via AJAX
                $.ajax({
                    type: 'POST',
                    url: './Assets/backendfiles/gd_pay.php?uid=' + formId, // Replace 'your_php_script.php' with the path to your PHP script
                    data: formData,
                    success: function(response) {
                        // console.log("Response:", response)
                        fetchloadrows(
                            ["opening", "posted", "bs_matched"],
                            ["table1", "table2", "table3"]
                        );

                    },
                    error: function(xhr, status, error) {
                        // Handle errors here, if any
                        console.error(xhr.responseText); // Log the error message to the console
                        // Optionally, you can display an error message or perform other actions
                    }
                });
            });


        });

        $(document).ready(function(){
    $('.submit, .cancel').prop('disabled', true).css('cursor', 'not-allowed');
    $('#gdno').keyup(function(){
        var isEmpty = $(this).val().length === 0;
        $('.submit, .cancel').prop('disabled', isEmpty).css('cursor', isEmpty ? 'not-allowed' : 'pointer');
    });
});
    </script>


</body>

</html>