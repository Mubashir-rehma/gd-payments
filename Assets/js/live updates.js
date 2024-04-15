

$(document).ready(function(){

    // Filter By label
    // $("body").on("click", ".label_filter", function(e){
    //     var label_id = $(this).data("label_id")

        $.ajax({
            url: "./components/load_data.php",
            method: "POST",
            data: {live_update: 1},
            success: function(data){
                $(".live_updates_tb").html(data)
                $(".assigned_to").select2({
                  // allowClear: true,
                  placeholder: "Assign a member",
                  language: {
                    noResults: function() {
                      return '<div class="select2-link2 select2-close" ><a href="./users.php">Add More members</a></div>';
                    },
                  },
                  escapeMarkup: function(markup) {
                    return markup;
                  },
                         
                })

                

            },
            error: function(data) {
                alertBtn("Something went wrong!", "#fa4e4e")
                data="<td colspan='8'> Something Went Wrong! </td>" 
                $(".live_updates_tb").html(data)
            }
        })
    // })
  $(function() {
      $("#assigned_to").select2();
  })

  $('.live_status').select2();

  // Listen for the "select2:unselect" event
  $('.live_status').on('select2:unselect, select2:remove', function (e) {
      var unselectedValue = e.params.data.id;
      console.log("removed");

      // Send an AJAX request to remove the unselected value from the database
      $.ajax({
          type: 'POST',
          url: '/Assets/backendfiles/live_updates.php?action_type=remove_select', // Replace with your PHP script's URL
          data: { unselectedValue: unselectedValue },
          success: function (response) {
              // Handle success response if needed
              console.log('Value removed successfully from the database.');
          },
          error: function () {
              // Handle error if needed
              console.error('Error removing value from the database.');
          }
      });
  });


  // Listen for the "select2:unselect" event
  $('body').on('select2:unselect', '.assigned_to', function (e) {
    var load_id = $(this).data("load_id")
    var col = $(this).data("column")
    var val = $(this).val()

    // Send an AJAX request to remove the unselected value from the database
    $.ajax({
        type: 'POST',
        url: './Assets/backendfiles/live_updates.php?action_type=delete_assigned_users', // Replace with your PHP script's URL
        data: {load_id: load_id, col: col, val: val},
        success: function (response) {
            // Handle success response if needed
        },
        error: function () {
            // Handle error if needed
            console.error('Error removing value from the database.');
        }
    });

    
  });

  // Listen for the "select2:unselect" event
  $('body').on('select2:select', '.assigned_to', function (e) {
    var load_id = $(this).data("load_id")
    var col = $(this).data("column")
    var val = $(this).val()


    url = './Assets/backendfiles/live_updates.php?action_type=live_update'
    if(col == "live_notes"){
        url = './Assets/backendfiles/live_updates.php?action_type=notes_update'
    }

    $.ajax({
        url: url,
        method: "POST",
        data: {load_id: load_id, col: col, val: val},
        success: function(data){
            data = JSON.parse(data)
            // alertBtn(data.msg)
        }
    })

    
  });

  // Listen for the "select2:unselect" event
  // $('body .assigned_to').on('select2:optionRemoved', function (e) {
  //   console.log("removed");
  //     var unselectedValue = e.params.data.id;
      

  //     // Send an AJAX request to remove the unselected value from the database
  //     $.ajax({
  //         type: 'POST',
  //         url: '/Assets/backendfiles/live_updates.php?action_type=remove_select', // Replace with your PHP script's URL
  //         data: { unselectedValue: unselectedValue },
  //         success: function (response) {
  //             // Handle success response if needed
  //             console.log('Value removed successfully from the database.');
  //         },
  //         error: function () {
  //             // Handle error if needed
  //             console.error('Error removing value from the database.');
  //         }
  //     });
  // });
    // $("#assigned_to").select2()

})
// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }

// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }
// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }

// // Show color div and hide the previously visible one
// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }

// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }

// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }

// var lastClickedElement = null;

// // Function to hide the color div and reset the last clicked element
// function hideColorDiv() {
//   if (lastClickedElement) {
//     $(lastClickedElement).find(".color").hide();
//     lastClickedElement = null;
//   }
// }

// var lastClickedElement = null;

var lastClickedElement = null;


// Show color div and hide the previously visible one
$("body").on("click", ".td, td", function(e) {
  $(".color").hide()
  var currentElement = this;
  var $colorDiv = $(currentElement).find(".color");
  $colorDiv.css("display", "flex");
});

$("body").on("click", '.color-icon', function(e){
  $(this).next().trigger("click")
})

// Function to show the color options when clicking on the icon
function showColorOptions(inputClass) {
  
  // console.log($(this).next()); //trigger
  $(this).next().trigger("click")
  // this.nextElementSibling.click()
  // var colorInput = $(this).closest ("." + inputClass).first();
  // console.log(colorInput);
  // colorInput.click();
  // e.stopPropagation(); 
}

// Prevent color input click event from propagating to the parent div
$("body").on("click", ".color input[type='color'], .text-color-input, .bg-input-color, .td, td, .color-icon", function(e) {
  e.stopPropagation();
});

// Hide color div when clicking anywhere else on the body
$("body").on("click", function(event) {
  if (!$(event.target).closest(".color").length) {
    $(".color").hide()
    // hideColorDiv();
  }
});

// Update values on change of input
$("body").on("change", ".live_status, .live_update, .live_hrs, .live_notes", function(e){
    var load_id = $(this).data("load_id")
    var col = $(this).data("column")
    var val = $(this).val()


    url = './Assets/backendfiles/live_updates.php?action_type=live_update'
    if(col == "live_notes"){
        url = './Assets/backendfiles/live_updates.php?action_type=notes_update'
    }

    $.ajax({
        url: url,
        method: "POST",
        data: {load_id: load_id, col: col, val: val},
        success: function(data){
            data = JSON.parse(data)
            // alertBtn(data.msg)
        }
    })
})


$("body").on("change", ".text-color-input", function(e){
    var color = $(this).val()
    var td = $(this).parent().parent().parent().parent()[0]
    this.previousElementSibling.style.color=this.value
    var load_id = $(this).parent().parent().parent().parent().parent()[0].querySelector(".load_id").value
    
    var col = $(this).data("col")

    try{
        if(td.querySelector(".live_update") !== null) { td.querySelector(".live_update").style.color = color }
        if(td.querySelector(".live_status") !== null) { td.querySelector(".live_status").style.color = color }
        if(td.querySelector(".live_hrs") !== null) { td.querySelector(".live_hrs").style.color = color }
        if(td.querySelector(".live_notes") !== null) { td.querySelector(".live_notes").style.color = color }
        if(td.querySelector(".select2-container--default .select2-selection--single .select2-selection__rendered") !== null) { td.querySelector(".select2-container--default .select2-selection--single .select2-selection__rendered").style.color = color }
    } catch(e){}
    
    td.style.color = color
    url = './Assets/backendfiles/live_updates.php?action_type=live_update'

    $.ajax({
        url: url,
        method: "POST",
        data: {load_id: load_id, col: col, val: color},
        success: function(data){
          $(".color").hide()
            data = JSON.parse(data)
            // alertBtn(data.msg)
        }
    })
})

$("body").on("change", ".bg-input-color", function(e){
    var color = $(this).val()
    var td = $(this).parent().parent().parent().parent()[0]
    this.previousElementSibling.style.color=this.value
    var load_id = $(this).parent().parent().parent().parent().parent()[0].querySelector(".load_id").value
    var col = $(this).data("col")

    try{
        if(td.querySelector(".live_update") !== null) { td.querySelector(".live_update").style.backgroundColor = color; td.style.backgroundColor = color}
        if(td.querySelector(".live_status") !== null) { td.querySelector(".live_status").style.backgroundColor = color }
        if(td.querySelector(".live_hrs") !== null) { td.querySelector(".live_hrs").style.backgroundColor = color; td.style.backgroundColor = color}
        if(td.querySelector(".live_notes") !== null) { td.querySelector(".live_notes").style.backgroundColor = color; td.style.backgroundColor = color}
        if(td.querySelector(".select2-container--default .select2-selection--single") !== null) { td.querySelector(".select2-container--default .select2-selection--single").style.backgroundColor = color }
    } catch(e){}
        
    url = './Assets/backendfiles/live_updates.php?action_type=live_update'

    $.ajax({
        url: url,
        method: "POST",
        data: {load_id: load_id, col: col, val: color},
        success: function(data){
          $(".color").hide()
            data = JSON.parse(data)
            // alertBtn(data.msg)
        }
    })
})

// $('body').click(function(event) {
//     // Check if the click target is not within the target div
//     if (!$(event.target).closest('.live_update_table').length) {
//         // If the click is outside the target div, hide the element to hide
//         $('.color').hide();
//     }
// });

// $('.live_update_table').click(function(event) {
//     event.stopPropagation();
// });

// Load dispatcher form
$("body").on("click", ".load_dispatcher_form", function(e){
    var load_id = $(this).data('load_id');

    $.ajax({
        url: "./components/dispatcher.php",
        method: "POST",
        data: {dispatcher_form: 1, load_id: load_id},
        success: function(data){
            $("body").append(data)
        }
    })
})

// Remove dispatcher FOrm
$("body").on("click", ".close_dispatcher.close", function(e){
    $(this).parent().parent().remove()
})



//  // Delete select2 record
//  $("body").on("click", ".assigned_to", function (e) {

//   var load_id = $(this).data("load_id");

//   var confirmation = confirm("Are you sure you want to delete the Record!");

//   if (confirmation) {
//     $.ajax({
//       url: "./Assets/backendfiles/load_updates.php?action_type=assigned_to",
//       method: "POST",
//       data: { load__id: load_id },
//       success: function (data) {
//         data = JSON.parse(data)[0];
//         if (data.success == 1) {
//           var alert_msg =
//             '<div id="alert_msg" style="display: block; background-color: #7dc77d;">' +
//             data.msg +
//           $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
//           $(".select2-selection__choice__remove").remove();
//           updateTable(
//             "./live updates.php",
//             "live_updates_tb",
//             "live_update_table"
//           );

//           $.ajax({
//             url: "./live updates.php",
//             method: "POST",
//             data: { load_id: load_id, live_updates_tb: 1 },
//             success: function (data) {
//               $("body").append(data);
//             },
//           });
//         }
//       },
//     });
//   }
// });

// Allert Message and button rest
function alertBtn(alert_msg, bg="#7dc77d"){
    var alert_msg = '<div id="alert_msg" style="display: block; background-color: "' + bg + '>' + alert_msg + '<span class="close_msg">x</span></div>';
    $("#alert_msgs").append(alert_msg).delay(30000).hide(500)

    // $("#alert_msg").css({display: "block", backgroundColor: bg})
    // $("#alert_msg").html(alert_msg);
    // $("#alert_msg").delay(30000).hide(500);
}


// Remove alert message
$("body").on("click", ".close_msg", function(e){
    $(this).parent().remove()
})


$('body').on("mouseenter", ".ktooltip", function(e){
  var load_id = $(this).data("load_id")
  var col = $(this).data("colu")
  var info = $(this).next()

  $.ajax({
    url: './Assets/backendfiles/live_updates.php?action_type=fetch_updates',
    data: {load_id: load_id, col: col},
    method: "post",
    success: function (data){
      data = JSON.parse(data)
      info.html(data.data)
    }
  })
})