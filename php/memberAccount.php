<?php
    /**** includes ****/
    
    require_once 'dbConnect.php';
	require_once 'loginUtils.php';
	require_once 'searchUtils.php';
	
	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks*days*hours*mins*secs)
	session_set_cookie_params(2*60*60);

    // Start the session
   	session_start();

    /**** Variables ****/
    
    $reply = array();

	$memberId = "";
    /**** Main line start ****/

	$role = getRole($_SESSION['userId']);

	if($role == 'Admin')
	{
		if(isset($_POST["adminSelect"]))
		{
			$memberId = $_POST["adminSelect"];
		}
		else
		{
			$memberId = $_SESSION['userId'];
		} 
	}
	else
	{
		$memberId = $_SESSION['userId'];
	}

	if(isset($_POST['mode']))
	{
		switch($_POST['mode'])
		{
			case 'first': uploadFirstInfo($reply);
                break;
			case 'pInfo': displayPersonalInfo($reply);
				break;
			case 'sInfo': displaySortInfo($memberId, $reply);
				break;
			case 'resPw': displayResetPassword($reply);
				break;
			case 'pic': displayPicSelect($reply);
				break;
			case 'focus': displayFocusSelect($reply);
				break;
			case 'exp': displayExperienceInfo($reply);
				break;
			case 'updatePInfo': updatePersonalInfo($reply, $role);
				break;
			case 'resetPw': updatePassword($reply);
				break;
			case 'blurb': updateBlurb($memberId,$reply);
				break;
			case 'updateFocus': updateFocus($memberId,$reply);
				break;
            case 'delPic': deleteProfilePic($memberId, $reply);
                break;
			default:
		}	
 
	}

	echo json_encode($reply);

	/**** Functions ****/
	function updateFocus($memberId, &$reply)
	{
		$conn = dbConnect();

		//prepare statement
        $stmt = $conn->prepare("UPDATE portfolio_data SET focus = ? WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("ss", trim($_POST['data']), $memberId);
		
		//execute query
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>Focus Updated Successfully</span>";
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'> Error updating data, if problem persists please contact an admin </span>";
		}
		
		//close connection
		$stmt->close();
		$conn->close();

		displaySortInfo($memberId, $reply);
		
		addEntry("focus", trim($_POST['data']), $memberId);
	}

	function updateBlurb($memberId, &$reply)
	{
		$conn = dbConnect();

		//prepare statement
        $stmt = $conn->prepare("UPDATE portfolio_data SET short_desc = ? WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("ss", trim($_POST['data']), $memberId);
		
		//execute query
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>Experience Updated Successfully</span>";
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'> Error updating data, if problem persists please contact an admin </span>";
		}
		
		//close connection
		$stmt->close();
		$conn->close();

		displaySortInfo($memberId, $reply);
		
		addEntry("blurb", trim($_POST['data']), $memberId);
	}

	function updatePersonalInfo(&$reply, $role)
	{
		$conn = dbConnect();
		$col = $conn->real_escape_string($_POST['col']);

		//prepare statement
        $stmt = $conn->prepare("UPDATE member_info SET ".$col." = ? WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("ss", $_POST['data'], $_SESSION['userId']);
		
		//execute query
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>Data Updated Successfully</span>";
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'> Error updating data, if problem persists please contact an admin </span>";
		}
		
		//close connection
		$stmt->close();
		$conn->close();
		
		if($col == "first" || $col == "last" && $role != "Admin")
		{
			$name = array();
			$name = getMemberName($_SESSION['userId']);
			
			addEntry("name", $name['first']." ".$name['last'], $_SESSION['userId']);
		}
	}

	function updatePassword(&$reply)
	{
        if(validatePassword($_POST['new'])) 
        {
            $reply['msg'] = "<span class='alert alert-danger'>New password is Invalid</span>";
            $reply['good'] = false;
        }
        else
        {
            $conn = dbConnect();
            
            if(isset($_POST['old']))
            {
                if(!checkLogin($_SESSION['userName'], $_POST['old']))
                {
                    $reply['msg'] = "<span class='alert alert-danger'>Old Password Invalid, Please try again</span>";
                    $reply['good'] = false;

                    // old password invalid, bail out
                    return;
                }
            }

            $stmt = $conn->prepare("UPDATE login SET password =? , change_pw = 0 WHERE user_id =?");
            $stmt->bind_param("ss", password_hash($_POST['new'], PASSWORD_DEFAULT), $_SESSION['userId']);

            //add in password strength testing here

            if($stmt->execute())
            {
                if($stmt->affected_rows > 0)
                {	
                    $reply['msg'] = "<span class='alert alert-success'>Password Updated Successfully</span>";
                    $reply['good'] = true;
                    $_SESSION['changePw'] = FALSE;
                }
                else
                {
                    //something bad happened??
                    $reply['msg'] = "<span class='alert alert-success'>Error updating password, if problem persists please contact an administrator</span>";
                    $reply['good'] = false;
                }
            }
            else
            {
                $reply['msg'] = "<span class='alert alert-danger'> Error: " . $sql . " " . $conn->error ."</span>";
                $reply['good'] = false;
            }

            //close connection
            $stmt->close();
            $conn->close();
        }
	}

	function getSortData($memberId)
	{
		$conn = dbConnect();
        $row = array();
 
        //prepare statement
        $stmt = $conn->prepare("SELECT short_desc, focus FROM portfolio_data WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $memberId);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows !== 0)
		{
            $row = $result->fetch_assoc();
            
			$row['good'] = TRUE;

			$result->close();
        }
        else
		{
			$row['good'] = FALSE;
			$row['focus'] = "";
			$row['short_desc'] = "";
		}

		$stmt->close();
		$conn->close();

        return $row;
	}

	function getInfo()
	{
		$conn = dbConnect();
        $row = array();
        
		//prepare statement
        $stmt = $conn->prepare("SELECT * FROM member_info WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $_SESSION['userId']);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows !== 0)
		{
            $row = $result->fetch_assoc();
            
			$row['good'] = TRUE;

			$result->close();
        }
        else
		{
			$row['good'] = FALSE;
		}

		$stmt->close();
		$conn->close();

        return $row;
	}

	function getMemberName($memberId)
	{
		$conn = dbConnect();
        $row = array();
        
		//prepare statement
        $stmt = $conn->prepare("SELECT first, last FROM member_info WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $memberId);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows !== 0)
		{
            $row = $result->fetch_assoc();
            
			$row['good'] = TRUE;

			$result->close();
        }
        else
		{
			$row['good'] = FALSE;
			$row['first'] = "";
			$row['last'] = "";
		}

		$stmt->close();
		$conn->close();

        return $row;
	}
	
	function getProfilePic($memberId)
	{
		$conn = dbConnect();
        $row = array();
        
        $stmt = $conn->prepare("SELECT uploaded_name FROM member_pics WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $memberId);
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows !== 0)
		{
            $row = $result->fetch_assoc();
            
            if($row['uploaded_name'] != NULL)
            {
                $row['good'] = TRUE;
            }
            else
            {
                $row['good'] = FALSE;
            }

			$result->close();
        }
        else
		{
			$row['good'] = FALSE;
		}

		$conn->close();

		$stmt->close();
        return $row;
	}

	function uploadFirstInfo(&$reply)
    {
		$conn = dbConnect();

        $first = trim($_POST["first"]);
        $mid = trim($_POST["mid"]);
        $last = trim($_POST["last"]);
        $phone = trim($_POST["phone"]);
        $email = trim($_POST["email"]);
        
        //prepare statement
        $stmt = $conn->prepare("UPDATE member_info
                                SET first=?, mi=?, last=?, phone=?, email=?
                                WHERE user_id=?");
        //bind parameters to statement
		$stmt->bind_param("ssssss",$first,$mid,$last,$phone,$email, $_SESSION['userId']);
		
		//execute query
        if($stmt->execute()) 
        {
			//add new user id to portfolio_data and resumes and member_pics if they are not an admin
			if($_SESSION['role'] == 'Member')
			{
				$stmt->close();
				$stmt = $conn->prepare("INSERT INTO portfolio_data(user_id) VALUES (?)");
				$stmt->bind_param("s", $_SESSION['userId']);
				$stmt->execute();
				$stmt->close();
				$stmt = $conn->prepare("INSERT INTO resumes(user_id) VALUES (?)");
				$stmt->bind_param("s", $_SESSION['userId']);
				$stmt->execute();
				$stmt->close();
				$stmt = $conn->prepare("INSERT INTO member_pics(user_id) VALUES (?)");
				$stmt->bind_param("s", $_SESSION['userId']);
				$stmt->execute();
			}

            $reply['msg'] = "<span class='alert alert-success'>Account updated successfully.</span>";
            $reply['success'] = TRUE;
        } 
        else 
        {
            $reply['msg'] = "<span class='alert alert-danger'>Database Error, please contact an administrator</span>";
            $reply['success'] = FALSE;
        }

		$stmt->close();
        $conn->close();
        
		addEntry("name", $first." ".$last, $memberId);
    }

	function displayPersonalInfo(&$reply)
	{
		$info = getInfo();
        $reply['account'] = $info;
		$reply['account'] = "<h3>Information for ".$_SESSION['userName']."</h3>
                    <form id='infoForm'>

                        <div class='input-group'>
                            <label for='txtFirst' class='input-group-addon'>First Name:</label>
                            <input type='text' class='form-control' disabled='disabled' value='".$info['first']."' id='txtFirst' />
                            <span class='input-group-btn'>
                                <button class='btn btn-default' id='btnFirst'>Edit</button>
                            </span>
                        </div>

                        <div class='input-group'>
                            <label for='txtFirst' class='input-group-addon'>Middle Initial:</label>
                            <input type='text' class='form-control' disabled='disabled' value='".$info['mi']."' id='txtMi' maxlength='1'/>
                            <span class='input-group-btn'>
                                <button class='btn btn-default' id='btnMi'>Edit</button>
                            </span>
                        </div>

                        <div class='input-group'>
                            <label for='txtFirst' class='input-group-addon'>Last Name:</label>
                            <input type='text' class='form-control' disabled='disabled' value='".$info['last']."' id='txtLast' />
                            <span class='input-group-btn'>
                                <button class='btn btn-default' id='btnLast'>Edit</button>
                            </span>
                        </div>

                        <div class='input-group'>
                            <label for='txtFirst' class='input-group-addon'>Phone Number:</label>
                            <input type='text' class='form-control' disabled='disabled' value='".$info['phone']."' id='txtPhone' />
                            <span class='input-group-btn'>
                                <button class='btn btn-default' id='btnPhone'>Edit</button>
                            </span>
                        </div>

                        <div class='input-group'>
                            <label for='txtFirst' class='input-group-addon'>Email Address:</label>
                            <input type='text' class='form-control' disabled='disabled' value='".$info['email']."' id='txtEmail' />
                            <span class='input-group-btn'>
                                <button class='btn btn-default' id='btnEmail'>Edit</button>
                            </span>
                        </div>
                    </form>";
	
	}

	function displaySortInfo($memberId, &$reply)
	{
		$info = getMemberName($memberId);
		$data = getSortData($memberId);
		$pic = getProfilePic($memberId);
		$array = explode("|",$data['focus']);
		$focus = "";
		$img = "";
		$size = count($array);
		$select = (getRole($_SESSION['userId']) == 'Admin'? "<div class='form-inline text-center'>
													<select class='form-control' id='adminSelect'>
													".getOptions()."</select> 
													<button class='btn btn-primary text-center' id='btnAdminSelect'>
														Select
													</button></div>":"");
		foreach($array as $value)
		{
			
			$focus .= $value;

			if(--$size >= 1)
				$focus .= "<br/>";
				
		}

		if($pic['good'])
		{
			$img = "<img class='img-responsive body-links' src='uploads/profile_pics/"
				.$pic['uploaded_name']."?x=".rand() 
				."' alt='Proflie picture for "
				.$info['first']." "
				.$info['last']."' width='150' height='150'>";
		}
		else
		{
			$img = "<img class='img-responsive body-links' 
					src='uploads/profile_pics/placeholder-member.png' 
					alt='Placeholder Profile Pic' width='150' height='150'>";
		}

		$reply['account'] = "
                    <h3>Sort page Information</h3>
                    
                    <div class='row'>
                        <div class='col-md-3'>
							".$select."
                            <div class='well well-sm text-left' id=''>
                                <button class='btn btn-link' id='btnPic'>".$img."</button>
                                <p>
                                    <b>Name:</b> <span id='spanName'>".$info['first']." ".$info['last']."</span><br/>
                                    <button class='btn btn-link noPad body-links' id='btnFocus'>Focus:</button><span id='spanFocus'>".$focus."</span><br/>
                                    <button class='btn btn-link noPad body-links' id='btnExp'>Experience:</button><span id='spanExp'>".$data['short_desc']."</span>
                                </p>
                            </div>

                        </div>
                        <div class='col-md-8 col-md-offset-1' id='sort'>
                            <h4>click on an item you want to edit</h4><br/><br/>
                            <dl class='dl-horizontal'>
                                <dt>Profile Picture</dt>
                                <dd>Click on the picture to change your profile picture</dd><br/>
                                <dt>Focus</dt>
                                <dd>Lets you choose your focus which determines the groups you appear in on the home page</dd><br/>
                                <dt>Experience</dt>
                                <dd>Short \"blurb\" that you want to share to entice in an employer</dd>
                            </dl>
                        </div>
                    </div>
                ";

	}

	function displayResetPassword(&$reply)
	{
		$reply['account'] = "<h3>Reset your Password</h3>
                    <form class='container-fluid line-height-50' id='accountForm'>
                        <div class='input-group row'>
                            <div class='col-sm-5'>
                                <label for='txtNewPw'>Old Password</label>
                            </div>

                            <div class='col-sm-7'>
                                <input class='input-sm' type='password' required='required' id='txtOldPw' />
                            </div>
                        </div>
                        <div class='input-group row'>
                            <div class='col-sm-5'>
                                <label class='' for='txtNewPw'>New Password</label>
                            </div>
                            
                            <div class='col-sm-7'>
                                <input class='input-sm' type='password' required='required' id='txtNewPw' />
                            </div>
                        </div>
                        <div class='input-group row'>
                            <div class='col-sm-5'>
                                <label class='' for='txtNewPw2'>Re-enter Password</label>
                                </div>
                                <div class='col-sm-7'>
                                    <input class='input-sm' type='password' required='required' id='txtNewPw2' />
                                </div>
                            </div>
                        <button class='btn btn-secondary' id='btnPw'>Submit</button>
                        <div>Passwords must contain:
                            <UL class='list-group pw-regex'>
                                <li class='list-group-item'>1 Upper case Character</li>
                                <li class='list-group-item'>1 Lower case Character</li>    
                                <li class='list-group-item'>1 Number</li>
                                <li class='list-group-item'>8 characters minimum</li>    
                            </UL>
                        </div>
                    </form>";


	}

	function displayPicSelect(&$reply)
	{
		$picForm = "";
        
        if($_SESSION['role'] == 'Admin')
        {
            $picForm ="<div class='row' id='picForm'>
                            <div class='col-sm-12 text-center' id='work-buffer'>
                                <p>Incase of Inappropriate Profile pic:</p>
                                <label class='checkbox-inline'><input type='checkbox' id='delAuth'>check to authorize</label>
                                <button class='btn btn-default' id='btnDelPic'>Delete Pic</button>
                                <p>*note* You will need to refresh this section to see the change</p>
                            </div>
                       </div>";
        }
        else
        {
            $picForm = " <h5 class='text-black'>Change profile picture</h5>
                            <form class='no-float' id='picForm' method='post' enctype='multipart/form-data'>
                                <label class='sr-only' for='filePic'>File Select</label>
                                <div class='input-group'>     
                                    <span class='btn btn-default btn-file input-group-addon'>
                                        Browse <input id='filePic' type='file' />
                                    </span>
                                    <label class='form-control' id='fileLabel'></label>
                                    <span class='input-group-btn'>
                                        <button class='btn btn-default' id='btnUpPic'>Submit</button>
                                    </span>
                                </div>
								<div>Max size: 500kb, jpg/jpeg/png/gif</div>
                            </form>";
        }
	
        $reply['sort'] = $picForm;
	}
				
	function displayFocusSelect(&$reply)
	{
		$reply['sort'] = " <h5>Choose which focus area(s) represent your interests</h5>
						   <p>Select up to 5</p>
                            <form class='no-float text-center' id='focusForm'>
                                ".getFocusGroups()."
                                <button class='btn btn-default' id='btnUpdateFocus'>Update</button>
                            </form>";
	
	}
				
	function displayExperienceInfo(&$reply)
	{
		$reply['sort'] = "<h5 class='text-black'>Give a short blurb regarding your experiance or achievements</h5>
                            <h6>(limit 75 characters)</h6>
                            <form class='no-float'>
                                <div>
                                    <textarea id='txtBlurb' maxlength='75' cols='30' rows='3'></textarea>
                                </div>
                                <button class='btn btn-default' id='btnBlurb'>Update</button>
                            </form>";
		
	}

    function deleteProfilePic($memberId, &$reply)
    {
		$conn = dbConnect();
	
		//delete member account that matches user id
        $stmt = $conn->prepare("UPDATE member_pics 
                                SET saved_name=NULL, uploaded_name=NULL, extension=NULL 
                                WHERE user_id =?");
        //bind parameters to statement
		$stmt->bind_param("s", $memberId);
		
        //execute query		
		if($stmt->execute())
		{
			$reply['msg'] = "<span class='alert alert-success'>Profile Pic Removed Successfully</span>";
			
			//if there is an image associated with this work item, delete it from the server
			if(file_exists("../member/uploads/profile_pics/".$memberId))
				unlink("../member/uploads/profile_pics/".$memberId);
		}
		else
		{
			$reply['msg'] = "<span class='alert alert-danger'>Error Removeing work item Pic</span>";
		}
					
		//close connection
        $stmt->close();
		$conn->close();
    }		
	
	function getFocusGroups()
	{
		$choices = "";
		$conn = dbConnect();
    
		//prepare statement
		$stmt = $conn->prepare("SELECT focus FROM focus_areas");
		
		//execute query
		if($stmt->execute())
		{
			$result = $stmt->get_result();     
			while($row = $result->fetch_row())
			{
				$choices .="<div class='checkbox'>
                                    <label>
                                        <input class='focus-checkbox' type='checkbox' value='".$row[0]."'>".$row[0]."
                                    </label>
                                </div>";
			}
				
			//free result set
			$result->close();
		}
		else 
		{
			//echo"an error has occured";
		}

		//close connection
		$stmt->close();
		$conn->close();
		
		return $choices;
	}
?>