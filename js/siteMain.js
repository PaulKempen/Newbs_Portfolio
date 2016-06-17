

$('document').ready(function () {

    var page = $('#page').val();
    var data = "";
    if(page == "search")
    {
        var url = window.location.href;
        var params = parseURLParams(url);
        if (typeof params !== 'undefined') {    
            data = params.findTerm.toString();
        }
    }
    populatePageData(page, data);

    // if (page === 'home') {
    //     var url = window.location.href;

    //     var params = parseURLParams(url);

    //     if (typeof params !== 'undefined') {    
    //         $('#myCarousel').carousel(params.focus);
    //     }
    // }

    // $('#myCarousel').carousel({
    //     pause: "false"
    // });
    // $('#playButton').click(function () {
    //     $('#myCarousel').carousel('cycle');
    //     $('#playButton').addClass('green-bord');
    //     $('#pauseButton').removeClass('red-bord');
    // });
    // $('#pauseButton').click(function () {
    //     $('#myCarousel').carousel('pause');
    //     $('#playButton').removeClass('green-bord');
    //     $('#pauseButton').addClass('red-bord');
    // });

    $("#questionPanel").on("click",".panel-button", function () {
        //get value of the button clicked which is the id of related answer
        var target = "#" + this.value;
        $(".faq-button-active").removeClass("faq-button-active");
        $(this).addClass("faq-button-active");
        //stop and finish other animations
        $(".answers").finish();

        //fade out current answer and fade in next one and switch faqTarg class
        $(".faqTarg").fadeOut("slow", function () {
            $(".faqTarg").removeClass("faqTarg");
            $(target).fadeIn("slow");
            $(target).addClass("faqTarg");
        });    
    })

    $("#btnContact").on("click", function (button) {
        event.stopPropagation();
        event.preventDefault();
        var button = $(this);
        if ($('#comment').val() != "") {
            button.attr('disabled', 'disabled');

            var subject = "New message from Newbs Unit'd Contact Form";

            var body = "Sender's info\n\n";
            body += "First name: " + $('#first').val() + "\n";
            body += "Last name: " + $('#last').val() + "\n";
            body += "Company: " + $('#company').val() + "\n";
            body += "Telephone: " + $('#tel').val() + "\n";
            body += "Email: " + $('#email').val() + "\n\n";
            body += "Message:\n";
            body += $('#comment').val();

            $('div#msg').html("<span class='alert alert-info'>Processing message...</span>");

            $.post('php/contact.php', { mode: 'all-admin', body: body, subject:subject }, function (data) { 
                $('div#msg').html(data);
                $('#first').val("");
                $('#last').val("");
                $('#company').val("");
                $('#tel').val("");
                $('#email').val("");
                $('#comment').val("");
                button.removeAttr('disabled');
            });
        }
      
    });
    
    $('#btnFocusSort').on('click',function(){
            var value = $('#selFocusSort').val();
            if(value === 'all'){
                $('.card').removeAttr('hidden');
            }
            else{
                $('.card').attr('hidden','hidden');
                $('.card.'+value).removeAttr('hidden');
            }
          
    });
    
    $('#fbShare').click(function(){ 
        window.open("http://www.facebook.com/sharer.php?u=" + window.location.href) ; 
        
        return false; 
    });
    $('#gpShare').click(function(){ 
        window.open("https://plus.google.com/share?url=" + window.location.href) ; 
        
        return false; 
    });
    $('#liShare').click(function(){ 
        window.open("http://www.linkedin.com/shareArticle?mini=true&amp;url=" + window.location.href) ; 
       
        return false; 
    });
    $('#twShare').click(function(){ 
        window.open("https://twitter.com/share?url=" + window.location.href + "&amp;text=Exceptional%20Individual%20Found&amp;hashtags=ExceptionalIndividual") ; 
        
        return false; 
    });
    $('#emailShare').click(function(){ 
        window.open("mailto:?Subject=Sharing%20Portfolios&amp;Body=I%20saw%20this%20and%20thought%20you%20might%20like%20this%20" + window.location.href) ; 
        
        return false; 
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

            $.post('php/contact.php', { mode: 'all-admin', body: body, subject: subject }, function (data) {
                $('#fbMsg').html(data);
                $('#txtFeedback').val("");
                button.removeAttr('disabled');
            });
        }

    });
   
});//end doc.rdy



function populatePageData(page, data)
{ 

    $.post('php/siteText.php', { page: page, data: data }, function (data) {
        $('p#copyright').html(data.copyright);
        $('address#foot').html(data.footer);
        // $('ul#listProgWeb').html(data.prog_web);
        // $('ul#listNetSec').html(data.net_sec);
        // $('ul#listProjMan').html(data.proj_man);
        $('div#user').html(data.user);
        $('span#log').html(data.log);
        $('#pretitle').html(data.group);
        document.title = data.title;
        //alert(data);
        switch (page)
        {
            case 'about':
                $('p#aboutText').html(data.about);
                break;
            case 'home':
                $('div#portfolios').html(data.carousel);

                $('.card:first-of-type').addClass("col-md-offset-1");
                $('.card:nth-of-type(6)').addClass("col-md-offset-1");
                break;
            case 'faq':
                $("div#questionPanel").html(data.questions);
                $("div#answerwell").html(data.answers);
                $(".answers:first-of-type").addClass("faqTarg");
                $(".panel-button:first-of-type").addClass("faq-button-active");
                $(".faqTarg").fadeIn("slow");
                break;
            case 'search':
                $("#searchResult").html(data.results);
                break;
            case 'contact':
                $("#contactinfo").html(data.contact);
                break;
            case 'viewAll':
                $("#cardHook").html(data.cards);
                $("#selFocusSort").append(data.options);
            default:
        }

    },'json');  
}


//function parseURLParams() obtained from http://stackoverflow.com/questions/814613/how-to-read-get-data-from-a-url-using-javascript
function parseURLParams(url) {
    var queryStart = url.indexOf("?") + 1,
        queryEnd = url.indexOf("#") + 1 || url.length + 1,
        query = url.slice(queryStart, queryEnd - 1),
        pairs = query.replace(/\+/g, " ").split("&"),
        parms = {}, i, n, v, nv;

    if (query === url || query === "") {
        return;
    }

    for (i = 0; i < pairs.length; i++) {
        nv = pairs[i].split("=");
        n = decodeURIComponent(nv[0]);
        v = decodeURIComponent(nv[1]);

        if (!parms.hasOwnProperty(n)) {
            parms[n] = [];
        }

        parms[n].push(nv.length === 2 ? v : null);
    }
    return parms;
}