<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$name = $_POST["name"];
$email = $_POST["email"];
$psw = $_POST["psw"];

$sql = "INSERT INTO users(name,email,psw)
VALUES ('$name', '$email','$psw')";

if ($conn->query($sql) == TRUE) {
	$message='Registration successful! Please login to continue.';
    	echo "<script type='text/javascript'>alert('$message');</script>";
	echo "<script language='javascript' type='text/javascript'> location.href='index.html#login' </script>";   
} else {
    	echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
 

