<?php 
	/**** includes ****/
    require_once 'dbConnect.php';
	require_once 'loginUtils.php';
	require_once 'searchUtils.php';
	
	/**** Session ****/
    // name session
    session_name('basisLogin');

    // Making the session cookie live for 2 hours (wks(*)*days(7)*hours(24)*mins(60)*secs(60))
	session_set_cookie_params(2*60*60);

	//start session
	session_start();

	/**** Variables ****/
	$page = (isset($_POST['page']) ? $_POST['page'] : "");
	$member = ($page == "member" || $page == "admin" ? true : false);

    $reply = array('log' => "<a class='header-link' href='".($member ? "../" : "")."login.html' id='login'>Login <span class='glyphicon glyphicon-log-in'></span></a>",
                   'msg' => "",
                   'user' => "");
	$about = false;
	$contact = false;

	/**** Constants ****/
	define("RANDOM",835);
	define("ALL",786);
	
	/**** Mainline ****/
	if(isset($_POST['page']))
	{
		switch($_POST['page'])
		{
			case 'home': populateSelection($reply);
				break;
			case 'about': $about = true;
				break;
			case 'faq': getFaq($reply);
				break;
			case 'contact': $contact = true;
				break;
			case 'search': getResults($_POST['data'], $reply);
				break;
			case 'viewAll': $reply['cards'] = getCards(ALL);
				$reply['options'] = getFocusOptions();
			default:
		}
	}

	//check if user is logged in or not
    if(isset($_SESSION['logedIn']) && $_SESSION['logedIn'] == TRUE)
    {
		$role = getRole($_SESSION['userId']);
        
		switch($page)
		{
			case 'member':
				$reply['log'] ="<a class='header-link' href='../login.html?logout'>Logout <span class='glyphicon glyphicon-log-out'></span></a>";
				if($role === 'Member')
					$reply['user'] = "Welcome, ".$_SESSION['userName']."<br/>
						<a class='header-link' href='index.html'>View account</a> | <a class='header-link' href='portfolio.html'>View Portfolio</a>";
				if($role === 'Admin')
					$reply['user'] = "Welcome, ".$_SESSION['userName']."<br/>
						<a class='header-link' href='index.html'>View account</a> | <a class='header-link' href='portfolio.html'>View Portfolios</a> | <a class='header-link' href='../admin/index.html'>Admin Control</a>";
				break;
			case 'admin':
				$reply['log'] ="<a class='header-link' href='../login.html?logout'>Logout <span class='glyphicon glyphicon-log-out'></span></a>";
				if($role === 'Member')
					$reply['user'] = "Welcome, ".$_SESSION['userName']."<br/>
						<a class='header-link' href='../member/index.html'>View account</a> | <a class='header-link' href='../member/portfolio.html'>View Portfolio</a>";
				if($role === 'Admin')
					$reply['user'] = "Welcome, ".$_SESSION['userName']."<br/>
						<a class='header-link' href='../member/index.html'>View account</a> | <a class='header-link' href='../member/portfolio.html'>View Portfolio</a> | <a class='header-link' href='index.html'>Admin Control</a>";
				break;
			default:
				$reply['log'] ="<a class='header-link' href='login.html?logout'>Logout <span class='glyphicon glyphicon-log-out'></span></a>";
				if($role === 'Member')
					$reply['user'] = "Welcome, ".$_SESSION['userName']."<br/>
						<a class='header-link' href='member/index.html'>View account</a> | <a class='header-link' href='member/portfolio.html'>View Portfolio</a>";
				if($role === 'Admin')
					$reply['user'] = "Welcome, ".$_SESSION['userName']."<br/>
						<a class='header-link' href='member/index.html'>View account</a> | <a class='header-link' href='member/portfolio.html'>View Portfolio</a> | <a class='header-link' href='admin/index.html'>Admin Control</a>";
				break;
		
		}
	}

	getSiteText($reply, $about, $contact);

	// $reply['prog_web'] = populateFooterLinks(PROGRAMMER, WEBDEV, $member);
	// $reply['net_sec'] = populateFooterLinks(NETWORKING, NETSECURITY, $member);
	// $reply['proj_man'] = populateFooterLinks(PROJMAN, "XXX", $member);

	echo json_encode($reply);

	/**** End Mainline ****/

	function getSiteText(&$reply, $about, $contact)
	{
		$conn = dbConnect();

		//prepare statement
        $stmt = $conn->prepare("SELECT * FROM site_text WHERE site_id = 1");
		//execute query
		$stmt->execute();
		//bind results to variable
		$result = $stmt->get_result();

		if($result && $result->num_rows > 0)
		{
            $row = $result->fetch_assoc();
            
			$reply['footer'] = $row['footer'];
			$reply['copyright'] = $row['copyright'];//.feedback(); //feedback disabled
			$reply['group'] = $row['group_name'];
			$reply['title'] = $row['title'];
			
			if($about)
				$reply['about'] = $row['about'];
			
			if($contact)
				$reply['contact'] = $row['contact'];

			$result->close();
        }
        else
		{
			$msg = "Error Retrieving Site info";
		}

		$stmt->close();
		$conn->close();
	}

	// function populateFooterLinks($val1,$val2,$member)
	// {
	// 	$list = "";
	// 	$conn = dbConnect();

	// 	//add wildcards;
	// 	$val1 = "%".$val1."%";
	// 	$val2 = "%".$val2."%";

	// 	//prepare statement
	// 	$stmt = $conn->prepare("SELECT a.user_id, a.first, a.last 
	// 	                        FROM member_info as a 
	// 							INNER JOIN portfolio_data as b 
	// 							ON a.user_id = b.user_id 
	// 							WHERE b.focus LIKE ? OR b.focus LIKE ?
	// 							ORDER BY a.last");

	// 	$stmt->bind_param("ss", $val1, $val2);

	// 	//execute query  
	// 	if($stmt->execute())
	// 	{
	// 		//bind results to variable
	// 		$result = $stmt->get_result();
	// 		//while data remains, display each row
	// 		while($row = $result->fetch_row())
	// 		{
	// 			$list .= "<li><a class='footer-links' href='".($member ? "../" : "")."individual.html?member=".$row[0]."'>".$row[1]." ".$row[2]."</a></li>";    
	// 		};
	// 		//free result set
	// 		$result->close();
	// 	}
	// 	else 
	// 	{
	// 		//echo"an error has occured";
	// 	}

    //     //close connection
	// 	$stmt->close();
	// 	$conn->close();
 
	// 	return $list;
	
	// }
	function populateSelection(&$reply)
	{
		$carousel = "<div class='body-general'>";
		$carousel .= getCards(RANDOM);
		$carousel .= "</div>";
		
		$reply['carousel'] = $carousel;	
	}
	// function populateCarousel(&$reply)
	// {
	// 	$carousel = "<div class='item active'>";
	// 	$carousel .= getCards("<h1>Programmers / Web Developers</h1>", PROGRAMMER, WEBDEV);
	// 	$carousel .= "</div>";

	// 	$carousel .= "<div class='item'>";
	// 	$carousel .= getCards("<h1>Networking / Security Engineers</h1>", NETWORKING, NETSECURITY);
	// 	$carousel .= "</div>";

	// 	$carousel .= "<div class='item'>";
	// 	$carousel .= getCards("<h1>Project Managers</h1>", PROJMAN, "XXX");
	// 	$carousel .= "</div>";

	// 	$reply['carousel'] = $carousel;	
	// }
	
	function getCards($mode)
	{
		//add wildcards;

		$sectionString = "<div class='row noMarg'>";
		$count = 0;
        $cards = array();
        $conn = dbConnect();
        
		//prepare statement
		$stmt = $conn->prepare("SELECT a.user_id, a.first, a.last,
		                               b.focus, b. short_desc,
									   c.uploaded_name
		                        FROM member_info as a 
								INNER JOIN portfolio_data as b
								ON a.user_id = b.user_id 
								INNER JOIN member_pics as c
								ON b.user_id = c.user_id 
								ORDER BY a.last");

		//execute query  
		if($stmt->execute())
		{
			//bind results to variable
			$result = $stmt->get_result();

			//while data remains, process each row
			while($set = $result->fetch_assoc())
			{

				if($set['uploaded_name'] != null && $set['uploaded_name'] != "")
				{
					$img = "<img class='img-thumbnail' src='member/uploads/profile_pics/"
						.$set['uploaded_name']."?x=".rand() 
						."' alt='Proflie picture for "
						.$set['first']." "
						.$set['last']."' width='150' height='150'>";
				}
				else
				{
					$img = "<img class='img-thumbnail' 
							src='member/uploads/profile_pics/placeholder-member.png' 
							alt='Placeholder Profile Pic' width='150' height='150'>";
				}
				$cards[$count] ="<div class='col-md-2 col-sm-4 col-xs-6 card".getSortClasses($set['focus'])."'>
								<a href='individual.html?member=".$set['user_id']."'>
									<div class='well well-sm card-inner body-content borders-content'>
										<div class='imgWrapper'>".$img."</div>
										<p>
											<b>Name:</b> ".$set['first']." ".$set['last']."<br>
											<b>Focus:</b> ".getFocusString($set['focus'])."<br>
											<b>Experience:</b> ".$set['short_desc']."
										</p>
									</div>
							    </a>
							</div>";

				//increment count
				$count++;
			};
		
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
		
		switch($mode)
		{
			case RANDOM:
				shuffle($cards);	
			
				for ($i=0; $i < $count && $i < 10; $i++) 
				{ 
					$sectionString .= $cards[$i];
				}
				break;
			case ALL:
				foreach ($cards as $card) {
					$sectionString .= $card;
				}
				break;
			default:
				break;
		}	
		
		
		
		$sectionString .= "</div>";

		return $sectionString;
	}
	function getSortClasses($focus)
	{
		$classes = "";
		$array = explode("|", $focus);
		foreach($array as $value)
		{
			$classes .= " ".preg_replace("/[\s_]/", "-", $value);
		}	
		return $classes;
	}
	function getFocusString($focus)
	{
		$array = explode("|", $focus);
		$focusString = "";
        $size = count($array);
		
		foreach($array as $value)
		{
			
			$focusString .= $value;

			if(--$size >= 1)
				$focusString .= "<br/>";
				
		}

		return $focusString;
	}
	function getFocusOptions()
	{
		$list = "<option value='all'>View All</option>";
    
		$conn = dbConnect();
		
		//prepare statement
		$stmt = $conn->prepare("SELECT * FROM focus_areas");
		
		//execute query
		if($stmt->execute())
		{
			$result = $stmt->get_result();     
			while($row = $result->fetch_row())
			{
				$list .="<option value='".preg_replace("/[\s_]/", "-", $row[0])."'>".$row[0]."</option>";
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
		
		return $list;
	}
	function getFaq(&$reply)
	{
		$conn = dbConnect();
		$questions = "";
		$answers = "";
        $i = 1;

		//prepare statement
		$stmt = $conn->prepare("SELECT question, answer FROM faq");

		//execute query  
		if($stmt->execute())
		{
			//bind results to variable
			$result = $stmt->get_result();

			//while data remains, process each row
			while($row = $result->fetch_assoc())
			{
				$questions .="<button type='button' class='btn btn-block panel-body panel-button faq-button' value='q".$i."'>
                                ".$row['question']."
                            </button>";

				$answers .="<div class='answers' id='q".$i."'>
                            <h4>QUESTION: ".$row['question']."</h4>
                            <p>".$row['answer']."</p>
                        </div>";
				$i++;
			}

			$questions .="<button type='button' class='btn btn-block panel-body panel-button faq-button' value='q".$i."'>
                                Have a Question you don't see here?
                            </button>";

				$answers .="<div class='answers' id='q".$i."'>
                            <h4>QUESTION: Have a Question you don't see here?</h4>
                            <p>Please visit our contact page <a href='contact.html'>here</a> and let us know about it.<br/><br/>
							With your help, we can supply all of our visitors with the information they desire.<br/><br/></p>
                        </div>";
		}

		//close connection
		$stmt->close();
		$conn->close();

		$reply['questions'] = $questions;
		$reply['answers'] = $answers;
	}

	////beta test feedback form////
	function feedback()
	{
		return " <!-- Modal Trigger button to be fixed placed -->
				<button class='btn btn-info' data-toggle='modal' data-target='#feedbackModal' id='btnFBModal'><span class='glyphicon glyphicon-comment'></span></button>

				<!-- Modal -->
				<div id='feedbackModal' class='modal fade' role='dialog'>
				  <div class='modal-dialog'>

					<!-- Modal content-->
					<div class='modal-content'>
					  <div class='modal-header'>
						<button type='button' class='close' data-dismiss='modal'>&times;</button>
						<h4 class='modal-title'>Newbs Unit'd Feedback Form</h4>
					  </div>
					  <div class='modal-body'>
						<p>Please use this form to provide any feedback that you have</p>
						<div>
							<label class='radio-inline'><input type='radio' value='Comment' name='fb' checked>Comment</label>
							<label class='radio-inline'><input type='radio' value='Recommendation' name='fb'>Recommendation</label>
							<label class='radio-inline'><input type='radio' value='Bug' name='fb'>Bug</label>
							<label class='radio-inline'><input type='radio' value='Criticism' name='fb'>Criticism</label>
						</div>
						<textarea class='form-control' rows='6' id='txtFeedback'></textarea><br/>
						<button class='btn btn-info' id='btnFeedback'>Submit</button>
						
					  </div>
					  <div class='modal-footer'>
						<div id='fbMsg' class='text-center'></div>
						<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
					  </div>
					</div>

				  </div>
				</div>";
	}

?>