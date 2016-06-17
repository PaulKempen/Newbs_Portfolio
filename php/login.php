<?php 
	/**** includes ****/
	require_once 'loginUtils.php';
	
	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

	//start session
	session_start();

    /**** Variables ****/
    
    $reply = array('msg' => "");

    /**** Main line start ****/

	if(isset($_POST['logout']))
	{
		$reply["msg"] = logout();
	}

	if(isset($_POST['login']))
	{
		if(checkLogin($_POST['userName'],$_POST['password']))
		{
			$reply["loggedIn"] = TRUE; 
			$reply["role"] = $_SESSION['role'];
		}
		else
		{
			$reply["loggedIn"] = FALSE;
			$reply["msg"] = "<span class='alert alert-danger'>Invalid username or password</span>";
		}
	}

	echo json_encode($reply);
?>