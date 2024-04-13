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

// if (!empty($sessData['status']['msg'])) {
//     $statusMsg = $sessData['status']['msg'];
//     $statusMsgType = $sessData['status']['type'];
//     unset($_SESSION['sessData']['status']);
// }



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

if (isset($_GET['id'])) {
    // Retrieve the ID from the URL
    $loadID = $_GET['id'];

    // Now you can use $loadID in your PHP code
    echo "The ID is: " . $loadID;
    $query = "SELECT * FROM gd_pay WHERE id = $loadID";
    $result = $mysqli->query($query);
    print_r($result);
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




    <div class="indexstats dispatchercharts">

        <div class="statusbar_Section" id="undelivered_load_bars">

            <div class="autocomplete" style="display: flex;justify-content: space-between;margin-right: 30px;margin-bottom: 30px;">
                <input id="myInput" type="text" name="myCountry" placeholder="Country" style="width: 72%;margin-right: 20px;">
            </div>

            <div style="display: flex;justify-content: space-between;margin-right: 30px;margin-bottom: 30px;">
                <input type="none " onkeyup="search()" style="width: 72%;margin-right: 20px; display: none" type="search" name="search_loadBars" id="search_loadBars" placeholder="search...">
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
                        <form id="driver_info_form" class="driver_info_form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="GDbank">GD Bank Date</label>
                                        <input type="date" class="form-control" name="gdbank" id="gdbank" placeholder="08/04/2024"  >
                                    </div>

                                    <div class="form-group">
                                        <label for="totalamount">Total Amount</label>
                                        <input type="text" class="form-control" name="total_amount" id="total_amount" placeholder="4454">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="GD#">GD #</label>
                                        <input type="text" class="form-control" name="gdno" id="gdno" placeholder="123456789" 
                                    >
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
                <div class="load_stats">
                    <div class="content" style="text-align: center;">
                        <p>Total Amount</p>
                        <h2 style="color:green"><?php echo '0' ?></h2>
                    </div>
                </div>
                <div class="load_stats">

                    <div class="content" style="text-align: center;">
                        <p>Total Paid</p>
                        <h2 style="color:red"><?php echo '0' ?></h2>
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
                <a href="#posted" class="nav-link" class="nav-link"><span class="title">Posted</span>
                    <span>
                        <?php echo $posted  ?>
                    </span>
                </a>

            </li>
            <li class="nav-item">
                <img id="shipped" src="" alt="" srcset="">
                <a href="#bs_matched" class="nav-link" class="nav-link"><span class="title"> BS Matched</span>
                    <span>
                        <?php echo $matched  ?>
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


    </div>







    <script src="./Assets/js/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.10.1/ol.min.css">
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
        // $(document).on("submit", "form#driver_info_form", function (e) { // Changed form selector
        //     e.preventDefault();
        //     var formData = new FormData(this);

        //     $.ajax({
        //       url: "./Assets/backendfiles/gd_pay.php?action_type=newgd", // Corrected URL path
        //       type: "POST",
        //       data: formData,
        //       success: function (data) {
        //         data = JSON.parse(data)[0];
        //         if (data.success == 1) {
        //           fetchloadrows(["opening"], ["table1"]);
        //         }
        //       },
        //       cache: false,
        //       processData: false,
        //     });
        //   });

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


            // Call getGds() function with the search query


        }




        $(document).ready(function() {
            $('#driver_info_form').submit(function(event) {
                // Prevent the default form submission
                event.preventDefault();

                // Serialize the form data
                var formData = $(this).serialize();

                // Send the form data via AJAX
                $.ajax({
                    type: 'POST',
                    url: './Assets/backendfiles/gd_pay.php', // Replace 'your_php_script.php' with the path to your PHP script
                    data: formData,
                    success: function(response) {
                        // Handle the response here, if needed
                        window.location.href = 'gd_payments.php';
                        // Optionally, you can display a success message or perform other actions
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here, if any
                        console.error(xhr.responseText); // Log the error message to the console
                        // Optionally, you can display an error message or perform other actions
                    }
                });
            });
        });

        var Data = [];

        // function fetchData(callback) {
        //     $.ajax({
        //         url: './Assets/backendfiles/gd_pay.php',
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function(response) {

        //             return response
        //             // Call the callback function with the response data
        //             callback(response);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Request failed with status ' + status);
        //             // Optionally, call the callback with null or an error message
        //             callback(null);
        //         }
        //     });
        // }

        // // Call fetchData with a callback function to handle the response
        // fetchData(function(response) {
        //     if (response) {
        //         // Handle the response data here
        //         Data = response;
        //         console.log("Data:", Data);
        //     } else {
        //         // Handle the case where the request fails or returns empty data
        //         console.log("Failed to fetch data.");
        //     }
        // });

        function filterByGDNumber(data, gdNumber) {
            return data.filter(function(record) {
                return record.GD_number === gdNumber;
            });
        }

        console.log("proper data:", Data)
        // Define a variable to store the filtered data
        var filteredData = []; // Initialize filteredData as an empty array

        // Define the callback function to handle the response
        function handleResponse(response) {
            if (response) {
                // Handle the response data here
                var Data = response;
                console.log("Data:", Data);

                // Extract GD_number values
                filteredData = Data.map(function(item) {
                    return item.GD_number;
                });
                console.log("Filtered Data:", filteredData);
            } else {
                // Handle the case where the request fails or returns empty data
                console.log("Failed to fetch data.");
            }
        }

        // Call fetchData with the callback function to handle the response
        fetchData(handleResponse);

        async function fetchDat() {
            try {
                const response = await $.ajax({
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

        var co;
        // Usage
        function f() {

            (async () => {
                try {
                    const data = await fetchDat();
                    if (data !== null) {
                        // Process the data here
                        console.log("data", data);
                        return data
                        co = data; // Store data in variable for future use
                    } else {
                        // Handle the error or null response
                        console.error('Failed to fetch data');
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            })();
        }

        console.log("co", f())


        // // Call the fetchData function to initiate the request
        // fetchData();

        // console.log("fetchData", fetchData())

        <?php while ($row = $result->fetch_assoc()) {
            print($row['GD_number']);
            echo $row['GD_number'];
        } ?>

        var c = [<?php while ($row = $result->fetch_assoc()) {
                        print($row['GD_number']);
                        echo $row['GD_number'];
                    } ?>]

        // var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua &amp; Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia &amp; Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre &amp; Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts &amp; Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad &amp; Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks &amp; Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];

        // function c() {
        //     var d = []
        //     $.ajax({
        //         url: './Assets/backendfiles/gd_pay.php',
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function(response) {
        //             d = response
        //             return response
        //             // Call the callback function with the response data
        //             // callback(response);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Request failed with status ' + status);
        //             // Optionally, call the callback with null or an error message
        //             // callback(null);
        //         }
        //     });

        //     return d
        // }

        var countries = () => {
            $.ajax({
                url: './Assets/backendfiles/gd_pay.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    return response
                    // Call the callback function with the response data
                    callback(response);
                },
                error: function(xhr, status, error) {
                    console.error('Request failed with status ' + status);
                    // Optionally, call the callback with null or an error message
                    callback(null);
                }
            });
        } // filteredData
        console.log("countries", c())

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
                            inp.value = this.getElementsByTagName("input")[0].value;
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
                closeAllLists(e.target);
            });
        }

        $("#myInput").on("keyup", function() {
            console.log(co)
        })

        if (co) {
            console.log(co)
            autocomplete(document.getElementById("myInput"), countries);
        }
    </script>


</body>

</html>