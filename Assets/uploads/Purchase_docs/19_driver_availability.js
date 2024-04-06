$("#driver_available_date_update").on('click', function(e){
    var truck_Number = $("#truck_Number").val()
    var arrivaldate = $("#arrivaldate").val()

    $(this).html("Updating...")
    $(this).css({backgroundColor: "#dadada"})
    $(this).attr('disabled', 'disabled');

    $.ajax({
        url: './Assets/backendfiles/driverstatus.php?action_type=update_arrivaldate',
        method: 'post',
        data: {
            'truck_Number': truck_Number,
            'arrivaldate': arrivaldate
        },
        success: function(data){
            data = JSON.parse(data)[0]
            if(data.success == 1){
                $(this).html("Updating...")
                $(this).css({backgroundColor: "var(--button)"})
                $(this).removeAttr('disabled', 'disabled');

                $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
                $("#alert_msg").html(data.msg);
                $("#alert_msg").delay(30000).hide(500);

                $('#myTable').DataTable().destroy();
                $("#table").html(data.rows);
                $('#myTable').DataTable().draw();
            }
        }
    })
})

function mconwc(event) {
    window.onclick
    if (event.target == truckNumber) {
        truckNumber.style.display = "none";
    }
}

$("body").on("click", ".close", function(e){
    $(this).parent().parent().hide()
})

$("body").on("click", "#truckeditbtn", function(e){
    $("#truckedit").show()
})

$("body").on("click", ".cancel", function(e){
    console.log($(this).parents().eq(4))
    console.log($(this).parents())
    $(this).parents().eq(4).hide()
    
})

$(document).ready(function(){

    updateTable("./components/driver_table.php", "driver_tableBody", "myTable")


    

    // pond = FilePond.create(
    //     document.querySelector('input#driver_attachments'), {
    //         allowMultiple: true,
    //         instantUpload: false,
    //         allowProcess: false
    // });


    $("#driver_info_form").submit(function (e) {
        e.preventDefault();
        var fd = new FormData(this);
        // append files array into the form data
        // pondFiles = pond.getFiles();
        // // console.log(pondFiles)
        // fd.append('truckstate', 1)
        // for (var i = 0; i < pondFiles.length; i++) {
        //     fd.append('driver_attachments[]', pondFiles[i].file);
        // }

        // var contents = Array.from(fd.entries());
        // console.log(contents);

        

        $("body .submit").html("Adding...")
        $("body .submit").style = "background-color: #dadada;"
        $("body .submit").attr('disabled', 'disabled');

        $.ajax({
            url: './Assets/backendfiles/driverstatus.php?action_type=truckstate',
            type: 'POST',
            data: fd,
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data[0].success == 1){
                    updateTable("./components/driver_table.php", "driver_tableBody", "myTable")
                    $(".truckNumber.modal").style = "display: none;"

                } else if(data[0].success == 0){
                    alertBtn(data[0].msg, ".submit", "#ce6c6c")
                }
            },
            error: function (data) {
                alertBtn("Something went Wrong!", ".submit", "#ce6c6c")
            }
        } );
    });
})




// Allert Message and button rest
function alertBtn(alert_msg, submit_btn_id, bg="#7dc77d"){
    var alert_msg = '<div id="alert_msg" style="display: block; background-color: "' + bg + '>' + alert_msg + '<span class="close_msg">x</span></div>';
    $("#alert_msgs").append(alert_msg).delay(30000).hide(500)

    // $("#alert_msg").css({display: "block", backgroundColor: bg})
    // $("#alert_msg").html(alert_msg);
    // $("#alert_msg").delay(30000).hide(500);

    $("body " + submit_btn_id + "").html("Submit")
    $("body " + submit_btn_id + "").style = "background-color: var(--button);"
    $("body " + submit_btn_id + "").removeAttr('disabled', 'disabled');
}

// Request for Updating table
function updateTable(filepath, table, tableid){
    // $(".loader")[1].style.display = "flex"
    $.ajax({
        url: filepath,
        method: "POST",
        data: {update_table: 1},
        success: function (data) {
            if(tableid !== ""){
                $('body #' + tableid).DataTable().destroy();
            }
            $("#" + table).html(data)
            if(tableid !== ""){
                $('body #' + tableid).DataTable({
                    "bStateSave": true,
                    responsive: true
                }).draw();
            }

            // $(".loader")[1].style.display = "none"
        },
    })
}