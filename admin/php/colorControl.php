<?php
require_once './../../php/dbConnect.php';

session_name('basisLogin');

define("BACKGROUNDS_COUNT", 13);
define("TEXT_COUNT", 19);
define("SHADOW_COUNT", 2);
define("BORDER_COUNT", 2);
define("TOTAL_COUNT", (BACKGROUNDS_COUNT + TEXT_COUNT + SHADOW_COUNT + BORDER_COUNT));
define("MODE_UPDATE", 342);
define("MODE_SAVE", 712);

if(isset($_POST['colMode']))
{
    $reply = array('msg' => "");
    
    switch ($_POST['colMode']) {
        case 'save':
            updateOrSaveProfile($_POST['profile'], $reply, MODE_SAVE);
            break;
        case 'load':
            loadProfile($_POST['profile'], $reply);
            break;
        case 'apply':
            applyCurrentProfile($_POST['profile'], $reply);
            break;
        case 'update':
            updateOrSaveProfile($_POST['profile'], $reply, MODE_UPDATE);
            break;
        default:
            break;
    }
    
    echo json_encode($reply);
}
 
function colorMain($reply)
{
    $currentProfile = getSiteProfile();
    $reply['nav'] = "<a class='dropdown-toggle' data-toggle='dropdown'>
                        ADMIN MENU
                        <span class='caret'></span>
                    </a>
                    <ul class='dropdown-menu header-general borders-content'>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnAdmin'>Admin</button></li>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnMember'>Members</button></li>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnText'>Site Text</span></li>
                        <li><span class='admin-nav btn header-nav-current'>Site Color</span></li>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnFaq'>FAQ</button></li>
                    </ul>";

    $reply['cont'] = "<div class='row noMarg' id='divContainer'>
                          ".getColorPickerModal()."
                          <div class='col-sm-6 col-md-5 col-md-offset-1 text-center'>
                                  <div class='well body-content'>
                                      <h3>Manage Color sets</h3>
                                      Site Profile:<b><span id='siteProfile'>".$currentProfile."</span></b><br/>
                                      Editing (current) Profile: <b><span id='setName'>".$currentProfile."</span></b>
                                      <input type='hidden' value='".$currentProfile."' id='profileName'><br/><br/>
                                      Load Color Profile:
                                      <select id='setSelect'>".getColorSets()."</select>
                                      <button class='btn btn-info' id='btnSelectSet'>Load</button><br/><br/>
                                      Update Current Color Profile:
                                      <button class='btn btn-info' id='btnUpdateSet'>Update</button><br/><br/>
                                      Save As New Profile:
                                      <input type='text' id='txtSaveSet'>
                                      <button class='btn btn-info' id='btnSaveSet'>Save</button><br/><br/>
                                      Apply Current Color Profile to website <br/>
                                      (be sure to update to use current colors):
                                      <button class='btn btn-info' id='btnWriteSet'>Apply</button>
                                  </div>
              
                              </div>
                              <div class='col-sm-6 col-md-5 text-center'>
                                  <div class='well body-content' >
                                      <h3>Color List</h3>
                                      <p>tips:<br/>
                                      - Enter colors as 6 digit hex values without the \"#\" and click update button<br/>
                                      - click a color box to bring up a color selector where you can enter rgb colors<br/>
                                      - you can copy and paste color hex code in right column to re-use color<br/>
                                      - click \"update\" under manage color sets to save current colors to current profile<br/>
                                      - click \"apply\" to write current color profile (from database) to css file for website</p>
                                       <div id='colorListMain'>
                                           ".getColorList($currentProfile)."
                                       </div>    
                                  </div>
                              </div>

                      </div>";

    return $reply;
}
function getIds()
{
    return array("bg1","bg2","bg3","bg4","bg5","bg6","bg7","bg8","bg9","bg10","bg11","bg12","bg13",
    "tx1","tx2","tx3","tx4","tx5","tx6","tx7","tx8","tx9","tx10","tx11","tx12","tx13","tx14","tx15","tx16","tx17","tx18","tx19",
    "sh1","sh2","bd1","bd2");
}
function getColorList($profile)
{
    $desc = array("Header (general)", "Header nav hover", "Header nav current","Body (general)",
    "Body spacers","Body content","FAQ buttons","FAQ button active/hover","individual button",
    "individual button hover/active 1","individual button hover/active 2","individual button hover/active 3",
    "Footer (general)","Header (general)","Header nav","Header nav hover","Header nav current","Header link",
    "Header link hover","Header \"group\"","Header Title","Body (general)","Body content","Body links",
    "Body links hover","FAQ button","FAQ button active/hover","individual button","individual button hover/active",
    "Footer (general)","Footer links","Footer links hover","Header \"group\"","Header Title","Borders main (unused)","Borders content");
    
    $ids = getIds();
    
    $list = "<h4>Background Color</h4>";
    $conn = dbConnect();
    
    $stmt = $conn->prepare("SELECT * FROM site_colors WHERE profile =?");
    $stmt->bind_param("s", $profile);
    
    if($stmt->execute())
    {
        //bind results to variable
        $result = $stmt->get_result(); 
        $row = $result->fetch_assoc();
    
        for ($i=0; $i < TOTAL_COUNT; $i++) { 
        
        $list .="<div class='colorList'>
                    <div class='colorDesc'>".$desc[$i]."</div> 
                    <div class='colorPreview' id='".$ids[$i]."' style='background-color:#".$row[$ids[$i]]."'></div> 
                    <input type='hidden' value='".$row[$ids[$i]]."' id='".$ids[$i]."-value'>
                    <input id='".$ids[$i]."-txt' type='text' class='colorHex' placeholder='".$row[$ids[$i]]."'/> 
                    <button class='btnColorUpdate' value='".$ids[$i]."'>Update</button>
                    <div class='spanBuffer' id='".$ids[$i]."-span'>".$row[$ids[$i]]."</div>
                </div>";
                
        switch ($i + 1) {
            case BACKGROUNDS_COUNT:
                $list .="<h4>Text Color</h4>";
                break;
            case (TEXT_COUNT + BACKGROUNDS_COUNT):
                $list .="<h4>Text Shadow Color</h4>";
                break;
            case (TEXT_COUNT + BACKGROUNDS_COUNT + SHADOW_COUNT):
                $list .="<h4>Border Color</h4>";
                break;
            default:
        }
    }
    
        $result->close();
    }
    else 
    {
        $list = "Error loading color list from database";
    }

    //close connection
    $stmt->close();
    $conn->close();
    
    
    return $list;                                     
}

function getColorSets()
{
  
    $select = "<option value='-1'>Select One</option>";
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT profile FROM site_colors");
    
    //execute query
    if($stmt->execute())
    {
        //bind results to variable
        $result = $stmt->get_result();
        //while data remains, display each row
        while($row = $result->fetch_row()){
            $select .= "<option value='".$row[0]."'>".$row[0]."</option>";
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
    
    return $select;
}

function getSiteProfile()
{
    $currentProfile = "";
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT current_profile FROM site_text WHERE site_id = 1");
	//execute query
    $stmt->execute();
	//bind results to variable
	$result = $stmt->get_result();

	if($result && $result->num_rows > 0)
	{
        $row = $result->fetch_assoc();
            
		$currentProfile = $row['current_profile'];
			
	    $result->close();
    }
    else
	{
		//boom
	}

	$stmt->close();
	$conn->close();
        
    return $currentProfile;
}
function updateSiteProfile($profile)
{
    $sql = "UPDATE site_text SET current_profile =? WHERE site_id='1'";
    $conn = dbConnect();
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("s", $profile);
    
    //execute query  
    if($stmt->execute())
    {
        //success!!      
    }
    else
    {
        //failure!!
    }
                
    //close connection
    $stmt->close();
    $conn->close();
}
function updateOrSaveProfile($profile, &$reply, $mode)
{
    $sql = "";
    $bg1 = $_POST["bg1"];$bg2 = $_POST["bg2"];$bg3 = $_POST["bg3"];$bg4 = $_POST["bg4"];$bg5 = $_POST["bg5"];$bg6 = $_POST["bg6"];$bg7 = $_POST["bg7"];$bg8 = $_POST["bg8"];$bg9 = $_POST["bg9"];$bg10 = $_POST["bg10"];$bg11 = $_POST["bg11"];$bg12 = $_POST["bg12"];$bg13 = $_POST["bg13"];
    $tx1 = $_POST["tx1"];$tx2 = $_POST["tx2"];$tx3 = $_POST["tx3"];$tx4 = $_POST["tx4"];$tx5 = $_POST["tx5"];$tx6 = $_POST["tx6"];$tx7 = $_POST["tx7"];$tx8 = $_POST["tx8"];$tx9 = $_POST["tx9"];$tx10 = $_POST["tx10"];$tx11 = $_POST["tx11"];$tx12 = $_POST["tx12"];$tx13 = $_POST["tx13"];$tx14 = $_POST["tx14"];$tx15 = $_POST["tx15"];$tx16 = $_POST["tx16"];$tx17 = $_POST["tx17"];$tx18 = $_POST["tx18"];$tx19 = $_POST["tx19"];$sh1 = $_POST["sh1"];$sh2 = $_POST["sh2"];$bd1 = $_POST["bd1"];$bd2 = $_POST["bd2"];
    
    switch ($mode) {
        case MODE_UPDATE:
            $sql = "UPDATE site_colors SET `bg1`=?,`bg2`=?,`bg3`=?,`bg4`=?,`bg5`=?,`bg6`=?,`bg7`=?,`bg8`=?,`bg9`=?,`bg10`=?,`bg11`=?,`bg12`=?,`bg13`=?,`tx1`=?,`tx2`=?,`tx3`=?,`tx4`=?,`tx5`=?,`tx6`=?,`tx7`=?,`tx8`=?,`tx9`=?,`tx10`=?,`tx11`=?,`tx12`=?,`tx13`=?,`tx14`=?,`tx15`=?,`tx16`=?,`tx17`=?,`tx18`=?,`tx19`=?,`sh1`=?,`sh2`=?,`bd1`=?,`bd2`=? WHERE profile = ?";
            $reply['msg'] = "<span class='alert alert-success'>Color Profile Updated Successfully</span>";
            break;
        case MODE_SAVE:
            $sql = "INSERT INTO `site_colors` (`bg1`, `bg2`, `bg3`, `bg4`, `bg5`, `bg6`, `bg7`, `bg8`, `bg9`, `bg10`, `bg11`, `bg12`, `bg13`, `tx1`, `tx2`, `tx3`, `tx4`, `tx5`, `tx6`, `tx7`, `tx8`, `tx9`, `tx10`, `tx11`, `tx12`, `tx13`, `tx14`, `tx15`, `tx16`, `tx17`, `tx18`, `tx19`, `sh1`, `sh2`, `bd1`, `bd2`, `profile`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $reply['msg'] = "<span class='alert alert-success'>New Color Profile Saved Successfully</span>";
            break;
        default:
            break;
    }
    
    
    $conn = dbConnect();
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("sssssssssssssssssssssssssssssssssssss", 
        $bg1, $bg2, $bg3, $bg4, $bg5, $bg6, $bg7, $bg8, $bg9, $bg10, $bg11, $bg12, $bg13, $tx1, $tx2, $tx3, $tx4, 
        $tx5, $tx6, $tx7, $tx8, $tx9, $tx10, $tx11, $tx12, $tx13, $tx14, $tx15, $tx16, $tx17, $tx18, $tx19, $sh1, $sh2, $bd1, $bd2, $profile );
 
    
	if($stmt->execute())
	{
		$reply['good'] = 1;
	}
	else
	{
		$reply['msg'] = "<span class='alert alert-danger'>Error: " . $sql . "<br>" . $stmt->error."</span>";
	    $reply['good'] = 0;
    }
		
	//close connection
    $stmt->close();
	$conn->close();
}

function loadProfile($profile, &$reply)
{
    $reply['list'] = getColorList($profile);
    $reply['profile'] = $profile;
    $reply['msg'] = "<span class='alert alert-success'>Profile {".$profile."} has been loaded</span>";
}

function applyCurrentProfile($profile, &$reply)
{
    $conn = dbConnect();
    
    $stmt = $conn->prepare("SELECT * FROM site_colors WHERE profile =?");
    $stmt->bind_param("s", $profile);
    
    if($stmt->execute())
    {
        //bind results to variable
        $result = $stmt->get_result();
        $row = $result->fetch_assoc(); 
    
   /* start document */ 
    $fileContents = 
"/* Header */
.header-general{
    background-color:#".$row['bg1'].";/*bg1*/
    color:#".$row['tx1'].";/*tx1*/
}

.header-nav{
    color:#".$row['tx2'].";/*tx2*/\n
}

.header-nav:hover{
    background-color:#".$row['bg2'].";/*bg2*/
    color:#".$row['tx3'].";/*tx3*/
}

.header-nav-current{
    background-color:#".$row['bg3'].";/*bg3*/
    color:#".$row['tx4'].";/*tx4*/
}

.header-link{
    color:#".$row['tx5'].";/*tx5*/
}

.header-link:hover{
    color:#".$row['tx6'].";/*tx6*/
}

.header-group{
    color:#".$row['tx7'].";/*tx7*/
    text-shadow: 0px 0px 5px #".$row['sh1'].";/*sh1*/
}

.header-title{
    color:#".$row['tx8'].";/*tx8*/
    text-shadow: 0px 0px 5px #".$row['sh2'].";/*sh2*/
}

/* Body */
.body-general{
    background-color:#".$row['bg4'].";/*bg4*/
    color:#".$row['tx9'].";/*tx9*/
}

.body-spacers{
    background-color:#".$row['bg5'].";/*bg5*/
}

.body-content{
    background-color:#".$row['bg6'].";/*bg6*/
    color:#".$row['tx10'].";/*tx10*/
}

.body-links{
    color:#".$row['tx11'].";/*tx11*/
}

.body-links:hover, .faq-button:active:focus {
    color:#".$row['tx12'].";/*tx12*/
}

:focus{
    border-color:#".$row['tx11']." !important;/*tx11*/
    outline-color:#".$row['tx11']." !important;/*tx11*/
}

.faq-button{
    background-color:#".$row['bg7'].";/*bg7*/
    color:#".$row['tx13'].";/*tx13*/
}

.faq-button-active{
    background-color:#".$row['bg8'].";/*bg8*/
    color:#".$row['tx14'].";/*tx14*/
}

.faq-button:hover{
    background-color:#".$row['bg8'].";/*bg8*/
    color:#".$row['tx14'].";/*tx14*/
}

.ind-button{
    background-color:#".$row['bg9'].";/*bg9*/
    color:#".$row['tx15'].";/*tx15*/
}

.ind-button:hover{
    background: #".$row['bg10']."; /* Old browsers */
    background: -moz-linear-gradient(top,  #".$row['bg10']." 0%, #".$row['bg11']." 20%, #".$row['bg12']." 50%, #".$row['bg11']." 80%, #".$row['bg10']." 100%); /* FF3.6-15 *//*bg10*//*bg11*//*bg12*/
    background: -webkit-linear-gradient(top,  #".$row['bg10']." 0%,#".$row['bg11']." 20%,#".$row['bg12']." 50%,#".$row['bg11']." 80%,#".$row['bg10']." 100%); /* Chrome10-25,Safari5.1-6 */
    background: linear-gradient(to bottom,  #".$row['bg10']." 0%,#".$row['bg11']." 20%,#".$row['bg12']." 50%,#".$row['bg11']." 80%,#".$row['bg10']." 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#".$row['bg10']."', endColorstr='#".$row['bg10']."',GradientType=0 ); /* IE6-9 */
    
    color:#".$row['tx16'].";/*tx16*/
}

.ind-button:active{
    background: #".$row['bg10']."; /* Old browsers */
    background: -moz-radial-gradient(center, ellipse cover,  #".$row['bg10']." 0%, #".$row['bg11']." 20%, #".$row['bg12']." 50%, #".$row['bg11']." 80%, #".$row['bg10']." 100%); /* FF3.6-15 */
    background: -webkit-radial-gradient(center, ellipse cover,  #".$row['bg10']." 0%,#".$row['bg11']." 20%,#".$row['bg12']." 50%,#".$row['bg11']." 80%,#".$row['bg10']." 100%); /* Chrome10-25,Safari5.1-6 */
    background: radial-gradient(ellipse at center,  #".$row['bg10']." 0%,#".$row['bg11']." 20%,#".$row['bg12']." 50%,#".$row['bg11']." 80%,#".$row['bg10']." 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#".$row['bg10']."', endColorstr='#".$row['bg10']."',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
    
    color:#".$row['tx16'].";/*tx16*/
}

.ind-button-active{
    background: #".$row['bg10']."; /* Old browsers */
    background: -moz-linear-gradient(top,  #".$row['bg10']." 0%, #".$row['bg11']." 20%, #".$row['bg12']." 50%, #".$row['bg11']." 80%, #".$row['bg10']." 100%); /* FF3.6-15 */
    background: -webkit-linear-gradient(top,  #".$row['bg10']." 0%,#".$row['bg11']." 20%,#".$row['bg12']." 50%,#".$row['bg11']." 80%,#".$row['bg10']." 100%); /* Chrome10-25,Safari5.1-6 */
    background: linear-gradient(to bottom,  #".$row['bg10']." 0%,#".$row['bg11']." 20%,#".$row['bg12']." 50%,#".$row['bg11']." 80%,#".$row['bg10']." 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#".$row['bg10']."', endColorstr='#".$row['bg10']."',GradientType=0 ); /* IE6-9 */
    
    color:#".$row['tx16'].";/*tx16*/
}

.borders-main{
    border-color:#".$row['bd1']." !important;/*bd1*/
}

.borders-content{
    border-color:#".$row['bd2']." !important;/*bd2*/
}

/* Footer */
.footer-general{
    background-color:#".$row['bg13'].";/*bg13*/
    color:#".$row['tx17'].";/*tx17*/
}

.footer-links{
    color:#".$row['tx18'].";/*tx18*/
}

.footer-links:hover{
    color:#".$row['tx19'].";/*tx19*/
}";
/* end document */
        $result->close();
        $colorFile = fopen("../../css/siteColors.css","w") or die("Error opening file");
        fwrite($colorFile, $fileContents);
        fclose($colorFile);
        
        updateSiteProfile($profile);
        
        $reply['msg'] = "<span class='alert alert-success'>Profile successfully written to site css</span";
    }
    else 
    {
        $reply['msg'] = "<span class='alert alert-danger'>Error loading color list from database</span>";
    }

    //close connection
    $stmt->close();
    $conn->close();
}

function getColorPickerModal()
{
		return " 
				<!-- Modal -->
				<div id='colorPickerModal' class='modal fade' role='dialog'>
				  <div class='modal-dialog'>

					<!-- Modal content-->
					<div class='modal-content text-center body-content'>
					  <div class='modal-header'>
						<button type='button' class='close' data-dismiss='modal'>&times;</button>
						<h4 class='modal-title'>Newbs Unit'd Color Picker</h4>
					  </div>
					  <div class='modal-body'>
						<p>Find a color you like and submit to apply it to the box you clicked on</p>
						<div>
                            <input type='hidden' id='idHolder' value='x'/>
                            Red<br/> 
                            <input type='range' min='0' max='255' id='redSlider' ><br/> 
                            <input type='text' id='redText'><button id='btnRedUpdate'>update</button><br/>
                            Green <br/> 
                            <input type='range' min='0' max='255' id='greenSlider'><br/> 
                            <input type='text' id='greenText'><button id='btnGreenUpdate'>update</button><br/>        
                            Blue <br/> 
                            <input type='range' min='0' max='255' id='blueSlider'><br/> 
                            <input type='text' id='blueText'><button id='btnBlueUpdate'>update</button><br/>
                                            
                            
                            <div id='testColor' ></div>
						</div>
						
						<button class='btn btn-info' id='btnSaveColor'>Submit</button>
						
					  </div>
					  <div class='modal-footer'>
						<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
					  </div>
					</div>

				  </div>
				</div>";
}

?>