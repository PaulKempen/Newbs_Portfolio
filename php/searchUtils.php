<?php 
    /**** includes ****/
    require_once 'dbConnect.php';
  
    /**** Session ****/   
    session_name('basisLogin');
    
    /**** Constants ****/
    define("MAX_PREVIEW_LENGTH", 150);//in characters
    
    /**** Mainline ****/
    
    
    /**** Functions ****/
    function addEntry($cat, $entry, $userId, $extra = "x")
    {
        $id = $cat."-".$userId."-".$extra;
             
        $sql="INSERT INTO site_index (index_id, entry, user_id, category, extra) 
              VALUES(?, ?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE entry= ?, user_id= ?, category= ?, extra= ?";
             
        $conn = dbConnect();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $id, $entry, $userId, $cat, $extra, 
                                            $entry, $userId, $cat, $extra);
        
        //execute query
		if($stmt->execute())
		{
			//success!
		}
		else
		{
			//error...
		}
		
		//close connections
		$stmt->close();
		$conn->close();
    }
    
    function deleteWorkEntries($workId)
    {
        $conn = dbConnect();

        $stmt = $conn->prepare("DELETE FROM site_index WHERE extra = ?");
		$stmt->bind_param("s", $workId);
		
        //execute query		
		if($stmt->execute())
        {
			//success!
		}
		else
		{
			//error...
		}
		
		//close connections
		$stmt->close();
		$conn->close();
    }
    
    function getResults($term, &$reply)
    {
        $reply['results'] = "<div id='searchDiv'><h4>You Searched for: \"".$term."\"</h4>";
        if(trim($term) === "")
        {
            $reply['results'] .= "<b> Matches found: 0</b>";
        }
        else 
        {
        
            $sql="SELECT a.entry, a.user_id, a.category, b.first, b.last 
                FROM site_index as a
                INNER JOIN member_info as b
                ON a.user_id = b.user_id  
                WHERE a.entry LIKE ?";
            $numRows = 0;
            
            $searchTerm = "%".$term."%";
            $conn = dbConnect();
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $numRows = $result->num_rows;
    
            $reply['results'] .= "<b> Matches found: ".$numRows."</b>";
            
            if($result && $numRows > 0)
            {
                $reply['results'] .= "<ul>";
                
                while($row = $result->fetch_assoc())
                {
                    $reply['results'] .= processResults($row, generatePreview($row['entry'], $term, $row['category']));
                }
                
                $result->close();
                
                $reply['results'] .= "</ul>";
            }
            else
            {
                //error...
            }
            
            //close connection
            $stmt->close();
            $conn->close();
        }
        $reply['results'] .="</div>";
    }
    
    function processResults($row, $preview)
    {
        $listItem = "<li><a class='body-links' href='individual.html?member=".$row['user_id']."'>member: ".$row['last'].", ".$row['first']." </a>
            <ul>
            <li>Category: ".$row['category']."</li><li> Preview: \"<i>".$preview."</i>\"</li>
            </ul>
        </li>";
        
        return $listItem;
    }
    
    function generatePreview($entry, $term, $cat)
    {
        $preview = "";
        $startIndex = -1;
        $entryLength = strlen($entry);
        $termLength = strlen($term);
 
        switch ($cat) {
            case 'about':
            case 'work-description':
            $startIndex = strpos(strtolower($entry), strtolower($term));
            if($entryLength > MAX_PREVIEW_LENGTH)
            {
                if($startIndex > MAX_PREVIEW_LENGTH)
                {
                    $entry = substr($entry, ($startIndex - (MAX_PREVIEW_LENGTH / 2)) , MAX_PREVIEW_LENGTH);
                    $entry = "...".$entry."...";
                }
                else
                { 
                    $entry = substr($entry,0,MAX_PREVIEW_LENGTH);
                    $entry .="...";
                }
            }
            default:
                $startIndex = strpos(strtolower($entry), strtolower($term));
                $entry = substr_replace($entry,"</b>",$startIndex + $termLength,0);
                $preview = substr_replace($entry,"<b>",$startIndex,0);
                break;
        }
        
        return $preview;
    }

?>