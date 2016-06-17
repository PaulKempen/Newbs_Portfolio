<?php 
	require_once 'dbConnect.php';

	/**** Session ****/
    // name session
    session_name('basisLogin');

	/**** MainLine ****/



	/**** Functions ****/
	
	
	function checkLogin($name, $pw)
	{	
		$valid = FALSE;

		$conn = dbConnect();
		//prepare statement
        $stmt = $conn->prepare("SELECT password, user_id, user_name, role, change_pw FROM login WHERE (user_name = ?)");
        //bind parameters to statement
		$stmt->bind_param("s",$name);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows !== 0)
		{
			$row = $result->fetch_row();
			if(password_verify($pw,$row[0]))
			{
				//password matched - load session variables with user data
				$_SESSION['userId'] = $row[1];
				$_SESSION['userName'] = $row[2];
				$_SESSION['role'] = $row[3];
				$_SESSION['changePw'] = $row[4];
				$_SESSION['logedIn'] = TRUE;
				$valid = TRUE;	
			}
			else
			{
				//password invalid
				$_SESSION['logedIn'] = FALSE;
			}
			//free result set
			$result->close();
		}
		else
		{
			//user name not found
			$_SESSION['logedIn'] = FALSE;
		}
		
		//close connection
		$stmt->close();
		$conn->close();

		return $valid;
	}

	function logOut()
    {
        // remove all session variables
		session_unset();
		
		// destroy the session
		session_destroy();
        
        return "<span class='alert alert-success'>User logged out successfully</span>";
    }
    
    function getRole($userId)
    {
        $role = "";
        
        $conn = dbConnect();
		//prepare statement
        $stmt = $conn->prepare("SELECT role FROM login WHERE (user_id = ?)");
        //bind parameters to statement
		$stmt->bind_param("s",$userId);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows != 0)
		{
            $row = $result->fetch_row();
            
            $role = $row[0];
            
            $result->close();
        }
        else
        {
            //invalid user id
        }
        
        //close connection
         $stmt->close();
        $conn->close();
        
        return $role;
    }
	function getOptions()
	{
		$options = "<option value='0'>Select one</option>";
		$conn = dbConnect();
        //prepare statement
        $stmt = $conn->prepare("SELECT a.user_id, b.first, b.last 
                                FROM login as a
                                INNER JOIN member_info as b
                                ON a.user_id = b.user_id
                                WHERE a.role='Member'");

		//execute query
		if($stmt->execute())
        {
            //bind results to variable
            $result = $stmt->get_result();

            //get row from results
            while($row = $result->fetch_assoc())
			{
				$options .= "<option value='".$row['user_id']."'>".$row['first']." ".$row['last']."</option>";
			}

          
        }
        else
        {
            //boom
        }
        
        //close datasets to reuse them
        $stmt->close();
        $result->close();
        $conn->close();
		return $options;
	}

    function validatePassword($password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);

        return (!$uppercase || !$lowercase || !$number || strlen($password) < 8);
            
    }
?>