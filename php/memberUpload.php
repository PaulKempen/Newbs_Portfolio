<?php
/**** includes ****/
    
    require_once 'dbConnect.php';
	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

    // Start the session
   	session_start();
	/**** Constants ****/
	define("FILE_TYPE_PDF",123);
    define("FILE_TYPE_PIC",234);

	define("STATUS_OK",0);
    define("ERR_INVALID_FILETYPE",2);
    define("ERR_EMPTY_FILENAME",3);
    define("ERR_FILE_TOO_LARGE",4);
    define("ERR_FILE_EMPTY",5);
    define("ERR_ILLIGAL_CHARS",6);
	define("ERR_NOT_AN_IMAGE",7);

    /**** Variables ****/
    
    $reply = array();
 

    /**** Main line start ****/


	if(isset($_POST['mode']))
	{
		switch($_POST['mode'])
		{
			case 'profilePic': 
				$dir = "../member/uploads/profile_pics/";
				$table = "member_pics";
				$fileType = FILE_TYPE_PIC;
				$file = $_FILES['pic'];
                break;
			case 'resume':
				$dir = "../member/uploads/resumes/";
				$table = "resumes";
				$fileType = FILE_TYPE_PDF;
				$file = $_FILES['res'];
				break;
			case 'workPic':
				$dir = "../member/uploads/work_pics/";
				$table = "work_pics";
				$fileType = FILE_TYPE_PIC;
				$file = $_FILES['pic'];
				break;
			default:
		}

		uploadFile($file,$dir,$table,$fileType,$reply);
	}

	echo json_encode($reply);

	/**** Functions ****/

	//uploadFile
    //handles the uploading of a file and handles all scenarios appropriatly
    function uploadFile($file,$dir,$table,$fileType,&$reply)
    {
        $fileName = $file["name"];
        $extension = pathinfo($file["name"],PATHINFO_EXTENSION);
		
        $uploadName = $_POST['mode'] == 'workPic' ? $_POST["workId"] :$_SESSION['userId'];
       
        $targetFile = $dir.$uploadName;

        $status = validateFile($file,$fileType);

        //display error message or proceed if none
        switch($status)
        {
            case ERR_INVALID_FILETYPE:
                $reply['msg'] = "<span class='alert alert-warning'>Invalid file type (.".$extension.")</span>";
				break;
            case ERR_EMPTY_FILENAME:
                $reply['msg'] = "<span class='alert alert-warning'>Please select a file to upload.</span>";
				break;
            case ERR_FILE_TOO_LARGE:
                $reply['msg'] = "<span class='alert alert-warning'>File must be less than 500KB</span>";
				break;
            case ERR_FILE_EMPTY:
                $reply['msg'] = "<span class='alert alert-warning'>File must not be empty</span>";
				break;
            case ERR_ILLIGAL_CHARS:
                $reply['msg'] = "<span class='alert alert-warning'>File name contains illegal characters</span>";
				break;
			case ERR_NOT_AN_IMAGE:
				$reply['msg'] = "<span class='alert alert-warning'>File is not an image</span>";
				break;
            default:
                if(move_uploaded_file($file["tmp_name"], $targetFile)) 
                {
                    $reply['msg'] =  $_POST['mode'] == 'workPic' ? 
						insertWorkPic($fileName,$uploadName,$extension)
						:insertFileInfo($fileName,$uploadName,$table,$extension);
                } 
                else 
                {
                    $reply['msg'] = "<span class='alert alert-danger'>Sorry, there was an error uploading your file.</span>";
                }
        }
        
    }
    
    
	function insertWorkPic($fileName,$uploadName,$extension)
    {
        $msg = "";
        $conn = dbConnect();

        //prepare statement
        $stmt = $conn->prepare("SELECT * FROM work_pics WHERE work_id = ?");
        //bind parameters to statement
		$stmt->bind_param("s",$_POST["workId"]);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();
        $stmt->close();

        if($result && $result->num_rows > 0)
        {        
		   $stmt = $conn->prepare("UPDATE work_pics 
								   SET uploaded_name=?, saved_name =?, extension =? 
								   WHERE work_id = ?");
		   $stmt->bind_param("ssss", $uploadName, $fileName, $extension, $_POST["workId"]);
        }
        else
        {
            $stmt = $conn->prepare("INSERT INTO work_pics(work_id,uploaded_name,saved_name,extension) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $_POST["workId"], $uploadName, $fileName, $extension);
        }
             
        if ($stmt->execute()) 
        {
            $msg = "<span class='alert alert-success'>".$fileName." Uploaded Successfully</span>";
        }
        else 
        {
            $msg = "<span class='alert alert-danger'>Error uploading file</span>";
        }

		$stmt->close();
        $conn->close();
        
        return $msg;
    }

	function insertFileInfo($fileName,$uploadName,$table,$extension)
	{
		$msg = "";
        $conn = dbConnect();
        
        //prepare statement
        $stmt = $conn->prepare("SELECT * FROM ".$table." WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $_SESSION['userId']);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();
        $stmt->close();

        if($result && $result->num_rows > 0)
        { 
            $stmt = $conn->prepare("UPDATE ".$table." SET uploaded_name=?, saved_name =?, extension =? WHERE user_id =?");
		    $stmt->bind_param("ssss", $uploadName, $fileName, $extension, $_SESSION['userId']);
        }
        else
        {
            $stmt = $conn->prepare("INSERT INTO ".$table."(user_id,uploaded_name,saved_name,extension) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $_SESSION['userId'], $uploadName, $fileName, $extension);
		}
    

        
        if ($stmt->execute()) 
        {
            $msg = "<span class='alert alert-success'>".$fileName." Uploaded Successfully</span>";
        }
        else 
        {
            $msg = "<span class='alert alert-danger'>Error uploading file</span>";
        }

        $conn->close();
        
        return $msg;
	}

    function validateFile($file,$type)
    {
        // get extension of file
        $fileExtension = pathinfo($file["name"],PATHINFO_EXTENSION);
        $baseFile = basename($file["name"]);

        if($type == FILE_TYPE_PDF)
        {
            if($fileExtension != "pdf") return ERR_INVALID_FILETYPE;
        }
        if($type == FILE_TYPE_PIC)
        {
            if(strcasecmp($fileExtension, "jpg") != 0 &&
               strcasecmp($fileExtension, "png") != 0 &&
               strcasecmp($fileExtension, "gif") != 0 &&
               strcasecmp($fileExtension, "jpeg") != 0) 
			   return ERR_INVALID_FILETYPE;

			if(!getimagesize($file["tmp_name"]))
				return ERR_NOT_AN_IMAGE;
        }

        //check if file name is empty
        if(trim($baseFile) == "") return ERR_EMPTY_FILENAME;

        //check if file is too large
        if($file["size"] > 500000) return ERR_FILE_TOO_LARGE;

        //check if file is empty
        if($file["size"] == 0) return ERR_FILE_EMPTY;

        //check if file name contains illegal characters
        if(htmlentities(iconv("utf-8","utf-8//IGNORE",$baseFile)
                        ,ENT_QUOTES,"utf-8") != $baseFile) return ERR_ILLIGAL_CHARS;
        
        return 0;
    }


    
?>