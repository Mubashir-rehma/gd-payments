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
                // $('#myTable').DataTable().draw();
                // Define your exclusion criteria
                var exclusionKeyword = "out_Of_service";

                // Initialize DataTable with custom filtering
                var table = $('#myTable').DataTable({
                    "search": {
                        "search": exclusionKeyword, // Search for the exclusion keyword initially
                        "smart": false, // Disable DataTables' smart search
                    },
                    "initComplete": function(settings, json) {
                        // Custom filtering function
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            // Get the content of the second column (index 1)
                            var columnContent = data[1]; // Assuming the keyword is in the second column

                            // Check if the column content contains the exclusion keyword
                            if (columnContent.indexOf(exclusionKeyword) !== -1) {
                                // Exclude rows that contain the keyword
                                return false;
                            }

                            // Include all other rows in the search results
                            return true;
                        });

                        // Apply the custom filter
                        table.draw();
                    }
                });

                // Update search input
                table.search(exclusionKeyword).draw();
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

// $("body").on("click", "#truckeditbtn", function(e){
//     // e.preventDefault()
//     $("#truckadd").show()
// })

$("body").on("click", ".cancel", function(e){
    $(this).parents().eq(4).hide()
    
})
updateTable("./components/driver_table.php", "driver_tableBody", "myTable")



$(document).ready(function(){

    


    // var html = `<div style="margin: 10px;">
    //     <label style="margin-right: 10px;">Add Notes for File</label>
    //     <textarea name="file_notes[]" id="" style="width: 100%;" cols="30" rows="3"></textarea>
    //     <div class="tags">
            
    //         <label style="margin-right: 10px;" for="">Add File Tags</label>
    //         <input  style="width: 100%;" type="text" class="tagsinput" placeholder="Press 'SPACE' to be tagged.." >
    //         <input type="hidden" name="filetags[]" value="" class="tags">
    //         <div class="tagContainer" ></div>
    //     </div></div>
    // `

    // var pond = FilePond.create(document.querySelector('input#driver_ide_attachments'), {
    //     allowMultiple: true,
    //     instantUpload: false,
    //     allowProcess: false,
    //     labelIdle: `<div style="width:100%;height:100%;">
    //         <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
    //     </div>`,
    //     dropOnPage: true,
    //     // oninitfile: function(file){
    //     //     setTimeout(function(){
    //     //         $(html).appendTo("#filepond--item-" + file.id)
    //     //     }, 1000)
            
    //     // }
    // });

    // var ins_attach = FilePond.create(document.querySelector('input#driver_ins_attachments'), {
    //     allowMultiple: true,
    //     instantUpload: false,
    //     allowProcess: false,
    //     labelIdle: `<div style="width:100%;height:100%;">
    //         <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
    //     </div>`,
    // });

    // var dri_ls_atta = FilePond.create(document.querySelector('input#driver_dl_attachments'), {
    //     allowMultiple: true,
    //     instantUpload: false,
    //     allowProcess: false,
    //     labelIdle: `<div style="width:100%;height:100%;">
    //         <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
    //     </div>`,
    // });

    // var dri_vanpics_atta = FilePond.create(document.querySelector('input#driver_vanpics_attachments'), {
    //     allowMultiple: true,
    //     instantUpload: false,
    //     allowProcess: false,
    //     labelIdle: `<div style="width:100%;height:100%;">
    //         <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
    //     </div>`,
    // });


    $(document).on('submit', "form#driver_info_form", function (e) {
        e.preventDefault();
        var fd = new FormData(this);
        // append files array into the form data
        // pondFiles = pond.getFiles();
        // for (var i = 0; i < pondFiles.length; i++) {
        //     fd.append('driver_ide_attachment[]', pondFiles[i].file);
        // }

        // ins_attac = ins_attach.getFiles();
        // for (var i = 0; i < ins_attac.length; i++) {
        //     fd.append('driver_ins_attachment[]', ins_attac[i].file);
        // }

        // dri_ls_att = dri_ls_atta.getFiles();
        // for (var i = 0; i < dri_ls_att.length; i++) {
        //     fd.append('driver_dl_attachment[]', dri_ls_att[i].file);
        // }

        // dri_van_att = dri_vanpics_atta.getFiles();
        // for (var i = 0; i < dri_van_att.length; i++) {
        //     fd.append('driver_vanpics_attachment[]', dri_van_att[i].file);
        // }

        // var contents = Array.from(fd.entries());

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
                console.log(data)
                if(data.success == 1){
                    $(".truckNumber.modal").hide()
                    updateTable("./components/driver_table.php", "driver_tableBody", "myTable")
                    // pond.removeFiles()
                    // ins_attach.removeFiles()
                    // dri_ls_atta.removeFiles()
                    // dri_vanpics_atta.removeFiles()
                    alertBtn(data.msg, ".submit", "#ce6c6c")

                } else if(data.success == 0){
                    alertBtn('Function Failed', ".submit", "#ce6c6c")
                }
            },
            error: function (data) {
                alertBtn("Something went Wrong!", ".submit", "#ce6c6c")
            }
        } );
    });


    $("#submit_files").submit(function(e){
        e.preventDefault()
        var fd = new FormData(this)

        $.ajax({
            url: './Assets/backendfiles/driverstatus.php?action_type=submitFile',
            type: 'POST',
            data: fd,
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $(".truckNumber.modal").hide()
                if(data[0].success == 1){
                    updateTable("./components/driver_table.php", "driver_tableBody", "myTable")
                    

                } else if(data[0].success == 0){
                    alertBtn(data[0].msg, ".submit", "#ce6c6c")
                }
            },
            error: function (data) {
                alertBtn("Something went Wrong!", ".submit", "#ce6c6c")
            }
        })
    })


    $("body").on("click", ".delete_i_attach", function(e){
        var id = $(this).data("id")

        var confirmed = confirm("Are you sure you want to delete!")
        
        if(confirmed){
            $(this).parent().parent().remove()

            $.ajax({
                url: './Assets/backendfiles/driverstatus.php?action_type=delete_insurance_file',
                type: "POST",
                data: {id: id},
                success: function(data){
                    alertBtn(data[0].msg, null, "#ce6c6c")
                }
            })
        }
        
    })

    $("body").on("click", ".delete_Driver", function(e){
        var id = $(this).data("id")

        var confirmed = confirm("Are you sure you want to delete!")
        
        if(confirmed){

            $.ajax({
                url: './Assets/backendfiles/driverstatus.php?action_type=delete_driver',
                type: "POST",
                data: {id: id},
                success: function(data){
                    data = JSON.parse(data)
                    if(data.success == 1){
                        updateTable("./components/driver_table.php", "driver_tableBody", "myTable")
                        alertBtn(data.msg, null, "#ce6c6c")
    
                    }
                    
                }
            })
        }
        
    })

    // truck status to unavailable
    $("body").on("click", ".driver_truck", function(e){
        var id = $(this).data("id")

        var confirmed = confirm("Are you sure you want to update!")
        
        if(confirmed){

            $.ajax({
                url: './Assets/backendfiles/driverstatus.php?action_type=driver_truck',
                
                type: "POST",
                data: {id: id},
                success: function(data){
                    // console.log(data);
                    data = JSON.parse(data)
                    if(data.success == 1){
                        updateTable("./components/driver_table.php", "driver_tableBody", "myTable")
                        alertBtn(data.msg, null, "#ce6c6c")
                        // window.location.reload();
    
                    }               
                }
            })
            console.log('Successful');
        }
        
    })
    // Edit Driver details
    $("body").on("click", ".edit_driver", function(e){
        var id = $(this).data("id");
        $("#truckedit").remove();
        $.ajax({
            url: "./components/edit_driver.php",
            method: 'POST',
            data: {id: id},
            success: function(data){
                console.log(data)
                if (data.trim() == "You are not authenticated!") {
                    console.log(data);
                    var alert_msg =
                        '<div id="alert_msg" style="display: block; background-color: red; color:white">' +
                        data +
                        '<span class="close_msg">x</span></div>';
                        if($('#alert_msgs').css('display') == 'none'){
                            $("#alert_msgs").html(alert_msg)
                            $("#alert_msgs").show()
                        } else {
                            $("#alert_msgs").html(alert_msg).delay(30000).hide(500);
                        }
                } else { 
                    $("body").append(data);
                }

            }
        });
        
    });
    
    // Add new driver
    $("body").on("click", "#truckeditbtn", function(e){
        $("#truckedit").remove()

        $.ajax({
            url: "./components/edit_driver.php",
            method: 'GET',
            success: function(data){
                $("body").append(data);
            }
        })
    })


    // Add new driver
    // $("body").on("click", "#truckeditbtn", function(e){
    //     $("#truckedit").remove()

        $.ajax({
            url: "./components/driver_summary.php",
            method: 'GET',
            success: function(data){
                $(".driverstatusSummary").append(data);
            }
        })
    // })

    $("body").on("click", "#calculate", function(e){
        $(this).html("Getting...")
        $(this).css({backgroundColor: "#dadada"})
        $(this).attr('disabled', 'disabled');
        
        var lat = $("#closest_lat").val()
        var lng = $("#closest_lng").val()

        $.ajax({
            url: './components/driver_table.php',
            method: 'POST',
            data: {getclosestunit: 1, lng: lng, lat: lat},
            success: function(data){
                $("#calculate").html("Get closest Unit")
                $("#calculate").css({backgroundColor: "var(--button)"})
                $("#calculate").removeAttr('disabled', 'disabled');

                $('body #myTable').DataTable().destroy();
                $("#driver_tableBody").html(data)
                $('body #myTable').DataTable({
                    "bStateSave": true,
                    responsive: true
                }).draw();                
                  }
        })
    })

    // hold status handling
    $("body").on("click", ".hold_btn", function(){
        var action = $(this).data("action_type");
        var last_status = $(this).data("last_status");
        var user_id = $(this).data("user_id");
        var truck_id = $(this).data("truck_id");
        var status = $(this).data("status");
        var status_updating_user = $(this).data("status_updating_user");
        var thi = $(this);
    
        var data = {action: action, last_status: last_status, user_id: user_id, truck_id: truck_id, status: status, status_updating_user: status_updating_user};
    
        $.ajax({
            url: './Assets/backendfiles/driverstatus.php?action_type=' + action,
            data: data,
            method: 'POST',
            success: function(data){
                data = JSON.parse(data);

                if(data.success === 1){
                    thi.data("action_type", "removehold");
                    thi.data("last_status", "");
                    var txt = action == "hold" ? "Unhold" : 'Hold';
                    var clas = action == "hold" ? "hold-success" : 'hold-danger';
                    thi.find('.hold.hold-success').removeClass("hold-success");
                    thi.find('.hold').html(txt);
                    thi.find('.hold').addClass(clas);
                    thi.parents().eq(1).prev().attr('id', "holdtime" + truck_id);
                    console.log(thi.parents().eq(1).prev());
                    
                    // Start the timer immediately after the AJAX request is successful
                    if(action == "hold"){
                        console.log($("body #holdtime" + truck_id));
                        startTimer($("body #holdtime" + truck_id), 0, 15, 0);
                        
                    }

                    updateTable("./components/driver_table.php", "driver_tableBody", "myTable");
                } else if(data.success === 0 ){
                    //alert("You are not authenticated to Update or un hold the truck!");
                        var alert_msg =
                        '<div id="alert_msg" style="display: block; background-color: #FF0000;color: white;">' +
                        data.msg +
                        '<span class="close_msg">x</span></div>';
                        if($('#alert_msgs').css('display') == 'none'){
                            $("#alert_msgs").html(alert_msg)
                            $("#alert_msgs").show()
                        } else {
                            $("#alert_msgs").html(alert_msg).delay(30000).hide(500);
                        }
                        
                }
                
                
            }
        });
    });



})

$("body").on("click", ".close_msg", function(e){
    $(this).parent().remove()
})

$("body").on("click", ".hold_btn ktooltip", function(e) {

})

// Add Tags For attached Files
$("body").on("keydown", ".tagsinput", function(event) {
    if (event.which == 13) {
        event.preventDefault();
    } else if (event.which == 32) {
        event.preventDefault();
        $(this).parent().find(".tagContainer").append('<span class="tag">' + ($(this).val()) + '<span class="removetag">x</span></span>');
        var tags = $(this).parent().find(".tags")
        if(tags.val() == null || tags.val() == ""){
            tags.val(tags.val() + $(this).val())
        } else {
            tags.val(tags.val() + ", " + $(this).val())
        }
        $(this).val("");
    }
});


// Remove tags for attched files
$("body").on("click", ".removetag", function(e){
    var cont = $(this).parent().text().replace('x', '')
    var tags = $(this).parent().parent().parent().find(".tags")
    var tag = tags.val().split(", ")
    var tagr = ""
    for(i=0; i<tag.length; i++){
        if(tag[i] !== cont){
            if(i === 0 || ( i == 1 && tag[0] == cont )){
                tagr = tag[i]
            } else if (i > 0) {
                tagr += ", " + tag[i]
            }
        }
    }
    var tags = tags.val(tagr)
    $(this).parent().remove()
    
})




// Select 2 function
// $(function() {
//     $(".select2").select2({
//         matcher: matchCustom,
//         templateResult: formatCustom
//     });
// })

function stringMatch(term, candidate) {
    return candidate && candidate.toLowerCase().indexOf(term.toLowerCase()) >= 0;
}

function matchCustom(params, data) {
    // If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
        return data;
    }
    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
        return null;
    }
    // Match text of option
    if (stringMatch(params.term, data.text)) {
        return data;
    }
    // Match attribute "data-foo" of option
    if (stringMatch(params.term, $(data.element).attr('data-foo'))) {
        return data;
    }
    // Return `null` if the term should not be displayed
    return null;
}

function formatCustom(state) {
    return $(
        '<div><div>' + state.text + '</div><div class="foo">' +
        $(state.element).attr('data-foo') +
        '</div></div>'
    );
}

function timerexe() {
    var tdtimer = $("body .timerDisplay")
    tdtimer.each(function() {
        $min = $(this).data("min")
        $sec = $(this).data("sec")
        $hur = $(this).data("hur")

        startTimer($(this), $hur, $min, $sec)
    });

    // for (i = 0; i < hold.length; i++) {
    //     // document.addEventListener("DOMContentLoaded", () => {
    //         startTimer(hold[i])
    //     // timer(hold[i])
    //     // });
    // }
}

// $(document).ready(function() {
//     $('[data-toggle="tooltip"]').tooltip();
// });


// Span element that will hold the timer
const clock = document.getElementById("event_timer");
// Duration in minutes
const duration = 15;

function resetStartTime(id) {
    const startTime = Date.now();
    const eventTime = duration * 60 * 1000;
    const eventDuration = new Date(startTime + eventTime);
    // Save to localStorage
    localStorage.setItem(id, eventDuration);
    return eventDuration;
}

function timer(id) {
    // document.addEventListener("DOMContentLoaded", () => {
    var startTime = new Date(
        localStorage.getItem(id) || resetStartTime(id)
    );

    timeInterval = setInterval(() => {
        // Stored value - current time
        const timer = startTime.getTime() - Date.now();

        // Uncomment these lines if you want to use 'days' and 'hours'
        // const days = Math.floor(timer / (1000 * 60 * 60 * 24))
        // const hours = Math.floor((timer % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
        const minutes = Math.floor((timer % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timer % (1000 * 60)) / 1000);

        // Add to DOM
        // for(i=0; i < hold.length; i++){
        // console.log(hold[i])
        document.getElementById(id).innerText = minutes + " : " + seconds;

        // Clear and reset when done
        if (timer <= 0) {
            var td = document.getElementById(id)
            var span = document.createElement('span')
            span.innerHTML = '<i class="uil uil-stopwatch-slash"></i>'
            span.style = 'cursor: pointer; color: #5AC748;'

            td.setAttribute("onclick", 'timer(' + id + ')');
            td.style = 'cursor: pointer; color: red;'
            td.innerText = 'Time Up';
            // td.appendChild(span)
            localStorage.removeItem(id);
            clearInterval(timeInterval);
        }
        // }
    }, 1000);
    // });
}

// Timer for status on hold trucks
function startTimer(id, hours, minutes, seconds) {
    const timerElement = id;
    let totalSeconds = (hours * 3600) + (minutes * 60) + seconds;
    let countingDown = totalSeconds < 0 ? false : true;
  
    setInterval(function () {
      // Calculate hours, minutes, and seconds
      const currentHours = Math.floor(Math.abs(totalSeconds) / 3600);
      const remainingSeconds = Math.abs(totalSeconds) % 3600;
      const currentMinutes = Math.floor(remainingSeconds / 60);
      const currentSeconds = remainingSeconds % 60;
  
      // Determine the sign based on counting direction
      const sign = countingDown ? '' : '- ';
  
      // Display the timer with leading zeros and sign
      const hoursStr = currentHours > 0 ? String(currentHours).padStart(2, '0') + ':' : '';
      const minutesStr = String(currentMinutes).padStart(2, '0');
      const secondsStr = String(currentSeconds).padStart(2, '0');
  
      // Combine hours, minutes, and seconds with the sign
      let timerText = `${sign}${hoursStr}${minutesStr}:${secondsStr}`;
  
      timerElement.text(timerText);
  
      // Change the color to red when counting up
      timerElement.css('color', countingDown ? 'green' : 'red');
  
      if (totalSeconds === 0) {
        countingDown = false; // Switch to counting up
      }

    //   if (countingDown) {
        totalSeconds--;
    //   } else {
    //     totalSeconds--;
    //   }
    }, 1000); // Update the timer every second (1000 milliseconds)
}
  
// Example usage: startTimer('timer', 1, -30, 45); // 1 hour, -30 minutes, 45 seconds
$(document).ready(function() {

    // var table = $('#myTable').DataTable({
    //     "bStateSave": true,
    //     rowReorder: {
    //         selector: 'td:nth-child(2)'
    //     },
    //     responsive: true
    // });
    // var exclusionKeyword = "out_Of_service";
    // var table;
    
    // $(document).ready(function() {
    //     // Initialize DataTable
    //     table = $('#myTable').DataTable({
    //         "initComplete": function(settings, json) {
    //             // Custom filtering function
    //             $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    //                 // Get the content of the second column (index 1)
    //                 var columnContent = data[1]; // Assuming the keyword is in the second column
    
    //                 // Check if a search term has been entered by the user
    //                 var searchValue = table.search();
    
    //                 // If no search term is provided, include the row
    //                 if (!searchValue) {
    //                     return true;
    //                 }
    
    //                 // Check if the column content includes the search term
    //                 if (columnContent.toLowerCase().includes(searchValue.toLowerCase())) {
    //                     // Exclude rows containing "out_Of_service"
    //                     if (columnContent.toLowerCase().includes(exclusionKeyword.toLowerCase())) {
    //                         return false;
    //                     }
    //                     return true;
    //                 }
    
    //                 // Include all other rows in the search results
    //                 return true;
    //             });
    //         }
    //     });
    // });
    

    var table = $('#myTable').DataTable({
        dom: '<"search-box-wrapper"f><"table-wrapper"t>',
    });

    // Add an id to the search box
    // $('.search-box-wrapper input').attr('id', 'my-search-box');

    // Re-draw the table when the search box is used
    console.log($('#myTable_filter input'));
    $('#myTable_filter input').unbind().bind('keyup', function() {
        table.search(this.value).draw();
    });

    // Add custom filtering
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            // Get the data for the second column (index 1)
            var columnData = data[1];

            // Check if the column data contains the 'out_of_Service' text
            if (columnData.indexOf('out_Of_Service') !== -1) {
                // If it does, exclude this row from the search results
                return false;
            }

            // Otherwise, include this row in the search results
            return true;
        }
    );

    // Add date on available_on status
    $("body").on("change", "#truckstatus", function(e){
        var html = `<label for="drivertime">Choose Date for Driver Availability</label>
            <input required type="datetime-local" name="drivertime" id="" placeholder="" value="<?php echo  !empty($truckdetail['driver_availability']) ? substr(date('c', strtotime($truckdetail['driver_availability'])), 0, 16) : ''; ?>">`

        if($(this).val() == "available_on_"){
            $("body #available_on_date").html(html)
        } else {
            $("body #available_on_date").html("")
        }
    })  


//     $("body").on("change", "#truckstatus", function(e) {
//         var available_on_ = $(this).val();
//         $.ajax({
//             url: "./components/edit_driver.php",
//             method: "POST",
//             data: {available_on_: available_on_ },
//            success: function (data) {
//                 if (available_on_ == "available_on_") {
//                     $("body #available_on_date").css("display", "flex");
//                 } else {
//                     $("body #available_on_date").css("display", "none");
//                 }
//             }
//         });
//    });
    


});





$(".ktooltip").mouseover(function(e) {
    var tooltip = $(this).siblings('.ktooltiptext'); // Get tooltip element (ktooltiptext)
    var tipX = $(this).outerWidth() + 5;             // 5px on the right of the ktooltip
    var tipY = -40;                                  // 40px on the top of the ktooltip
    tooltip.css({ top: tipY, left: tipX });          // Position tooltip
  
    // Get calculated tooltip coordinates and size
    var tooltip_rect = tooltip[0].getBoundingClientRect();
    // Corrections if out of window
    if ((tooltip_rect.x + tooltip_rect.width) > $(window).width()) // Out on the right
      tipX = -tooltip_rect.width - 5; // Simulate a "right: tipX" position
    if (tooltip_rect.y < 0)            // Out on the top
      tipY = tipY - tooltip_rect.y;    // Align on the top
  
    // Apply corrected position
    tooltip.css({ top: tipY, left: tipX }); 
});




// Allert Message and button rest
function alertBtn(alert_msg, submit_btn_id, bg="#7dc77d"){
    var alert_msg = '<div id="alert_msg" style="display: block; background-color: "' + bg + '>' + alert_msg + '<span class="close_msg">x</span></div>';
    $("#alert_msgs").append(alert_msg).delay(30000).hide(500)

    // $("#alert_msg").css({display: "block", backgroundColor: bg})
    // $("#alert_msg").html(alert_msg);
    // $("#alert_msg").delay(30000).hide(500);

    if(submit_btn_id !== null){
        $("body " + submit_btn_id + "").html("Submit")
        $("body " + submit_btn_id + "").style = "background-color: var(--button);"
        $("body " + submit_btn_id + "").removeAttr('disabled', 'disabled');
    }
}

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
            var tdtimer = $("body .timerDisplay")
            tdtimer.each(function() {
                $min = $(this).data("min")
                $sec = $(this).data("sec")
                $hur = $(this).data("hur")

                startTimer($(this), $hur, $min, $sec)
            });
            // for(i=0; i<hold.length; i++){
            //     startTimer(hold[i].id, hold[i].hours, hold[i].mins, hold[i].sec)
            // }
            if(tableid !== ""){
                $('body #' + tableid).DataTable({
                    "bStateSave": true,
                    responsive: true,
                    // "ordering": false
                }).draw();
            }

            

            // $(".loader")[1].style.display = "none"
        },
    })
}