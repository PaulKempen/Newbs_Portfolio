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
	               'control' => "");

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

		if(isset($_POST['loading']) && $_POST['loading'] == 'true')
		{
			loadPageContent($memberId,$reply);

			if($_SESSION['role'] == 'Admin')
			{
				$reply['control'] = "<li class='form-inline no-bull text-center' id='controlHook'>
										<select class='form-control' id='adminSelect'>
											".getOptions()."</select> 
										<button class='btn btn-primary text-center' id='btnAdminSelect'>
											Select
										</button>
									</li>";
			}
		}


		switch($_POST['mode'])
		{	
			case 'about':
				$reply['content'] = getAboutText($memberId);
				break;
			case 'work':
				$reply['content'] = getWorkMain($memberId);
				break;
			case 'resume':
				$reply['content'] = getResumeInfo($memberId);
				break;
			case 'contact':
				$reply['content'] = getContactForm($memberId);
				break;
			case 'workItem':
				$reply['workItem'] = getWorkItem($_POST['workId'],$memberId);
				break;
			default:
				$reply['content'] = "somthing bad has happened";
		}
	
	}

	
	

	echo json_encode($reply);

	/**** Functions ****/
	function getLoginRedirect()
	{
		$html = "<div class='well well-sm col-sm-offset-3 col-sm-6 text-center body-content' id='redirectWell'>
                        You must be logged in to view this page<br/>
                        Please go <a class='body-links' href='../login.html'>here</a> to login or register.
                    </div>";


		return $html;
	}
	
	function loadPageContent($memberId,&$reply)
	{
		$conn = dbConnect();		

        //prepare statement
        $stmt = $conn->prepare("SELECT a.first, a.last, b.uploaded_name 
		                        FROM member_info as a
								INNER JOIN member_pics as b
								ON a.user_id = b.user_id 
								WHERE a.user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		if(!$stmt->execute()){trigger_error("there was an error....".$conn->error, E_USER_WARNING);}

		//bind results to variable
		$result = $stmt->get_result();
    
        if($result && $result->num_rows > 0)
		{
			$row = $result->fetch_assoc();

			$reply['name'] = $row['first']." ".$row['last'];

			if(isset($row['uploaded_name']))
			{
				$reply['img'] = "<img class='img-responsive' 
			                      src='uploads/profile_pics/".$row['uploaded_name']."' 
								  alt='".$reply['name']."\'s Profile Pic'>";
			}
			else
			{
				$reply['img'] = "<img class='img-responsive' 
								 src='uploads/profile_pics/placeholder-member.png' 
								 alt='Placeholder Profile Pic'>";
			}
			$result->close();
		}
		else
		{

		}

		$stmt->close();
		$conn->close();
	}

	function getAboutText($memberId)
	{
		$text = "<h3>About Me:</h3>";
		$conn = dbConnect();		

        //prepare statement
        $stmt = $conn->prepare("SELECT about_me 
		                        FROM portfolio_data
								WHERE user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		$stmt->execute();

		//bind results to variable
		$result = $stmt->get_result();
    
        if($result && $result->num_rows > 0)
		{
			$row = $result->fetch_assoc();

			$text .= "<textarea class='form-control about' id='about' rows='14'>".$row['about_me']."</textarea>";
			$text .= "<br/><button class='btn btn-link editBtn body-links' value='about' id='btnAbout'>Save Text</button>";
			$result->close();
		}
		else
		{
			$text .= "Error, please contact an administrator";
		}
		$stmt->close();
		$conn->close();

		return $text;
	}

	function getWorkMain($memberId)
	{
        $count = 1;
        $workItems = "";
        $total = 0;
        $br = false;
        $name = getName($memberId);
        $img = "placeholder-work.png";
        $conn = dbConnect();		

         //prepare statement
        $stmt = $conn->prepare("SELECT a.name, a.work_id, b.uploaded_name 
		                        FROM past_work as a
                                INNER JOIN work_pics as b
                                ON a.work_id = b.work_id
								WHERE a.user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		if($stmt->execute())
        {

            //bind results to variable
            $result = $stmt->get_result();

            $total = $result->num_rows + 1;

            //get row from results
            while($row = $result->fetch_assoc())
            {
                if(isset($row['uploaded_name']) 
                   && $row['uploaded_name'] !== 'null' 
				   && $row['uploaded_name'] !== ""
				   && $row['uploaded_name'] !== NULL)
				{
					$img = $row['uploaded_name'];
				}
				else
				{
					$img = "placeholder-work.png";
				}
                $workItems .= "<br/><button class='btn btn-default noPad workBtn body-content' value='".$row['work_id']."'>
                                        <img class='img-responsive' src='uploads/work_pics/".$img."' alt='work item thumbnail for ".$row['name']."' />
                                    </button>";

                $count++;
                //start a new row after 7 are displayed or at half the total count if total is more than 15
                if($count > 8 && $br == false)
                {
                    if($total < 16)
                    {
                        $workItems .= "<br/>";
                        $br = true;
                    }
                    else
                    {
                        if($count == (($total >> 1)+1))
                        {
                             $workItems .= "<br/>";
                             $br = true;
                        }     
                    }
                }
            }
        }
        //close connections   
        $stmt->close();
        $result->close();
        $conn->close();
        
        //prepare return string
		$html = "<div class='row'>
                            <div class='col-xs-12' id='workMain'>
                                <div class='row'>
                                    <div class='col-xs-4 col-sm-4 col-md-3'>
                                        <img class='img-responsive' src='uploads/work_pics/work-home.svg.png' />
                                    </div>
                                    <div class='col-xs-6  col-sm4'>
                                        <h2 id='workTitle'>".$name."'s Past Work </h2>
                 
                                    </div>
                                </div>
                                <div class='row'>
                                   
                                    <p class='work-greeting' id='workDesc'>
                                        By clicking the images to the right, you can see the work I have chosen to display that showcase my skills
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class='row'>

                            <div class='container-fluid' id='workWrap'>
                                <button type='button' class='btn btn-link body-links' id='workCollapse'>
                                    <span id='workChevron' class='glyphicon glyphicon-chevron-right'></span>
                                </button>
                                <div class='workLinks'>
                                    <button class='btn btn-default noPad workBtn body-content' value='home'>
                                        <img class='img-responsive' src='uploads/work_pics/work-home.svg.png' alt='work home page image' />
                                    </button>".$workItems."<br/><button class='btn btn-default noPad body-content' value='add' id='btnAddWork'>
                                        <img class='img-responsive' src='uploads/work_pics/add-work.png' alt='work item thumbnail for ".$row['name']."' />
                                    </button>
                                </div>
                            </div>

                        </div>
                        <!-- Modal -->
                        <div id='delModal' class='modal' role='dialog'>
                          <div class='modal-dialog'>

                            <!-- Modal content-->
                            <div class='modal-content body-content'>
                              <div class='modal-header'>
                                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                                <h4 class='modal-title'>Delete Work Confirmation</h4>
                              </div>
                              <div class='modal-body'>
                                <p>Are you sure you want to delete this work item?</p>
                                <button type='button' class='btn btn-default' id='btnDeleteWork'>Yes</button>
                                <button type='button' class='btn btn-default' data-dismiss='modal'>No</button>
                              </div>
                              <div class='modal-footer'>
                                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                              </div>
                            </div>

                          </div>
                        </div>";

		return $html;
	}

	function getResumeInfo($memberId)
	{
        $name = "";
        $phone = "N/A";
        $email = "";
        $edu = "";
        $employ = "";
        $awards = "";
        $interests = "";
        $skills = "";
        $certs = "";
        
        $conn = dbConnect();
        
        //prepare statement
        $stmt = $conn->prepare("SELECT a.first, a.last, a.phone, a.email, b.*
		                        FROM member_info as a
                                INNER JOIN resumes as b
                                ON a.user_id = b.user_id
								WHERE a.user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		if($stmt->execute())
        {

            //bind results to variable
            $result = $stmt->get_result();

            //get row from results
            $row = $result->fetch_assoc();
                
            $name = $row['first']." ".$row['last'];
            
            if($row['phone'] != null)
                $phone = $row['phone'];
            $email = $row['email'];
            
            $edu = makeList($row['education'], 'edu');     
            $employ = makeList($row['employment'], 'emp');    
            $awards = makeList($row['awards'], 'awa');    
            $interests = makeList($row['interests'] , 'int');  
            $skills = makeList($row['skills'], 'ski');
            $certs = makeList($row['certs'], 'cer');
            
            $result ->close();
        }
        else
        {
            //boom
        }
        
        //close connections
        $stmt->close();
        $conn->close();
        
        //build return string
		$html = "<div class='row'>
                            <div class='col-sm-6'>
                                <div>
									<b>Refresh:</b> <button class='btn btn-link body-links' id='resumeRefresh'><span class='glyphicon glyphicon-refresh'></span></button><br/>
                                    <b>Name:</b> ".$name."<br/>
                                    <b>Phone:</b> ".$phone."<br/>
                                    <b>Email:</b> ".$email."
                                </div>
                                <div>
                                    <h4>Education history:</h4>
                                    <ul class='noPad'>
                                        ".$edu."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Employment History:</h4>
                                    <ul class='noPad'>
                                        ".$employ."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Awards/Recognitions:</h4>
                                    <ul class='noPad'>
                                        ".$awards."
                                    </ul>
                                </div>
                            </div>
                            <div class='col-sm-6'>

                                <div>
                                    <h4>Interests/Focus:</h4>
                                    <ul class='noPad'>
                                        ".$interests."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Special Skills:</h4>
                                    <ul class='noPad'>
                                        ".$skills."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Certifications:</h4>
                                    <ul class='noPad'>
                                        ".$certs."
                                    </ul>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-xs-12'>
                                    <b>Members: Add information as you wish, if you need to remove somthing 
									just leave the box empty and it won't be saved</b>
                                </div>
                            </div>
                        </div>";

        return $html;
	}
    
    function makeList($temp, $type)
    {   
        $array = explode('|',$temp);
        $returnString = "<li class='no-bull'>
                            <button class='btn btn-link addBtn body-links' id='btnAdd-".$type."' value='".$type."'>Add</button>
                            <button class='btn btn-link editBtn body-links' id='btnEdit-".$type."' value='".$type."'>Save</button>
                          </li>";
        
        if(count($array) >= 1)
        {
            foreach($array as $entry)
            {
                if(trim($entry) != "")
                $returnString .= "<li class='no-bull'><input type='text' class='".$type." form-control' value='".$entry."' /></li>";     
            }
            if(count($array) == 1)
                $returnString .= "<li class='no-bull'><input type='text' class='".$type." form-control' value='' /></li>";
        }
       
        
        
        
        
        return $returnString;
    }

	function getContactForm($memberId)
	{
		$phone = "";
		$email = "";
		$conn = dbConnect();		

        //prepare statement
        $stmt = $conn->prepare("SELECT phone, email 
		                        FROM member_info
								WHERE user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		$stmt->execute();

		//bind results to variable
		$result = $stmt->get_result();
    
        if($result && $result->num_rows > 0)
		{
			$row = $result->fetch_assoc();

			$phone = $row['phone'] == null ? "N/A" : $row['phone'];
			$email = $row['email'];
			$result->close();
		}
		else
		{
			//boom
		}
		$stmt->close();
		$conn->close();

		$form = "<p><b/>
                            ** Please Note ** This form is diabled on this page, If you wish to 
                            contact yourself please use the public individual page
                        </b></p>
                        <div>
                            <b>Phone:</b> ".$phone."<br />
                            <b>Email:</b> ".$email."
                        </div>
                        <form class='form-horizontal no-float' role='form'>
                            <h4>Comment Card</h4><br />
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for='txtName'>Name: </label>
                                <div class='col-xs-8 col-sm-6 col-md-4'>
                                    <input class='form-control' type='text' id='txtName' disabled />
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for='txtEmail'>Email: </label>
                                <div class='col-xs-8 col-sm-6 col-md-4'>
                                    <input class='form-control' type='email' id='txtEmail' disabled/>
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for='txtPhone'>Phone: </label>
                                <div class='col-xs-8 col-sm-6 col-md-4'>
                                    <input class='form-control' type='tel' id='txtPhone' disabled/>
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for=''>Comment:</label>
                                <div class='col-xs-10 col-sm-8 col-md-6'>
                                    <textarea class='form-control noMarg' id='txtComment' disabled></textarea>
                                </div>
                            </div>
                            <div class='form-group'>
                                <div class='text-center'>
                                    <button class='btn btn-primary' disabled>Submit</button>
                                </div>
                            </div>
                        </form>";

		return $form;
	}

	function getWorkItem($workId, $memberId)
	{
        if(isset($_POST['home']) && $_POST['home'] == 1)
        {
            $name = getName($memberId);
            
            return "<div class='row'>
                        <div class='col-xs-4 col-sm-4 col-md-3'>
                            <img class='img-responsive' src='uploads/work_pics/work-home.svg.png' />
                        </div>
                        <div class='col-xs-6  col-sm4'>
                            <h2 id='workTitle'>".$name."'s Past Work </h2>

                        </div>
                    </div>
                    <div class='row'>

                        <p id='workDesc'>
                            By clicking the images to the right, you can see the work I have chosen to display that showcase my skills
                        </p>
                    </div>";
        }

        $title = "";
        $desc = "";
        $link = "";
        $picForm = "";
    
        $img = "<img class='img-responsive' src='uploads/work_pics/placeholder-work.png' alt='placeholder image'/>";
        
        if($_SESSION['role'] == 'Admin')
        {
            $picForm ="<div class='row'>
                            <div class='col-sm-12 text-center' id='work-buffer'>
                                <p>Incase of Inappropriate work pic:</p>
                                <label class='checkbox-inline'><input type='checkbox' id='delAuth'>check to authorize</label>
                                <button class='btn btn-default' id='btnDelPic'>Delete Pic</button>
                                <p>*note* You will need to refresh work items to see the change</p>
                            </div>
                       </div>";
        }
        else
        {
            $picForm = "<form class='no-float' id='picForm' enctype='multipart/form-data'>
                                <label class='sr-only' for='fileWorkPic'>File Select</label>
								Choose an Image for this work item (jpg/jpeg/png/gif):
                                <div class='input-group'>     
                                    <span class='btn btn-default btn-file input-group-addon'>
                                        Browse <input id='fileWorkPic' type='file' />
                                    </span>
                                    <label class='form-control' id='fileLabel'></label>
                                    <span class='input-group-btn'>
                                        <button class='btn btn-default' id='btnUpWorkPic'>Submit</button>
                                    </span>
                                </div>
								Ideal Dimensions: Square at least 200x200, Max Size: 500kb
                            </form>";
        }
		$conn = dbConnect();

        //prepare statement
        $stmt = $conn->prepare("SELECT a.name, a.description, a.link, b.uploaded_name
                                FROM past_work as a
                                INNER JOIN work_pics as b
                                ON a.work_id = b.work_id
                                WHERE a.work_id = ?");
        
        $stmt->bind_param("s", $workId);
        
		//execute query
		if($stmt->execute())
        {
            //bind results to variable
		    $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            $title = $row['name'];
            $desc = $row['description'];
            $link = $row['link'];
            
            if(isset($row['uploaded_name']) && $row['uploaded_name'] != 'null' && $row['uploaded_name'] != "")
            {
                $img = "<img class='img-responsive' src='uploads/work_pics/".$row['uploaded_name']."' alt='work item image'/>";
            }
                 
   
        }
        else
        {
            //boom
        }
        
        //close connections
        $result->close();
        $stmt->close();
		$conn->close();
		
        //build return string
		$workItem = "   
                        <span class='del-button'>
                            Delete this card: 
                            <button class='btn btn-link body-links' data-toggle='modal' data-target='#delModal'>
                                <span class='glyphicon glyphicon-remove'></span>
                            </button>
                        </span>
                        <div class='row' role='form'>
						<input type='hidden' value='".$workId."' id='workId'>
                        <div class='col-xs-4 col-sm-4 col-md-3'>
                           ".$img."
                        </div>
                        <div class='col-xs-8  col-sm-8'> 
                            <div class='input-group'>
								<label for='txtTitle' class='input-group-addon workLabel'>Title:</label>
								<input type='text' class='form-control title'  value='".$title."' id='txtTitle' />
								<span class='input-group-btn'>
									<button class='btn btn-default editBtn' value='title' id='btnTitle'>Save</button>
								</span>
							 </div>
							<div class='input-group'>
								<label for='txtUrl' class='input-group-addon workLabel'>URL:</label>
								<input type='text' class='form-control url'  value='".$link."' id='txtUrl' />
								<span class='input-group-btn'>
									<button class='btn btn-default editBtn' value='url' id='btnUrl'>Save</button>
								</span>
							 </div>
                            ".$picForm."
                        </div>
                    </div>
                    <div class='row' role='form'>
                        <h4>Description:</h4><label for='txtDesc' class='sr-only' aria-label='Description'></label>
						<textarea class='form-control desc' id='txtDesc' rows='7' >".$desc."</textarea>
						<br/><button class='btn btn-link editBtn' value='desc' id='btnDesc'>Save Text</button>
				
                    </div>";

		return $workItem;
	}

    function getName($memberId)
    {
        $name = "";
        $conn = dbConnect();
        //prepare statement
        $stmt = $conn->prepare("SELECT first, last 
		                        FROM member_info
								WHERE user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		if($stmt->execute())
        {
            //bind results to variable
            $result = $stmt->get_result();

            //get row from results
            $row = $result->fetch_assoc();

            $name = $row['first']." ".$row['last'];
        }
        else
        {
            //boom
        }
        
        //close datasets to reuse them
        $stmt->close();
        $result->close();
        $conn->close();
        
        return $name;
    }

	
?>