<?php 
    require_once './../../php/dbConnect.php';

    session_name('basisLogin');
    
    function memberMain($reply)
    {
        $div1 = memberList();
        $div3 = modeSelect();
        
        $reply['nav'] = "<a class='dropdown-toggle' data-toggle='dropdown'>
							ADMIN MENU
							<span class='caret'></span>
						</a>
						<ul class='dropdown-menu header-general borders-content'>
							<li><button class='btn btn-link admin-nav header-nav' id='btnAdmin'>Admin</button></li>
							<li><span class='admin-nav btn header-nav-current'>Members</span></li>
							<li><button class='btn btn-link admin-nav header-nav' id='btnText'>Site Text</button></li>
                            <li><button class='btn btn-link admin-nav header-nav' id='btnColor'>Site Color</button></li>
							<li><button class='btn btn-link admin-nav header-nav' id='btnFaq'>FAQ</button></li>
						</ul>";
        
        $reply['cont'] = "<div class='row noMarg' id='divContainer'>
                              <div class='col-sm-4 col-md-4 text-center'>
                                  <div class='well body-content'>
                                      <h3>Member list</h3>
                                      <br/>".$div1."
                                  </div>
                              </div>
                              <div class='col-sm-3 col-md-4 text-center'>
                                  <div class='well body-content'>
                                      <h3>Options</h3>
                                      <button class ='btn btn-link body-links' id='btnAddMem'>
                                          Add Member
                                      </button>
                                      <br/><input type='hidden' value='member' id='adminMode'><br/>
                                      <button class='btn btn-link body-links' id='btnRenMem'>
                                          Change Member Username
                                      </button><br/>
                                      <button class='btn btn-link body-links' id='btnResMemPW'>
                                          Reset Member Password
                                      </button><br/>
                                      <button class='btn btn-link body-links' id='btnDelMem'>
                                          Delete Member
                                      </button>
                                  </div>
                              </div>
                              <div class='col-sm-5 col-md-4 text-center'>
                                  <div class='well body-content'>
                                      ".$div3."
                                  </div>
                              </div>
                          </div>";
            
        return $reply;
    }


function memberList()
{
    $table = "<table class='table text-left'>
                <tr>
                    <th class='text-center'>
                        Count
                    </th>
                    <th class='text-center'>
                        User Name
                    </th>
                    <th class='text-center'>
                        Full Name
                    </th>
                    <th class='text-center'>
                        Email
                    </th>
                <tr/>";
    $count = 0;
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT a.user_name, b.first, b.last, b.email 
                            FROM login as a
                            INNER JOIN member_info as b
                            ON a.user_id = b.user_id
                            WHERE a.role = 'Member'");
    
    //execute query
    if($stmt->execute())
    {
        //bind results to variable
        $result = $stmt->get_result();
        //while data remains, display each row
        while($row = $result->fetch_assoc())
        {
            $table .= "<tr>
                        <td>".(++$count)."</td>
                        <td>".$row['user_name']."</td>
                        <td>".$row['first']." ".$row['last']."</td>
                        <td>".$row['email']."</td>
                      </tr>";    
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
    
    $table .= "</table>";
    return $table;
}

function memberSelect()
{
    $select = "<select id='memberSelect' class='adminSel form-control'>";
    $select .= "<option value='-1'>Select One</option>";
    $conn = dbConnect();

    //prepare statement
    $stmt = $conn->prepare("SELECT user_id, user_name FROM login WHERE role = 'Member'");
    
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


function modeSelect()
{
    if(isset($_POST['mode']))
    {
        switch($_POST['mode'])
        {
            case 'rename':
                return "<h3>Change selected Username</h3>
                        ".memberSelect()."<br/>
                        <div class='form-group text-left'>
                            <label class='control-label' for='txtName'>
                                New User Name:
                            </label>     
                            <input type='text' class='form-control' id='txtName'>  
                        </div>
                        <br/>
                         <div class='form-group'>
                             <label for='txtAuth'>
                                Enter Admin password to authorize and submit:
                            </label>
                            <input class='form-control' type='password' id='txtAuth'/><br/>
                        </div>
                        <div class='form-group'>
                            <button class='btn btn-default' id='btnRename'>Submit</button>
                        </div>";
  
            case 'reset':
                return "<h3>Reset Password of Selected Member</h3>
                        ".memberSelect()."<br/>
                        <div class='form-group text-left'>
                            <label class='control-label' for='txtPassword'>
                                New Password:
                            </label>     
                            <input type='password' class='form-control' id='txtPassword'>  
                        </div>
                       <div class='form-group text-left'>
                            <label class='control-label' for='txtConfirm'>
                                Confirm Password:
                            </label>     
                            <input type='password' class='form-control' id='txtConfirm'>  
                        </div>
                        <div class='form-group'>
                        <label class='checkbox-inline' for='chkForce'>
                        <input type='checkbox' id='chkForce' />
                            <b>Force member to change password on next login</b>
                        </label><br/><br/>
                        <div/>
                        <div class='form-group'>
                            <label for='txtAuth'>
                                Enter Admin password to authorize and submit:
                            </label>
                            <input class='form-control' type='password' id='txtAuth'/><br/>
                        </div>
                        <div class='form-group'>
                            <button class='btn btn-default' id='btnReset'>Submit</button>
                        </div>
                        <div>Passwords must contain:
                            <UL class='list-group pw-regex'>
                                <li class='list-group-item'>1 Upper case Character</li>
                                <li class='list-group-item'>1 Lower case Character</li>    
                                <li class='list-group-item'>1 Number</li>
                                <li class='list-group-item'>8 characters minimum</li>    
                            </UL>
                        </div>";
  
            case 'delete':
                return "<h3>Delete Selected Member</h3>
                        ".memberSelect()."<br/>
                        <div class='form-group'>
                             <label for='txtAuth'>
                                Enter Admin password to authorize and submit:
                            </label>
                            <input class='form-control' type='password' id='txtAuth'/><br/>
                        </div>
                        <div class='form-group'>
                            <button class='btn btn-default' id='btnDelete'>Submit</button>
                        </div>";
  
            default:
                break;
        }
    }
    return "<h3>Add Member Account</h3>   
        <div class='form-group text-left'>
            <label class='control-label' for='txtName'>
                User Name:
            </label>     
            <input type='text' class='form-control' id='txtName'>  
        </div>
        <div class='form-group text-left'>
            <label class='control-label' for='txtPassword'>
                Password:
            </label>     
            <input type='password' class='form-control' id='txtPassword'>  
        </div>
       <div class='form-group text-left'>
            <label class='control-label' for='txtConfirm'>
                Confirm Password:
            </label>     
            <input type='password' class='form-control' id='txtConfirm'>  
        </div>
        <br/>
        <div class='form-group'>
             <label for='txtAuth'>
                Enter Admin password to authorize and submit:
            </label>
            <input class='form-control' type='password' id='txtAuth'/><br/>
        </div>
        <div class='form-group'>
            <button class='btn btn-default' id='btnAddMember'>Submit</button>
        </div>";
}

?>