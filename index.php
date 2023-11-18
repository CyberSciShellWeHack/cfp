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

		<h2 class="text-center">Welcome to C-Sides 2024 Call for Presentations</h2>

<?php include 'lib.php';?>
<?php

	// Sign in to the account
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!array_key_exists('username',$_POST) || !array_key_exists('password',$_POST)) {

			http_response_code(401);
			echo "<h3 class=\"error\">Please provide username and password!</h3>";

		} else {
			$username = $_POST['username'];
			$password = $_POST['password'];
			
			$token = sign_in($username, $password);
			
			if (!is_null($token)){

				setcookie("token", $token);
				header("Location: /portal.php");
				exit;

			} else {

				http_response_code(401);
				echo "<h3 class=\"error\">Invalid login!</h3>";

			}
		}
	} else {
		setcookie("token", "");
	}
?>
		<br>
	
		<h3 class="text-center">Please sign in</h3>
		<br>

		<table class="cage">
			<form class="text-center" action="/index.php" method="post">
				<tr><td><label>Username: </label></td><td><input type="text" name="username"></td></tr>
				<tr><td><label>Password: </label></td><td><input type="password" name="password"></td></tr>
				<tr><td colspan=2><br><input class="center" type="submit" value="Login"></td></tr>
			</form> 
		</table>
		
		<br>
		
		<a class="flex-center" href="/signup.php">I don't have an account</a>
	
	</body>
</html>