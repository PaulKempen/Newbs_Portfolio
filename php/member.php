<?php
    /**** includes ****/
    
    require_once 'dbConnect.php';
	require_once 'loginUtils.php';
	
	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

    // Start the session
   	session_start();

    /**** Variables ****/
    
    $reply = array('msg' => "",
                   'cont' => "",
				   'change_pw' => 0);


    /**** Main line start ****/

    //check if user is not loged in
    if(!isset($_SESSION['logedIn']) || $_SESSION['logedIn'] == FALSE)
    {             
        $reply['cont'] = displayRedirect();
    }
    else// user is loged in
    {   
        loadMemberContent($reply);    
    }

    //return 
    echo json_encode($reply);

/**** end main line - functions below ****/


    //verify user login info and set session variables so user is "loged in"
    function loadMemberContent(&$reply)
    {
        if(isset($_SESSION['changePw']) && $_SESSION['changePw'])
        {
            $reply['cont'] = displayPasswordReset();
        }
        else
        {
            getAccountInfo($reply);
        }      	        
    }
    
    function getAccountInfo(&$reply)
    {
        $conn = dbConnect();
        //prepare statement
        $stmt = $conn->prepare("SELECT first FROM member_info WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s",$_SESSION['userId']);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();
		//varify results
		if($result && $result->num_rows !== 0)
		{
            $row = $result->fetch_row();
            if($row[0] != NULL)
            {
                $reply['cont'] = displayMainContent($row[0]);
            }
            else
            {
                $reply['cont'] = displayInfoForm();
            }

			$result->close();
        }
        else
		{
			//boom;
		}
		$stmt->close();
		$conn->close();

		
    }


    function displayRedirect()
    {
        return "<div class='well col-md-6 col-md-offset-3 body-content' id='divLog'>
                    <p class='text-primary'>
                        Members Only<br />
                        Please log in 
                    </p>
                    
                </div>";
    }
    
    function displayInfoForm()
    {
        return "<div class='well col-md-6 col col-md-offset-3 body-content' id='firstLogin'>
                    <h3>Welcome, ".$_SESSION['userName']."</h3>
                    <p>
                        It appears that this is your first time loging in,
                        so lets collect some information
                    </p>
                    note: Items marked with a <span class='redText'>*</span> are required<br />
                    <form class='line-height-50 no-float' method='post'>
                        <label class='label label-default' for='txtFirst'>First name:</label><span class='redText'>*</span>
                        <input class='input-sm' type='text' id='txtFirst' required='required'><br />
                        <label class='label label-default' for='txtMiddle'>Middle initial:</label><span class='redText'>*</span>
                        <input class='input-sm' type='text' id='txtMiddle' required='required' maxlength='1'><br />
                        <label class='label label-default' for='txtLast'>Last name:</label><span class='redText'>*</span>
                        <input class='input-sm' type='text' id='txtLast' required='required'><br />
                        <label class='label label-default' for='txtPhone'>Phone number:</label>
                        <input class='input-sm' type='text' id='txtPhone'><br />
                        <label class='label label-default' for='txtEmail'>Email address:</label><span class='redText'>*</span>
                        <input class='input-sm' type='email' id='txtEmail' required='required'><br />
                        <label class='label label-default' for='txtConfirm'>Confirm Email address:</label><span class='redText'>*</span>
                        <input class='input-sm' type='email' id='txtConfirm' required='required'><br />
                        <input class='btn btn-primary' type='submit' id='btnInfoUpdate' value='Submit' />
                    </form>
                </div>";
    }


	function displayMainContent($first)
    {	      
        $text = "";
        
        if(getRole($_SESSION['userId']) == 'Admin')
        {
            $text = "Admin on this section can edit their own personal information and change their own password if they wish<br/>
                     You also have the ability to edit member's sort information by selecting a member<br/>
                     from the dropdown box above the photo on the sort page";
        }
        else
        {
            $text ="On this page you can edit your personal information, information displayed on the \"sort\" page, and change your password<br/><br/>
                    If you would like to edit your portfolio Information, click <a href='portfolio.html'>here</a> or use the link at the top of the page<br/><br/>
					sort page contains the data displayed on the home page carousel as well as lets you change your profile image";
        }
        return "<div class='well well-lg col-md-3 col-md-offset-1 text-center line-height-50 body-content'>
                    <h3>Choose an item to view or edit</h3>
                    <button class='btn btn-link body-links' id='btnPInfo'>Personal Information</button><br/>
                    <button class='btn btn-link body-links' id='btnSInfo'>'Sort' page information</button><br/>
                    <button class='btn btn-link body-links' id='btnResPw'>Reset Password</button>
                </div>
                <div class='well well-lg col-md-6 col-md-offset-1 text-center body-content' id='account'>
                    <h3>Welcome, ".$first."</h3>
                    <p>".$text."</p>
                </div>";
    }

	function displayPasswordReset()
    {
        return "<div class='well col-md-6 col-md-offset-3 text-center body-content'>
                <h3>You are required to change your password</h3>
                    <form class='container-fluid line-height-50' id='accountForm'>
                        <div class='input-group row'>
                            <div class='col-sm-5 text-right'>
                                <label class='' for='txtNewPw'>New Password</label>
                            </div>
                            
                            <div class='col-sm-7 text-left'>
                                <input class='input-sm' type='password' required='required' id='txtNwPw' />
                            </div>
                        </div>
                        <div class='input-group row'>
                            <div class='col-sm-5 text-right'>
                                <label class='' for='txtNewPw2'>Re-enter Password</label>
                                </div>
                                <div class='col-sm-7 text-left'>
                                    <input class='input-sm' type='password' required='required' id='txtNwPw2' />
                                </div>
                            </div>
                        <button class='btn btn-secondary' id='btnResetPw'>Submit</button>
                        <div>Passwords must contain:
                            <UL class='list-group pw-regex'>
                                <li class='list-group-item'>1 Upper case Character</li>
                                <li class='list-group-item'>1 Lower case Character</li>    
                                <li class='list-group-item'>1 Number</li>
                                <li class='list-group-item'>8 characters minimum</li>    
                            </UL>
                        </div>
                    </form>
        </div>";
    }
?>