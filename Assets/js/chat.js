// Get users
$("body").on("click", ".c_f", function(e){
    var filter = $(this).data("filter_type")
    $(".c_f").removeClass("active")
    $(this).addClass("active")

    $.ajax({
        url : "./components/chatusers.php",
        method : "POST",
        data: {filter : filter},
        success : function(data){
            $("._c_contacts").html(data)
        }
    })
})


// Send message
// $("body").on("click", "#send_msg", function(e){
//     e.preventDefault()
//     var msg = $("#writemsg").val()
   
//     var incomning_id = $("#incomning_id").val()

//     // if((e.type === 'keydown' && e.which === 13) || e.type === 'click'){
//         console.log("btn msg: " + msg)
//         sendmsg(msg, incomning_id, ncTime)
//     // }
    

// })

// Restrict text area to break line on pressing enter
$('#writemsg').keydown(function(e) {
    var msg = $("#writemsg").val()
    
    var incomning_id = $("#incomning_id").val()

    if (e.keyCode == 13) {
        
        // e.preventDefault();
        var currentVal = $(this).val();
        $(this).val(currentVal + '\n');
        document.getElementById("send_msg").click()
        // sendmsg(msg, incomning_id, ncTime)
    }
});


// Get user chat data
$("body").on("click", ".c_contact", function(e){
    var id = $(this).data("user_id")
    $("#incomning_id").val(id)
    var username = $(this).find(".c_user_name").text()
    var status = $(this).find(".c_status").attr('class').split(' ')[1]
    var last
    var current_status = ""
    status == "inactive" ? current_status = "offline" : current_status = "online"
    $(".c_contact").removeClass("active")
    $(this).addClass("active")

    $.ajax({
        url : './components/chatdata.php',
        method: "POST",
        data : {inc_id : id},
        success: function(data){
            data = JSON.parse( data)
            if(data[0].records === 1){
                $(".c_msg_c").html(data[0].data)
            } else {
                console.log($(".c_msg_c").find(".c_user_name").length)
                if($(".c_msg_c").find(".c_user_name").length == 0){
                    var output = `<div class="msg_u_pro">
                        <div class="c_img">
                            <img src="./Assets/Images/PngItem_1468479.png" alt="">
                        </div>
                        
                        <div class="contact_info">
                            <p class="c_user_name">${username}</p>
                            <p class="current_status">${current_status}</p>
                        </div>
                    </div>
                    <div class="c_msgs">${data[0].data}</div>`
                    $(".c_msg_c").html(output)
                } else {
                    // $(".c_msgs").html(data)
                    $(".c_msg_c").find(".c_user_name").html(username)
                    
                    $(".c_msg_c").find(".current_status").html(current_status)
                    $(".c_msgs").html(data[0].data)
                }
                
            }
            $('.c_msg_c').scrollTop($('.c_msg_c')[0].scrollHeight);
            console.log($(".c_msgs").find(".msgs:last").data("msg_id"))
        }
    })
})


// Toggle msg actions
$('body').on("click", ".open_actions", function(e) {
    $(this).next().toggle();
});


// Delete msg
$("body").on("click", ".delete_msg", function(e){
    var id = $(this).data("id")
    var msg = $(this).parent().parent().parent().parent()

    var confirmed = confirm("Are you sure you want to Delte the message?")

    if(confirmed){
        $.ajax({
            url : "./Assets/backendfiles/chat.php",
            method: "POST",
            data: {delete_msg: true, id: id},
            success: function(data){
                msg.remove()
            }
        })
    }
})




// Search users
$("body").on("keyup", "#search_contacts", function(e){
    var searchword = $(this).val()
    var activetab = $(".c_f.active").data("filter_type")
    var post = ''
    activetab == "employee" ? post = {'employee_search': searchword, words: searchword.length} : post = {'driver_search': searchword, words: searchword.length}
    // post = {post : searchword}

    if(searchword.trim().length > 0 || searchword.length == 0){
        $.ajax({
            url: 'components/chatusers.php',
            data : post,
            method: 'POST',
            success: function (data){
                $("._c_contacts").html(data)
            }
        })
    }
})


// Search messages
$("body").on("keyup", "#search_msgs", function(e){
    var searchword = $(this).val()
    var id = $("#incomning_id").val()
    
    // post = {post : searchword}

    if(searchword.trim().length > 0 || searchword.length == 0){
        $.ajax({
            url: 'components/chatdata.php',
            data : {'search_msg': searchword, words: searchword.length, inc_id: id},
            method: 'POST',
            success: function (data){
                data = JSON.parse( data)
                // if(data[0].records === 1){
                    $(".c_msgs").html(data[0].data)
                // }
            }
        })
    }
})

Dropzone.autoDiscover = false;
$(document).ready(function() {
    // Get users when page is loaded
    $.ajax({
        url : "./components/chatusers.php",
        method : "POST",
        data: {filter : "employee"},
        success : function(data){
            $("._c_contacts").html(data)
        }
    })


    // DROP ZONE
    // Dropzone.autoDiscover = false;
    // var myDropzone = new Dropzone("div#dropzone", { 
    //     autoProcessQueue: false,
    //     // acceptedFiles:".png,.jpg,.gif,.bmp,.jpeg",
    //     init: function(){
    //         var submitButton = document.querySelector('#send_msg');
    //         myDropzon = this;
    //         console.log(myDropzon)
    //         submitButton.addEventListener("click", function(){
    //             myDropzon.processQueue();
    //         });
    //         this.on("complete", function(){
    //             if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0){
    //                 var _this = this;
    //                 console.log(_this)
    //                 _this.removeAllFiles();
    //             }
    //             //   list_image();
    //         });
    //     },
        
        
    //     url: "./components/chatusers.php",
    //     addRemoveLinks: true,

    //     success: function (file, response) {
    //         var imgName = response;
    //         $("#attachments").append(file.previewElement)
    //         console.log(file.previewElement)
    //         file.previewElement.classList.add("dz-success");
    //         console.log("Successfully uploaded :" + imgName);
    //     },
    //     error: function (file, response) {
    //         file.previewElement.classList.add("dz-error");
    //     }
    // });


    // Refresh chat data
    setInterval(() =>{
        
        if($(".c_msgs").length > 0){
            var lm = $(".c_msgs").find(".msgs:last").data("msg_id")
            var id = $("#incomning_id").val()
            if(typeof lm !== "undefined"){
                $.ajax({
                    url : './components/chatdata.php',
                    method: "POST",
                    data : {refreshdata: true, inc_id : id, lm_id: lm},
                    success: function(data){
                        data = JSON.parse( data)
                        if(data[0].records === 1){
                            $(".c_msgs").append(data[0].data)
                            $('.c_msg_c').scrollTop($('.c_msg_c')[0].scrollHeight);
                        }
                    }
                })
            }
        }
    }, 500);

    // Refresh Users
    setInterval(() =>{
        if($("._c_contacts").length > 0){
            var lat = $("body ._c_contacts").find(".last_active:first").data("lat")
            var activetab = $(".c_f.active").data("filter_type")
            var inm_id = $("#incomning_id").val()
            var actvuser_id = $(".c_contact.active").data("user_id")

            // console.log(actvuser_id)

            if(typeof lat !== "undefined" && lat != ""){
                $.ajax({
                    url : './components/refresh_chatusers.php',
                    method: "POST",
                    data : {refreshdata: true, lat: lat, filter: activetab},
                    success: function(data){
                        data = JSON.parse(data)
                        // console.log(data)
                        if(data[0].output == 0){
                            // console.log(data)
                        } else {
                            // console.log(data)
                            var id = data[0].id
                            var $item = $(".c_contact").filter(function() {
                                return $(this).data("user_id") == id;
                            });
                            

                            $item.remove()
                            $("._c_contacts").prepend(data[0].output)
                            var actv_user = $(".c_contact").filter(function() {
                                return $(this).data("user_id") == actvuser_id;
                            });
                            if(inm_id == actvuser_id){actv_user.addClass("active")}
                        }
                        
                    }
                })
            }
        }
    }, 500);


    var fileList = []
    $('#attach_docs_i').on('change', function() {
        // Get selected files
        var files = $(this)[0].files;
        $('#attachments').css('display', 'flex');
    
        // Loop through each file
        for (var i = 0; i < files.length; i++) {
            // Create a new file object
            var file = files[i];
            fileList.push(file);
            console.log(file.name)
    
            // Create a new file item element
            var fileItem = $('<div></div>').addClass('file-item');
            var filetype = file['type'].split('/')[0]
            const image = document.createElement("img");
            console.log(filetype)
            if(filetype === 'image'){
                
                image.src = URL.createObjectURL(file);
                fileItem.append(image)
            } else if(['xlsx', 'xlsm'].includes(file.name.split(".")[1])){
                image.src = "./Assets/Images/excel.svg";
                fileItem.append(image)
            } else if(file.name.split(".")[1] === 'pdf'){
                image.src = "./Assets/Images/pdf file.svg";
                fileItem.append(image)
            } else if(file.name.split(".")[1] === 'docx' || file.name.split(".")[1] === 'doc' || file.name.split(".")[1] === 'txt' ){
                image.src = "./Assets/Images/word.svg";
                fileItem.append(image)
            } else if(filetype === 'video'){
                var videoElement = document.createElement('video');

                // set video source URL
                videoElement.src = URL.createObjectURL(file);;

                // enable video controls
                videoElement.controls = true;
                fileItem.append(videoElement)
            }else {
                image.src = "./Assets/Images/other.png";
                fileItem.append(image)
            }
    
            // Add file name to the file item element
            fileItem.append($('<p></p>').addClass("file_name").text(file.name));
        
            // Add file size to the file item element
            fileItem.append($('<span></span>').addClass("file_size").text(formatBytes(file.size)));

            // Add buttom to remove file

            var removebtn = document.createElement("span")
            fileItem.append(removebtn)
            removebtn.classList.add("remove-file")
            removebtn.textContent = "x"
            removebtn.setAttribute('data-index', fileList.length - 1)
            
        
            // Append the file item element to the file list div
            $('#attachments').append(fileItem);
        }
    });

    $('#attachments').on('click', '.remove-file', function() {
        // get the index of the file to be removed
        var index = $(this).data('index');
        
        // remove the file from the file list
        var removedFile = fileList.splice(index, 1)[0];
        if(fileList.length <= 0){
            $('#attachments').css('display', 'none');
        }
        
        // remove the file from the input file element
        var inputFiles = $('#attach_docs_i')[0].files;
        var newInputFiles = new DataTransfer();
        for (var i = 0; i < fileList.length; i++) {
            newInputFiles.items.add(new File(["content"], fileList[i].name));
        
        }
        $('#attach_docs_i').val(null); // clear the input file element
        $('#attach_docs_i')[0].files =  newInputFiles.files   // set the input file element's files property to the new file list
        
        // remove the file from the file list display
        $(this).parent().remove();
    });
    
    // Function to format file size to human readable format
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
    
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    
        const i = Math.floor(Math.log(bytes) / Math.log(k));
    
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // $("#dropzone").dropzone({
    //     url: "hn_SimpeFileUploader.ashx",
    //     addRemoveLinks: true,
    //     success: function (file, response) {
    //         var imgName = response;
    //         file.previewElement.classList.add("dz-success");
    //         console.log("Successfully uploaded :" + imgName);
    //     },
    //     error: function (file, response) {
    //         file.previewElement.classList.add("dz-error");
    //     }
    // });
})


// Increase textarea height automaticaly
$("textarea").each(function () {
    this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:auto; max-height: 25vh;");
}).on("input", function () {
    this.style.height = 0;
    this.style.height = (this.scrollHeight) + "px";
    
    // $(this).parent().style.marginTop = -(this.scrollHeight) + "px";
});


$("#msg_form").on("submit", (function (e) {
        
    e.preventDefault();
    
    // Get input field values
    var fd = new FormData(this)
    var msg = $("#writemsg").val().trim()
    var formData = $("#msg_form").serialize();
    const hasFile = fd.has('file');
    var file = $('#attach_docs_i').val().trim();
    // fd.append("incomning_id", incomning_id)
    fd.append("msgtype", "new_msg")

    // Simple validation at client's end
    // We simply change border color to red if empty field using .css()
    var proceed = true;
    // console.log(msg)

    if (msg === "") {
        proceed = false;
    }

    console.log(formData)
    console.log("msg: " + msg)
    console.log("file: " + file)

    if (msg != "" || file != "") {
        console.log("sending msg request")
        $.ajax({
            url : "./Assets/backendfiles/chat.php",
            method: "POST",
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data){
                data = JSON.parse(data)
                if(data[0].msg_id !== ""){
                    var html = `<div class="msg_sent msgs" data-msg_id="`+ data[0].msg_id +`">
                        <p class="s_msg">` + msg + `</p>
                        <span class="time">`+ ncTime + `</span>
                    </div>`
                    $(".c_msgs").append(html)
                }
                
                $("#writemsg").val("")
                $('#writemsg').css('height', '30px');
                if(file != ""){
                    $(".c_msgs").append(data[0].output)
                }
                $('.c_msg_c').scrollTop($('.c_msg_c')[0].scrollHeight);
                $("#msg_form")[0].reset();
                $('#attachments').empty()
            }
        })
    }
}));

// Send message
// function sendmsg(msg, incomning_id, ncTime){
//     // console.log("submitting form")
//     // document.getElementById("send_msg").click()
//     $("#msg_form").on("submit", (function (e) {
        
//         e.preventDefault();
        
//         // Get input field values
//         var fd = new FormData(this)
//         var msg = $("#writemsg").val()
//         // fd.append("incomning_id", incomning_id)
//         fd.append("msgtype", "new_msg")
    
//         // Simple validation at client's end
//         // We simply change border color to red if empty field using .css()
//         var proceed = true;
//         // console.log(msg)
    
//         if (msg === "") {
//             proceed = false;
//         }
    
//         if (proceed) {
//             console.log("sending msg request")
//             $.ajax({
//                 url : "./Assets/backendfiles/chat.php",
//                 method: "POST",
//                 data: fd,
//                 cache: false,
//                 contentType: false,
//                 processData: false,
//                 success: function(data){
//                     var html = `<div class="msg_sent msgs" data-msg_id="`+ data +`">
//                         <p class="s_msg">` + msg + `</p>
//                         <span class="time">`+ ncTime + `</span>
//                     </div>`
//                     $(".c_msgs").append(html)
//                     $("#writemsg").val("")
//                     $('#writemsg').css('height', '30px');
//                     $('.c_msg_c').scrollTop($('.c_msg_c')[0].scrollHeight);
//                 }
//             })
//         }
//     }));

    
// }


// Drop zone
// Dropzone.options.dropFiles = {
//     autoProcessQueue: false,
//     acceptedFiles:".png,.jpg,.gif,.bmp,.jpeg",
//     init: function(){
//      var submitButton = document.querySelector('#submit-all');
//      myDropzone = this;
//      submitButton.addEventListener("click", function(){
//       myDropzone.processQueue();
//      });
//      this.on("complete", function(){
//       if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0)
//       {
//        var _this = this;
//        _this.removeAllFiles();
//       }
//     //   list_image();
//      });
//     },
//    };


// Dropzone.options.dropzone = {
//     paramName: "file", // The name that will be used to transfer the file
//     maxFilesize: 2, // MB
//     addRemoveLinks: true, // Add a remove button for each file
//     dictDefaultMessage: "Drag and drop files here or click to upload",
//     dictRemoveFile: "Remove file",
//     init: function() {
//       this.on("success", function(file, response) {
//         console.log("File uploaded:", file.name);
//       });
//     }
// };


// const dropZone = document.getElementById('msg_container');

// // Prevent default behavior on drag over event
// dropZone.ondragover = (event) => {
//   event.preventDefault();
// };

// // Handle drop event
// dropZone.ondrop = (event) => {
//   event.preventDefault();

//   const fileList = event.dataTransfer.files;

//   // Do something with the dropped files
//   console.log(fileList);
// };



// Now
const now = new Date();
const options = {
  timeZone: 'America/New_York',
  hour12: true, // use AM/PM format
  hour: '2-digit',
  minute: '2-digit',
};
const formatter = new Intl.DateTimeFormat([], options);
const ncTime = formatter.format(now);