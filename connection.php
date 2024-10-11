<?php 
$server="localhost";
$username="root";
$password="";
$db="elms";


$con = new mysqli($server, $username, $password, $db);
if($con->connect_error){
	die("connection failed! ".$con->connect_error);
}else{
	//echo "Database connected!";
}
?>