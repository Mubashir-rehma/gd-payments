// Add new Goal
function goal(btnid, actionType){
    var btn = document.getElementById(btnid)
    var user = document.getElementById("user").value
    var timeline = document.getElementById("timeline").value
    var profit_goal = document.getElementById("profit_goal").value

    $("#" + btnid).attr('disabled', 'disabled')
    btn.textContent = "Adding..."
    btn.style.backgroundColor = "#dadada"


    $.ajax({
        method: "POST",
        url: "./Assets/backendfiles/goals.php?action_type=" + actionType,
        data: {
            user: user,
            timeline: timeline,
            profit_goal: profit_goal,
        },
        success: function(data) {
            data = data.split(" : ")
            if (data[0] == "success") {
                setTimeout(function(){
                    $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
                    $("#alert_msg").html(data[1]);
                    $("#" + btnid).removeAttr('disabled', 'disabled');
                    const inputs = document.querySelectorAll('#user, #timeline, #profit_goal');

                    inputs.forEach(input => {
                        input.value = '';
                    });

                    btn.textContent = "Submit"
                    btn.style.backgroundColor = "var(--button)"
                    $("#alert_msg").delay(5000).hide(500);

                }, 1000);
                
            } else {
                $("#alert_msg").css({display: "block", backgroundColor: "#ce6c6c"})
                $("#alert_msg").delay(5000).hide(500);
                // $("#" + msgID).html(data);
                // $("#" + btnid).attr('disabled', 'disabled')
            }
        }
    });
}


try{
    document.getElementById("newgoal").addEventListener("click", function() {
        goal("newgoal", "newgoal")
    })
}catch(e){}


// Update Goal
try{
document.getElementById("updategoal").addEventListener("click", function() {
    if(window.location.search.includes("editgoal")){
        var userid = window.location.search.split("&")[1]
        goal("updategoal", "editgoal&" + userid)
    }
})
}catch(e){}