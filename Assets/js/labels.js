$("#label_name, #background_color, #label_text_color").on("change", function(e){
    var label = $("#label_name").val()
    var bg = $("#background_color").val()
    var tc = $("#label_text_color").val()

    if(!label == "" || !label == "undefined"){
        $(".sample_label").html(label)
    }

    if(!bg == "" || !bg == "undefined"){
        $(".sample_label").css({backgroundColor: bg})
    }

    if(!tc == "" || !tc == "undefined"){
        $(".sample_label").css({color: tc})
    }
})

$(".cancel").on("click", function(e){
    $(this).parent().parent().parent().parent().parent().hide()
})

$(".close").on("click", function(e){
    $(this).parent().parent().hide()
})

$(".add_label_btn").on("click", function(e){
    $("#add_labelModal").show()
})


// Add new label
$("#add_labelForm").on("submit", function(e){
    e.preventDefault();

    var label = $("#label_name").val()
    var bg = $("#background_color").val()
    var tc = $("#label_text_color").val()

    var labelList = `<li>
        <svg width="30" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.9208 0H2C0.895431 0 0 0.89543 0 2V23C0 24.1046 0.895431 25 2 25H15.0362C15.6452 25 16.2211 24.7225 16.6005 24.2461L24.0973 14.8351C24.6359 14.159 24.6792 13.2135 24.2046 12.491L16.5925 0.901995C16.2227 0.339028 15.5944 0 14.9208 0Z" fill="${bg}"/>
        </svg>
        ${label}
    </li>`

    $.ajax({
        url: "./Assets/backendfiles/labels.php",
        method: "POST",
        data: {addlabel: 1, label: label, bg: bg, tc: tc},
        success: function(e){
            $(".label_list").append(labelList)
            $("#add_labelForm").hide()
            $("#add_labelForm").reset()
        }
    })
})

$('#label_tabel').DataTable({
    "bStateSave": true,
    responsive: true,
    dom: 'Bfrtip',
});