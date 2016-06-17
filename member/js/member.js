
/*
 * Page Hooks:
 * span#log : toggles logout/login buttons
 * div#user : displayed welcome [user] with links to account and portfolio
 * div#msg : used to display feedback to user
 * div#cont : main content display
 * 
 */

$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
    console.log("Error = " + thrownError);
});

$(document).ready(function () {
    var id = 0;
    loadContent();

    //first login info submit
    $('div#cont').on('click', '#btnInfoUpdate', function (event) {
        event.stopPropagation();
        //event.preventDefault();

        var first = $('input#txtFirst').val();
        var middle = $('input#txtMiddle').val();
        var last = $('input#txtLast').val();
        var phone = $('input#txtPhone').val();
        var email = $('input#txtEmail').val();
        var check = $('input#txtConfirm').val();

        //if email and confirm do not match
        if (email != check) {
            $('div#msg').html(
                "<span class='alert alert-warning'>Email and Confirmation do not match,please try again</span>");
        }
        else//proceed
        {
            if (first == "" ||
               middle == "" ||
               last == "" ||
               email == "") {
                $('div#msg').html(
                "<span class='alert alert-warning'>Please fill out all required fields</span>");
            }
            else {
                $.post('../php/memberAccount.php',
                       {
                           mode: 'first',
                           first: first,
                           mid: middle,
                           last: last,
                           phone: phone,
                           email: email
                       },
                    function (data) {
                        $('div#msg').html(data.msg);

                        if (data.success) {
                            loadContent();
                        }

                    }, 'json');
            }
        }

    });//end button

    /* 
     * 
     * event handlers for account side buttons 
     * 
     */
    $('div#cont').on('click', '#btnPInfo', function (event) {
        $.post('../php/memberAccount.php', { mode: 'pInfo' }, function (data) {
            //console.log(data);
            $('div#account').html(data.account);
            $('div#msg').html("");
        }, 'json');
    });//end button

    $('div#cont').on('click', '#btnSInfo', function (event) {
        $.post('../php/memberAccount.php', { mode: 'sInfo', adminSelect: id }, function (data) {
            //alert(data);
            $('div#account').html(data.account);
            $('div#msg').html("");
        },'json');
        
    });//end button

    $('div#cont').on('click', '#btnResPw', function (event) {
        $.post('../php/memberAccount.php', { mode: 'resPw' }, function (data) {
            $('div#account').html(data.account);
            $('div#msg').html("");
        }, 'json');
    });//end button
    
    /* 
     * 
     * event handlers for sort page buttons
     * 
     */
    $('div#cont').on('click', '#btnPic', function (event) {
        $.post('../php/memberAccount.php', { mode: 'pic' }, function (data) {
            $('div#sort').html(data.sort);
        }, 'json');
    });//end button

    $('div#cont').on('click', '#btnFocus', function (event) {
        $.post('../php/memberAccount.php', { mode: 'focus' }, function (data) {
            $('div#sort').html(data.sort);
        }, 'json');
    });//end button

    $('div#cont').on('click', '#btnExp', function (event) {
        $.post('../php/memberAccount.php', { mode: 'exp' }, function (data) {
            $('div#sort').html(data.sort);
        }, 'json');
    });//end button


    //event handlers for picture file selection
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $('div#cont').on('fileselect', '.btn-file :file', function (event, numFiles, label) {
        $('#fileLabel').text(label);
    });

    /* Event handlers for personal info updateing */
    //first name
    $('div#cont').on('click', '#btnFirst', function (event) {
        event.stopPropagation();
        event.preventDefault();

        if ($('#btnFirst').text() == "Edit") {
            $('#txtFirst').prop("disabled", false);
            $('#btnFirst').text("Save");
            $('div#msg').html("");
        }
        else {
            var text = $('#txtFirst').val();
            $.post('../php/memberAccount.php', { mode: 'updatePInfo', col: 'first', data: text }, function (data) {
                $('div#msg').html(data.msg);
                //alert(data);
            },'json');
            
            $('#txtFirst').prop("disabled", true);
            $('#btnFirst').text("Edit");
        }
    });//
    //middle
    $('div#cont').on('click', '#btnMi', function (event) {
        event.stopPropagation();
        event.preventDefault();

        if ($('#btnMi').text() == "Edit") {
            $('#txtMi').prop("disabled", false);
            $('#btnMi').text("Save");
            $('div#msg').html("");
        }
        else {
            var text = $('#txtMi').val();
            $.post('../php/memberAccount.php', { mode: 'updatePInfo', col: 'mi', data: text }, function (data) {
                $('div#msg').html(data.msg);
            }, 'json');

            $('#txtMi').prop("disabled", true);
            $('#btnMi').text("Edit");
        }
    });//
    //last name
    $('div#cont').on('click', '#btnLast', function (event) {
        event.stopPropagation();
        event.preventDefault();

        if ($('#btnLast').text() == "Edit") {
            $('#txtLast').prop("disabled", false);
            $('#btnLast').text("Save");
            $('div#msg').html("");
        }
        else {
            var text = $('#txtLast').val();
            $.post('../php/memberAccount.php', { mode: 'updatePInfo', col: 'last', data: text }, function (data) {
                $('div#msg').html(data.msg);
            }, 'json');

            $('#txtLast').prop("disabled", true);
            $('#btnLast').text("Edit");
        }
    });//
    //phone
    $('div#cont').on('click', '#btnPhone', function (event) {
        event.stopPropagation();
        event.preventDefault();

        if ($('#btnPhone').text() == "Edit") {
            $('#txtPhone').prop("disabled", false);
            $('#btnPhone').text("Save");
            $('div#msg').html("");
        }
        else {
            var text = $('#txtPhone').val();
            $.post('../php/memberAccount.php', { mode: 'updatePInfo', col: 'phone', data: text }, function (data) {
                $('div#msg').html(data.msg);
            }, 'json');

            $('#txtPhone').prop("disabled", true);
            $('#btnPhone').text("Edit");
        }
    });//
    //email
    $('div#cont').on('click', '#btnEmail', function (event) {
        event.stopPropagation();
        event.preventDefault();

        if ($('#btnEmail').text() == "Edit") {
            $('#txtEmail').prop("disabled", false);
            $('#btnEmail').text("Save");
            $('div#msg').html("");
        }
        else {
            var text = $('#txtEmail').val();
            $.post('../php/memberAccount.php', { mode: 'updatePInfo', col: 'email', data: text }, function (data) {
                $('div#msg').html(data.msg);
            }, 'json');

            $('#txtEmail').prop("disabled", true);
            $('#btnEmail').text("Edit");
        }
    });//

    /* Event handler for reset password */
    $('div#cont').on('click', '#btnPw', function (event) {
        event.stopPropagation();
        event.preventDefault();

        var oldPw = $('#txtOldPw').val();
        var newPw = $('#txtNewPw').val();
        var checkPw = $('#txtNewPw2').val();
        
        if (newPw !== checkPw) {
            $('div#msg').html("<span class='alert alert-warning'>The passwords you entered do not match, please try again</span>");
        } else if (oldPw == "" || newPw == "") {
            $('div#msg').html("<span class='alert alert-warning'>Please fill in all the fields</span>");
        }
        else {
            $.post('../php/memberAccount.php', { mode: 'resetPw', old: oldPw, new: newPw }, function (data) {
                $('div#msg').html(data.msg);
                if (data.good) {
                    $('#txtOldPw').val('');
                    $('#txtNewPw').val('');
                    $('#txtNewPw2').val('');
                }
            }, 'json');
        }
    });//
    $('div#cont').on('click', '#btnResetPw', function (event) {
        event.stopPropagation();
        event.preventDefault();

        var newPw = $('#txtNwPw').val();
        var checkPw = $('#txtNwPw2').val();

        if (newPw !== checkPw) {
            $('div#msg').html("<span class='alert alert-warning'>The passwords you entered do not match, please try again</span>");
        } else if (newPw == "") {
            $('div#msg').html("<span class='alert alert-warning'>Please fill in all the fields</span>");
        }
        else {
            $.post('../php/memberAccount.php', { mode: 'resetPw', new: newPw }, function (data) {
                $('div#msg').html(data.msg);
                if (data.good) {
                    loadContent();    
                }
                
            }, 'json');
        }
    });//

    /* Event handlers for sort page info updates */
    $('div#cont').on('click', '#btnBlurb', function (event) {
        event.stopPropagation();
        event.preventDefault();

        var blurb = $('#txtBlurb').val();

        $.post('../php/memberAccount.php', { mode: 'blurb',adminSelect: id,  data: blurb }, function (data) {
            $('div#msg').html(data.msg);
            $('div#account').html(data.account);
            if (data.good) {
                $('#txtBlurb').val('');
            }
            //alert(data);
        },'json');
    });//

    $('div#cont').on('click', '#btnUpdateFocus', function (event) {
        event.stopPropagation();
        event.preventDefault();

        var focus = "";
        var number = $("input[type=checkbox]:checked").length;
        $('input[type=checkbox]').each(function () {
            if (this.checked)
                focus += $(this).val() + (--number > 0? "|":"");
        });

        $.post('../php/memberAccount.php', { mode: 'updateFocus', adminSelect: id,  data: focus }, function (data) {
            $('div#msg').html(data.msg);
            $('div#account').html(data.account);
 
        }, 'json');
    });//
    
    //prevents more than {limit} checkboxes being checked
    $('div#cont').on('change','input[type=checkbox]', function(evt) {
        var limit = 5;
        if($("input[type=checkbox]:checked").length > limit) {
            this.checked = false;
        }
    });
    
    /* Event handler for uploading profile picture */
    $('div#cont').on('click', '#btnUpPic', function () {
        event.stopPropagation();
        event.preventDefault();

        var data = new FormData();
        data.append("pic", $('#filePic')[0].files[0]);
        data.append("mode", "profilePic");

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
            //console.log(info);
            $.post('../php/memberAccount.php', { mode: 'sInfo', adminSelect: id  }, function (data) {
                $('div#account').html(data.account);
                $('div#msg').html(info.msg);
            }, 'json');
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // Handle errors here
            console.log('ERRORS: ' + errorThrown + textStatus);
        });
    });

    //event handle for admin control
    $("div#cont").on("click", "#btnAdminSelect", function () {
        id = $('#adminSelect').val();
        $.post('../php/memberAccount.php', { mode: 'sInfo', adminSelect: id }, function (data) {
            //alert(data);
            $('div#account').html(data.account);
            $('div#msg').html("");
        },'json');
    });

    //event handler for deleting work pics
    $("div#cont").on("click", "#btnDelPic", function () {
        
        if ($('#delAuth').is(':checked')) {
            $.post('../php/memberAccount.php', { adminSelect: id, mode: 'delPic'}, function (data) {
                $('#msg').html(data.msg);
            },'json');
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

});//end document.ready

function loadContent() {
    $.post('../php/member.php', {}, function (data) {
        //alert(data);
        $('div#cont').html(data.cont);
        populatePageData();
    },'json');
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
    },'json');
}