function checkuniquebroker(ciID, action_type, msgID, btnid) {
  var civalue = document.getElementById(ciID).value;
  $.ajax({
    method: "POST",
    url:
      "./Assets/backendfiles/broker_check.php?action_type=" +
      action_type +
      "&broker_name=" +
      civalue,
    data: {
      user_name: civalue,
    },
    success: function (data) {
      if (data.trim() == "success") {
        $("#" + btnid).removeAttr("disabled", "disabled");
        $("#" + msgID).html("");
      } else {
        $("#" + msgID).html(data);
        $("#" + btnid).attr("disabled", "disabled");
      }
    },
  });
}

function formatAMPM(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? "pm" : "am";
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? "0" + minutes : minutes;
  var strTime = hours + ":" + minutes + " " + ampm;
  return strTime;
}

function addCheckCall() {
  var btn = document.getElementById("check_call_submit");
  var checkpoints = document.getElementById("checkpoints").value;
  var check_notes = document.getElementById("check_notes").value;
  var userid = $("#load_id").val();

  var tdate = new Date();
  var for_date =
    tdate.getMonth() +
    1 +
    "-" +
    tdate.getDay() +
    "-" +
    tdate.getFullYear().toString().substr(-2);
  var ttime = formatAMPM(new Date());

  document.getElementsByClassName("buttons")[0].style = "margin-left: 40%;";
  $("#check_call_submit").attr("disabled", "disabled");
  btn.textContent = "Adding...";
  btn.style.backgroundColor = "#dadada";

  if (check_notes == "" || typeof check_notes == "undefined") {
    document.getElementById("check_notes").style = "border: 1px solid red;";
    $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
    $("#alert_msg").delay(30000).hide(500);
    $("#alert_msg").html("Notes can not be blank!");

    btn.textContent = "Add";
    document.getElementsByClassName("buttons")[0].style = "margin-left: 40%;";
    btn.style.backgroundColor = "var(--button)";
    $("#check_call_submit").removeAttr("disabled", "disabled");
  } else {
    $.ajax({
      method: "POST",
      url:
        "./Assets/backendfiles/broker_check.php?action_type=addCheckCall&id=" +
        userid,
      data: {
        checkpoints: checkpoints,
        check_notes: check_notes,
      },
      success: function (data) {
        data = JSON.parse(data)[0];
        if (data.success == 1) {
          setTimeout(function () {
            $("#alert_msg").css({
              display: "block",
              backgroundColor: "#7dc77d",
            });
            $("#alert_msg").html(data.msg);
            $("#check_call_submit").removeAttr("disabled", "disabled");
            const inputs = document.querySelectorAll(
              "#checkpoints, #check_notes"
            );
            var td =
              "<tr>" +
              '<td title="Date and time the check call was made to driver.">' +
              for_date +
              '<p class="light">' +
              ttime +
              "</p></td>" +
              ' <td title="The user who made the call">' +
              data.user +
              "</td>" +
              ' <td title="Check points added by the user">' +
              checkpoints +
              "</td>" +
              ' <td title="Check notes added by the user">' +
              check_notes +
              "</td>" +
              ' <td title="check call Notes added by the user"></td>' +
              " </tr>";

            inputs.forEach((input) => {
              input.value = "";
            });

            $("#check_call_table tbody").append(td);

            btn.textContent = "Add";
            document.getElementsByClassName("buttons")[0].style =
              "margin-left: 40%;";
            btn.style.backgroundColor = "var(--button)";
            $("#alert_msg").delay(30000).hide(500);
            document.getElementById("check_notes").style =
              "border: 1px solid #dadada;";
          }, 1000);
        } else {
          $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
          $("#alert_msg").delay(30000).hide(500);
          $("#alert_msg").html(data.msg);
          btn.textContent = "Add";
          document.getElementsByClassName("buttons")[0].style =
            "margin-left: 40%;";
          btn.style.backgroundColor = "var(--button)";
          $("#check_call_submit").removeAttr("disabled", "disabled");
          document.getElementById("check_notes").style =
            "border: 1px solid #dadada;";
        }
      },
    });
  }
}

function addnewload() {
  var btn = document.getElementById("check_call_submit");
  var checkpoints = document.getElementById("checkpoints").value;
  var check_notes = document.getElementById("check_notes").value;
  var userid = $("#load_id").val();

  var tdate = new Date();
  var for_date =
    tdate.getMonth() +
    1 +
    "-" +
    tdate.getDay() +
    "-" +
    tdate.getFullYear().toString().substr(-2);
  var ttime = formatAMPM(new Date());

  document.getElementsByClassName("buttons")[0].style = "margin-left: 40%;";
  $("#check_call_submit").attr("disabled", "disabled");
  btn.textContent = "Adding...";
  btn.style.backgroundColor = "#dadada";

  if (check_notes == "" || typeof check_notes == "undefined") {
    document.getElementById("check_notes").style = "border: 1px solid red;";
    $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
    $("#alert_msg").delay(30000).hide(500);
    $("#alert_msg").html("Notes can not be blank!");

    btn.textContent = "Add";
    document.getElementsByClassName("buttons")[0].style = "margin-left: 40%;";
    btn.style.backgroundColor = "var(--button)";
    $("#check_call_submit").removeAttr("disabled", "disabled");
  } else {
    $.ajax({
      method: "POST",
      url:
        "./Assets/backendfiles/broker_check.php?action_type=addnewload&" +
        userid,
      data: {
        checkpoints: checkpoints,
        check_notes: check_notes,
      },
      success: function (data) {
        data = JSON.parse(data)[0];
        if (data.success == 1) {
          setTimeout(function () {
            $("#alert_msg").css({
              display: "block",
              backgroundColor: "#7dc77d",
            });
            $("#alert_msg").html(data.msg);
            $("#check_call_submit").removeAttr("disabled", "disabled");
            const inputs = document.querySelectorAll(
              "#checkpoints, #check_notes"
            );
            var td =
              "<tr>" +
              '<td title="Date and time the check call was made to driver.">' +
              for_date +
              '<p class="light">' +
              ttime +
              "</p></td>" +
              ' <td title="The user who made the call">' +
              data.user +
              "</td>" +
              ' <td title="Check points added by the user">' +
              checkpoints +
              "</td>" +
              ' <td title="Check notes added by the user">' +
              check_notes +
              "</td>" +
              ' <td title="check call Notes added by the user"></td>' +
              " </tr>";

            inputs.forEach((input) => {
              input.value = "";
            });

            $("#check_call_table tbody").append(td);

            btn.textContent = "Add";
            document.getElementsByClassName("buttons")[0].style =
              "margin-left: 40%;";
            btn.style.backgroundColor = "var(--button)";
            $("#alert_msg").delay(30000).hide(500);
            document.getElementById("check_notes").style =
              "border: 1px solid #dadada;";
          }, 1000);
        } else {
          $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
          $("#alert_msg").delay(30000).hide(500);
          $("#alert_msg").html(data.msg);
          btn.textContent = "Add";
          document.getElementsByClassName("buttons")[0].style =
            "margin-left: 40%;";
          btn.style.backgroundColor = "var(--button)";
          $("#check_call_submit").removeAttr("disabled", "disabled");
          document.getElementById("check_notes").style =
            "border: 1px solid #dadada;";
        }
      },
    });
  }
}
function loadlocationStatus(status, pickup_loc, destination, userid) {
  var btn = document.getElementById("Update_current_loc");
  var current_loc = document.getElementById("current_loc").value;
  // var userid = window.location.search.split("&")[1]

  var tdate = new Date();
  var for_date =
    tdate.getMonth() +
    1 +
    "-" +
    tdate.getDay() +
    "-" +
    tdate.getFullYear().toString().substr(-2);
  var ttime = formatAMPM(new Date());

  const loc_status = document.querySelectorAll(".update_load_location_staus");

  loc_status.forEach((di) => {
    di.style.backgroundColor = "#dadada";
  });
  $('select[name="update_load_status[]"]').attr("disabled", "disabled");

  $("#Update_current_loc").attr("disabled", "disabled");
  btn.textContent = "Adding...";
  btn.style.backgroundColor = "#dadada";

  $.ajax({
    method: "POST",
    url:
      "./Assets/backendfiles/index.php?action_type=loadtracking&id=" + userid,
    data: {
      current_loc: current_loc,
      status: status,
      pickup_loc: pickup_loc,
      destination: destination,
    },
    success: function (data) {
      data = JSON.parse(data)[0];
      if (data.success == 1) {
        // setTimeout(function(){
        $("#alert_msg").css({ display: "block", backgroundColor: "#7dc77d" });
        $("#alert_msg").html(data.msg);
        $("#check_calls").html(data.rows);
        const dis = document.querySelectorAll(".update_load_location_staus");
        dis.forEach((di) => {
          di.style.backgroundColor = "var(--container)";
        });
        $('select[name="update_load_status[]"]').removeAttr(
          "disabled",
          "disabled"
        );
        $("#Update_current_loc").removeAttr("disabled", "disabled");
        const inputs = document.querySelectorAll("#current_loc");

        inputs.forEach((input) => {
          input.value = "";
        });

        btn.textContent = "Update";
        btn.style.backgroundColor = "var(--button)";
        $("#alert_msg").delay(30000).hide(500);

        // }, 1000);
      } else {
        $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
        $("#alert_msg").delay(30000).hide(500);
        $("#alert_msg").html(data.msg);
        btn.textContent = "Add";
        btn.style.backgroundColor = "var(--button)";
        $("#Update_current_loc").removeAttr("disabled", "disabled");
      }
    },
  });
}

function deleteTrackingRecord(id) {
  $.ajax({
    method: "POST",
    url: "./Assets/backendfiles/index.php?action_type=deleteTrackingRecord",
    data: {
      id: id,
    },
    success: function (data) {
      data = JSON.parse(data)[0];
      if (data.success == 1) {
        setTimeout(function () {
          $("#alert_msg").css({ display: "block", backgroundColor: "#7dc77d" });
          $("#alert_msg").html(data.msg);
          $("#alert_msg").delay(30000).hide(500);
        }, 1000);
      } else {
        $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
        $("#alert_msg").delay(30000).hide(500);
        $("#alert_msg").html(data.msg);
      }
    },
  });
}

// Functions for draging and droping Files
function sendFileToServer(formData, status) {
  var uploadURL = "../tests/dragdrop/upload.php"; //Upload URL
  var extraData = {}; //Extra Data.
  var jqXHR = $.ajax({
    xhr: function () {
      var xhrobj = $.ajaxSettings.xhr();
      if (xhrobj.upload) {
        xhrobj.upload.addEventListener(
          "progress",
          function (event) {
            var percent = 0;
            var position = event.loaded || event.position;
            var total = event.total;
            if (event.lengthComputable) {
              percent = Math.ceil((position / total) * 100);
            }
            //Set progress
            status.setProgress(percent);
          },
          false
        );
      }
      return xhrobj;
    },
    url: uploadURL,
    type: "POST",
    contentType: false,
    processData: false,
    cache: false,
    data: formData,
    success: function (data) {
      data = JSON.parse(data);
      status.setProgress(100);
      status.fileId(data.fileID);

      // $("#status1").append("File upload Done<br>");
    },
  });

  status.setAbort(jqXHR);
}

var rowCount = 0;

function createStatusbar(obj) {
  rowCount++;

  var row = "odd";
  if (rowCount % 2 == 0) row = "even";
  this.statusbar = $("<div class='statusbar " + row + "'></div>");
  this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
  this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
  this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(
    this.statusbar
  );
  this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
  this.delete = $(
    "<div class='abort delete' data-file_rowID='' > Delete </div>"
  )
    .appendTo(this.statusbar)
    .hide();
  obj.after(this.statusbar);

  this.fileId = function (fileId) {
    this.delete.attr("data-file_rowID", fileId);
  };

  this.setFileNameSize = function (name, size) {
    var sizeStr = "";
    var sizeKB = size / 1024;
    if (parseInt(sizeKB) > 1024) {
      var sizeMB = sizeKB / 1024;
      sizeStr = sizeMB.toFixed(2) + " MB";
    } else {
      sizeStr = sizeKB.toFixed(2) + " KB";
    }

    this.filename.html(name);
    this.size.html(sizeStr);
  };
  this.setProgress = function (progress) {
    var progressBarWidth = (progress * this.progressBar.width()) / 100;
    this.progressBar
      .find("div")
      .animate(
        {
          width: progressBarWidth,
        },
        10
      )
      .html(progress + "% ");
    if (parseInt(progress) >= 100) {
      this.abort.hide();
      this.progressBar.hide();
      this.delete.show();
    }
  };
  this.setAbort = function (jqxhr) {
    var sb = this.statusbar;
    this.abort.click(function () {
      jqxhr.abort();
      sb.hide();
    });
  };
}

function handleFileUpload(files, obj) {
  for (var i = 0; i < files.length; i++) {
    var fd = new FormData();
    fd.append("file", files[i]);

    var status = new createStatusbar(obj); //Using this we can set progress.
    status.setFileNameSize(files[i].name, files[i].size);
    sendFileToServer(fd, status);
  }
}

// Delete Tracker Record
$(".delete_tracker_record").on("click", function (e) {
  var id = $(this).data("tracker_id");
  var confirmed = confirm("Are you sure to delete data?");

  if (confirmed) {
    deleteTrackingRecord(id);

    $(this).parent().parent().remove();
  }
});

// Delete Tracker Record After adding new records
$("#tracking_tableBody").on("click", ".delete_tracker_record", function (e) {
  var id = $(this).data("tracker_id");
  var confirmed = confirm("Are you sure to delete data?");

  if (confirmed) {
    deleteTrackingRecord(id);

    $(this).parent().parent().remove();
  }
});

// Add check call notes
$("body").on("click", "#check_call_submit", function (e) {
  addCheckCall();
});

// document.getElementById("check_call_submit").addEventListener("click", function() {
//     addCheckCall()
// })

// Update Load location/tracking status
$("body").on("change", ".update_load_location_staus", function (e) {
  var status = $(this).val();
  var id = $(this).data("load_id");
  var pickup_location = e.target.nextElementSibling.value;
  var destination = e.target.nextElementSibling.nextElementSibling.value;
  loadlocationStatus(status, pickup_location, destination, id);

  var selectedIndex = $(this)[0].selectedIndex;
  var activestatus =
    $(this).parent()[0].nextElementSibling.children[0].children[selectedIndex];
  // var statusStar = $(this).parent()[0].nextElementSibling.children[0].children[2]
  // var stsusp = $(this).parent()[0].nextElementSibling.children[1]

  // var statsbarWidth = (selectedIndex * 16.66)
  // var statsstarWidth = (selectedIndex * 16.66) - 3

  // var bg = "var(--button)"
  // if(selectedIndex <= 4){
  //     bg = "var(--button)"
  // } else if(selectedIndex == 5){
  //     bg = "red"
  // } else if(selectedIndex == 6){
  //     bg = "green"
  // }

  $(".csstriangle").removeClass("step_performed");
  $(".csstriangle").removeClass("active_step");
  $(".csstriangle").removeClass("issue");

  if (selectedIndex == 4) {
    for (i = 0; i < selectedIndex + 1; i++) {
      $(this)
        .parent()[0]
        .nextElementSibling.children[0].children[i].classList.add("issue");
    }
  } else {
    activestatus.classList.add("active_step");
    for (i = 0; i < selectedIndex; i++) {
      $(this)
        .parent()[0]
        .nextElementSibling.children[0].children[i].classList.add(
          "step_performed"
        );
    }
  }

  // var ss = statusbar.style = "background-color: " + bg + ";width: " + statsbarWidth + "%;"
  // statusbar.innerHTML = status
  // stsusp.innerHTML = status
  // var sss =statusStar.style = "background-color: " + bg + ";margin-left: " + statsstarWidth + "%;"
});

// Update Current address for the Driver
$("body").on("click", "#Update_current_loc", function (e) {
  var current_loc = $(this).parent()[0].children[0].value;
  var lat = $(this).parent()[0].children[1].value;
  var lng = $(this).parent()[0].children[2].value;
  var truck_no = $(this).parent()[0].children[3].value;
  var c_loc_des = document.getElementsByName("c_loc_des[]");
  var c_loc_pu = document.getElementsByName("c_loc_pu[]");
  var c_loc_distance = document.getElementsByName("c_loc_distance[]");
  var c_loc_duration = document.getElementsByName("c_loc_duration[]");
  var o_loc_distance = document.getElementsByName("o_loc_distance[]");

  if (current_loc == "" || typeof current_loc == "undefined") {
    $(this).parent()[0].children[0].style = "border: 1px solid red;";
    $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
    $("#alert_msg").delay(30000).hide(500);
    $("#alert_msg").html("Current Location can not be blank!");
  } else {
    var pickup = [];
    var destination = [];
    var duration = [];
    var distance = [];
    var originaDistance = [];

    for (i = 0; i < c_loc_des.length; i++) {
      destination.push(c_loc_des[i].value);
      pickup.push(c_loc_pu[i].value);
      distance.push(c_loc_distance[i].value);
      duration.push(c_loc_duration[i].value);
      originaDistance.push(o_loc_distance[i].value);
    }

    var data = JSON.stringify({
      current_loc: current_loc,
      lat: lat,
      lng: lng,
      destination: destination,
      pickup: pickup,
      distance: distance,
      originaDistance: originaDistance,
      duration: duration,
      truck_no: truck_no,
    });

    var userid = $("#load_id").val();
    $.ajax({
      method: "POST",
      url:
        "./Assets/backendfiles/index.php?action_type=currentlocUpdate&id=" +
        userid,
      data: {
        data: data,
      },
      success: function (data) {
        data = JSON.parse(data)[0];
        if (data.success == 1) {
          setTimeout(function () {
            // var style = 'style="display: block; background-color: #7dc77d;"'
            // var msg = data.msg
            // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
            // $('#alertmsg_container').append(msgdiv)

            $("#alert_msg").css({
              display: "block",
              backgroundColor: "#7dc77d",
            });
            $("#alert_msg").html(data.msg);
            $("#check_calls").html(data.rows);

            const dis = document.querySelectorAll(
              ".update_load_location_staus"
            );
            dis.forEach((di) => {
              di.style.backgroundColor = "var(--container)";
            });
            $('select[name="update_load_status[]"]').removeAttr(
              "disabled",
              "disabled"
            );
            $("#Update_current_loc").removeAttr("disabled", "disabled");
            const inputs = document.querySelectorAll("#current_loc");

            inputs.forEach((input) => {
              input.value = "";
            });

            btn.textContent = "Update";
            btn.style.backgroundColor = "var(--button)";
            $("#alert_msg").delay(30000).hide(500);
          }, 1000);
        } else {
          // var style = 'style="display: block; background-color: #7dc77d;"'
          // var msg = data.msg
          // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
          // $('#alertmsg_container').append(msgdiv)

          $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
          $("#alert_msg").delay(30000).hide(500);
          $("#alert_msg").html(data.msg);
          btn.textContent = "Add";
          btn.style.backgroundColor = "var(--button)";
          $("#Update_current_loc").removeAttr("disabled", "disabled");
        }
      },
    });
  }
});

$("body").on("click", ".tab_nav", function (e) {
  var activevan = $(this).index();

  $(".dis_tab.active").removeClass("active");
  $(".tab_nav.active").removeClass("active");

  for (i = 0; i < $(".dis_tab").length; i++) {
    if (($("div.dis_tab")[i].style.display = "flex")) {
      $("div.dis_tab")[i].style.display = "none";
    }
  }

  $(this).addClass(" active");
  $(".dis_tab").eq(activevan).css("display", "flex");
});



// Load Load rating
$(document).on("submit", "form#load_rating", function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  var id = $("#load_id").val();

  $("body .load_rating_btn").html("Adding...");
  $("body .load_rating_btn").style = "background-color: var(--button);";
  $("body .load_rating_btn").attr("disabled", "disabled");

  $.ajax({
    url: "./Assets/backendfiles/index.php?action_type=load_rating&id=" + id,
    type: "POST",
    data: formData,
    success: function (data) {
      data = JSON.parse(data)[0];
      if (data.success == 1) {
        $("#alert_msg").css({ display: "block", backgroundColor: "#7dc77d" });
        $("#alert_msg").html(data.msg);
        $("#load_rating")[0].reset();
        $("body form#load_rating").html(data.rows);
        $("#alert_msg").delay(30000).hide(500);

        $("body .load_rating_btn").html("Add");
        $("body .load_rating_btn").style = "background-color: var(--button);";
        $("body .load_rating_btn").removeAttr("disabled", "disabled");
      } else {
        $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
        $("#alert_msg").delay(30000).hide(500);
        $("#alert_msg").html(data.msg);
        $("body .load_rating_btn").html("Add");
        $("body .load_rating_btn").style = "background-color: var(--button);";
        $("body .load_rating_btn").removeAttr("disabled", "disabled");
      }
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});

// Load Driver Rating
$(document).on("submit", "form#driver_rating", function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  var id = $("#load_id").val();

  $("body .driver_rating_btn").html("Adding...");
  $("body .driver_rating_btn").style = "background-color: var(--button);";
  $("body .driver_rating_btn").attr("disabled", "disabled");

  $.ajax({
    url: "./Assets/backendfiles/index.php?action_type=driver_rating&id=" + id,
    type: "POST",
    data: formData,
    success: function (data) {
      data = JSON.parse(data)[0];
      if (data.success == 1) {
        $("#alert_msg").css({ display: "block", backgroundColor: "#7dc77d" });
        $("#alert_msg").html(data.msg);
        $("#driver_rating")[0].reset();
        $("body form#driver_rating").html(data.rows);
        $("#alert_msg").delay(30000).hide(500);

        $("body .driver_rating_btn").html("Add");
        $("body .driver_rating_btn").style = "background-color: var(--button);";
        $("body .driver_rating_btn").removeAttr("disabled", "disabled");
      } else {
        $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
        $("#alert_msg").delay(30000).hide(500);
        $("#alert_msg").html(data.msg);
        $("body .driver_rating_btn").html("Add");
        $("body .driver_rating_btn").style = "background-color: var(--button);";
        $("body .driver_rating_btn").removeAttr("disabled", "disabled");
      }
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});

function initMap(ele, lat, lng) {
  var startAutocomplete = new google.maps.places.Autocomplete(ele);

  startAutocomplete.addListener("place_changed", function () {
    place = startAutocomplete.getPlace();
    lat.value = place.geometry.location.lat();
    lng.value = place.geometry.location.lng();
    endValue = place.formatted_address;
  });
}

function showAlternativeRoutes(
  directionsService,
  directionsRenderer,
  startValue,
  endValue,
  disele,
  durele
) {
  var clobtn = document.getElementById("Update_current_loc");
  clobtn.setAttribute("disabled", "disabled");
  clobtn.textContent = "Processing...";
  directionsService.route(
    {
      origin: startValue,
      destination: endValue,
      travelMode: "DRIVING",
      provideRouteAlternatives: true,
    },
    function (response, status) {
      if (status === "OK") {
        var totle_routes = response.routes.length - 1;

        var distance = response.routes[totle_routes].legs[0].distance.text;
        var duration = response.routes[totle_routes].legs[0].duration.text;

        disele.value = distance;
        durele.value = duration;

        clobtn.removeAttribute("disabled", "disabled");
        clobtn.textContent = "Update";

      } else {
        clobtn.removeAttribute("disabled", "disabled");
        clobtn.textContent = "Update";

        window.alert("Directions request failed due to " + status);
      }
    }
  );
}

// Autocompalete
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function (e) {
    var a,
      b,
      i,
      val = this.value;
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
        b.addEventListener("click", function (e) {
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
  inp.addEventListener("keydown", function (e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
      /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
      currentFocus++;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 38) {
      //up
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
    if (currentFocus < 0) currentFocus = x.length - 1;
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

$("#brokercompany").on("click", function (e) {
  $.ajax({
    method: "GET",
    url: "./Assets/backendfiles/index.php?action_type=brokercompany_list",
    data: {},
    success: function (data) {
      data = JSON.parse(data)[0];
      if (data.success == 1) {
        autocomplete(document.getElementById("brokercompany"), data.brokers);
      }
    },
  });
});

// Get the broker agents for brokerage company
$("body").on("change", "select#broker", function (e) {
  // var optionSelected = $("option:selected", this)
  var optionSelected = $("option:selected", this).text();
  var brokeragent = $(this).parent().parent().parent()[0].children[1]
    .children[1];

  $.ajax({
    method: "GET",
    url: "./Assets/backendfiles/index.php?action_type=broker_agentList",
    data: {
      brokerage: optionSelected,
    },
    success: function (data) {
      data = JSON.parse(data);
      if (data) {
        brokeragent.innerHTML = data;
      }
    },
  });
});

// Add new broker
$("#brokersubmit").on("click", function (e) {
  e.preventDefault();

  $(this).html("Adding...");
  $(this).style = "background-color: #dadada;";
  $(this).attr("disabled", "disabled");

  $.ajax({
    type: "POST",
    url: "./Assets/backendfiles/index.php?action_type=broker_submit",
    data: $("#broker_form").serialize(),
    success: function (data) {
      data = JSON.parse(data)[0];
      if (data.success == "1") {
        setTimeout(function () {
          // var style = 'style="display: block; background-color: #7dc77d;"'
          // var msg = data.msg
          // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
          // $('#alertmsg_container').append(msgdiv)

          $("#alert_msg").css({ display: "block", backgroundColor: "#7dc77d" });
          $("#alert_msg").html(data.msg);
          $("#broker_form")[0].reset();
          $(".broker").html(data.rows);

          $("#brokersubmit").html("Submit");
          $("#brokersubmit").style = "background-color: #dadada;";
          $("#brokersubmit").removeAttr("disabled", "disabled");

          $("#alert_msg").delay(30000).hide(500);
        }, 1000);
      } else {
        // var style = 'style="display: block; background-color: #7dc77d;"'
        // var msg = data.msg
        // var msgdiv = $('<div id="alert_msg" class="alert_msg" ' + style + '><span class="close_alrt_msg">x</span>' + msg + '</div>')
        // $('#alertmsg_container').append(msgdiv)

        $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
        $("#alert_msg").delay(30000).hide(500);
        $("#alert_msg").html(data.msg);
        $("#brokersubmit").html("Submit");
        $("#brokersubmit").style = "background-color: #dadada;";
        $("#brokersubmit").removeAttr("disabled", "disabled");
      }
    },
  });
});

// Delete Load
$(
  "#loadBoard1, #loadBoard2, #loadBoard3, #loadBoard4, #loadBoard5, #loadBoard6"
).on("click", ".load_action", function (e) {
  var loadID = $(this).data("load_id");
  var action_type = $(this).data("action_type");
  if (action_type == "delete") {
    var confirmed = confirm("Are you sure you want to delete!");
  } else {
    var confirmed = confirm("Are you sure you want to Update!");
  }

  if (confirmed) {
    // $(".loader")[1].style.display = "flex";
    $.ajax({
      type: "POST",
      url:
        "./Assets/backendfiles/index.php?action_type=" +
        action_type +
        "&id=" +
        loadID,
      data: loadID,
      success: function (data) {
        data = JSON.parse(data)[0];
        if (data.success == "1") {
          setTimeout(function () {
            $("#alert_msg").css({
              display: "block",
              backgroundColor: "#7dc77d",
            });
            $("#alert_msg").html(data.msg);

            fetchloadrows(
              ["opening", "posted", "bs_matched"],
              ["table1", "table2", "table3"]
            );

            fetchgdrows(
              ["Payable", "partially_paid", "Paid"],
              ["table4", "table5", "table6"]
            );

            $(".loader")[1].style.display = "none";
            $("#alert_msg").delay(30000).hide(500);
          }, 1000);
        } else {
          $("#alert_msg").css({ display: "block", backgroundColor: "#ce6c6c" });
          $("#alert_msg").delay(30000).hide(500);
          $("#alert_msg").html(data.msg);
          // $(".loader")[1].style.display = "none";
        }
      },
    });
  }
});



$("body").on("click", ".load_edit_close", function (e) {
  $(".newloadedit_modal")[0].remove();
});

// Load dispatcher form
$("body").on("click", ".load_dispatcher_form", function (e) {
  var load_id = $(this).data("load_id");

  $.ajax({
    url: "./components/dispatcher.php",
    method: "POST",
    data: { dispatcher_form: 1, load_id: load_id },
    success: function (data) {
      $("body").append(data);
    },
  });
});

// Remove dispatcher FOrm
$("body").on("click", ".close_dispatcher.close", function (e) {
  $(this).parent().parent().remove();
});



// Add multiple inputs dynamically
$(document).ready(function () {
  var maxField = 10; //Input fields increment limitation
  var addButton = $(".add_button"); //Add button selector
  var wrapper = $(".field_wrapper"); //Input field wrapper
  var fieldHTML =
    '<div class="inputgroup">' +
    '<div class="inputbox">' +
    '<input class="start pac-target-input" type="text" name="pick_up_Location[]" value="" placeholder="Pick Up Location"/>' +
    '<span class="pu_blank_msg"></span>' +
    '<input type="text" name="start_lat[]" class="start_lat" hidden>' +
    '<input type="text" name="start_lng[]" class="start_lng" hidden>' +
    "</div>" +
    '<div class="inputbox" style="flex-direction: row;">' +
    '<div style="width: 100%;">' +
    '<input style="width: 100%;" type="text" name="destination[]" value="" class="end pac-target-input" placeholder="Drop off"/>' +
    '<input type="text" name="end_lat[]" class="end_lat" hidden>' +
    '<input type="text" name="end_lng[]" class="end_lng" hidden>' +
    '<input type="text" name="distance[]" class="distance" hidden>' +
    '<input type="text" name="duration[]" class="duration" hidden>' +
    "</div>" +
    '<a href="javascript:void(0);" class="remove_button">-</a>' +
    "</div></div>"; //New input field html
  var x = 1; //Initial field counter is 1

  fetchloadrows(
    ["opening", "posted", "bs_matched"],
    ["table1", "table2", "table3"]
  );

  fetchgdrows(
    ["Payable", "partially_paid", "Paid"],
    ["table4", "table5", "table6"]
  );
  //Once add button is clicked
  $("body").on("click", ".add_button", function (e) {
    //Check maximum number of input fields
    if (x < maxField) {
      x++; //Increment field counter
      $(this).parent().parent().parent().parent().append(fieldHTML);

      // $(wrapper).append(fieldHTML); //Add field html
      $(".msg").html(" ");
      $(".msg").removeClass("error");
    } else {
      $(".msg").html("Max Limit reached.");
      $(".msg").addClass("error");
    }
  });

  //Once remove button is clicked
  $("body").on("click", ".remove_button", function (e) {
    e.preventDefault();
    $(this).parent()[0].parentElement.remove(); //Remove field html
    x--; //Decrement field counter
    if (x-- < -7 || x-- <= 8) {
      $(".msg").html(" ");
      $(".msg").removeClass("error");
    }
  });

  $("body").on("click", ".brokerDetailsbtn", function (e) {
    $(".brokerdetails").show();
  });

  $("body").on("click", ".truckNumberbtn", function (e) {
    $(".truckNumber").show();
  });

  $("body").on("click", ".truck_close", function (e) {
    $(".truckNumber").hide();
  });

  $("body").on("click keydown", ".start", function (e) {
    var lat = e.target.nextElementSibling.nextElementSibling;
    var lng = e.target.nextElementSibling.nextElementSibling.nextElementSibling;
    initMap(this, lat, lng);
  });

  // Current Location Events
  $("body").on(" change", "#current_loc", function (e) {
    var lat = e.target.nextElementSibling;
    var lng = e.target.nextElementSibling.nextElementSibling;
    initMap(this, lat, lng);
    var thisval = $(this)[0].value;

    if (typeof e.which == "undefined") {
      var c_loc_des = document.getElementsByName("c_loc_des[]");
      var c_loc_pu = document.getElementsByName("c_loc_pu[]");
      var c_loc_distance = document.getElementsByName("c_loc_distance[]");
      var c_loc_duration = document.getElementsByName("c_loc_duration[]");
      var directionsService = new google.maps.DirectionsService();
      var directionsRenderer = new google.maps.DirectionsRenderer();

      setTimeout(function () {
        for (i = 0; i < c_loc_des.length; i++) {
          showAlternativeRoutes(
            directionsService,
            directionsRenderer,
            $("#current_loc").val(),
            c_loc_des[i].value,
            c_loc_distance[i],
            c_loc_duration[i]
          );
        }
      }, 1000);
    }
  });
});

function fetchloadrows(query = [], tableIds = []) {
  for (var i = 0; i < query.length; i++) {
    (function (i) {
      $.ajax({
        url: "./components/load_data.php?" + query[i] + "=1",
        method: "POST", // Corrected typo here
        success: function (data) {
          $("#" + tableIds[i])
            .DataTable()
            .destroy();
          $("#" + tableIds[i] + " > tbody").html(data);
          $("#" + tableIds[i])
            .DataTable()
            .draw();
        },
      });
    })(i);
  }
}

function fetchgdrows(query = [], tableIds = []) {
  for (var i = 0; i < query.length; i++) {
    (function (i) {
      $.ajax({
        url: "./components/gd_data.php?" + query[i] + "=1",
        method: "POST", // Corrected typo here
        success: function (data) {
          $("#" + tableIds[i])
            .DataTable()
            .destroy();
          $("#" + tableIds[i] + " > tbody").html(data);
          $("#" + tableIds[i])
            .DataTable()
            .draw();
        },
      });
    })(i);
  }
}
