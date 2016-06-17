
$('document').ready(function () {

    var url = window.location.href;

    var member = parseURLParams(url);

    var memId = "";

    if (typeof member !== 'undefined')
    {
        memId = member.member.toString();
    }

    loadMemberInfo(memId);

    $('.ind-button').on('click', function () {
        var mode = this.id;
        $('.ind-button-active').removeClass('ind-button-active');
        $('#' + mode).addClass('ind-button-active');

        $.post('php/individual.php', { memId: memId, mode: mode, loading: false }, function (data) {
            //alert(data);
            $('#msg').html(data.msg);
            $('#indvmain').html(data.content);
        },'json');
    })

    $("#indvmain").on("click", ".workBtn", function (button) {
        var workItem = this.value;
        var home = 0;

        if(workItem === 'home')
        {
            home = 1;
            workItem = memId;
        }
        
        $.post('php/individual.php', { workId: workItem, home: home, mode: 'workItem', loading: false }, function (data) {
            //alert(data.workItem);
            $('#workMain').html(data.workItem);
        }, 'json');
    });

    $("#indvmain").on("click", "#btnSendMsg", function (button) {
        event.stopPropagation();
        event.preventDefault();
        var button = $(this);
        if ($('#txtComment').val() != "") {
            button.attr('disabled', 'disabled');

            var subject = "New message from Newbs Unit'd Contact Form";

            var body = "Sender's info\n\n";
            body += "Name: " + $('#txtName').val() + "\n";
            body += "Telephone: " + $('#txtPhone').val() + "\n";
            body += "Email: " + $('#txtEmail').val() + "\n\n";
            body += "Message:\n";
            body += $('#txtComment').val();

            $('div#msg').html("<span class='alert alert-info'>Processing message...</span>");

            $.post('php/contact.php', { mode: 'member',memId: memId, body: body, subject: subject }, function (data) {
                $('div#msg').html(data);

                $('#txtName').val("");
                $('#txtPhone').val("");
                $('#txtEmail').val("");
                $('#txtComment').val("");

                button.removeAttr('disabled');
            });
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
});

function loadMemberInfo(id)
{
    $.post('php/individual.php', { memId: id, mode: 'about', loading: true }, function (data) {
        //alert(data);
        $('#msg').html(data.msg);
        $('#title').html(data.name);
        $('#imgHook').html(data.img);
        $('#indvmain').html(data.content);
    },'json');
}

