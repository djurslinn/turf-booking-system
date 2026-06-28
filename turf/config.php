<?php
	$conn=new mysqli("localhost","root","","turf");
		if($conn->connect_error){
			die("connection failed".$conn->connect_error);
		}
		
?>
	