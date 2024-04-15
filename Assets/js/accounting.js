var $j = jQuery.noConflict();

$(".tab_navs").on("click", ".tab_nav", function () {
  var activevan = $(this).index();

  if (activevan !== 3 && activevan !== 2) {
    $(".accounting.tab.active").removeClass("active");
    $(".tab_nav.active").removeClass("active");

    for (i = 0; i < $(".accounting.tab").length; i++) {
      if (($("div.accounting.tab")[i].style.display = "flex")) {
        $("div.accounting.tab")[i].style.display = "none";
      }
    }

    $(this).addClass(" active");
    $("div.accounting.tab")[activevan].style.display = "flex";
  } else {
    console.log("Sub nav");
    $(".tab_nav.active").removeClass("active");
    $(this).addClass(" active");
    if ($(this)[0].children[1].style.display == "flex") {
      $(this)[0].children[1].style.display = "none";
    } else {
      $(this)[0].children[1].style.display = "flex";
    }
  }
});

$("body").on("click", ".d_nsv", function () {
  $(".loader")[0].style.display = "flex";
  var activevan = $(this).index();

  $(".d_tab.active").removeClass("active");
  $(".d_nsv.active").removeClass("active");

  for (i = 0; i < $(".d_tab").length; i++) {
    if (($("div.d_tab")[i].style.display = "flex")) {
      $("div.d_tab")[i].style.display = "none";
    }
  }

  $(this).addClass(" active");
  $("div.d_tab")[activevan].style.display = "flex";
});

$(
  ".all_purchases_nav, .driver_nav, .unpaid_driver_nav, .sheduled_pay, .sheduled_payment_week"
).on("click", function (e) {
  for (i = 0; i < $(".accounting.tab").length; i++) {
    if (($("div.accounting.tab")[i].style.display = "flex")) {
      $("div.accounting.tab")[i].style.display = "none";
    }
  }

  $(".sub_nav.active").removeClass("active");
  $(this).addClass("active");

  if ($(this).hasClass("all_purchases_nav")) {
    console.log("all purchase .");
    $("div.accounting.tab.all_purchases")[0].style.display = "flex";
  } else if ($(this).hasClass("driver_nav")) {
    console.log("driver nav.");
    $("div.accounting.tab.driver")[0].style.display = "flex";
  } else if ($(this).hasClass("unpaid_driver_nav")) {
    console.log("unpaid driver");
    $("div.accounting.tab.driver_unpaid")[0].style.display = "flex";
  } else if ($(this).hasClass("sheduled_payment_week")) {
    console.log("Sheduled payment for next week");
    $("div.accounting.tab.next_week_sheduled_pay")[0].style.display = "flex";
  } else {
    console.log("sheduled pay");
    $("div.accounting.tab.sheduled_payment")[0].style.display = "flex";
  }
});

// unpaid load
$(".load_nav, .unpaid_nav").on("click", function (e) {
  $(".sub_nav.active").removeClass("active");
  $(this).addClass("active");
  for (i = 0; i < $(".accounting.tab").length; i++) {
    if (($("div.accounting.tab")[i].style.display = "flex")) {
      $("div.accounting.tab")[i].style.display = "none";
    }
  }

  console.log("Loop finished executing."); // Log when the loop finishes

  $(".sub_navs.active").removeClass("active");
  $(this).addClass(" active");

  if ($(this).hasClass("unpaid_nav")) {
    console.log("Clicked element has class 'loads_unpaid'.");
    $("div.accounting.tab.loads_unpaid")[0].style.display = "flex";
  } else {
    console.log("Clicked element does not have class 'unpaid.nav'.");
    $("div.accounting.tab.sales")[0].style.display = "flex";
  }

  console.log("Click event handler finished executing."); // Log when the click event handler finishes
});

// Data Tables
$(document).ready(function () {
  // New vendor btn to open vendor form
  $(".vendor_btn").on("click", function (e) {
    $("#new_vendor").show();
  });

  // New item btn to open item form
  $(".add_new_item").on("click", function (e) {
    $("#new_item").show();
  });

  // open Settings form
  $(".setting").on("click", function (e) {
    $("#payment_method_status").show();
  });

  $(".cancel").on("click", function (e) {
    $(this).parent().parent().parent().parent().parent().hide();
  });

  $("body").on("click", ".close", function (e) {
    $(this).parent().parent().hide();
  });

  $(".new_pur").on("click", function (e) {
    $("#new_purchase").show();
  });

  // Open Contract Details
  $("body").on("click", ".contract", function (e) {
    var truck_id = $(this).data("driver_id");
    $.ajax({
      url: "Accounting/driver_details.php",
      method: "POST",
      data: { truck_id: truck_id },
      success: function (data) {
        $(".contracts").html(data);
      },
    });
  });

  // Remove dispatcher FOrm
  $("body").on("click", ".close_dispatcher.close", function (e) {
    $(this).parent().parent().remove();
  });

  $("body").on("click", ".contract_tabn", function (e) {
    $.ajax({
      url: "Accounting/contracts.php",
      method: "GET",
      success: function (data) {
        $(".contracts").html(data);
      },
    });
  });

  // FIlter contracts
  $("body").on("click", "#driver_con_filter", function (e) {
    var driver_name = $("#filter_by_driver_name").val();
    var truck_num = $("#filterC_by_truck_no").val();

    $.ajax({
      url: "Accounting/contracts.php",
      method: "POST",
      data: { driver_name: driver_name, truck_num: truck_num },
      success: function (data) {
        $(".contracts").html(data);
      },
    });
  });

  // Open Load from contract details table
  $("body").on("click", ".open_load_from_contract", function (e) {
    var load_id = $(this).data("load_id");

    $.ajax({
      url: "components/dispatcher.php",
      method: "POST",
      data: { dispatcher_form: 1, load_id: load_id },
      success: function (data) {
        $("body").append(data);
      },
    });
  });

  // Check if the page bottom is reached and scroll more data
  var countScroll = 0;
  $(window).scroll(function () {
    var resords = $(".contract").length;
    if (
      $(window).scrollTop() == $(document).height() - $(window).height() &&
      resords > 0
    ) {
      console.log(resords);
      $.ajax({
        url: "Accounting/contracts.php",
        method: "POST",
        data: { startLimit: resords },
        success: function (data) {
          $(".contracts").append(data);
        },
      });
    }
    countScroll++;
  });

  /*
      We want to preview images, so we need to register the Image Preview plugin
      */
  FilePond.registerPlugin(
    // encodes the file as base64 data
    FilePondPluginFileEncode,

    // validates the size of the file
    FilePondPluginFileValidateSize,

    // corrects mobile image orientation
    FilePondPluginImageExifOrientation,

    // previews dropped images
    FilePondPluginImagePreview
  );

  // Select the file input and use create() to turn it into a pond
  pond = FilePond.create(document.querySelector("input#attachments"), {
    allowMultiple: true,
    instantUpload: false,
    allowProcess: false,
  });

  $("#upload_form").submit(function (e) {
    e.preventDefault();
    var fd = new FormData(this);
    // append files array into the form data
    pondFiles = pond.getFiles();
    for (var i = 0; i < pondFiles.length; i++) {
      fd.append("attachments[]", pondFiles[i].file);
    }

    $.ajax({
      url: "fileupload2.php",
      type: "POST",
      data: fd,
      dataType: "JSON",
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        //    todo the logic
        // remove the files from filepond, etc
      },
      error: function (data) {
        //    todo the logic
      },
    });
  });

  var dragTimer;
  $("input#attachments").on("dragover", function (e) {
    var dt = e.originalEvent.dataTransfer;
    if (
      dt.types &&
      (dt.types.indexOf
        ? dt.types.indexOf("Files") != -1
        : dt.types.contains("Files"))
    ) {
      $("#dropzone").show();
      window.clearTimeout(dragTimer);
    }
  });
  $("input#attachments").on("dragleave", function (e) {
    dragTimer = window.setTimeout(function () {
      $("#dropzone").hide();
    }, 25);
  });

  // Vendor Attachments
  ven_attachments = FilePond.create(
    document.querySelector("input#ven_attachments"),
    {
      allowMultiple: true,
      instantUpload: false,
      allowProcess: false,
    }
  );

  // Vendor Attachments
  pur_attachments = FilePond.create(
    document.querySelector("input#pur_attachments"),
    {
      allowMultiple: true,
      instantUpload: false,
      allowProcess: false,
    }
  );

  // Add new vendor
  $("#new_vendor_form").submit(function (e) {
    e.preventDefault();
    var fd = new FormData(this);
    // append files array into the form data
    pondFiles = ven_attachments.getFiles();
    // console.log(pondFiles)
    for (var i = 0; i < pondFiles.length; i++) {
      fd.append("ven_attachments[]", pondFiles[i].file);
    }

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=new_vendor",
      type: "POST",
      data: fd,
      dataType: "JSON",
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        if (data[0].success == 1) {
          updateTable(
            "./Accounting/vendors.php",
            "vendor_table_body",
            "vendor_table"
          );
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#new_vendor_form")[0].reset();

          if (ven_attachments.getFiles().length != 0) {
            for (var i = 0; i < ven_attachments.getFiles().length + 4; i++) {
              ven_attachments.removeFile(ven_attachments.getFiles()[0].id);
            }
          }
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Update Sale PAyment
  $("#sale_payment_update_form").submit(function (e) {
    e.preventDefault();
    var fd = {
      id: $("#sale_payment_update_form #id").val(),
      total_amount: $("#sale_payment_update_form #total_amount").val(),
      payment_date: $("#sale_payment_update_form #payment_date").val(),
      payment_method: $("#sale_payment_update_form #payment_method").val(),
      paid_amount: $("#sale_payment_update_form #paid_amount").val(),
      payment_status: $("#sale_payment_update_form #payment_status").val(),
    };

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=sale_payment_update",
      type: "POST",
      data: fd,
      dataType: "JSON",
      success: function (data) {
        if (data[0].success == 1) {
          updateTable("./Accounting/sales.php", "salesTableBody", "salesTable");
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#sale_payment_update_form")[0].reset();
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Update Purchase PAyment
  $("#pur_payment_update_form").submit(function (e) {
    e.preventDefault();

    var quick_pay = "";
    if (document.getElementById("quick_pay").checked) {
      console.log("checked");
      quick_pay = $("#pur_payment_update_form #quick_pay").val();
    }
    var fd = {
      id: $("#pur_payment_update_form #id").val(),
      total_amount: $("#pur_payment_update_form #total_amount").val(),
      payment_date: $("#pur_payment_update_form #payment_date").val(),
      payment_method: $("#pur_payment_update_form #payment_method").val(),
      paid_amount: $("#pur_payment_update_form #paid_amount").val(),
      payment_status: $("#pur_payment_update_form #payment_status").val(),
      quick_pay: quick_pay,
    };

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $(".loader")[1].style.display = "flex";

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=pur_payment_update",
      type: "POST",
      data: fd,
      dataType: "JSON",
      success: function (data) {
        if (data[0].success == 1) {
          updateTable(
            "./Accounting/driver.php",
            "driver_table_body",
            "driver_table"
          );
          alertBtn(data[0].msg, ".submit");

          if (data[0].mailStatus == "Please add the driver email First!") {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #de6262">' +
              data[0].mailStatus +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          } else {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: green;">' +
              data[0].mailStatus +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          }

          // Rest Form
          $("#pur_payment_update_form")[0].reset();
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Add new item
  $("#new_item_form").submit(function (e) {
    e.preventDefault();
    var fd = {
      item_name: $("#item_name").val(),
      account_type: $("#account_type").val(),
      prefered_vendor: $("#prefered_vendor").val(),
      item_notes: $("#item_notes").val(),
    };

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=new_item",
      type: "POST",
      data: fd,
      dataType: "JSON",
      // contentType: false,
      // cache: false,
      // processData: false,
      success: function (data) {
        if (data[0].success == 1) {
          updateTable("./Accounting/items.php", "items_body", "items_table");
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#new_item_form")[0].reset();
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Add new Purchase
  $("#new_Purchase").submit(function (e) {
    e.preventDefault();
    var fd = new FormData(this);
    // append files array into the form data
    pondFiles = pur_attachments.getFiles();
    // console.log(pondFiles)
    for (var i = 0; i < pondFiles.length; i++) {
      fd.append("pur_attachments[]", pondFiles[i].file);
    }

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=new_purchase",
      type: "POST",
      data: fd,
      dataType: "JSON",
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        if (data[0].success == 1) {
          updateTable(
            "./Accounting/other_purchases.php",
            "pur_table_body",
            "pur_table"
          );
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#new_Purchase")[0].reset();

          if (pur_attachments.getFiles().length != 0) {
            for (var i = 0; i < pur_attachments.getFiles().length + 4; i++) {
              pur_attachments.removeFile(pur_attachments.getFiles()[0].id);
            }
          }
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Add new Setting
  $("#payment_method_status_form").submit(function (e) {
    e.preventDefault();
    var fd = {
      setting_added_for: $("#setting_added_for").val(),
      setting_payment_method: $("#setting_payment_method").val(),
      setting_payment_status: $("#setting_payment_status").val(),
    };

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=payment_setting",
      type: "POST",
      data: fd,
      dataType: "JSON",
      // contentType: false,
      // cache: false,
      // processData: false,
      success: function (data) {
        if (data[0].success == 1) {
          updateTable("./Accounting/settings.php", "settings_div");
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#payment_method_status_form")[0].reset();
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Update Purchase Payment
  $("body").on("click", ".update_pur_payment", function (e) {
    $("#pur_payment_update").show();

    var total_amount = $(this).data("total_amount");
    var id = $(this).data("load_id");
    $("#pur_payment_update_form #id").val(id);
    $("#pur_payment_update_form #total_amount").val(total_amount);
    total_amount = "Total Amount: <strong>" + total_amount + "</strong>";
    $("#pur_payment_update_form #driver_dynamic_total_amount").html(
      total_amount
    );
  });

  // Update Sale Payment
  $("body").on("click", ".update_payment", function (e) {
    $("#sale_payment_update").show();

    var total_amount = $(this).data("total_amount");
    var id = $(this).data("load_id");
    $("#sale_payment_update #id").val(id);
    $("#sale_payment_update #total_amount").val(total_amount);
    total_amount = "Total Amount: <strong>" + total_amount + "</strong>";
    $("#sale_payment_update #dynamic_total_amount").html(total_amount);
  });

  // Update Statuses for Driver table
  $("body").on("click", ".driver_payment_update", function (e) {
    var status = $(this).data("action_type");
    var load_id = $(this).data("load_id");
    var total_amount = $(this).data("total_amount");

    $(".loader")[1].style.display = "flex";

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=driver_payment_update",
      method: "post",
      data: { load_id: load_id, status: status, total_amount: total_amount },
      success: function (data) {
        data = JSON.parse(data);
        if (data[0].success == 1) {
          updateTable(
            "./Accounting/driver.php",
            "driver_table_body",
            "driver_table"
          );
          alertBtn(data[0].msg, ".submit");

          if (data[0].mailStatus == "Please add the driver email First!") {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #de6262">' +
              data[0].mailStatus +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          } else {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: green;">' +
              data[0].mailStatus +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          }
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Update Sale Statuses
  $("body").on("click", ".sale_payment_action", function (e) {
    var status = $(this).data("action_type");
    var load_id = $(this).data("load_id");
    var total_amount = $(this).data("total_amount");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=sale_payment_status_update",
      method: "post",
      data: { load_id: load_id, status: status, total_amount: total_amount },
      success: function (data) {
        data = JSON.parse(data);
        if (data[0].success == 1) {
          updateTable("./Accounting/sales.php", "salesTableBody", "salesTable");
          alertBtn(data[0].msg, ".submit");
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Generate Invoice
  $("body").on("click", ".inv_generate", function () {
    var status = $(this).data("action_type");
    var load_id = $(this).data("load_id");
    var total_amount = $(this).data("total_amount");

    var win = window.open(
      `./Accounting/invoice.php?load_id=${load_id}&generate_inv=1`,
      "_blank"
    );
    if (win) {
      //Browser has allowed it to be opened
      $.ajax({
        url: "./Assets/backendfiles/accounting.php?action_type=sale_payment_status_update",
        method: "post",
        data: { load_id: load_id, status: status, total_amount: total_amount },
      });
      win.focus();
    } else {
      //Browser has blocked it
      alert("Please allow popups for this website");
    }

    // $.ajax({
    //     url: "./Accounting/invoice.php",
    //     method: "post",
    //     data: {load_id: load_id, total_amount: total_amount, generate_inv : 1},
    //     success: function (data) {
    //         $("body").append(data)

    //         $.ajax({
    //             url: "./Assets/backendfiles/accounting.php?action_type=sale_payment_status_update",
    //             method: "post",
    //             data: {load_id: load_id, status: status, total_amount: total_amount},
    //         })
    //         // data = JSON.parse(data)
    //         // if(data[0].success == 1){
    //         //     updateTable("./Accounting/sales.php", "salesTableBody", "salesTable")
    //         //     alertBtn(data[0].msg, ".submit")

    //         // } else if(data[0].success == 0){
    //         //     alertBtn(data[0].msg, ".submit", "#ce6c6c")
    //         // }
    //     },
    //     error: function (data) {
    //         alertBtn("Something went Wrong!", ".submit", "#ce6c6c")
    //     }
    // })
  });

  // close invoice
  $("body").on("click", ".invoice_close.close", function () {
    $(this).parent().parent().remove();
  });

  // Delete Item
  $("body").on("click", ".delet_item", function (e) {
    var item_id = $(this).data("item_id");

    var confirmation = confirm("Are you sure you want to delete the item?");

    if (confirmation) {
      $.ajax({
        url: "./Assets/backendfiles/accounting.php?action_type=delet_item",
        method: "post",
        data: { item_id: item_id },
        success: function (data) {
          data = JSON.parse(data);
          if (data[0].success == 1) {
            updateTable("./Accounting/items.php", "items_body", "items_table");

            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #7dc77d">' +
              data[0].msg +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);

            // $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
            // $("#alert_msg").html(data[0].msg);
            // $("#alert_msg").delay(30000).hide(500);
          } else if (data[0].success == 0) {
            alertBtn(data[0].msg, ".submit", "#ce6c6c");
          }
        },
        error: function (data) {
          alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
        },
      });
    }
  });

  // Delete Vendor
  $("body").on("click", ".delete_vendor", function (e) {
    var vendor_id = $(this).data("vendor_id");

    var confirmation = confirm("Are you sure you want to delete the Vendor?");

    if (confirmation) {
      $.ajax({
        url: "./Assets/backendfiles/accounting.php?action_type=delete_vendor",
        method: "post",
        data: { vendor_id: vendor_id },
        success: function (data) {
          data = JSON.parse(data);
          if (data[0].success == 1) {
            updateTable(
              "./Accounting/vendors.php",
              "vendor_table_body",
              "vendor_table"
            );

            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #7dc77d">' +
              data[0].msg +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);

            // $("#alert_msg").css({display: "block", backgroundColor: "#7dc77d"})
            // $("#alert_msg").html(data[0].msg);
            // $("#alert_msg").delay(30000).hide(500);
          } else if (data[0].success == 0) {
            alertBtn(data[0].msg, ".submit", "#ce6c6c");
          }
        },
        error: function (data) {
          alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
        },
      });
    }
  });

  // Remove alert message
  $("body").on("click", ".close_msg", function (e) {
    $(this).parent().remove();
  });

  // Filter unpaid load table by payment status
  $("body").on("change", "#unpaid_payment_status_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var payment_status = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_loads.php",
      method: "POST",
      data: { update_table: 1, payment_status: payment_status },
      success: function (data) {
        $("body #unpaidsalesTable").DataTable().destroy();
        $("body #unpaidsalesTableBody").html(data);
        $("body #unpaidsalesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter unpaid load table by load #
  $("body").on("change", "#filter_by_load_no_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_num = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_loads.php",
      method: "POST",
      data: { update_table: 1, load_num: load_num },
      success: function (data) {
        $("body #unpaidsalesTable").DataTable().destroy();
        $("body #unpaidsalesTableBody").html(data);
        $("body #unpaidsalesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter sale table by payment status
  $("body").on("change", "#load_status_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_status = $(this).val();

    $.ajax({
      url: "./Accounting/sales.php",
      method: "POST",
      data: { update_table: 1, load_status: load_status },
      success: function (data) {
        $("body #salesTable").DataTable().destroy();
        $("body #salesTableBody").html(data);
        $("body #salesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });
  // Filter sale table by payment status
  $("body").on("change", "#sale_payment_status_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var payment_status = $(this).val();

    $.ajax({
      url: "./Accounting/sales.php",
      method: "POST",
      data: { update_table: 1, payment_status: payment_status },
      success: function (data) {
        $("body #salesTable").DataTable().destroy();
        $("body #salesTableBody").html(data);
        $("body #salesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter sale table by load no
  $("body").on("change", "#filter_by_load_no", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_no = $(this).val();

    $.ajax({
      url: "./Accounting/sales.php",
      method: "POST",
      data: { update_table: 1, load_no: load_no },
      success: function (data) {
        $("body #salesTable").DataTable().destroy();
        $("#salesTableBody").html(data);
        $("body #salesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by payment status
  $("body").on("change", "#driver_payment_status_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var payment_status = $(this).val();

    $.ajax({
      url: "./Accounting/driver.php",
      method: "POST",
      data: { update_table: 1, payment_status: payment_status },
      success: function (data) {
        $("body #driver_table").DataTable().destroy();
        $("#driver_table_body").html(data);
        $("body #driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by truck Number
  $("body").on("change", "#filter_by_truck_no", function (e) {
    $(".loader")[1].style.display = "flex";
    var truck_number = $(this).val();

    $.ajax({
      url: "./Accounting/driver.php",
      method: "POST",
      data: { update_table: 1, truck_number: truck_number },
      success: function (data) {
        $("body #driver_table").DataTable().destroy();
        $("#driver_table_body").html(data);
        $("body #driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by truck Number
  $("body").on("change", "#filter_by_truck_no_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var truck_number_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_loads.php",
      method: "POST",
      data: { update_table: 1, truck_number_unpaid: truck_number_unpaid },
      success: function (data) {
        $("body #unpaidsalesTable").DataTable().destroy();
        $("#unpaidsalesTableBody").html(data);
        $("body #unpaidsalesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter broker table by truck Number
  $("body").on("change", "#filter_by_truck_no_sales", function (e) {
    $(".loader")[1].style.display = "flex";
    var truck_number_sales = $(this).val();

    $.ajax({
      url: "./Accounting/sales.php",
      method: "POST",
      data: { update_table: 1, truck_number_sales: truck_number_sales },
      success: function (data) {
        $("body #salesTable").DataTable().destroy();
        $("#salesTableBody").html(data);
        $("body #salesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by load no
  $("body").on("change", "#filter_by_load", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_no = $(this).val();

    $.ajax({
      url: "./Accounting/driver.php",
      method: "POST",
      data: { update_table: 1, load_no: load_no },
      success: function (data) {
        $("body #driver_table").DataTable().destroy();
        $("#driver_table_body").html(data);
        $("body #driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by payment method
  $("body").on("change", "#driver_payment_method_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var payment_method = $(this).val();

    $.ajax({
      url: "./Accounting/driver.php",
      method: "POST",
      data: { update_table: 1, payment_method: payment_method },
      success: function (data) {
        $("body #driver_table").DataTable().destroy();
        $("#driver_table_body").html(data);
        $("body #driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter broker table by payment method
  $("body").on("change", "#broker_payment_method_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var broker_payment_method = $(this).val();

    $.ajax({
      url: "./Accounting/sales.php",
      method: "POST",
      data: { update_table: 1, broker_payment_method: broker_payment_method },
      success: function (data) {
        $("body #salesTable").DataTable().destroy();
        $("#salesTableBody").html(data);
        $("body #salesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter broker table by unpaid loads
  $("body").on("change", "#broker_payment_method_filter_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var broker_payment_method_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_loads.php",
      method: "POST",
      data: {
        update_table: 1,
        broker_payment_method_unpaid: broker_payment_method_unpaid,
      },
      success: function (data) {
        $("body #unpaidsalesTable").DataTable().destroy();
        $("#unpaidsalesTableBody").html(data);
        $("body #unpaidsalesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // // Filter broker table by payment status
  // $("body").on("change", "#broker_payment_method_filter", function (e) {
  //   $(".loader")[1].style.display = "flex";
  //   var sales_payment_method = $(this).val();

  //   $.ajax({
  //     url: "./Accounting/sales.php",
  //     method: "POST",
  //     data: { update_table: 1, sales_payment_method: sales_payment_method },
  //     success: function (data) {
  //       $("body #salesTable").DataTable().destroy();
  //       $("#salesTableBody").html(data);
  //       $("body #salesTable").DataTable().draw();

  //       $(".loader")[1].style.display = "none";
  //     },
  //   });
  // });

  // Filter driver table by driver_name
  $("body").on("change", "#driver_name_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var driver_name = $(this).val();

    $.ajax({
      url: "./Accounting/driver.php",
      method: "POST",
      data: { update_table: 1, driver_name: driver_name },
      success: function (data) {
        $("body #driver_table").DataTable().destroy();
        $("#driver_table_body").html(data);
        $("body #driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver_name UNPAID
  $("body").on("change", "#driver_name_filter_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var driver_name_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_driver.php",
      method: "POST",
      data: { update_table: 1, driver_name_unpaid: driver_name_unpaid },
      success: function (data) {
        $("body #unpaid_driver_table").DataTable().destroy();
        $("#unpaid_driver_table_body").html(data);
        $("body #unpaid_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver payment method UNPAID
  $("body").on("change", "#driver_payment_method_filter_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var payment_method_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_driver.php",
      method: "POST",
      data: { update_table: 1, payment_method_unpaid: payment_method_unpaid },
      success: function (data) {
        $("body #unpaid_driver_table").DataTable().destroy();
        $("#unpaid_driver_table_body").html(data);
        $("body #unpaid_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver truck number UNPAID
  $("body").on("change", "#filter_by_truck_no_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var truck_number_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_driver.php",
      method: "POST",
      data: { update_table: 1, truck_number_unpaid: truck_number_unpaid },
      success: function (data) {
        $("body #unpaid_driver_table").DataTable().destroy();
        $("#unpaid_driver_table_body").html(data);
        $("body #unpaid_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver load number UNPAID
  $("body").on("change", "#filter_by_load_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_no_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_driver.php",
      method: "POST",
      data: { update_table: 1, load_no_unpaid: load_no_unpaid },
      success: function (data) {
        $("body #unpaid_driver_table").DataTable().destroy();
        $("#unpaid_driver_table_body").html(data);
        $("body #unpaid_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver_name all sheduled
  $("body").on("change", "#driver_name_filter_sheduled", function (e) {
    $(".loader")[1].style.display = "flex";
    var driver_name_sheduled = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver.php",
      method: "POST",
      data: { update_table: 1, driver_name_sheduled: driver_name_sheduled },
      success: function (data) {
        $("body #all_sheduled_pay_driver_table").DataTable().destroy();
        $("#sheduled_pay_driver_table_bod").html(data);
        $("body #all_sheduled_pay_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver payment method shedule
  $("body").on(
    "change",
    "#driver_payment_method_filter_sheduled",
    function (e) {
      $(".loader")[1].style.display = "flex";
      var payment_method_sheduled = $(this).val();

      $.ajax({
        url: "./Accounting/sheduled_pay_driver.php",
        method: "POST",
        data: {
          update_table: 1,
          payment_method_sheduled: payment_method_sheduled,
        },
        success: function (data) {
          $("body #all_sheduled_pay_driver_table").DataTable().destroy();
          $("#sheduled_pay_driver_table_bod").html(data);
          $("body #all_sheduled_pay_driver_table").DataTable().draw();

          $(".loader")[1].style.display = "none";
        },
      });
    }
  );

  // Filter driver table by driver truck number sheduled
  $("body").on("change", "#filter_by_truck_no_sheduled", function (e) {
    $(".loader")[1].style.display = "flex";
    var truck_number_sheduled = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver.php",
      method: "POST",
      data: { update_table: 1, truck_number_sheduled: truck_number_sheduled },
      success: function (data) {
        $("body #all_sheduled_pay_driver_table").DataTable().destroy();
        $("#sheduled_pay_driver_table_bod").html(data);
        $("body #all_sheduled_pay_driver_table").DataTable().draw();
        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver load number UNPAID
  $("body").on("change", "#filter_by_load_sheduled", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_no_sheduled = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver.php",
      method: "POST",
      data: { update_table: 1, load_no_sheduled: load_no_sheduled },
      success: function (data) {
        $("body #all_sheduled_pay_driver_table").DataTable().destroy();
        $("#sheduled_pay_driver_table_bod").html(data);
        $("body #all_sheduled_pay_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver_name weekly
  $("body").on("change", "#driver_name_filter_weekly", function (e) {
    $(".loader")[1].style.display = "flex";
    var driver_name_weekly = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver_weekly.php",
      method: "POST",
      data: { update_table: 1, driver_name_weekly: driver_name_weekly },
      success: function (data) {
        $("body #weekly_sheduled_pay_driver_table").DataTable().destroy();
        $("#weekly_sheduled_pay_driver_table_body").html(data);
        $("body #weekly_sheduled_pay_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver payment method week
  $("body").on("change", "#driver_payment_method_filter_weekly", function (e) {
    $(".loader")[1].style.display = "flex";
    var payment_method_weekly = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver_weekly.php",
      method: "POST",
      data: {
        update_table: 1,
        payment_method_weekly: payment_method_weekly,
      },
      success: function (data) {
        $("body #weekly_sheduled_pay_driver_table").DataTable().destroy();
        $("#weekly_sheduled_pay_driver_table_body").html(data);
        $("body #weekly_sheduled_pay_driver_table").DataTable().draw();
        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver truck number weekly
  $("body").on("change", "#filter_by_truck_no_weekly", function (e) {
    $(".loader")[1].style.display = "flex";
    var truck_number_weekly = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver_weekly.php",
      method: "POST",
      data: { update_table: 1, truck_number_weekly: truck_number_weekly },
      success: function (data) {
        $("body #weekly_sheduled_pay_driver_table").DataTable().destroy();
        $("#weekly_sheduled_pay_driver_table_body").html(data);
        $("body #weekly_sheduled_pay_driver_table").DataTable().draw();
        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter driver table by driver load number weekly
  $("body").on("change", "#filter_by_load_weekly", function (e) {
    $(".loader")[1].style.display = "flex";
    var load_no_sheduled = $(this).val();

    $.ajax({
      url: "./Accounting/sheduled_pay_driver_weekly.php",
      method: "POST",
      data: { update_table: 1, load_no_sheduled: load_no_sheduled },
      success: function (data) {
        $("body #weekly_sheduled_pay_driver_table").DataTable().destroy();
        $("#weekly_sheduled_pay_driver_table_body").html(data);
        $("body #weekly_sheduled_pay_driver_table").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter Broker table by broker_name
  $("body").on("change", "#Broker_name_filter", function (e) {
    $(".loader")[1].style.display = "flex";
    var broker_name = $(this).val();

    $.ajax({
      url: "./Accounting/sales.php",
      method: "POST",
      data: { update_table: 1, broker_name: broker_name },
      success: function (data) {
        $("body #salesTable").DataTable().destroy();
        $("#salesTableBody").html(data);
        $("body #salesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Filter Broker table by broker company unpaid
  $("body").on("change", "#broker_name_filter_unpaid", function (e) {
    $(".loader")[1].style.display = "flex";
    var broker_company_unpaid = $(this).val();

    $.ajax({
      url: "./Accounting/unpaid_loads.php",
      method: "POST",
      data: { update_table: 1, broker_company_unpaid: broker_company_unpaid },
      success: function (data) {
        $("body #unpaidsalesTable").DataTable().destroy();
        $("#unpaidsalesTableBody").html(data);
        $("body #unpaidsalesTable").DataTable().draw();

        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Get sale update list
  $("body").on("click", ".get_sale_update_list", function (e) {
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

  // remove sale_payment_updates_close form
  $("body").on(
    "click",
    ".sale_payment_updates_close, .broker_contact_form_close",
    function (e) {
      $(this).parent().parent().remove();
    }
  );

  // Delete sale payment update
  $("body").on("click", ".delete_sale_payment_update", function (e) {
    var sale_payment_id = $(this).data("sale_payment_id");
    var load_id = $(this).data("load_id");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=delete_sale_payment_update",
      method: "POST",
      data: { sale_payment_id: sale_payment_id },
      success: function (data) {
        data = JSON.parse(data)[0];
        if (data.success == 1) {
          var alert_msg =
            '<div id="alert_msg" style="display: block; background-color: #7dc77d;">' +
            data.msg +
            '<span class="close_msg">x</span></div>';
          $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          $(".sale_payment_updates").remove();
          updateTable("./Accounting/sales.php", "salesTableBody", "salesTable");

          $.ajax({
            url: "./Accounting/sales_payment_updates.php",
            method: "POST",
            data: { load_id: load_id, get_sale_payment_Updates: 1 },
            success: function (data) {
              $("body").append(data);
            },
          });
        }
      },
    });
  });

  // Get sales details
  //    $("body").on("click", "#sales_company", function(e){
  //     var load_id = $(this).data("load_id")

  //     $.ajax({
  //         url: "./accounting/broker_info.php",
  //         method: "POST",
  //         data: {load_id: load_id, get_sale_payment_Updates: 1},
  //         success: function(data){
  //             $("body").append(data)
  //         }
  //     })
  // })
  $("body").on("click", ".add_contact", function (e) {
    var broker_id = $(this).data("broker_id");
    $(".loader")[1].style.display = "flex";

    var data = { broker_contact_form: "1", broker_id: broker_id };
    $.ajax({
      url: "./accounting/info_broker.php",
      method: "POST",
      data: data,
      success: function (data) {
        $("body").append(data);
        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Get driver details
  $("body").on("click", "#sale_driver_company", function (e) {
    var load_id = $(this).data("load_id");

    $.ajax({
      url: "./accounting/info.php",
      method: "POST",
      data: { load_id: load_id, get_driver_payment_Updates: 1 },
      success: function (data) {
        $("body").append(data);
      },
    });
  });

  // remove info form
  $("body").on("click", ".info_close", function (e) {
    $(this).parent().parent().remove();
  });

  // Get driver amount
  $("body").on("click", ".get_purchase_payment_Updates", function (e) {
    var load_id = $(this).data("load_id");

    $.ajax({
      url: "./accounting/purchase_payment_updates.php",
      method: "POST",
      data: { load_id: load_id, get_driver_payment_Updates: 1 },
      success: function (data) {
        $("body").append(data);
      },
    });
  });

  // Get sale amount
  $("body").on("click", ".get_sale_payment_Updates", function (e) {
    var load_id = $(this).data("load_id");

    $.ajax({
      url: "./accounting/sales_payment_updates.php",
      method: "POST",
      data: { load_id: load_id, get_sale_payment_Updates: 1 },
      success: function (data) {
        $("body").append(data);
      },
    });
  });

  // Add broker Contact Info
  $("body").on("click", ".add_contact_info", function (e) {
    var broker_id = $(this).data("broker_id");
    $(".loader")[1].style.display = "flex";

    var data = { broker_contact_form: "1", broker_id: broker_id };
    $.ajax({
      url: "./accounting/broker_contact_details.php",
      method: "POST",
      data: data,
      success: function (data) {
        $("body").append(data);
        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Broker contact details
  broker_accounting_attachments = FilePond.create(
    document.querySelector("input#broker_accounting_attachments"),
    {
      allowMultiple: true,
      instantUpload: false,
      allowProcess: false,
    }
  );

  // Add broker contact info
  $("body").on("submit", "#broker_Accounting_details", function (e) {
    e.preventDefault();

    var fd = new FormData(this);
    // append files array into the form data
    pondFiles = broker_accounting_attachments.getFiles();
    // console.log(pondFiles)
    for (var i = 0; i < pondFiles.length; i++) {
      fd.append("broker_accounting_attachments[]", pondFiles[i].file);
    }

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=broker_accounting_info",
      type: "POST",
      data: fd,
      dataType: "JSON",
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        if (data[0].success == 1) {
          updateTable(
            "./Accounting/vendors.php",
            "vendor_table_body",
            "vendor_table"
          );
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#new_vendor_form")[0].reset();

          if (ven_attachments.getFiles().length != 0) {
            for (var i = 0; i < ven_attachments.getFiles().length + 4; i++) {
              ven_attachments.removeFile(ven_attachments.getFiles()[0].id);
            }
          }
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Delete Broker attachments
  $("body").on("click", ".delete_broker_file", function (e) {
    var id = $(this).data("file_id");
    var confirmation = confirm("Are you sure you want to delete the File!");

    if (confirmation) {
      $(this).parent().parent().remove();

      $.ajax({
        url: "./Assets/backendfiles/accounting.php?action_type=delete_broker_attachment",
        type: "POST",
        data: { id: id },
        success: function (data) {
          data = JSON.parse(data)[0];
          if (data.success == 1) {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #7dc77d;">' +
              data.msg +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          }
        },
      });
    }
  });

  // Get Driver payment update list
  $("body").on("click", ".get_purchase_update_list", function (e) {
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

  // Delete Driver payment update
  $("body").on("click", ".delete_driver_payment_update", function (e) {
    var pur_payment_id = $(this).data("pur_payment_id");
    var load_id = $(this).data("load_id");

    var confirmation = confirm("Are you sure you want to delete the Record!");

    if (confirmation) {
      $.ajax({
        url: "./Assets/assets/backendfiles.php?action_type=delete_driver_payment_update",
        method: "POST",
        data: { pur_payment_id: pur_payment_id },
        success: function (data) {
          data = JSON.parse(data)[0];
          if (data.success == 1) {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #7dc77d;">' +
              data.msg +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
            $(".purchase_payment_updates").remove();
            updateTable(
              "./Accounting/driver.php",
              "driver_table_body",
              "driver_table"
            );

            $.ajax({
              url: "./Accounting/purchase_payment_updates.php",
              method: "POST",
              data: { load_id: load_id, get_sale_payment_Updates: 1 },
              success: function (data) {
                $("body").append(data);
              },
            });
          }
        },
      });
    }
  });

  // Get broker details
  $("body").on("click", "#company", function (e) {
    var load_id = $(this).data("load_id");

    $.ajax({
      url: "./components/broker_details.php",
      method: "POST",
      data: { load_id: load_id, get_sale_payment_Updates: 1 },
      success: function (data) {
        $("body").append(data);
      },
    });
  });

  // // Get driver details
  // $("body").on("click", "#driver_company", function(e){
  //     var load_id = $(this).data("load_id")

  //     $.ajax({
  //         url: "./components/driver_details.php",
  //         method: "POST",
  //         data: {load_id: load_id, get_sale_payment_Updates: 1},
  //         success: function(data){
  //             $("body").append(data)
  //         }
  //     })
  // })

  // Add Driver Contact Info
  // $("body").on("click", ".add_driver_contact_info, .update_contact_info, .broker_pay_status invoiced", function(e){
  //     var truck_id = $(this).data("truck_id")
  //     $(".loader")[1].style.display = "flex"

  //     var data = {broker_contact_form: "1", truck_id: truck_id}
  //     $.ajax({
  //         url: "./Accounting/driver_info.php",
  //         method: "POST",
  //         data: data,
  //         success: function(data){

  //             $("body").append(data)
  //             $(".loader")[1].style.display = "none"
  //         }
  //     })
  // })

  // Add Driver Contact Info
  $("body").on("click", ".add_driver_contact_info", function (e) {
    var truck_id = $(this).data("truck_id");
    $(".loader")[1].style.display = "flex";

    var data = { broker_contact_form: "1", truck_id: truck_id };
    $.ajax({
      url: "./Accounting/driver_contact_details.php",
      method: "POST",
      data: data,
      success: function (data) {
        $("body").append(data);
        $(".loader")[1].style.display = "none";
      },
    });
  });

  // Add Driver Contact Info
  $("body").on(
    "click",
    ".add_driver_info, .update_contact_info, .broker_pay_status invoiced",
    function (e) {
      var truck_id = $(this).data("truck_id");
      $(".loader")[1].style.display = "flex";

      var data = { broker_contact_form: "1", truck_id: truck_id };
      $.ajax({
        url: "./Accounting/info.php",
        method: "POST",
        data: data,
        success: function (data) {
          $("body").append(data);
          $(".loader")[1].style.display = "none";
        },
      });
    }
  );

  // Driver contact details
  driver_accounting_attachments = FilePond.create(
    document.querySelector("input#driver_accounting_attachments"),
    {
      allowMultiple: true,
      instantUpload: false,
      allowProcess: false,
    }
  );

  // Add Driver contact info
  $("body").on("submit", "#driver_Accounting_details", function (e) {
    e.preventDefault();

    var fd = new FormData(this);
    // append files array into the form data
    pondFiles = driver_accounting_attachments.getFiles();
    // console.log(pondFiles)
    for (var i = 0; i < pondFiles.length; i++) {
      fd.append("driver_accounting_attachments[]", pondFiles[i].file);
    }

    $("body .submit").html("Adding...");
    $("body .submit").style = "background-color: #dadada;";
    $("body .submit").attr("disabled", "disabled");

    $.ajax({
      url: "./Assets/backendfiles/accounting.php?action_type=driver_accounting_info",
      type: "POST",
      data: fd,
      dataType: "JSON",
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        if (data[0].success == 1) {
          updateTable(
            "./Accounting/driver.php",
            "driver_table_body",
            "driver_table"
          );
          alertBtn(data[0].msg, ".submit");

          // Rest Form
          $("#driver_Accounting_details")[0].reset();

          if (driver_accounting_attachments.getFiles().length != 0) {
            for (
              var i = 0;
              i < driver_accounting_attachments.getFiles().length + 4;
              i++
            ) {
              driver_accounting_attachments.removeFile(
                driver_accounting_attachments.getFiles()[0].id
              );
            }
          }
        } else if (data[0].success == 0) {
          alertBtn(data[0].msg, ".submit", "#ce6c6c");
        }
      },
      error: function (data) {
        alertBtn("Something went Wrong!", ".submit", "#ce6c6c");
      },
    });
  });

  // Delete Driver attachments
  $("body").on("click", ".delete_driver_file", function (e) {
    var id = $(this).data("file_id");
    var confirmation = confirm("Are you sure you want to delete the File!");

    if (confirmation) {
      $(this).parent().parent().remove();

      $.ajax({
        url: "./Assets/backendfiles/accounting.php?action_type=delete_driver_attachment",
        type: "POST",
        data: { id: id },
        success: function (data) {
          data = JSON.parse(data)[0];
          if (data.success == 0) {
            var alert_msg =
              '<div id="alert_msg" style="display: block; background-color: #7dc77d;">' +
              data.msg +
              '<span class="close_msg">x</span></div>';
            $("#alert_msgs").append(alert_msg).delay(30000).hide(500);
          }
        },
      });
    }
  });

  // Load tables dynamically
  var path = [
    "./Accounting/sales_by_company.php",
    "./Accounting/sales.php",
    "./Accounting/driver.php",
    "./Accounting/vendors.php",
    "./Accounting/other_purchases.php",
    "./Accounting/unpaid_driver.php",
    "./Accounting/unpaid_loads.php",
    "./Accounting/sheduled_pay_driver.php",
    "./Accounting/sheduled_pay_driver_weekly.php",
  ];
  var tbid = [
    "customerTableBody",
    "salesTableBody",
    "driver_table_body",
    "vendor_table_body",
    "pur_table_body",
    "unpaid_driver_table_body",
    "unpaidsalesTableBody",
    "sheduled_pay_driver_table_bod",
    "weekly_sheduled_pay_driver_table_body",
  ];
  var tid = [
    "customerTable",
    "salesTable",
    "driver_table",
    "vendor_table",
    "pur_table",
    "unpaid_driver_table",
    "unpaidsalesTable",
    "all_sheduled_pay_driver_table",
    "weekly_sheduled_pay_driver_table",
  ];
  for (i = 0; i < path.length; i++) {
    updateTable(path[i], tbid[i], tid[i]);
  }

  

  // $("body #filter_date_shedule").on("change input", function(e){
  //   console.log("Change event");
  // })  

  // $("body").on("change", "#filter_date_shedule", function (e) {
  //   $(".loader")[1].style.display = "flex";
  //   var week_no = selectedWeek; // Use the stored selected week
  //   console.log("Change event");
  
  //   $.ajax({
  //     url: "./Accounting/sheduled_pay_driver.php",
  //     method: "POST",
  //     data: { update_table: 1, week_no: week_no },
  //     success: function (data) {
  //       $("body #all_sheduled_pay_driver_table").DataTable().destroy();
  //       $("#sheduled_pay_driver_table_bod").html(data);
  //       $("body #all_sheduled_pay_driver_table").DataTable().draw();
  //       console.log("Week added into filter successfully");
  //       $(".loader")[1].style.display = "none";
  //     },
  //   });
  // });



  jQuery.noConflict(); // Release the $ variable to avoid conflicts


  // $(function ($) { // Use the jQuery variable as an argument
    var selectedWeek = ''; // Declare a variable to store the selected week
    $j("#filter_date_shedule").datepicker({
      
      showWeek: true,
      firstDay: 1,
      maxDate: 'today',
      beforeShow: function (week_no, ui) {
        // week_no = $(this)
        
        $(ui.dpDiv).one('click', 'tbody .ui-datepicker-week-col', function () {
          selectedWeek = $(this).text(); // Store the selected week
          $(week_no).val(selectedWeek) //.datepicker("hide");

          $(".loader")[1].style.display = "flex";
         $j("#filter_date_shedule").datepicker("hide");
            // var week_no = selectedWeek; // Use the stored selected week
            console.log("Change event");
            $.ajax({
              url: "./Accounting/sheduled_pay_driver.php",
              method: "POST",
              data: { update_table: 1, week_no: selectedWeek },
              success: function (data) {
                console.log($("body #all_sheduled_pay_driver_table"));
                $("body #all_sheduled_pay_driver_table").DataTable().destroy();
                $("#sheduled_pay_driver_table_bod").html(data);
                $("body #all_sheduled_pay_driver_table").DataTable().draw();
                console.log("Week added into filter successfully");
                $(".loader")[1].style.display = "none";
              },
          });
          console.log(selectedWeek);
          console.log("Week added successfully");
        });
          
        
        return
      }
    });
    console.log(selectedWeek);
  // });


 
});

function filter_date_shedule(){
  console.log("Change event");
}

function printDiv(div) {
  var divContents = $(div).prop("outerHTML");
  var printWindow = window.open("", "", "height=500,width=800");
  printWindow.document.write(
    "<html><head><title>GTMM Transportation Invoice # </title>"
  );

  // Array of CSS files to load
  var cssFiles = ["./Assets/css/invoice.css", "./Assets/css/style.css"];

  // Function to fetch and inject each CSS file
  function loadCssFiles(index) {
    if (index < cssFiles.length) {
      var cssFile = cssFiles[index];
      $.get(cssFile, function (cssContent) {
        printWindow.document.write("<style>" + cssContent + "</style>");
        loadCssFiles(index + 1); // Recursively load the next CSS file
      });
    } else {
      printWindow.document.write("</head><body>");
      printWindow.document.write(divContents);
      printWindow.document.write("</body></html>");
      printWindow.document.close();
      printWindow.print();
    }
  }

  // Start loading CSS files
  loadCssFiles(0);
}


var start_date = "";
var end_date = "";


$j(
  "#filter_date, #filter_date_sale, #dashboard_filter, #filter_date_unpaid, #unload_filter, #filter_date_shedule_weekly"
).daterangepicker(
  {
    showDropdowns: true,
    showWeekNumbers: true,
    ranges: {
      Today: [moment(), moment()],
      Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
      "This Week": [moment().startOf("week"), moment().endOf("week")],
      "Last Week": [
        moment().subtract(1, "week").startOf("week"),
        moment().subtract(1, "week").endOf("week"),
      ],
      "This Month": [moment().startOf("month"), moment().endOf("month")],
      "Last Month": [
        moment().subtract(1, "month").startOf("month"),
        moment().subtract(1, "month").endOf("month"),
      ],
    },
    parentEl: "Parent_Element",
    startDate: "07/01/2022",
    endDate: new Date(),
    minDate: "05/01/2022",
    maxDate: new Date(),
    todayHighlight: true,
  },
  function (start, end, label) {
    start_date = start.format("YYYY-MM-DD");
    end_date = end.format("YYYY-MM-DD");
  }
);

var weekpicker, start_date, end_date;

function set_week_picker(date) {
  start_date = new Date(
    date.getFullYear(),
    date.getMonth(),
    date.getDate() - date.getDay()
  );
  end_date = new Date(
    date.getFullYear(),
    date.getMonth(),
    date.getDate() - date.getDay() + 6
  );
  weekpicker.datepicker("update", start_date);
  weekpicker.val(
    start_date.getMonth() +
      1 +
      "/" +
      start_date.getDate() +
      "/" +
      start_date.getFullYear() +
      " - " +
      (end_date.getMonth() + 1) +
      "/" +
      end_date.getDate() +
      "/" +
      end_date.getFullYear()
  );
}
// // $(document).ready(function() {
// weekpicker = $(".week-picker");
// console.log(weekpicker);
// weekpicker
//   .datepicker({
//     autoclose: true,
//     forceParse: false,
//     container: "#filter_date_shedule",
//   })
//   .on("changeDate", function (e) {
//     set_week_picker(e.date);
//   });
// $(".week-prev").on("click", function () {
//   var prev = new Date(start_date.getTime());
//   prev.setDate(prev.getDate() - 1);
//   set_week_picker(prev);
// });
// $(".week-next").on("click", function () {
//   var next = new Date(end_date.getTime());
//   next.setDate(next.getDate() + 1);
//   set_week_picker(next);
// });
// set_week_picker(new Date());
// // });

//Filter driver table by Date
function driver_date_filter() {
  // $("body").on("change", ".driver_date_filter", function(e){
  $(".loader")[1].style.display = "flex";

  $.ajax({
    url: "./Accounting/driver.php",
    method: "POST",
    data: { update_table: 1, start_date: start_date, end_date: end_date },
    success: function (data) {
      $("body #driver_table").DataTable().destroy();
      $("#driver_table_body").html(data);
      $("body #driver_table").DataTable().draw();
      $(".loader")[1].style.display = "none";
    },
  });
  // })
}

// Filter unpaid  driver table by Date
// function sheduled_date_filter() {
//   // $("body").on("change", ".driver_date_filter", function(e){
//   $(".loader")[1].style.display = "flex";

//   $.ajax({
//     url: "./Accounting/sheduled_pay_driver.php",
//     method: "POST",
//     data: { update_table: 1, week_no: week_no },
//     success: function (data) {
//       $("body #all_sheduled_pay_driver_table").DataTable().destroy();
//       $("#sheduled_pay_driver_table_bod").html(data);
//       $("body #all_sheduled_pay_driver_table").DataTable().draw();
//       $(".loader")[1].style.display = "none";
//     },
//   });
//   // })
// }



function weekly_sheduled_date_filter() {
  // $("body").on("change", ".driver_date_filter", function(e){
  $(".loader")[1].style.display = "flex";

  $.ajax({
    url: "./Accounting/sheduled_pay_driver_weekly.php",
    method: "POST",
    data: { update_table: 1, start_date: start_date, end_date: end_date },
    success: function (data) {
      $("body #weekly_sheduled_pay_driver_table").DataTable().destroy();
      $("#weekly_sheduled_pay_driver_table_body").html(data);
      $("body #weekly_sheduled_pay_driver_table").DataTable().draw();
      $(".loader")[1].style.display = "none";
    },
  });
  // })
}

// Filter unpaid  driver table by Date
function unpaid_driver_date_filter() {
  // $("body").on("change", ".driver_date_filter", function(e){
  $(".loader")[1].style.display = "flex";

  $.ajax({
    url: "./Accounting/unpaid_driver.php",
    method: "POST",
    data: { update_table: 1, start_date: start_date, end_date: end_date },
    success: function (data) {
      $("body #unpaid_driver_table").DataTable().destroy();
      $("#unpaid_driver_table_body").html(data);
      $("body #unpaid_driver_table").DataTable().draw();
      $(".loader")[1].style.display = "none";
    },
  });
  // })
}

// Filter unpaid  loads table by Date
function unpaid_date_filter() {
  // $("body").on("change", ".driver_date_filter", function(e){
  $(".loader")[1].style.display = "flex";

  $.ajax({
    url: "./Accounting/unpaid_loads.php",
    method: "POST",
    data: { update_table: 1, start_date: start_date, end_date: end_date },
    success: function (data) {
      $("body #unpaidsalesTable").DataTable().destroy();
      $("#unpaidsalesTableBody").html(data);
      $("body #unpaidsalesTable").DataTable().draw();
      $(".loader")[1].style.display = "none";
    },
  });
  // })
}

//filter broker table
function broker_date_filter() {
  // $("body").on("change", ".driver_date_filter", function(e){
  $(".loader")[1].style.display = "flex";

  $.ajax({
    url: "./Accounting/sales.php",
    method: "POST",
    data: { update_table: 1, start_date: start_date, end_date: end_date },
    success: function (data) {
      $("body #salesTable").DataTable().destroy();
      $("#salesTableBody").html(data);
      $("body #salesTable").DataTable().draw();

      $(".loader")[1].style.display = "none";
    },
  });
  // })
}

function item() {
  $("#new_item").show();
}

function new_vendor() {
  $("#new_vendor").show();
}

// Filter dashboard Data by Date
function dashboard_date_filter() {
  // $("body").on("change", ".driver_date_filter", function(e){
  $(".loader")[1].style.display = "flex";

  $.ajax({
    url: "./Accounting/dasboard.php",
    method: "POST",
    data: { update_table: 1, start_date: start_date, end_date: end_date },
    success: function (data) {
      $("#dashboard_data").html(data);

      $(".loader")[1].style.display = "none";
    },
  });
  // })
}

// Set today as default date
window.addEventListener("load", function () {
  var now = new Date();
  var utcString = now.toISOString().substring(0, 19);
  var year = now.getFullYear();
  var month = now.getMonth() + 1;
  var day = now.getDate();
  var hour = now.getHours();
  var minute = now.getMinutes();
  var second = now.getSeconds();
  var localDatetime =
    year +
    "-" +
    (month < 10 ? "0" + month.toString() : month) +
    "-" +
    (day < 10 ? "0" + day.toString() : day) +
    "T" +
    (hour < 10 ? "0" + hour.toString() : hour) +
    ":" +
    (minute < 10 ? "0" + minute.toString() : minute);
  var datetimeField = document.getElementsByClassName("today_default_Date");
  for (i = 0; i < datetimeField.length; i++) {
    datetimeField[i].value = localDatetime;
  }
});

// Allert Message and button rest
function alertBtn(alert_msg, submit_btn_id, bg = "#7dc77d") {
  var alert_msg =
    '<div id="alert_msg" style="display: block; background-color: "' +
    bg +
    ">" +
    alert_msg +
    '<span class="close_msg">x</span></div>';
  $("#alert_msgs").append(alert_msg).delay(30000).hide(500);

  // $("#alert_msg").css({display: "block", backgroundColor: bg})
  // $("#alert_msg").html(alert_msg);
  // $("#alert_msg").delay(30000).hide(500);

  $("body " + submit_btn_id + "").html("Submit");
  $("body " + submit_btn_id + "").style = "background-color: var(--button);";
  $("body " + submit_btn_id + "").removeAttr("disabled", "disabled");
}

// Request for Updating table
function updateTable(filepath, table, tableid) {
  $(".loader")[1].style.display = "flex";
  $.ajax({
    url: filepath,
    method: "POST",
    data: { update_table: 1 },
    success: function (data) {
      if (tableid !== "") {
        $("body #" + tableid)
          .DataTable()
          .destroy();
      }
      $("#" + table).html(data);
      if (tableid !== "") {
        $("body #" + tableid)
          .DataTable()
          .draw();
      }

      $(".loader")[1].style.display = "none";
    },
  });
}

// Format data table child rows
function format(value) {
  // console.log(value)

  var string = "";

  for (var i in value) {
    let newtext = value[i].replaceAll("|", '"');

    string += newtext;

    // return  '<tr>' + newtext1 + '</tr>' +  '<tr>' + newtext2 + '</tr>' ;
  }
  // let newtext1 = value[0].replaceAll('|', '"');
  // let newtext2 = value[1].replaceAll('|', '"');
  // return  '<tr>' + newtext1 + '</tr>' +  '<tr>' + newtext2 + '</tr>' ;
  return string;
}
