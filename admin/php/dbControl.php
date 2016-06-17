
<?php
	/**** includes ****/
    require_once './../../php/dbConnect.php';
    require_once './../../php/loginUtils.php';
    include_once './../../php/GUID.php';

	/**** Session ****/
    session_name('basisLogin');
    session_start();

	/**** Variables ****/
    $msg = "";
	$auth = "";

	/**** Mainline ****/
    if(isset($_POST['auth'])) $auth = $_POST['auth'];
	
    if(authorizeChange($auth))
    {
        if(isset($_POST['mode']))
        {
            switch($_POST['mode'])
            {
                case 'add': $msg = addAccount();
                    break;
                case 'rename': $msg = changeUsername();
                    break;
                case 'reset': $msg = resetPassword();
                    break;
                case 'delete': $msg = deleteAccount();
                    break;
                case 'faqAdd': $msg = addFaq();
                    break;
                case 'faqQ': $msg = modifyQuestion();
                    break;
                case 'faqA': $msg = modifyAnswer();
                    break;
                case 'faqDel': $msg = deleteFaq();
                    break;
                case 'copy': $msg = updateSiteText('c');
                    break;
                case 'foot': $msg = updateSiteText('f');
                    break;
                case 'about': $msg = updateSiteText('a');
                    break;
                case 'head': $msg = updateSiteText('g');
                    break;
                case 'title': $msg = updateSiteText('t');
                    break;
                case 'contact': $msg = updateSiteText('i');
                    break;
                case 'addFocus': $msg = addFocus();
                    break;
                case 'delFocus': $msg = deleteFocus();
                    break;
                default:
                    break;
            }
        }
    }
    else
    {
        $msg = "<span class='alert alert-warning'>Authorization Failed, please try again</span>";
    }
    
    echo $msg;
    
    /**** Functions ****/
    function authorizeChange($auth)
    {
        $conn = dbConnect();
		
		//prepare statement
        $stmt = $conn->prepare("SELECT password FROM login WHERE (user_id =?)");
        //bind parameters to statement
		$stmt->bind_param("s", $_SESSION['userId']);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();
        
        
        //close connection
        $stmt->close();
		$conn->close();
        
		if($result && $result->num_rows !== 0)
		{
			$row = $result->fetch_row();
            $result->close();
            
            //check if password and salted hash match
            return password_verify($auth,$row[0]);
		}	
    }
    
    
    function addAccount()
    {
        $name = trim($_POST["name"]);
        $role = trim($_POST["role"]);
        $pw = trim($_POST["password"]);
        $newUser = GUID();
        $conn = dbConnect();

        //prepare statement
        $stmt = $conn->prepare("INSERT INTO login VALUES (?,?,?,?,1)");
        //bind parameters to statement
		$stmt->bind_param("ssss", $newUser, $name, password_hash($pw, PASSWORD_DEFAULT),$role);
		
        //execute query
        if ($stmt->execute()) 
        {
            $msg = "<span class='alert alert-success'>New record created successfully.</span>";
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO member_info (user_id) VALUES (?)");
		    $stmt->bind_param("s", $newUser);
            $stmt->execute();
        } 
        else 
        {
            $msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
        }
        
        $stmt->close();
        $conn->close();
        return $msg;
    }
    
    function changeUsername()
    {
        $name = trim($_POST["name"]);
        $userId = trim($_POST["user_id"]);
        
        $conn = dbConnect();
		
		//prepare statement
        $stmt = $conn->prepare("UPDATE login SET user_name =? WHERE user_id=?");
        //bind parameters to statement
		$stmt->bind_param("ss", $name, $userId);
		
        //execute query
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>User name Updated Successfully</span";
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
		
		//close connection
        $stmt->close();
		$conn->close();
        
        return $msg;
    }

    function resetPassword()
    {
        $pw = trim($_POST["password"]);
        $userId = trim($_POST["user_id"]);
        $change_pw = $_POST['force'];
        if(validatePassword($pw))
        {
            $msg = "<span class='alert alert-warning'>New password is invalid</span>";
        }
        else
        {
            $conn = dbConnect();

            //prepare statement
            $stmt = $conn->prepare("UPDATE login SET password =?, change_pw=? WHERE user_id =?");
            //bind parameters to statement
            $stmt->bind_param("sis", password_hash($pw, PASSWORD_DEFAULT), $change_pw, $userId);

            //execute query
            if($stmt->execute())
            {
                $msg = "<span class='alert alert-success'>Member's password Updated Successfully</span>";
            }
            else
            {
                $msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
            }

            //close connection
            $stmt->close();
            $conn->close();
        }
        return $msg;
    }
    
    function deleteAccount()
    {
        $userId = trim($_POST["user_id"]);
		$conn = dbConnect();
	
		//delete member account that matches user id
        $stmt = $conn->prepare("DELETE FROM login WHERE user_id = ?");
        //bind parameters to statement
		$stmt->bind_param("s", $userId);
		
        //execute query		
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>Member Deleted Successfully</span>";
			if(file_exists("../../member/uploads/profile_pics/".$userId))
				unlink("../../member/uploads/profile_pics/".$userId);
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
        return $msg;
    }

    function addFaq()
    {
        
        $question = trim($_POST["question"]);
        
        $answer = trim($_POST["answer"]);

        $conn = dbConnect();

        //prepare statement
        $stmt = $conn->prepare("INSERT INTO faq VALUES (?,?,?)");
        //bind parameters to statement
		$stmt->bind_param("sss",GUID(), $question, $answer);
		
        //execute query
        if ($stmt->execute()) 
        {
            $msg = "<span class='alert alert-success'>New FAQ created successfully</span>";
        } 
        else 
        {
            $msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
        }
        $stmt->close();
        $conn->close();
        return $msg;
    }
                  
    function modifyQuestion()
    {
        $question = trim($_POST["question"]);
        $faqId = trim($_POST["faq_id"]);
        
        $conn = dbConnect();
		
		//prepare statement
        $stmt = $conn->prepare("UPDATE faq SET question =? WHERE faq_id=?");
        //bind parameters to statement
		$stmt->bind_param("ss", $question, $faqId);
		
        //execute query   
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>FAQ Question Updated Successfully</span>";
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
		
		//close connection
        $stmt->close();
		$conn->close();
        
        return $msg;
    }
                   
    function modifyAnswer()
    {
        $answer = trim($_POST["answer"]);
        $faqId = trim($_POST["faq_id"]);
        $conn = dbConnect();
		
		//prepare statement
        $stmt = $conn->prepare("UPDATE faq SET answer =? WHERE faq_id=?");
        //bind parameters to statement
		$stmt->bind_param("ss", $answer, $faqId);
		
        //execute query  
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>FAQ Answer Updated Successfully</span>";
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
		
		//close connection
        $stmt->close();
		$conn->close();
        
        return $msg;            
    }
                    
    function deleteFaq()
    {
        if(isset($_POST["faq_id"])) 
            $faqId = mysql_real_escape_string(trim($_POST["faq_id"]));
	
		$conn = dbConnect();
	
		//delete FAQ Question that matches faq id
        $stmt = $conn->prepare("DELETE FROM faq WHERE faq_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $faqId);
        
        //execute query  
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>FAQ Deleted Successfully</span>";
			
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
        return $msg;
    }

    function updateSiteText($mode)
    {
        $msg = "";
        $data = $_POST['data'];
        $sql = "";
        $col = "";
        switch($mode)
        {
            case 'c': 
                $sql = "UPDATE site_text SET copyright =? WHERE site_id='1'";
                $col = "Copyright Text";
                break;
            case 'f': 
                $sql = "UPDATE site_text SET footer =? WHERE site_id='1'";
                $col = "Footer Text";
                break;
            case 'a': 
                $sql = "UPDATE site_text SET about =? WHERE site_id='1'";
                $col = "About Text";
                break;
            case 'g': 
                $sql = "UPDATE site_text SET group_name =? WHERE site_id='1'";
                $col = "Group Name";
                break;
            case 't': 
                $sql = "UPDATE site_text SET title =? WHERE site_id='1'";
                $col = "HTML Title Text";
                break;
            case 'i': 
                $sql = "UPDATE site_text SET contact =? WHERE site_id='1'";
                $col = "Contact Information";
                break;
            default:
                break;
        }
        
        $conn = dbConnect();
	
		
        $stmt = $conn->prepare($sql);
        //bind parameters to statement
		$stmt->bind_param("s", $data);
        
        //execute query  
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>".$col." Updated Successfully</span>";
			
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
        return $msg;
        
        return $msg;
    }
    
    function addFocus()
    {   
        $focus = trim($_POST["data"]);

        $conn = dbConnect();

        //prepare statement
        $stmt = $conn->prepare("INSERT INTO focus_areas VALUES (?)");
        //bind parameters to statement
		$stmt->bind_param("s", $focus);
		
        //execute query
        if ($stmt->execute()) 
        {
            $msg = "<span class='alert alert-success'>Focus area added successfully</span>";
        } 
        else 
        {
            $msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
        }
        $stmt->close();
        $conn->close();
        return $msg;
    }
    
    function deleteFocus()
    {
        
        $focus = mysql_real_escape_string(trim($_POST["data"]));
	
		$conn = dbConnect();
	
		//delete FAQ Question that matches faq id
        $stmt = $conn->prepare("DELETE FROM focus_areas WHERE focus =?");
        //bind parameters to statement
		$stmt->bind_param("s", $focus);
        
        //execute query  
		if($stmt->execute())
		{
			$msg = "<span class='alert alert-success'>Focus Entry Deleted Successfully</span>";
			
		}
		else
		{
			$msg = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
        return $msg;
    }
?>