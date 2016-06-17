<?php 
	/**** includes ****/
    require_once 'dbConnect.php';

	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

	//start session
	session_start();

	/**** variables ****/
	$emailTo = "";
	$msg = "";
	$txt = "";
	/**** Mainline Start ****/

	if(isset($_POST['mode']))
	{
		switch($_POST['mode'])
		{
			case 'all-admin': 
					$emailTo = getAdminEmails();
					$txt = $_POST['body'];
				break;
			case 'member': $emailTo = getMemberEmail($_POST['memId']);
					$txt = $_POST['body'];
				break;
			case 'lost-password': fixLostPassword($_POST['username'], $emailTo, $txt);
				break;
			case 'lost-username': getLostUsername($_POST['email'], $emailTo, $txt);
				break;
			default:
				break;
		}

		$msg = sendEmail($emailTo,$_POST['subject'], $txt);
	}

	echo $msg;


	/**** Functions ****/
	function getAdminEmails()
	{
		$emails = "";
		$conn = dbConnect();

        $stmt = $conn->prepare("SELECT a.email 
		                        FROM member_info as a 
								INNER JOIN login as b
								ON a.user_id = b.user_id
								WHERE b.role = 'Admin'");
		
		$stmt->execute();

		$result = $stmt->get_result();
		$count = $result->num_rows;

        if($result && $count > 0)
		{
			while($row = $result->fetch_row())
			{
				$emails .= $row[0];
				$count--;

				if($count > 0)
					$emails .= ",";
			}

			$result->close();
		}
		else
		{
			//boom
		}
		
		//close connection
		$stmt->close();
		$conn->close();

		return $emails;
	}

	function getMemberEmail($memberId)
	{
		$email = "";
		$conn = dbConnect();

        $stmt = $conn->prepare("SELECT email FROM member_info
								WHERE user_id =?");
 
		$stmt->bind_param("s", $memberId);
		
		$stmt->execute();

		//bind results to variable
		$result = $stmt->get_result();
    
        if($result && $result->num_rows > 0)
		{
			$row = $result->fetch_row();

			$email = $row[0];

			$result->close();
		}
		else
		{
			//boom
		}
		
		//close connection
		$stmt->close();
		$conn->close();

		return $email;
	}

	function sendEmail($to, $subject, $txt)
	{
		$msg = "";
		
		$headers = "From: Newbs Unit'd Portfolio Website";
		if($to != NULL)
		{
			if(mail($to,$subject,$txt,$headers))
			{
				$msg = "<span class='alert alert-success'>Message sent Successfully</span>";
			}
			else
			{
				$msg = $to;//"<span class='alert alert-warning'>Message failed to be sent</span>";
			}
		}
		else
		{
			$msg = "<span class='alert alert-warning'>No email on file, please contact an admin to fix your issue</span>";
		}
		return $msg;
	}

	//function randomPassword found at: http://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
	function randomPassword() 
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	function fixLostPassword($username, &$emailTo, &$txt)
	{
		$newPassword = randomPassword();
		$changePW = 1;
		$conn = dbConnect();
		
		//prepare statement
		$stmt = $conn->prepare("SELECT a.email 
				                FROM member_info as a
								INNER JOIN login as b
								ON a.user_id = b.user_id
								WHERE b.user_name =?");
		//bind parameters to statement	
		$stmt->bind_param("s", $username);
		//execute query
		if($stmt->execute())
		{
			$result = $stmt->get_result();
			$row = $result->fetch_row();
			$emailTo = $row[0];
			$result->close();
		}
		$stmt->close();

		if($emailTo != "" && $emailTo != NULL)
		{
			//prepare statement - repeat process to change password if email is on file
			$stmt = $conn->prepare("UPDATE login SET password =?, change_pw=? WHERE user_name =?");
			//bind parameters to statement
			$stmt->bind_param("sis", password_hash($newPassword, PASSWORD_DEFAULT), $changePW, $username);

			//execute query
			if($stmt->execute())
			{
				$txt = "Greetings from Newbs Unit'd Portfolio website\n\n
						Recently a new password was requested for your account\n
						a new password has been generated and you will be required to change it when you next log in\n\n
						New Password = ".$newPassword;	
			}
			else
			{
				$txt = "Greetings from Newbs Unit'd Portfolio website\n\n
						Recently a new password was requested for your account\n
						I am sorry to inform you that there was an error in applying a new password to your account\n
						If this problem persists please contact a website administrator";
			}

			$stmt->close();
		}
		else
		{
			$emailTo = NULL;
		}
		
        //close connection
       
        $conn->close(); 
	}

	function getLostUsername($email, &$emailTo, &$txt)
	{
		$emailTo = $email;
		$username = "";
		$conn = dbConnect();

		$stmt = $conn->prepare("SELECT b.user_name 
				                FROM member_info as a
								INNER JOIN login as b
								ON a.user_id = b.user_id
								WHERE a.email =? AND b.role = 'Member'");
				
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result && $result->num_rows > 0)
		{
			$row = $result->fetch_row();
			$username = $row[0];

			$txt = "Greetings from Newbs Unit'd Portfolio website\n\n
				    Recently a Request was made to retrieve the username for your account\n
					Below is the username associated with this email address\n\n
					Username = ".$username;
				
		}
		else
		{
			$txt = "Greetings from Newbs Unit'd Portfolio website\n\n
				    Recently a Request was made to retrieve the username for your account\n
					I am sorry to inform you that there is currently no username associated with this email address\n\n
					If you think this is in error, please contant a website administrator\n\n
					If you need an account, please fill out the registration form on the log in page\n
					but keep in mind member accounts are only for BAS-IS cohort 2017 students";
		}

		//close connection
		$result->close();
        $stmt->close();
        $conn->close(); 
	}
?>