$(document).ready(function(){

    var pond = FilePond.create(document.querySelector('input#driver_ide_attachments'), {
        allowMultiple: true,
        instantUpload: false,
        allowProcess: false,
        labelIdle: `<div style="width:100%;height:100%;">
            <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
        </div>`,
        dropOnPage: true,
        // oninitfile: function(file){
        //     setTimeout(function(){
        //         $(html).appendTo("#filepond--item-" + file.id)
        //     }, 1000)
            
        // }
    });

    var ins_attach = FilePond.create(document.querySelector('input#driver_ins_attachments'), {
        allowMultiple: true,
        instantUpload: false,
        allowProcess: false,
        labelIdle: `<div style="width:100%;height:100%;">
            <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
        </div>`,
    });

    var dri_ls_atta = FilePond.create(document.querySelector('input#driver_dl_attachments'), {
        allowMultiple: true,
        instantUpload: false,
        allowProcess: false,
        labelIdle: `<div style="width:100%;height:100%;">
            <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
        </div>`,
    });

    var dri_vanpics_atta = FilePond.create(document.querySelector('input#driver_vanpics_attachments'), {
        allowMultiple: true,
        instantUpload: false,
        allowProcess: false,
        labelIdle: `<div style="width:100%;height:100%;">
            <p> Drag &amp; Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span><br></p>
        </div>`,
    });


    $(document).on('submit', "form#driver_info_form", function (e) {
        e.preventDefault();
        var fd = new FormData(this);
        // append files array into the form data
        pondFiles = pond.getFiles();
        for (var i = 0; i < pondFiles.length; i++) {
            fd.append('driver_ide_attachment[]', pondFiles[i].file);
        }

        ins_attac = ins_attach.getFiles();
        for (var i = 0; i < ins_attac.length; i++) {
            fd.append('driver_ins_attachment[]', ins_attac[i].file);
        }

        dri_ls_att = dri_ls_atta.getFiles();
        for (var i = 0; i < dri_ls_att.length; i++) {
            fd.append('driver_dl_attachment[]', dri_ls_att[i].file);
        }

        dri_van_att = dri_vanpics_atta.getFiles();
        for (var i = 0; i < dri_van_att.length; i++) {
            fd.append('driver_vanpics_attachment[]', dri_van_att[i].file);
        }

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
                    pond.removeFiles()
                    ins_attach.removeFiles()
                    dri_ls_atta.removeFiles()
                    dri_vanpics_atta.removeFiles()
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
})