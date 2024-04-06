$('.tab_navs').on('click', '.tab_nav', function() {
    $(".loader")[0].style.display = "flex"
    var activevan = $(this).index()
    var tab = $(this).parent().next().find("div.tab")[activevan]
    var notification_type = $(this).data("notification_type");
    
    var alrt = $(this).data("alrt")

    $(".tab.active").removeClass("active")
    $(".tab_nav.active").removeClass("active")

    for(i=0; i<$(".tab").length; i++){
        if($("div.tab")[i].style.display = "flex"){
            $("div.tab")[i].style.display = "none"
        } 
    }

    $(this).addClass(" active")
    tab.style.display = "flex"

    if(alrt == "notification"){
        getNotifications(notification_type, null, tab)
    } else if (alrt == "timer"){
        var data = {notification_type : notification_type, alrt: alrt}
        getonholdtrucks(notification_type, data, tab)
        // timer() Run timer function here
    }
    
});

$(".not_icon_con").on('click', function(e){
    var nav_box_style = e.delegateTarget.nextElementSibling

    if(nav_box_style.style.display == "none"){
        nav_box_style.style.display = "block"
    } else{
        nav_box_style.style.display = "none"
    }
})

$('.not_alert_container').on('click', '.close', function(e){
    $(this).parent()[0].remove()
})

$('.tabs').on('click', '.flag', function(e){

    var id = $(this).data("notification_id");

    $.ajax({
        type: "GET",
        url: "./Assets/backendfiles/notification_calls.php?notification_read=" + id,
        // success: function(data) {
        //     data = JSON.parse(data)[0]
        //     $(".not_count")[0].innerText= data.total_not
        //     tab.innerHTML = data.html_content
        // }
    });

    $(this).remove()
})

$(document).ready(function () {
    getNotifications()
    getonholdtrucks()

    // Ask for notification permissions
    showNotification();
    setInterval(function(){ showNotification(); }, 50000);

    
})


function getNotifications(notification_type = null, data = null, tab = $(".tab.active")[1]){
    tab.innerHTML = ""
    if(notification_type == ""){notification_type = null}
    $.ajax({
        type: "GET",
        url: "./Assets/backendfiles/notification_calls.php?notification_type=" + notification_type,
        data: data,
        success: function(data) {
            data = JSON.parse(data)[0]
            setTimeout(function(){
                $(".not_count")[0].innerText= data.total_not
                tab.innerHTML = data.html_content

                $(".loader")[0].style.display = "none"
            }, 1000)
        }
    });
}


// Get on hold trucks
function getonholdtrucks(notification_type = null, data = null, tab = $(".tab.active")[0]){
    tab.innerHTML = ""
    if(notification_type == ""){notification_type = null}
    $.ajax({
        type: "post",
        url: "./components/hold_time_row.php?notification_type=" + notification_type,
        data: data,
        success: function(data) {
            
            // setTimeout(function(){
                $(".not_count")[0].innerText= data.total_not
                tab.innerHTML = data
                // console.log(holdid);
                var tdtimer = $("body .timerDisplayheader")
                tdtimer.each(function() {
                    $min = $(this).data("min")
                    $sec = $(this).data("sec")
                    $hur = $(this).data("hur")

                    startTimertruck($(this), $hur, $min, $sec)
                });

                $(".loader")[0].style.display = "none"


            // }, 1000)
        }
    });
}


function showNotification() {
    if (!Notification) {
        $('body').append('<h4 style="color:red">*Browser does not support Web Notification</h4>');
        return;
    }
    if (Notification.permission !== "granted")
        Notification.requestPermission();
    // else {
        $.ajax({
            url : "./Assets/backendfiles/notification_calls.php?notification_alert=null",
            type: "POST",
            success: function(data, textStatus, jqXHR) {
                var data = jQuery.parseJSON(data)[0];
                if(data.result == true) {
                    var notifikasi = new Notification(data.msg, {
                        icon: "./Assets/Images/WhatsApp Image 2022-05-16 at 2.20 1.png",
                        body: data.text,
                    });
                    var noti_alert = '<div class="not_alert"> '+
                        '<span class="close">X</span> '+
                        '<div class="not_alert_content"> '+
                            '<div class="not_alert_text"> '+
                                '<p class="light">'+ data.msg +'</p> '+
                                '<p>Added</p> '+
                                '<a href="'+ data.action_link +'">'+ data.action_text +'</a> '+
                                '<p class="not_time">'+ data.time_ago +'</p> '+
                            '</div> '+
                            '<div class="flag" data-notification_id='+ data.not_id + '></div> '+
                        '</div> '+
                    '</div>'

                    var load_id = data.load_id;


                    $.ajax({
                        url: "./components/dispatcher.php",
                        method: "POST",
                        data: {dispatcher_form: 1, load_id: load_id},
                        success: function(data){
                            $(".dispetcher").remove()
                            $("body").append(data)
                        }
                    })

                    $('.not_alert_container')[0].innerHTML = noti_alert

                    notifikasi.onclick = function () {
                        window.open(data.action_link);
                        notifikasi.close();
                    };

                    setTimeout(function(){
                        notifikasi.close();
                        $('.not_alert_container')[0].innerHTML = ""
                    }, 5000);
                    
                } else {}
            },
            error: function(jqXHR, textStatus, errorThrown) {}
        });
    // }
};

// Timer for status on hold trucks
function startTimertruck(id, hours, minutes, seconds) {
    const timerElement = id;
    // console.log(id.data("min"));
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
  
// Filter By label
$("body").on("click", ".label_filter", function(e){
    var label_id = $(this).data("label_id")
    console.log(label_id)

    $.ajax({
        url: "./components/load_data.php",
        method: "POST",
        data: {label: label_id},
        success: function(data){
            if (window.location.href.indexOf("labels.php") > -1) {
                $('body #label_tabel').DataTable().destroy();
                $("#label_tabelbody").html(data)
                $('body #label_tabel').DataTable().draw();
            }else{
                location.href = 'labels.php';
                $('body #label_tabel').DataTable().destroy();
                $("#label_tabelbody").html(data)
                $('body #label_tabel').DataTable().draw();
            }

        }
    })
})


// Show color div and hide the previously visible one
$("body").on("click", ".truck_hold_icon, .notification_icon", function(e) {
    $(".notification_box").hide()
    var currentElements = $(this).parents().eq(1);
    var $notiDiv = $(currentElements).find(".notification_box");
    // if($notiDiv.style.display == "flex"){
    //     $notiDiv.css("display", "none");
    // } else {
        $notiDiv.css("display", "flex");
    // }
});
  
//   $("body").on("click", '.color-icon', function(e){
//     $(this).next().trigger("click")
//   })
  
// Function to show the color options when clicking on the icon
function showOption(inputClass) {
    
    // console.log($(this).next()); //trigger
    $(this).next().trigger("click")
    // this.nextElementSibling.click()
    // var colorInput = $(this).closest ("." + inputClass).first();
    // console.log(colorInput);
    // colorInput.click();
    // e.stopPropagation(); 
  }

  // Prevent color input click event from propagating to the parent div
  $("body").on("click", ".truck_hold_icon, .notification_icon, .notification_container, .not_icon_con ", function(e) {
    e.stopPropagation();
  });

  // Hide color div when clicking anywhere else on the body
  $("body").on("click", function(event) {
    if (!$(event.target).closest(".notification_box").length) {
      $(".notification_box").hide()
      // hideColorDiv();
    }
}
  );