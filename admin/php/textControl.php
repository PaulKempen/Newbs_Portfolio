<?php
session_name('basisLogin');
function textMain($reply)
{
    $reply['nav'] = "<a class='dropdown-toggle' data-toggle='dropdown'>
                        ADMIN MENU
                        <span class='caret'></span>
                    </a>
                    <ul class='dropdown-menu header-general borders-content'>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnAdmin'>Admin</button></li>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnMember'>Members</button></li>
                        <li><span class='admin-nav btn header-nav-current'>Site Text</span></li>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnColor'>Site Color</button></li>
                        <li><button class='btn btn-link admin-nav header-nav' id='btnFaq'>FAQ</button></li>
                    </ul>";

    $reply['cont'] = "<div class='row noMarg' id='divContainer'>

                          <div class='well col-sm-12 col-md-offset-2 col-md-8 text-center body-content'>
                              <h3>Site Text</h3>
                              <div>".getSiteText()."</div>
                          </div>

                      </div>";

    return $reply;
}

function getSiteText()
{
    $html = "";
    $about = "";
    $copyright = "";
    $footer = "";
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT * FROM site_text");
    
    //execute query
    if($stmt->execute())
    {
        $result = $stmt->get_result();     
        $row = $result->fetch_assoc();
        
        $about = $row['about'];
        $copyright = $row['copyright'];
        $footer = $row['footer'];
        $title = $row['title'];
        $head = $row['group_name'];
        $contact = $row['contact'];
        
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
    
    
    $html = "<div class='form-group'>
                 <label for='txtAuth'>
                    You must provide your admin password to update any of these:
                </label>
                <input class='form-control' type='password' id='txtAuth' placeholder='Admin Password'/><br/>
            </div><br/>
            <div class='form-group text-left'>
                <label class='control-label' for='txtFocus'>
                    Member Focus Areas: <button class='btn btn-link site-text body-links' value='addFocus' id='btnAddFoc'>Add Focus</button> | 
                    <button class='btn btn-link site-text body-links' value='delFocus' id='btnDelFoc'>Delete Selected Focus</button>
                </label>
                <input type='text' class='form-control' id='txtFocus' placeholder='Enter Focus Name'/>     
                <select class='form-control' id='selFocus'>".getFocusList()."</select>
            </div>
            <div class='form-group text-left'>
                <label class='control-label' for='txtCopyright'>
                    Copyright: <button class='btn btn-link site-text body-links' value='copy' id='btnCopy'>Save Copyright</button>
                </label>     
                <textarea class='form-control' rows='3' id='txtCopyright'>".$copyright."</textarea>
            </div>
            <div class='form-group text-left'>
                <label class='control-label' for='txtFooter'>
                    Footer Text: <button class='btn btn-link site-text body-links' value='foot' id='btnFooter'>Save Footer Text</button>
                </label>     
                <textarea class='form-control' rows='3' id='txtFooter'>".$footer."</textarea>  
            </div>
            <div class='form-group text-left'>
                <label class='control-label' for='txtAbout'>
                    About Text: <button class='btn btn-link site-text body-links' value='about' id='btnAbout'>Save About Text</button>
                </label>     
                <textarea class='form-control' rows='10' id='txtAbout'>".$about."</textarea>  
            </div>
            <div class='form-group text-left'>
                <label class='control-label' for='txtTitle'>
                    Website Title Text: <button class='btn btn-link site-text body-links' value='title' id='btnTitle'>Save Title Text</button>
                </label>     
                <textarea class='form-control' rows='1' id='txtTitle'>".$title."</textarea>  
            </div>
            <div class='form-group text-left'>
                <label class='control-label' for='txtHead'>
                    Group Name Text: <button class='btn btn-link site-text body-links' value='head' id='btnHead'>Save Group Name Text</button>
                </label>     
                <textarea class='form-control' rows='1' id='txtHead'>".$head."</textarea>  
            </div>
            <div class='form-group text-left'>
                <label class='control-label' for='txtContact'>
                    Website Contact Information Text: <button class='btn btn-link site-text body-links' value='contact' id='btnContact'>Save Contact Information Text</button>
                </label>     
                <textarea class='form-control' rows='10' id='txtContact'>".$contact."</textarea>  
            </div>";
    
    return $html;
}

function getFocusList()
{
    $list = "<option value='select'>Select One</option>";
    
    $conn = dbConnect();
    
    //prepare statement
    $stmt = $conn->prepare("SELECT * FROM focus_areas");
    
    //execute query
    if($stmt->execute())
    {
        $result = $stmt->get_result();     
        while($row = $result->fetch_row())
        {
            $list .="<option value='".$row[0]."'>".$row[0]."</option>";
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


?>