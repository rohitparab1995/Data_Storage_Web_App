<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";
$conn = new mysqli($servername, $username, $password, $dbname);

session_start();
$_SESSION['email'] = $_POST['email'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$email = $_POST['email'];
$pw = $_POST['psw'];

$query = $conn->query("SELECT * FROM users WHERE email='$email' and psw='$pw'");
$rowcount=$query->num_rows;

if($rowcount!=0){
	$message='Login successful!';
    	echo "<script type='text/javascript'>alert('$message');</script>";
	echo "<script language='javascript' type='text/javascript'> location.href='upload.php' </script>";   
}else{
    	echo "<script type='text/javascript'>alert('User Name Or Password Invalid!')</script>";
	echo "<script language='javascript' type='text/javascript'> location.href='index.html' </script>";
}

?>
