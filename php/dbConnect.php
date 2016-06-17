<?php
//    BAS-IS Portfolio Website
//    Newbs Unit'd
//    author: Paul Kempen
//    date: 1/18/2016

    //dbConncet
    //contains database connection informantion
    //returns: mysqli database connection object
    function dbConnect()
    {
        
        $servername = getenv('NEWBS_SERVER');
		$username = getenv('NEWBS_USER');
		$password = getenv('NEWBS_PW');
		$dbname = getenv('NEWBS_DB');

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($conn->connect_error)
		{
			die("Connection failed: " . $conn->connect_error);
		}
		else
		{
			return $conn;
		}
    }
?>