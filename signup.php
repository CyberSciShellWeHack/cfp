<!DOCTYPE html>
<!--

CyberSci Regionals 2023/24

CFP Service by 0xd13a

-->

<html lang="en">
	<head>
		<meta charset=UTF-8>
		<link rel="stylesheet" href="styles.css">
		<title>C-Sides 2024: Call for Presentations</title>
	</head>
    <body>
		<img class="center" src="logo.png">

		<h2 class="text-center">Create an account to submit a presentation</h2>
		
<?php include 'lib.php';?>
<?php

	// Create a new user record
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!array_key_exists('username',$_POST) 
		 || !array_key_exists('password',$_POST) 
		 || !array_key_exists('password2',$_POST) 
		 || !array_key_exists('fullname',$_POST) 
		 || !array_key_exists('email',$_POST)) {

			http_response_code(401);
			echo "<h3 class=\"error\">Invalid parameters!</h3>";

		} elseif ($_POST['password'] != $_POST['password2'])  {

			http_response_code(401);
			echo "<h3 class=\"error\">Passwords do not match!</h3>";

		} else {
			$username = $_POST['username'];
			$password = $_POST['password'];
			$password2 = $_POST['password2'];
			$fullname = $_POST['fullname'];
			$email = $_POST['email'];			
			
			if (register_user($username, $password, $fullname, $email)) {
				
				header("Location: /index.php");
				exit;

			} else {
				http_response_code(401);
				echo "<h3 class=\"error\">Unable to register user!</h3>";
			}
		}
	}
?>
		<table class="cage">
			<form class="text-center" action="/signup.php" method="post">
				<tr><td><label>Username: </label></td><td><input type="text" name="username"></td></tr>
				<tr><td><label>Password: </label></td><td><input type="password" name="password"></td></tr>
				<tr><td><label>Repeat password: </label></td><td><input type="password" name="password2"></td></tr>
				<tr><td><label>Full name: </label></td><td><input type="text" name="fullname"></td></tr>			
				<tr><td><label>E-mail: </label></td><td><input type="text" name="email"></td></tr>
				<tr><td colspan=2><br><input class="center" type="submit" value="Create account"></td></tr>
			</form> 
		</table>
	</body>
</html>