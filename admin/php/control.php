<?php
    /**** includes ****/
    
    require_once './../../php/dbConnect.php';
    require_once './../../php/loginUtils.php';
    include_once 'adminControl.php';
    include_once 'memberControl.php';
    include_once 'textControl.php';
    include_once 'colorControl.php';
    include_once 'faqControl.php';


    /**** Variables ****/
    
    $reply = array('nav' => "",
                   'msg' => "",
                   'cont' => "");

    /**** Session ****/
    
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

    // Start the session
   	session_start();

    /**** Main line start ****/
    
    //check if user is not loged in
    if(!isset($_SESSION['logedIn']) || $_SESSION['logedIn'] == FALSE)
    {
        $reply['cont'] = displayRedirect();
    }
    else// user is loged in
    {      
        $reply = loadAdminConsole($reply);                
    }
    
    //return 
    echo json_encode($reply);
   

/***** functions below this line *****/
    function loadAdminConsole($reply)
    {
		$role = getRole($_SESSION['userId']);

        if($role != 'Admin')
        {
            $reply['msg'] = "<span class='alert alert-warning'>Only Admin are allowed here<span>";
            $reply['cont'] = "<div class='well col-md-6 col-md-offset-3 body-content' id='divLog'>
                                Members go <a class='body-links' href='../member/index.html'>here</a>
                              </div>";
        }
        else
        {
            if(!isset($_POST['admin']))
            {
                $reply = memberMain($reply);
            }
            else
            {
                switch($_POST['admin'])
                {
                    case "admin":
                        $reply = adminMain($reply);
                        break;
                    case "text":
                        $reply = textMain($reply);
                        break;
                    case "textUpdate":
                        $reply['list'] = getFocusList();
                        break;
                    case "faq":
                        $reply = faqMain($reply);
                        break;
                    case "color":
                        $reply = colorMain($reply);
                        break;
                    default:
                        $reply = memberMain($reply);
                        
                }
            }
        }
        
        return $reply;
    }
    

    function displayRedirect()
    {
        return "<div class='well col-md-6 col-md-offset-3 text-center body-content' id='divLog'>
                Admin only beyond this point<br/><br/>
              go <a class='body-links' href='../login.html'>here</a> to log in</div>";
    }
/**** end functions ****/
?>