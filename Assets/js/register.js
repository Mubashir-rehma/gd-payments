$(document).ready(function() {

    var table = $('#myTable').DataTable({
        "bStateSave": true,
        responsive: true
    });

    // Add or update Users
    $("body").on('submit', "form#register_user", function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        var btnuser = document.getElementById("newuser");
        var pass = $("#pass1").val()
        var confirm_pass = $("#cnfrmpass").val()
        var submit = false

        var ser_data = {}; // Declare ser_data here
        
        // Deserialize or unserialize the formData into an array of objects
        var unserializedData = decodeURIComponent(formData)
            .split('&')
            .map(function(item) {
                var pair = item.split('=');
                return {
                    name: decodeURIComponent(pair[0]),
                    value: decodeURIComponent(pair[1])
                };
            });
        
        // Iterate through the unserialized data using .each()
        $.each(unserializedData, function(index, field) {
            ser_data[field.name] = field.value;
        
            // Select the associated error message element
            var errorElement = $("#" + field.name + "_msg");
        
            if (field.value === "") {
                // If the field is empty, display an error message
                errorElement.html("Field is required!");
            } else {
                // If the field is not empty, clear any existing error message
                errorElement.html("");
            }
        });
        
        
        

        // if((pass == null || confirm_pass == null) || ser_data['first_name'] == null ){
        //     submit == false
        // }
        if(pass == confirm_pass & (pass !== "" || confirm_pass !== "")){
            submit = true
            
        } 
    
        // console.log(formData);
        if(submit){
            $.ajax({
                method: "POST",
                url: "./Assets/backendfiles/register.php?action_type=newuseradd",
                data: formData,
                success: function(data) {
                    data = JSON.parse(data)
                    //console.log(data);
                    if (data.success == 1) {
                        $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
                        $("#alert_msg").html(data[1]);
                        $("#newuser").removeAttr('disabled', 'disabled');
                        $("#register_user")[0].reset();
                        $(".modal").hide()
    
                        btnuser.textContent = "Submit"
                        btnuser.style.backgroundColor = "var(--button)"
                        $("#alert_msg").delay(5000).hide(500);
    
                        $.ajax({
                            url: "./components/user_row.php",
                            success: function(data){
                                $('body #myTable').DataTable().destroy()
                                $("body #tablebody").html(data)
                                $('body #myTable').DataTable({
                                    "bStateSave": true,
                                    responsive: true
                                });
                            }
                        })
                        
                    } else {
                        $("#alert_msg").css({display: "block", backgroundColor: "#ce6c6c"})
                        // $("#" + msgID).html(data);
                        // $("#newuser").attr('disabled', 'disabled')
                    }
                }
            });
        } else {

        }
        
    
    })
});




// Truck details
var truckedit = document.getElementById("truckedit");
var truckeditbtn = document.getElementById("truckeditbtn");
var span = document.getElementsByClassName("close")[0];
truckeditbtn.onclick = function() {
    truckedit.style.display = "block";
}

function truckNumberclose() {
    truckedit.style.display = "none";
}

function mconwc(event) {
    window.onclick
    if (event.target == truckedit) {
        truckedit.style.display = "none";
    }
}

// Goal Form
try{
    var goalform = document.getElementById("goalform");
    var goalFormbtn = document.getElementById("goalFormbtn");
    var span = document.getElementsByClassName("close")[0];
    goalFormbtn.onclick = function() {
        goalform.style.display = "block";
    }
}catch(e){


}

function goalformclose() {
    goalform.style.display = "none";
}

function mconwc(event) {
    window.onclick
    if (event.target == goalform) {
        goalform.style.display = "none";
    }
}


function checkunique(ciID, action_type, msgID) {
    var civalue = document.getElementById(ciID).value
    $.ajax({
        method: "POST",
        url: "./Assets/backendfiles/register.php?action_type=" + action_type + "&user_name=" + civalue,
        data: {
            user_name: civalue
        },
        success: function(data) {
            if (data == "success") {
                $("#newuser").removeAttr('disabled', 'disabled')
                $("#" + msgID).html("");
            } else {

                $("#" + msgID).html(data);
                $("#newuser").attr('disabled', 'disabled')
            }
        }
    });
}

// check Username
document.getElementById("username").addEventListener("change", function() {
    if(window.location.search.includes("edituser")){
        var userid = window.location.search.split("&")[1]
        checkunique('username', 'oldusernamecheck&' + userid, 'user_name_msg')
    }else{
        checkunique('username', 'newusernamecheck', 'user_name_msg')
    }
})

// check email
document.getElementById("email").addEventListener("change", function() {
    if(window.location.search.includes("edituser")){
        var userid = window.location.search.split("&")[1]
        checkunique('email', 'olduseremailcheck&' + userid, 'email_msg')
    }else{
        checkunique('email', 'newuseremailcheck', 'email_msg')
    }
    
})

// Password check
function passCheck(pass1, pass2, btnid, msgid){
    var pass1 = document.getElementById(pass1).value
    var pass2 = document.getElementById(pass2).value
    var msg = "Password Do not match. Please try again."

    if(pass1 != pass2){
        $(msgid).html(msg);
        $(btnid).attr('disabled', 'disabled')
    } else {
        $(btnid).removeAttr('disabled', 'disabled')
        $(msgid).html("");
    }
}

$("#cnfrmpass").on("keyup", function() {
    passCheck("pass1", "cnfrmpass", "#newuser", "#confirm_password_msg")
})

// password reset check
try{
    document.getElementById("passReset_cnfrm").addEventListener("keyup", function() {
        passCheck("passreset1", "passReset_cnfrm", "#password_rest_btn", "#cnfrm_pass_error")
    })
}catch(e){}


// Time for allert messgae
$("#alert_msg").show().delay(5000).queue(function(n) {
    $(this).hide();
    n();
});

