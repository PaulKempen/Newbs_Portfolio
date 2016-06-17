

$('document').ready(function () {
    var id = 0;

    loadMemberInfo(id);

    //primary page navigation buttons
    $('.ind-button').on('click', function () {
        var mode = this.id;
        $('.ind-button-active').removeClass('ind-button-active');
        $('#' + mode).addClass('ind-button-active');

        $.post('../php/portfolio.php', { adminSelect: id, mode: mode, loading: false }, function (data) {
            //alert(data);
            $('#msg').html(data.msg);
            $('#indvmain').html(data.content);
        },'json');
    })

    //resume page refresh button
    $("#indvmain").on('click','#resumeRefresh', function () {

        $.post('../php/portfolio.php', { adminSelect: id, mode: 'resume', loading: false }, function (data) {
            $('#msg').html(data.msg);
            $('#indvmain').html(data.content);
        }, 'json');
    })

    //past work page work item navigation
    $("#indvmain").on("click", ".workBtn", function (button) {
        var workItem = this.value;
        var home = 0;

        if (workItem === 'home') {
            home = 1;
            workItem = id;
        }

        $.post('../php/portfolio.php', { adminSelect: id, workId: workItem, home: home, mode: 'workItem', loading: false }, function (data) {
            //alert(data.workItem);
            $('#workMain').html(data.workItem);
        }, 'json');
        
    });

    //event handler for all edit buttons
    $("#indvmain").on("click", ".editBtn", function (button) {
        var input = this.value;
        // var btnEditText = "Edit";
        var btnSaveText = "Save";
        var workId = "";
   
        switch (input)
        {
            case 'desc':
                workId = $('#workId').val();
            case 'about':
                btnEditText = "Edit Text";
                btnSaveText = "Save Text";
                break;
            case 'title':
            case 'url':
                workId = $('#workId').val();
                break;
            default:
                break;
        }

        // if ($(this).text() == btnEditText) {
        //     $('.' + input).prop("disabled", false);
        //     $(this).text(btnSaveText);
            $('div#msg').html("");
        // }
        // else {
            var text = [];
            $('.' + input).each(function(index, obj){
                text.push($(this).val());
            });

            // $('.' + input).prop("disabled", true);
            // $(this).text(btnEditText);

            $.post('../php/portfolioUtils.php', { adminSelect: id, workId: workId, mode: input, text: text}, function (data) {
                //alert(data);
                $('#msg').html(data.msg);
            },'json');
        // }
        
    });

    //event handler to add empty text boxes to resume lists
    $('#indvmain').on('click', '.addBtn', function () {
        var isDisabled = true;

        if ($("." + this.value).length) {
            var isDisabled = $('.' + this.value).is(':disabled');
        } 
        
        $(this).parent().parent().append("<li class='no-bull'><input type='text' class='" + this.value + " form-control' /></li>")
    });

    // event handler for work pic uploads
    $('#indvmain').on('click', '#btnUpWorkPic', function () {
        event.stopPropagation();
        event.preventDefault();
        var workId = $('#workId').val();
        var data = new FormData();
        data.append("pic", $('#fileWorkPic')[0].files[0]);
        data.append("mode", "workPic");
        data.append("workId", workId);

        $.ajax({
            url: '../php/memberUpload.php?',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false
        }).done(function (info, textStatus, jqXHR) {
            $('#msg').html(info.msg);

            $.post('../php/portfolio.php', { adminSelect: id, mode: 'work', loading: false }, function (data) {
                $('#indvmain').html(data.content);

                $.post('../php/portfolio.php', { adminSelect: id, workId: workId, home: 0, mode: 'workItem', loading: false }, function (data) {
                    $('#workMain').html(data.workItem);
                }, 'json');
            }, 'json');
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // Handle errors here
            console.log('ERRORS: ' + errorThrown + textStatus);
        });
    });

    //event handlers for picture file selection
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $('#indvmain').on('fileselect', '.btn-file :file', function (event, numFiles, label) {
        $('#fileLabel').text(label);
    });

    //event handler for adding work items
    $("#indvmain").on("click", "#btnAddWork", function () {
        var workId = "";
        $.post('../php/portfolioUtils.php', { adminSelect: id, mode: 'add'}, function (data) {
            workId = data.workId;
            $.post('../php/portfolio.php', { adminSelect: id, mode: 'work', loading: false }, function (data) {
                $('#indvmain').html(data.content);
                $.post('../php/portfolio.php', { adminSelect: id, workId: workId, home: 0, mode: 'workItem', loading: false }, function (data) {
                    $('#workMain').html(data.workItem);
                }, 'json');
            }, 'json');
        },'json');
    });

    //event handler for deleting work items
    $("#indvmain").on("click", "#btnDeleteWork", function () {
        var workId = $('#workId').val();
        $('#delModal').modal('hide');
        $.post('../php/portfolioUtils.php', { adminSelect: id, workId: workId, mode: 'delete' }, function (data) {
            $('#msg').html(data.msg);
            $.post('../php/portfolio.php', { adminSelect: id, mode: 'work', loading: false }, function (data) {
                $('#indvmain').html(data.content);
                
            }, 'json');
        }, 'json');
    });

    //event handle for admin control
    $("#indvmenu").on("click", "#btnAdminSelect", function () {
        id = $('#adminSelect').val();
   
        $.post('../php/portfolio.php', { adminSelect: id, mode: 'about', loading: true }, function (data) {
            $('#title').html(data.name);
            $('#imgHook').html(data.img);
            $('#indvmain').html(data.content);
        }, 'json');
    });

    //event handler for deleting work pics
    $("#indvmain").on("click", "#btnDelPic", function () {
        var workId = $('#workId').val();
        if ($('#delAuth').is(':checked'))
        {
            $.post('../php/portfolioUtils.php', { adminSelect: id, mode: 'delPic', workId: workId}, function (data) {
                $('#msg').html(data.msg);
            },'json');
        }
    });
    
    $("#indvmain").on("click", "#workCollapse", function (button) {
        if($("#workChevron").hasClass("glyphicon-chevron-right")){
            $("#workChevron").removeClass("glyphicon-chevron-right");
            $("#workChevron").addClass("glyphicon-chevron-left");
            $(".workLinks").css({"width":"0px","overflow-y":"hidden"});
            $("#workMain").css({"padding-right":"20px"});    
        }
        else{
            $("#workChevron").removeClass("glyphicon-chevron-left");
            $("#workChevron").addClass("glyphicon-chevron-right");
            $(".workLinks").css({"width":"unset","overflow-y":"scroll"});
            $("#workMain").css({"padding-right":"100px"});        
        }
    });
    
    /////////TEMP FEEDBACK FORM HANDLER///////////////
    $("#copyright").on("click", "#btnFeedback", function (button) {
        var button = $(this);
        
        if ($('#txtFeedback').val() != "") {
            button.attr('disabled', 'disabled');

            var subject = "New message from Newbs Unit'd Feedback Form";

            var body = "Feedback from Newbs Unit'd feedback form\n\n";
            body += "Category: " + $("input[type='radio'][name='fb']:checked").val() + "\n";
            body += "From page: " + $('#page').val() + "\n\n";
            body += "Feedback:\n" + $('#txtFeedback').val();

            $('#fbMsg').html("<span class='alert alert-info'>Processing message...</span>");

            $.post('../php/contact.php', { mode: 'all-admin', body: body, subject: subject }, function (data) {
                $('#fbMsg').html(data);
                $('#txtFeedback').val("");
                button.removeAttr('disabled');
            });
        }
    });

});//end doc.ready


function loadMemberInfo(id) {
    $.post('../php/portfolio.php', { adminSelect: id, mode: 'about', loading: true }, function (data) {
        //alert(data);
        if (data.loggedin) {
            $('#title').html(data.name);
            $('#imgHook').html(data.img);
            $('#indvmain').html(data.content);
            $('#indvmenu').prepend(data.control);
        }
        else {
            $('#portMain').html(data.content);
        }
    },'json');

    populatePageData()
}

function populatePageData() {
    var page = $('#page').val();

    $.post('../php/siteText.php', { page: page }, function (data) {
        $('p#copyright').html(data.copyright);
        $('address#foot').html(data.footer);
        $('ul#listProgWeb').html(data.prog_web);
        $('ul#listNetSec').html(data.net_sec);
        $('ul#listProjMan').html(data.proj_man);
        $('div#user').html(data.user);
        $('span#log').html(data.log);
        $('#pretitle').html(data.group);
        document.title = data.title;
        //alert(data);
    },'json');
}