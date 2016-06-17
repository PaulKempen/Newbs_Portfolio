
$(document).ready(function () {

    loadContent();
 
    //admin nav click
    $('li#navCtrl').on('click','#btnAdmin', function(event){
        $.post('php/control.php?',{admin:'admin',mode:'default'},function(data){
            
            $('div#cont').html(data.cont);
            $('div#msg').html(data.msg);
            $('li#navCtrl').html(data.nav);
           
        },'json');
    });
    
    //member nav click
    $('li#navCtrl').on('click','#btnMember', function(event){
        $.post('php/control.php',{admin:'member',mode:'default'},function(data){
            
            $('#cont').html(data.cont);
            $('#msg').html(data.msg);
            $('li#navCtrl').html(data.nav);
            
        },'json');
    });
    
    //site text nav click
    $('li#navCtrl').on('click','#btnText', function(event){
        $.post('php/control.php',{admin:'text'},function(data){
            
            $('div#cont').html(data.cont);
            $('div#msg').html(data.msg);
            $('li#navCtrl').html(data.nav);
            
        },'json');
    });
    //site color nav click
    $('li#navCtrl').on('click','#btnColor', function(event){
        $.post('php/control.php',{admin:'color'},function(data){
            //alert(data);
            $('div#cont').html(data.cont);
            $('div#msg').html(data.msg);
            $('li#navCtrl').html(data.nav);
            
        },'json');
    });
    //faq nav click
    $('li#navCtrl').on('click','#btnFaq', function(event){
        $.post('php/control.php',{admin:'faq'},function(data){
            
            $('div#cont').html(data.cont);
            $('div#msg').html(data.msg);
            $('li#navCtrl').html(data.nav);
            
        },'json');
    });
  
    /* admin management options */
    // add admin
     $('div#cont').on('click','#btnAddAdm', function(event){
        $.post('php/control.php?',{admin:'admin',mode:'default'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    $('div#cont').on('click','#btnAddAdmin', function(event){
        var auth = $('#txtAuth').val();
        var name = $('#txtName').val();
        var pw = $('#txtPassword').val();
        var conf = $('#txtConfirm').val();
        
        (pw === conf)?
            $.post('php/dbControl.php?',
                {mode:'add',auth:auth,name:name,password:pw,role:"Admin"},function(data){
                    $.post('php/control.php?',{admin:'admin',mode:'default'},function(data){
                        $('div#cont').html(data.cont);
                    },'json');
                $('div#msg').html(data);
            })
        : $('div#msg').html("Password and confirmation doesn't match, please try again");
     });
    
    // rename admin
    $('div#cont').on('click','#btnRenAdm', function(event){
        $.post('php/control.php?',{admin:'admin',mode:'rename'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    // reset admin pw
    $('div#cont').on('click','#btnResAdmPW', function(event){
        $.post('php/control.php?',{admin:'admin',mode:'reset'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    // delete admin
    $('div#cont').on('click','#btnDelAdm', function(event){
        $.post('php/control.php?',{admin:'admin',mode:'delete'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    
    
    /* member management options */
    // add member
     $('div#cont').on('click','#btnAddMem', function(event){
        $.post('php/control.php?',{admin:'member',mode:'default'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    
    $('div#cont').on('click','#btnAddMember', function(event){
        var auth = $('#txtAuth').val();
        var name = $('#txtName').val();
        var pw = $('#txtPassword').val();
        var conf = $('#txtConfirm').val();

        (pw === conf)?
            $.post('php/dbControl.php?',
                {mode:'add',auth:auth,name:name,password:pw,role:"Member"},function(data){
                    $.post('php/control.php?',{admin:'member',mode:'default'},function(data){
                        $('div#cont').html(data.cont);
                },'json');
                $('div#msg').html(data);
            }): $('div#msg').html("Password and confirmation doesn't match, please try again");
     });

    // rename member
    $('div#cont').on('click','#btnRenMem', function(event){
        $.post('php/control.php?',{admin:'member',mode:'rename'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    $('div#cont').on('click','#btnRename', function(event){
        var auth = $('#txtAuth').val();
        var name = $('#txtName').val();
        var userId = $('#memberSelect').val();
        var mode = $('#adminMode').val();
        
        $.post('php/dbControl.php?',
            {mode:'rename',auth:auth,name:name,user_id:userId},function(data){
                $.post('php/control.php?',{admin:mode,mode:'rename'},function(data){
                    $('div#cont').html(data.cont);
                },'json');
            $('div#msg').html(data);
        });

     });
    
    // reset pw
    $('div#cont').on('click','#btnResMemPW', function(event){
        $.post('php/control.php?',{admin:'member',mode:'reset'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
     $('div#cont').on('click','#btnReset', function(event){
        var auth = $('#txtAuth').val();
        var userId = $('#memberSelect').val();
        var pw = $('#txtPassword').val();
        var conf = $('#txtConfirm').val();
        var mode = $('#adminMode').val();
        var force = 0;
        
        if($("#chkForce").is(':checked')){
            var force = 1;
        }

        (pw === conf)?
            $.post('php/dbControl.php?',
                {mode:'reset',auth:auth,password:pw,user_id:userId,force:force}, function(data){
                    $.post('php/control.php?',{admin:mode,mode:'reset'}, function(data){
                        $('div#cont').html(data.cont);
                    },'json');
                $('div#msg').html(data);
            })
        : $('div#msg').html("Password and confirmation doesn't match, please try again");
     });
    
    // delete member
    $('div#cont').on('click','#btnDelMem', function(event){
        $.post('php/control.php?',{admin:'member',mode:'delete'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    $('div#cont').on('click','#btnDelete', function(event){
        var auth = $('#txtAuth').val();
        var userId = $('#memberSelect').val();
        var mode = $('#adminMode').val();
        
        $.post('php/dbControl.php?',
            {mode:'delete',auth:auth,user_id:userId},function(data){
                $.post('php/control.php?',{admin:mode,mode:'delete'},function(data){
                    $('div#cont').html(data.cont);
                },'json');
            $('div#msg').html(data);
        });

     });
    
    /* faq management options */
    // add faq
     $('div#cont').on('click','#btnAddFaq', function(event){
        $.post('php/control.php?',{admin:'faq',mode:'default'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    
    $('div#cont').on('click','#btnFaqAdd', function(event){
        var auth = $('#txtAuth').val();
        var question = $('#txtQ').val();
        var answer = $('#txtA').val();
        
        $.post('php/dbControl.php?',
               {mode:'faqAdd',auth:auth,question:question,answer:answer},function(data){
                    $.post('php/control.php?',{admin:'faq',mode:'default'},function(data){
                        $('div#cont').html(data.cont);
                    },'json');
                $('div#msg').html(data);
            });
     });

    // modify question
    $('div#cont').on('click','#btnModQuest', function(event){
        $.post('php/control.php?',{admin:'faq',mode:'question'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    $('div#cont').on('click','#btnModQ', function(event){
        var auth = $('#txtAuth').val();
        var question = $('#txtQ').val();
        var faqId = $('#faqSelect').val();
 
        $.post('php/dbControl.php?',
            {mode:'faqQ',auth:auth,question:question,faq_id:faqId},function(data){
                $.post('php/control.php?',{admin:'faq',mode:'question'},function(data){
                    $('div#cont').html(data.cont);
                },'json');
            $('div#msg').html(data);
        });

     });
    
    // modify answer
    $('div#cont').on('click','#btnModAnsw', function(event){
        $.post('php/control.php?',{admin:'faq',mode:'answer'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
     $('div#cont').on('click','#btnModA', function(event){
        var auth = $('#txtAuth').val();
        var answer = $('#txtA').val();
        var faqId = $('#faqSelect').val();
        
        $.post('php/dbControl.php?',
            {mode:'faqA',auth:auth,answer:answer,faq_id:faqId},function(data){
                $.post('php/control.php?',{admin:'faq',mode:'answer'},function(data){
                    $('div#cont').html(data.cont);
                },'json');
            $('div#msg').html(data);
        });
     });
    
    // delete faq
    $('div#cont').on('click','#btnDelFaq', function(event){
        $.post('php/control.php?',{admin:'faq',mode:'delete'},function(data){
            $('div#cont').html(data.cont);
        },'json');
     });
    $('div#cont').on('click','#btnDeleteFaq', function(event){
        var auth = $('#txtAuth').val();
        var faqId = $('#faqSelect').val();
 
        $.post('php/dbControl.php?',
            {mode:'faqDel',auth:auth,faq_id:faqId},function(data){
                $.post('php/control.php?',{admin:'faq',mode:'delete'},function(data){
                    $('div#cont').html(data.cont);
                },'json');
            $('div#msg').html(data);
        });
        
    });

    $('div#cont').on('click', '.site-text', function (event) {
        var mode = this.value;
        var data = "";
        var auth = $('#txtAuth').val();
        switch(mode)
        {
            case 'copy': data = $('#txtCopyright').val();
                break;
            case 'foot': data = $('#txtFooter').val();
                break;
            case 'about': data = $('#txtAbout').val();
                break;
            case 'title': data = $('#txtTitle').val();
                break;
            case 'head': data = $('#txtHead').val();
                break;
            case 'contact': data = $('#txtContact').val();
                break;   
            case 'addFocus': data = $('#txtFocus').val();
                break;
            case 'delFocus': data = $('#selFocus').val();
                break;
            default:
                break;
        }

        $.post('php/dbControl.php', { mode: mode, auth: auth, data: data }, function (data) {
            $('div#msg').html(data);
            populatePageData();
            
            switch(mode)
            {
                case 'addFocus': $('#txtFocus').val("");
                case 'delFocus':
                    $.post('php/control.php',{admin:'textUpdate'},function(data){
                        $('#selFocus').empty().append(data.list);
                    },'json'); 
                    break;
                default:
                    break; 
            }
        });
        
        

    });
    
    $('#cont').on("change",'#redSlider',function(){
        $('#redText').val($('#redSlider').val());
        $('#testColor').css('background-color',"rgb("
                            + $('#redText').val()
                            + "," + $('#greenText').val()
                            + "," + $('#blueText').val()
                            + ")");
    });
    $('#cont').on("change",'#greenSlider',function(){
        $('#greenText').val($('#greenSlider').val());
        $('#testColor').css('background-color',"rgb("
                            + $('#redText').val()
                            + "," + $('#greenText').val()
                            + "," + $('#blueText').val()
                            + ")");
    });
    $('#cont').on("change",'#blueSlider',function(){
        $('#blueText').val($('#blueSlider').val());
        $('#testColor').css('background-color',"rgb("
                            + $('#redText').val()
                            + "," + $('#greenText').val()
                            + "," + $('#blueText').val()
                            + ")");
    });
    
    
    $('#cont').on("click",'#btnRedUpdate',function(){
        $('#redSlider').val($('#redText').val());
        $('#testColor').css('background-color',"rgb("
                            + $('#redText').val()
                            + "," + $('#greenText').val()
                            + "," + $('#blueText').val()
                            + ")");
    });
    $('#cont').on("click",'#btnGreenUpdate',function(){ 
        $('#greenSlider').val($('#greenText').val());
        $('#testColor').css('background-color',"rgb("
                            + $('#redText').val()
                            + "," + $('#greenText').val()
                            + "," + $('#blueText').val()
                            + ")");
    });
    $('#cont').on("click",'#btnBlueUpdate',function(){
        $('#blueSlider').val($('#blueText').val());
        $('#testColor').css('background-color',"rgb("
                            + $('#redText').val()
                            + "," + $('#greenText').val()
                            + "," + $('#blueText').val()
                            + ")");
    });
    
     $("#cont").on("click",".colorPreview", function () {
        var id = $(this).attr('id');
        var hex = $('#'+id+'-value').val();
        var red = parseInt(hex.substring(0,2),16);
        var green = parseInt(hex.substring(2,4),16);
        var blue = parseInt(hex.substring(4),16);
        
        $('#idHolder').val(id);
        $('#redSlider').val(red);
        $('#redText').val(red);
        $('#greenSlider').val(green);
        $('#greenText').val(green);
        $('#blueSlider').val(blue);
        $('#blueText').val(blue);
        $('#testColor').css('background-color','#'+hex);
        $('#colorPickerModal').modal('show');
        
    });
    
    $('#cont').on("click","#btnSaveColor", function(){
        $('#colorPickerModal').modal('hide');
        var id = $('#idHolder').val();
        var red = parseInt($('#redText').val());
        var green = parseInt($('#greenText').val());
        var blue = parseInt($('#blueText').val());
        var hex = (red < 16 ? "0" + red.toString(16)  : red.toString(16) )+ 
                  (green < 16 ? "0" + green.toString(16)  : green.toString(16) ) + 
                  (blue < 16 ? "0" + blue.toString(16)  : blue.toString(16) );
       
        updateColorListItem(id, hex);
    });
    
    $('#cont').on("click", ".btnColorUpdate",function(){
        var id = $(this).val();
        var hex = $('#'+id+'-txt').val();
        updateColorListItem(id, hex);
    });
    
    $('#cont').on("click", "#btnSelectSet",function(){
        var profile = $('#setSelect').val();
        if(profile !== "-1")
        {
            $.post('php/colorControl.php', { colMode: "load",  profile: profile }, function (data) {
                $('div#msg').html(data.msg);
                $('#profileName').val(data.profile);
                $('#setName').html(data.profile);
                $('#colorListMain').html(data.list);
                
            },'json');
        }
    });
    
    $('#cont').on("click", "#btnUpdateSet",function(){
         var data = {colMode : "update", profile: $('#profileName').val()};
         
         $('.colorList').each(function(){
             var id = $(this).children('.colorPreview').attr('id');
             var value = $('#' + id + "-value").val();
             data[id] = value;
         });
         
        $.post('php/colorControl.php', data, function (data) {
             $('div#msg').html(data.msg);  
        }, 'json');
    });
    
    $('#cont').on("click", "#btnSaveSet",function(){
        var profile = $('#txtSaveSet').val();
        if(profile !== ""){
            var data = {colMode : "save", profile: profile};
            
            $('.colorList').each(function(){
                var id = $(this).children('.colorPreview').attr('id');
                var value = $('#' + id + "-value").val();
                data[id] = value;
            });
            
            $.post('php/colorControl.php', data, function (data) {
                $('div#msg').html(data.msg);
                if(data.good)
                {   
                    $('#profileName').val(profile);
                    $('#setName').html(profile);
                    $('#setSelect').append($("<option/>", {
                        value: profile,
                        text: profile
                    }));
                }
            },'json');
        }
        else{
            $('div#msg').html("<span class='alert alert-warning'>Enter a Unique name to save Profile</span>"); 
        }
    });
    
    $('#cont').on("click", "#btnWriteSet",function(){
        var profile = $('#profileName').val();
        $.post('php/colorControl.php', { colMode: "apply",  profile: profile }, function (data) {
                $('div#msg').html(data.msg);
                $('#siteProfile').html(profile);
        },'json');
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

function loadContent()
{
    $.post('php/control.php',{},function(data){
        
            $('div#cont').html(data.cont);
            $('div#msg').html(data.msg);
            $('li#navCtrl').html(data.nav);
            //alert(data);
            populatePageData();
        },'json');
}

function populatePageData() {
    var page = $('#page').val();

    $.post('../php/siteText.php', { page: page }, function (data) {
        $('p#copyright').html(data.copyright);
        $('address#foot').html(data.footer);
        $('div#user').html(data.user);
        $('span#log').html(data.log);
        $('#pretitle').html(data.group);
        document.title = data.title;
        //alert(data);
    },'json');
}

function updateColorListItem(id, hex)
{
    hex = hex.toUpperCase();
    
    $('#'+id+'-txt').attr('placeholder', hex);
    $('#'+id+'-value').val(hex);
     $('#'+id+'-span').html(hex);
    $('#'+id).css('background-color', "#"+hex);
    $('#'+id+'-txt').val("");
}