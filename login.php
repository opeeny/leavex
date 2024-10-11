<?php 
session_start();
require_once 'connection.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = $_POST["username"];
	$password = $_POST["password"];
	$usertype = $_POST["userType"];
	
	if($usertype === 'admin'){
		$table = 'admin';
		//$sql="SELECT * FROM admin WHERE username=?";	
	}else{
		$table = 'emp';
		//$sql="SELECT * FROM employee WHERE username=?";
	}
	//$sql="SELECT * FROM $table WHERE username = ?";
	$sql = "SELECT * FROM " . $table . " WHERE username = ?";
	$stmt=$con->prepare($sql);
	$stmt->bind("s", $username);
	$stmt->execute();
	$result=$stmt->get_result();
	if($result->num_rows == 1){
		$user=$result->fetch_assoc();
		if(password_verify($password, $user['password'])){
			$_SESSION["uid"] = $user["id"];
			$_SESSION["username"] = $user["username"];
			$_SESSION["userType"] = $user["userType"];
			echo json_encode(["success" => true, "userType" => $usertype]);
		}else{
			echo json_encode(["success" => false, "message" => "Invalid username or password"]);
		}
	}else {
		echo json_encode(["success" => false, "message" => "Invalid username or password"]);
	}
	$stmt->close();
	$con->close();
}
?>