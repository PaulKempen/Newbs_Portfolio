
$('document').ready(function () {
    var url = window.location.href;
    var param = getURLParam(url);
 
    switch (param)
    {
        case 'logout': logout();
            break;
        default:
            break;
    }

    $("#btnLogin").on("click", function (button) {
        event.stopPropagation();
        event.preventDefault();

        var userName = $('#txtUN').val();
        var password = $('#txtPW').val();

        $.post('php/login.php', { login: 'login', userName: userName, password: password }, function (data) {
            $('div#msg').html(data.msg);
            if (data.loggedIn)
            {
                switch (data.role)
                {
                    case 'Member': window.location.href = "member/index.html";
                        break;
                    case 'Admin': window.location.href = "admin/index.html";
                        break;
                    default:
                        break;
                }
            }
        },'json');
    });
    $("#btnSendUN").on("click", function (button) {
        var button = $(this);

        if ($('#forgotPW').val() != "") {
            button.attr('disabled', 'disabled');

            var subject = "Newbs United Lost Username Recovery";
            var email = $('#forgotPW').val();
            $("#userNameModal").modal("hide");
            $('div#msg').html("<span class='alert alert-info'>Processing request...</span>");

            $.post('php/contact.php', { mode: 'lost-username', email: email, subject: subject }, function (data) {
                $('div#msg').html(data);
                $('#forgotPW').val("");

                button.removeAttr('disabled');
            });
        }
    });
    $("#btnResetPW").on("click", function (button) {
        var button = $(this);

        if ($('#forgotUN').val() != "") {
            button.attr('disabled', 'disabled');

            var subject = "Newbs United Lost Password Recovery";
            var username = $('#forgotUN').val();
            $("#passwordModal").modal("hide");
            $('div#msg').html("<span class='alert alert-info'>Processing request...</span>");

            $.post('php/contact.php', { mode: 'lost-password', username: username, subject: subject }, function (data) {
                $('div#msg').html(data);
                $('#forgotPW').val("");

                button.removeAttr('disabled');
            });
        }
    });
    $("#btnRegister").on("click", function (button) {
        event.stopPropagation();
        event.preventDefault();
        var button = $(this);
        if ($('#basisNo').is(':checked')) {
            $('div#msg').html("<span class='alert alert-warning'>You must be a student of the BAS-IS '17 cohort to register</span>");
        }
        else {
            if ($('#email').val() != "" && $('#first').val() != "") {
                button.attr('disabled', 'disabled');

                var subject = "Member Request for BAS-IS portfolio website";

                var body = "Requester's info\n\n";
                body += "First name: " + $('#first').val() + "\n";
                body += "Last name: " + $('#last').val() + "\n";
                body += "Telephone: " + $('#tel').val() + "\n";
                body += "Email: " + $('#email').val() + "\n\n";
                body += "This individual would like an account set up for them\n"
                body += "If they are not a student in this cohort, please let them know";


                $('div#msg').html("<span class='alert alert-info'>Processing message...</span>");

                $.post('php/contact.php', { mode: 'all-admin', body: body, subject: subject }, function (data) {
                    $('div#msg').html(data);
                    $('#first').val("");
                    $('#last').val("");
                    $('#tel').val("");
                    $('#email').val("");
                    $('#regModal').modal('show');
                    button.removeAttr('disabled');
                });
            }
            else {
                $('div#msg').html("<span class='alert alert-warning'>Email and First name fields are required</span>");
            }
        }
    });
});

function logout()
{
    $.post('php/login.php', {logout: 'logout'}, function (data) {
        populatePageData("login");
        $('div#msg').html(data.msg);
    }, 'json');
}

function getURLParam(url) {
    var queryStart = url.indexOf("?") + 1,
        queryEnd = url.indexOf("#") + 1 || url.length + 1,
        query = url.slice(queryStart, queryEnd - 1);

    return query;
}
