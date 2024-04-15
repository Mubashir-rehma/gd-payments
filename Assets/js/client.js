$(document).ready(function() {

    var table = $('#myTable').DataTable({
        "bStateSave": true,
        // rowReorder: {
        //     selector: 'td:nth-child(2)'
        // },
        responsive: true
    });

    $("#truckeditbtn").on('click', function(e) {
        if ($("#brokerDetails")[0].style.display === "none") {
            $("#brokerDetails")[0].style.display = "block"
        } else {
            $("#brokerDetails")[0].style.display = "none"
        }
    })

    $(".close").on('click', function(e) {
        var modalDisplay = $(this).parent().parent()[0].style.display = "none"
    })

    $('.cancel').on('click', function(e) {
        $(this).parent().parent()[0].reset();
        $(this).parent().parent().parent().parent().parent()[0].style.display = "none"
    })
});


// Autocompalete
function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
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
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

$("#brokercompany").on("click", function(e){
    $.ajax({
        method: "GET",
        url: "./Assets/backendfiles/index.php?action_type=brokercompany_list",
        data: {
        },
        success: function(data) {
            data = JSON.parse(data)[0]
            if (data.success == 1) {
                autocomplete(document.getElementById("brokercompany"), data.brokers);
            }
        }
    });
})

$("#brokersubmit").on("click", function(e){
    e.preventDefault();

    $(this).html("Adding...")
    $(this).style = "background-color: #dadada;"
    $(this).attr('disabled', 'disabled');

    $.ajax({
        type: "POST",
        url: "./Assets/backendfiles/client.php?action_type=broker_submit",
        data: $("#broker_form").serialize(),
        success: function(data) {
            data = JSON.parse(data)[0]
            console.log(data.rows)
            if(data.success == "1"){
                setTimeout(function(){
                    // var style = 'style="display: block; background-color: #7dc77d;"'
                    // var msg = data.msg
                    // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
                    // $('#alertmsg_container').append(msgdiv)

                    $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
                    $("#alert_msg").html(data.msg);
                    $("#broker_form")[0].reset();

                    $('#myTable').DataTable().destroy();
                    $("#brokers_tableBody").html(data.rows);
                    $('#myTable').DataTable().draw();

                    $("#brokersubmit").html("Submit")
                    $("#brokersubmit").style = "background-color: #dadada;"
                    $("#brokersubmit").removeAttr('disabled', 'disabled');

                    $("#alert_msg").delay(30000).hide(500);


                }, 1000);

            } else {
                // var style = 'style="display: block; background-color: #7dc77d;"'
                // var msg = data.msg
                // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
                // $('#alertmsg_container').append(msgdiv)

                $("#alert_msg").css({display: "block", backgroundColor: "#ce6c6c"})
                $("#alert_msg").delay(30000).hide(500);
                $("#alert_msg").html(data.msg);
                $("#brokersubmit").html("Submit")
                $("#brokersubmit").style = "background-color: #dadada;"
                $("#brokersubmit").removeAttr('disabled', 'disabled');
                
            }
        }
    });
})

$("#brokers_tableBody").on('click', '.broker_edit, .broker_delete', function(e){
    var id = $(this).data("broker_id")
    var action_type = $(this).data("action_type")

    var request = true

    if(action_type == "broker_delete"){
        var confirmed = confirm('Are you sure to delete Broker?')

        if(confirmed) {
            request = true
        }else{
            request = false
        }
    }

    if(request){
        $(".loader")[0].style.display = "flex"
        $.ajax({
            type: "GET",
            url: "./Assets/backendfiles/client.php?action_type=" + action_type + "&id=" + id ,
            data: id,
            success: function(data) {
                data = JSON.parse(data)[0]
                if(data.success == "1"){
                    // var style = 'style="display: block; background-color: #7dc77d;"'
                    // var msg = data.msg
                    // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
                    // $('#alertmsg_container').append(msgdiv)

                    // console.log($(this).data("action_type"))
                    // console.log($("#broker_id"))

                    if(action_type == "broker_edit"){
                        $("#broker_id")[0].value = data.broker_id
                        $("#brokercompany")[0].value = data.broker_company
                        $("#brokerName")[0].value = data.brokerName
                        $("#brokeremail")[0].value = data.brokeremail
                        $("#brokerphone")[0].value = data.brokerphone
                        $("#brokerAddress")[0].value = data.brokerAddress
                        $("#brokercity")[0].value = data.brokercity
                        $("#brokerState")[0].value = data.brokerState
                        $("#notesprivate")[0].value = data.brokernotes

                        $("#brokerDetails")[0].style.display = "block";

                        // $("#broker_form")[0].reset();
                    } else {
                        $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
                        $("#alert_msg").html(data.msg);

                        $('#myTable').DataTable().destroy();
                        $("#brokers_tableBody").html(data.rows);
                        $('#myTable').DataTable().draw();

                        $("#alert_msg").delay(30000).hide(500);
                    }

                    $(".loader")[0].style.display = "none"

                    // $("#brokersubmit").html("Submit")
                    // $("#brokersubmit").style = "background-color: #dadada;"
                    // $("#brokersubmit").removeAttr('disabled', 'disabled');

                } else {
                    // var style = 'style="display: block; background-color: #7dc77d;"'
                    // var msg = data.msg
                    // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
                    // $('#alertmsg_container').append(msgdiv)

                    $("#alert_msg").css({display: "block", backgroundColor: "#ce6c6c"})
                    $("#alert_msg").delay(30000).hide(500);
                    $("#alert_msg").html(data.msg);
                    $("#brokersubmit").html("Submit")
                    $("#brokersubmit").style = "background-color: #dadada;"
                    $("#brokersubmit").removeAttr('disabled', 'disabled');
                    
                }
            }
        });
    }
   
})

$("#brokers_tableBody").on('click', '.view_load_history', function(e){
    var id = $(this).data("broker_id")
    var action_type = $(this).data("action_type")
    console.log(id)
    console.log(action_type)

    $("#load_history")[0].style.display = "block";

    $(".loader")[0].style.display = "flex"
    $.ajax({
        type: "GET",
        url: "./Assets/backendfiles/client.php?action_type=" + action_type + "&id=" + id ,
        data: id,
        success: function(data) {
            console.log(data)
            data = JSON.parse(data)
            console.log(data)
            if(data.success == "1"){
                setTimeout(function(){

                    $("#load_history_Tbody").html(data.rows);

                    $(".loader")[0].style.display = "none"


                }, 1000);

            } else {

                $("#alert_msg").css({display: "block", backgroundColor: "#ce6c6c"})
                $("#alert_msg").delay(30000).hide(500);
                $("#alert_msg").html(data.msg);
                $("#brokersubmit").html("Submit")
                $("#brokersubmit").style = "background-color: #dadada;"
                $("#brokersubmit").removeAttr('disabled', 'disabled');
                
            }
        }
    });

})