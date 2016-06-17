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

	/**** Variables ****/
    $reply = array('msg' => "");
	$memberId = 0;

	/**** Mainline ****/

	if(isset($_POST['memId']))
	{
		$memberId = $_POST['memId'];

		validateId($memberId,$reply);
	}

	if(isset($_POST['loading']) && $_POST['loading'] == 'true')
	{
		loadPageContent($memberId,$reply);
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
			$reply['workItem'] = getWorkItem($_POST['workId']);
			break;
		default:
			$reply['content'] = "somthing bad has happened";
	}
	

	echo json_encode($reply);

	/**** Functions ****/
	function validateId(&$memberId, &$reply)
	{
	
        $conn = dbConnect();		

         //prepare statement
        $stmt = $conn->prepare("SELECT * 
								FROM login
								WHERE user_id = ?");

        //bind parameters to statement
		$stmt->bind_param("s",$memberId);

		//execute query
		$stmt->execute();
        $result = $stmt->get_result();
        
		if($result && $result->num_rows > 0)
        {
			//id is good, do nothing
			$result->close();
		}
		else//bad id
		{
			$reply['msg'] = "<span class='alert alert-warning'>
				Random member was selected due to not following a link from Portfolios or Footer</span>";
			
			if(isset($_SESSION['randomMember']) && $_POST['loading'] == 'false')
			{
				$memberId = $_SESSION['randomMember'];
			}
			else
			{
				$memberId = getRandomMemberId();
			}
		
		}

        $stmt->close();
		$conn->close();
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

			if(isset($row['uploaded_name']) && $row['uploaded_name'] != NULL && $row['uploaded_name'] != "")
			{
				$reply['img'] = "<img class='img-responsive' 
			                      src='member/uploads/profile_pics/".$row['uploaded_name']."' 
								  alt='".$reply['name']."\'s Profile Pic'>";
			}
			else
			{
				$reply['img'] = "<img class='img-responsive' 
								 src='member/uploads/profile_pics/placeholder-member.png' 
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

			$text .= $row['about_me'] == null ? "About me information coming soon" : $row['about_me'];

			$result->close();
		}
		else
		{
			//boom
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
        $img = "";
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

            $total = $result->num_rows;

            //get row from results
            while($row = $result->fetch_assoc())
            {
                 if(isset($row['uploaded_name']) 
                   && $row['uploaded_name'] != NULL && $row['uploaded_name'] != "")
				{
					$img = $row['uploaded_name'];
				}
				else
				{
					$img = "placeholder-work.png";
				}
                $workItems .= "<br/><button class='btn btn-default noPad workBtn body-content' value='".$row['work_id']."'>
                                        <img class='img-responsive' src='member/uploads/work_pics/".$img."' alt='work item thumbnail for ".$row['name']."' />
                                    </button>";

                $count++;
                //start a new row after 7 are displayed or at half the total count if total is more than 15
                /*if($total > 8 && $br == false)
                {
                    if($total < 16 && $count == 8)
                    {
                        $workItems .= "<br/>";
                        $br = true;
                    }
                    else
                    {
                        if($count == (($total >> 1) +1))
                        {
                             $workItems .= "<br/>";
                             $br = true;
                        }     
                    }
                }*/
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
                                        <img class='img-responsive' src='member/uploads/work_pics/work-home.svg.png' />
                                    </div>
                                    <div class='col-xs-6  col-sm4'>
                                        <h2 id='workTitle'>".$name."'s Past Work </h2>
                 
                                    </div>
                                </div>
                                <div class='row'>
                                   
                                    <p class='work-greeting' id='workDesc'>
                                        By clicking the images on the right, you can see the work I have chosen to display that showcase my skills
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>

                            <div class='container-fluid' id='workWrap'>
                                <button type='button' class='btn btn-link body-links' id='workCollapse'>
                                    <span id='workChevron' class='glyphicon glyphicon-chevron-right'></span>
                                </button>
                                <div class='workLinks'>
                                    <button class='btn btn-default noPad workBtn body-content' value='home'>
                                        <img class='img-responsive' src='member/uploads/work_pics/work-home.svg.png' alt='work home page image' />
                                    </button>".$workItems."
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
            
            $edu = makeList($row['education']);     
            $employ = makeList($row['employment']);    
            $awards = makeList($row['awards']);    
            $interests = makeList($row['interests']);  
            $skills = makeList($row['skills']);
            $certs = makeList($row['certs']);
            
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
                                    <b>Name:</b> ".$name."<br/>
                                    <b>Phone:</b> ".$phone."<br/>
                                    <b>Email:</b> ".$email."
                                </div>
                                <div>
                                    <h4>Education history:</h4>
                                    <ul>
                                        ".$edu."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Employment History:</h4>
                                    <ul>
                                        ".$employ."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Awards/Recognitions:</h4>
                                    <ul>
                                        ".$awards."
                                    </ul>
                                </div>
                            </div>
                            <div class='col-sm-6'>

                                <div>
                                    <h4>Interests/Focus:</h4>
                                    <ul>
                                        ".$interests."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Special Skills:</h4>
                                    <ul>
                                        ".$skills."
                                    </ul>
                                </div>
                                <div>
                                    <h4>Certifications:</h4>
                                    <ul>
                                        ".$certs."
                                    </ul>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-xs-12'>
                                    Please note - This is a summary of my resume, 
                                    if you would like a copy of my actual resume please 
                                    contact me and i will be glad to provide it for you.
                                </div>
                            </div>
                        </div>";

        return $html;
	}
    
    function makeList($temp)
    {   
        $array = explode('|',$temp);
        $returnString = "";
        
        if(count($array) >= 1 && trim($array[0]) != "")
        {
            foreach($array as $entry)
            {
                if(trim($entry) != "")
                $returnString .= "<li>".$entry."</li>";     
            }

        }
        else
        {
            $returnString = "<li>No Entry</li>";
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

		$form = "<p>
                            <b>Please feel free to contact me by phone,
                            email, or through the form below if you have
                            any questions, comments, or would like to request
                            more information.</b>
                        </p>
                        <div>
                            <b>Phone:</b> ".$phone."<br />
                            <b>Email:</b> ".$email."
                        </div>
                        <form class='form-horizontal no-float' role='form'>
                            <h4>Comment Card</h4><br />
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for='txtName'>Name: </label>
                                <div class='col-xs-8 col-sm-6 col-md-4'>
                                    <input class='form-control' type='text' id='txtName' autofocus />
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for='txtEmail'>Email: </label>
                                <div class='col-xs-8 col-sm-6 col-md-4'>
                                    <input class='form-control' type='email' id='txtEmail' />
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for='txtPhone'>Phone: </label>
                                <div class='col-xs-8 col-sm-6 col-md-4'>
                                    <input class='form-control' type='tel' id='txtPhone' />
                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-xs-2' for=''>Comment:</label>
                                <div class='col-xs-10 col-sm-8 col-md-6'>
                                    <textarea class='form-control noMarg' id='txtComment'></textarea>
                                </div>
                            </div>
                            <div class='form-group'>
                                <div class='text-center'>
                                    <button class='btn btn-primary' id='btnSendMsg'>Submit</button>
                                </div>
                            </div>
                        </form>";

		return $form;
	}

	function getRandomMemberId()
	{
		$conn = dbConnect();
		$id = 0;

        //prepare statement
        $stmt = $conn->prepare("SELECT user_id FROM portfolio_data");

		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

        if($result && $result->num_rows > 0)
		{
			$number = $result->num_rows;

			$random = rand(0, $number - 1);

			$result->data_seek($random);

			$row = $result->fetch_row();

			$id = $row[0];
            
            $result->close();
		}
		else
		{
			//no members
		}

		$stmt->close();
		$conn->close();

		$_SESSION['randomMember'] = $id;

		return $id;
	}

	function getWorkItem($workId)
	{
        if(isset($_POST['home']) && $_POST['home'] == 1)
        {
			validateId($workId,$reply);
            $name = getName($workId);
            
            return "<div class='row'>
                                    <div class='col-xs-4 col-sm-4 col-md-3'>
                                        <img class='img-responsive' src='member/uploads/work_pics/work-home.svg.png' />
                                    </div>
                                    <div class='col-xs-6  col-sm4'>
                                        <h2 id='workTitle'>".$name."'s Past Work </h2>
                 
                                    </div>
                                </div>
                                <div class='row'>
                                   
                                    <p class='work-greeting' id='workDesc'>
                                        By clicking the images on the right, you can see the work I have chosen to display that showcase my skills
                                    </p>
                                </div>";
        }

        $title = "";
        $desc = "";
        $link = "";
        $img = "<img class='img-responsive' src='member/uploads/work_pics/placeholder-work.png' alt='placeholder image'/>";
        
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
            if($row['link'] != null)
            {
                $link = "URL to project: 
                        <a class='body-links' href='".$row['link']."'>
                            link <img src='assets/leavingPost.png' alt='external link'/>
                        </a>";
            }
            
            if(isset($row['uploaded_name']) && $row['uploaded_name'] != NULL)
            {
                $img = "<img class='img-responsive' src='member/uploads/work_pics/".$row['uploaded_name']."' alt='work item image'/>";
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
		$workItem = "<div class='row'>
                        <div class='col-xs-4 col-sm-4 col-md-3'>
                           ".$img."
                        </div>
                        <div class='col-xs-6  col-sm4'>
                            <h2 id='workTitle'>".$title."</h2>
                            <span id='workUrl'>".$link."</span>
                        </div>
                    </div>
                    <div class='row'>
                        <h4>Description:</h4>
                        <p id='workDesc'>
                            ".$desc."       
                        </p>
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