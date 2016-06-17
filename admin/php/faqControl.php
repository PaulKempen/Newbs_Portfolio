<?php 
    require_once './../../php/dbConnect.php';
  
    session_name('basisLogin');
    
    function faqMain($reply)
    {
        $div1 = faqList();
        $div3 = faqModeSelect();
        
        $reply['nav'] = "<a class='dropdown-toggle' data-toggle='dropdown'>
							ADMIN MENU
							<span class='caret'></span>
						</a>
						<ul class='dropdown-menu header-general borders-content'>
							<li><button class='btn btn-link admin-nav header-nav' id='btnAdmin'>Admin</button></li>
							<li><button class='btn btn-link admin-nav header-nav' id='btnMember'>Members</button></li>
							<li><button class='btn btn-link admin-nav header-nav' id='btnText'>Site Text</button></li>
                            <li><button class='btn btn-link admin-nav header-nav' id='btnColor'>Site Color</button></li>
							<li><span class='admin-nav header-nav-current btn'>FAQ</span></li>
						</ul>";
        
        $reply['cont'] = "<div class='row noMarg' id='divContainer'>
                              <div class ='col-sm-4 col-md-4 text-center'>
                                  <div class='well body-content'>
                                      <h3>FAQ list</h3>
                                      <br/>".$div1."
                                  </div>
                              </div>
                              <div class ='col-sm-3 col-md-4 text-center'>
                                  <div class='well body-content'>
                                      <h3>Options</h3>
                                      <button class ='btn btn-link body-links' id='btnAddFaq'>
                                          Add FAQ
                                      </button>
                                      <br/><br/>
                                      <button class='btn btn-link body-links' id='btnModQuest'>
                                          Modify FAQ question
                                      </button><br/>
                                      <button class='btn btn-link body-links' id='btnModAnsw'>
                                          Modify FAQ answer
                                      </button><br/>
                                      <button class='btn btn-link body-links' id='btnDelFaq'>
                                          Delete FAQ
                                      </button>
                                  </div>
                              </div>
                              <div class ='col-sm-5 col-md-4 text-center'>
                                  <div class='well body-content'>
                                    ".$div3."
                                  </div>
                              </div>
                          </div>";
            
        return $reply;
    }




function faqList()
{
    $list = "<dl>";
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT question, answer FROM faq");
    
    //execute query
    if($stmt->execute())
    {
        //bind results to variable
        $result = $stmt->get_result();
        //while data remains, display each row
        while($row = $result->fetch_row())
        {
            $list .= "<dt><b>".$row[0]."</b></dt><dd> - ".$row[1]."</dd>";    
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
    
    $list .= "</dl>";
    return $list;
}

function faqSelect()
{
    $select = "<select id='faqSelect' class='adminSel form-control'>";
        $select .= "<option value='-1'>Select One</option>";
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT faq_id, question FROM faq");
    
    //execute query
    if($stmt->execute())
    {
        //bind results to variable
        $result = $stmt->get_result();
        //while data remains, display each row
        while($row = $result->fetch_row()){
            $select .= "<option value='".$row[0]."'>".$row[1]."</option>";
            
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
           
    $select .="</select>";
    
    return $select;
}


function faqModeSelect()
{
    if(isset($_POST['mode']))
    {
        switch($_POST['mode'])
        {
            case 'question':
                return "<h3>Modify FAQ Question</h3>
                        ".faqSelect()."<br/>
                        <div class='form-group text-left'>
                            <label class='control-label' for='txtQ'>
                                Question:
                            </label>     
                            <textarea class='form-control' rows='5' id='txtQ'></textarea>  
                        </div>
                        
                         <br/>
                         <div class='form-group'>
                             <label for='txtAuth'>
                                Enter Admin password to authorize and submit:
                            </label>
                            <input class='form-control' type='password' id='txtAuth'/><br/>
                        </div>
                        <div class='form-group'>
                            <button class='btn btn-default' id='btnModQ'>Submit</button>
                        </div>";
  
            case 'answer':
                return "<h3>Modify FAQ Answer</h3>
                        ".faqSelect()."<br/>
                
                        <div class='form-group text-left'>
                            <label class='control-label' for='txtA'>
                                Answer:
                            </label>     
                            <textarea class='form-control' rows='8' id='txtA'></textarea>  
                        </div>
                        
                         <br/>
                         <div class='form-group'>
                             <label for='txtAuth'>
                                Enter Admin password to authorize and submit:
                            </label>
                            <input class='form-control' type='password' id='txtAuth'/><br/>
                        </div>
                        <div class='form-group'>
                            <button class='btn btn-default' id='btnModA'>Submit</button>
                        </div>";
  
            case 'delete':
                return "<h3>Delete Selected FAQ</h3>
                        ".faqSelect()."<br/>
                
                        <div class='form-group'>
                             <label for='txtAuth'>
                                Enter Admin password to authorize and submit:
                            </label>
                            <input class='form-control' type='password' id='txtAuth'/><br/>
                        </div>
                        <div class='form-group'>
                            <button class='btn btn-default' id='btnDeleteFaq'>Submit</button>
                        </div>";
  
            default:
                break;
        }
    }
    return "<h3>Add FAQ</h3>
        <div class='form-group text-left'>
            <label class='control-label' for='txtQ'>
                Question:
            </label>     
            <textarea class='form-control' rows='5' id='txtQ'></textarea>  
        </div>
    
        <div class='form-group text-left'>
            <label class='control-label' for='txtA'>
                Answer:
            </label>     
            <textarea class='form-control' rows='8' id='txtA'></textarea>  
        </div>

         <br/>
         <div class='form-group'>
             <label for='txtAuth'>
                Enter Admin password to authorize and submit:
            </label>
            <input class='form-control' type='password' id='txtAuth'/><br/>
        </div>
        <div class='form-group'>
            <button class='btn btn-default' id='btnFaqAdd'>Submit</button>
        </div>";
}
?>