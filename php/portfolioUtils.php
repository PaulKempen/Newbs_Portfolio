<?php 
	/**** includes ****/
    require_once 'dbConnect.php';
	require_once 'GUID.php';
	require_once 'searchUtils.php';
	
	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

    // Start the session
   	session_start();

	/**** Variables ****/
    $reply = array('msg' => "");

	$memberId = "";
		
	/**** Mainline ****/

	

	if(!isset($_SESSION['logedIn']) || $_SESSION['logedIn'] == FALSE)
    {
		$reply['content'] = getLoginRedirect();
		$reply['loggedin'] = false;
	}
	else
	{
		$reply['loggedin'] = true;

		if($_SESSION['role'] != 'Admin')
		{
			$memberId = $_SESSION['userId'];
		}
		else
		{
			$memberId = $_POST['adminSelect'];
		}


		switch($_POST['mode'])
		{	
			case 'about': updateAbout($memberId,$reply);
				break;
			case 'add': addWorkItem($memberId, $reply);
				break;
			case 'delete': deleteWorkItem($_POST['workId'], $reply);
				break;
            case 'delPic': deleteWorkPic($_POST['workId'], $reply);
                break;
			case 'title':
			case 'url':
			case 'desc': updateWorkItem($memberId, $_POST['workId'], $_POST['mode'], $reply);
				break;
			case 'edu':
			case 'emp':
			case 'awa':
			case 'int':
			case 'ski':
			case 'cer': updateResume($memberId, $_POST['mode'], $reply);
				break;
			default:
				$reply['msg'] = "<span class='alert alert-danger'>Invalid mode detected</span>";
		}
	
	}


	echo json_encode($reply);

	/**** Functions ****/
	function updateAbout($memberId, &$reply)
	{
		$conn = dbConnect();

		//prepare statement
        $stmt = $conn->prepare("UPDATE portfolio_data SET about_me = ? WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("ss", trim($_POST['text'][0]), $memberId);
		
		//execute query
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>'About Me' Updated Successfully</span>";
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'> Error updating data, if problem persists please contact an admin </span>";
		}
		
		//close connection
		$stmt->close();
		$conn->close();
		
		addEntry("about", $_POST['text'][0], $memberId);
	}

	function updateWorkItem($memberId, $workId, $mode, &$reply)
	{
		$sql = "";
		$msg = "";
		$cat = "";
		switch($mode)
		{
			case 'title':
				$sql = "UPDATE past_work SET name = ? WHERE work_id =?";
				$msg =	"Work Item Title Updated Successfully";
				$cat = "work-title";
				break;
			case 'url':
				$sql = "UPDATE past_work SET link = ? WHERE work_id =?";
				$msg =	"Work Item URL Updated Successfully";
				break;
			case 'desc':
				$sql = "UPDATE past_work SET description = ? WHERE work_id =?";
				$msg =	"Work Item Description Updated Successfully";
				$cat = "work-description";
				break;
			default:
				break;
		}
		$conn = dbConnect();

		//prepare statement
        $stmt = $conn->prepare($sql);
        //bind parameters to statement
		$stmt->bind_param("ss", trim($_POST['text'][0]), $workId);
		
		//execute query
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>".$msg."</span>";
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'> Error updating data, if problem persists please contact an admin </span>";
		}
		
		//close connection
		$stmt->close();
		$conn->close();
		
		if($cat != "")
			addEntry($cat, $_POST['text'][0], $memberId, $workId);
	}


	function updateResume($memberId, $mode, &$reply)
	{
		$sql = "";
		$msg =	"";
		$data = arrayToResumeString($_POST['text']);
        $cat = "resume-";
		switch($mode)
		{
			case 'edu':
				$sql = "UPDATE resumes SET education =? WHERE user_id =?";
				$msg =	"Resume Education Updated Successfully";
				$cat .= "education";
				break;
			case 'emp':
				$sql = "UPDATE resumes SET employment =? WHERE user_id =?";
				$msg =	"Resume Employment Updated Successfully";
				$cat .= "employment";
				break;
			case 'awa':
				$sql = "UPDATE resumes SET awards =? WHERE user_id =?";
				$msg =	"Resume Awards Updated Successfully";
				$cat .= "awards";
				break;
			case 'int':
				$sql = "UPDATE resumes SET interests =? WHERE user_id =?";
				$msg =	"Resume Interests Updated Successfully";
				$cat .= "interests";
				break;
			case 'ski':
				$sql = "UPDATE resumes SET skills =? WHERE user_id =?";
				$msg =	"Resume Skills Updated Successfully";
				$cat .= "skills";
				break;
			case 'cer':
				$sql = "UPDATE resumes SET certs =? WHERE user_id =?";
				$msg =	"Resume Certifications Updated Successfully";
				$cat .= "certifications";
				break;
			default:
				break;
		}

		$conn = dbConnect();

		//prepare statement
        $stmt = $conn->prepare($sql);
        //bind parameters to statement
		$stmt->bind_param("ss", $data, $memberId);
		
		//execute query
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>".$msg."</span>";
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'> Error updating data, if problem persists please contact an admin </span>";
		}
		
		//close connection
		$stmt->close();
		$conn->close();
		
		addEntry($cat, $data, $memberId);
	}

	function arrayToResumeString($array)
	{
		$returnString = "";
		$count = count($array);
		$i = 0;

		foreach($array as $index)
		{
			//disregard empty strings
			if(trim($index) != "")
			{
				$returnString .= trim($index);
				//add pipe between each entry and not at the end
				if($i < $count - 1)
					 $returnString .= "|";			
			}
			$i++;
		}

		return $returnString;
	}

	 function addWorkItem($memberId, &$reply)
	 {
		$workId = GUID();
		$conn = dbConnect();
		
        //prepare statement
        $stmt = $conn->prepare("INSERT INTO past_work(work_id, user_id) VALUES (?,?)");
        //bind parameters to statement
		$stmt->bind_param("ss",$workId, $memberId);
		
        //execute query
        if ($stmt->execute()) 
        {
            $reply['msg'] = "<span class='alert alert-success'>New Work Item created successfully</span>";
			$reply['workId'] = $workId;
			$stmt->close();
		
			$stmt = $conn->prepare("INSERT INTO work_pics(work_id) VALUES (?)");
			$stmt->bind_param("s",$workId);
			$stmt->execute();

        } 
        else 
        {
            $reply['msg'] = "<span class='alert alert-danger'>Error creating work item</span>";
        }

        $stmt->close();
        $conn->close();
	 }

	 function deleteWorkItem($workId, &$reply)
	 {
		$conn = dbConnect();
	
		//delete member account that matches user id
        $stmt = $conn->prepare("DELETE FROM past_work WHERE work_id = ?");
        //bind parameters to statement
		$stmt->bind_param("s", $workId);
		
        //execute query		
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>Work Item Deleted Successfully</span>";
			
			//if there is an image associated with this work item, delete it from the server
			if(file_exists("../member/uploads/work_pics/".$workId))
				unlink("../member/uploads/work_pics/".$workId);
			
			//remove references to this work item in the site index	
			deleteWorkEntries($workId);
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'>Error deleting work item</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
		
		
	 }

     function deleteWorkPic($workId, &$reply)
	 {
		$conn = dbConnect();
	
		//delete member account that matches user id
        $stmt = $conn->prepare("UPDATE work_pics 
                                SET saved_name=NULL, uploaded_name=NULL, extension=NULL 
                                WHERE work_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $workId);
		
        //execute query		
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>Work Item Pic Removed Successfully</span>";
			
			//if there is an image associated with this work item, delete it from the server
			if(file_exists("../member/uploads/work_pics/".$workId))
				unlink("../member/uploads/work_pics/".$workId);
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'>Error Removeing work item Pic</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
	 }
?>