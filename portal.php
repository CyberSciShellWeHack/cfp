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

		<h2 class="text-center">C-Sides 2024 Presentation Proposals</h2>

<?php include 'lib.php';?>
<?php
	$token = "";
	if (array_key_exists("token",$_COOKIE)) {
		$token = $_COOKIE["token"];	
	}
						
	if ($token == "") {
		header("Location: /index.php");
		exit;
	}
	
	$user_info = decode_token($token);

	if (is_null($user_info)) {
		header("Location: /index.php");
		exit;
	}

	$userid = get_user_id($user_info['username']);
	if (is_null($userid)) {
		header("Location: /index.php");
		exit;
	}

	echo "<span class=\"text-right\"><span class=\"bold\">Welcome ". $user_info['fullname'] . 
			" | </span>&nbsp;<a href=\"/index.php\">Logout</a></span><br>";

	$user_folder = get_slides_folder($userid);
	if (!is_dir($user_folder)) {
		mkdir($user_folder);
	}

	// Process creation of new presentation and slide upload
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$slides_path = $user_folder . $_FILES['slides']['name'];
		
		if (!move_uploaded_file($_FILES['slides']['tmp_name'], $slides_path) ||
			!add_presentation($userid, $_POST['title'], $_POST['abstract'], $_FILES['slides']['name'])) {
				http_response_code(401);
				echo "<h3 class=\"error\">Error uploading presentation!</h3>";
		}
	}

	// Process deletion of presentation
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if (array_key_exists('delete',$_GET)) {
			delete_presentation($userid, $_GET['delete']);
			header("Location: /portal.php");
			exit;
		}
	}
?>
			<table class="pres">
				<tr>
					<th>Title</th>
					<th>Abstract</th>
					<th>Slides</th>
				</tr>
			<?php	
				// List existing presentations
				$pres = get_presentations($userid);
				foreach ($pres as $row) {
					$slides_path = $user_folder . $row['slides'];
					echo "<tr><td class=\"text-center\">" . $row['title'] . "</td><td>" . $row['abstract'] . "</td><td>" 
						. "<span class=\"flex-center\"><a href=\"" . $slides_path . "\">" . $row['slides'] . "</a> <a href=\"/portal.php?delete=" . $row['id'] . "\"><img src=\"delete.png\"></a></span></td></tr>";
				} 
			?>
			</table>
			
			<br>
							
				<table class="cage">
					<form action="/portal.php" method="post" enctype="multipart/form-data">
						<tr><td colspan=2><p>Submit a presentation proposal:</p></td></tr>
						<tr><td><label>Title: </label></td><td><input type="text" name="title"></td></tr>
						<tr><td><label>Abstract: </label></td><td><textarea name="abstract" rows="4" cols="50"></textarea></td></tr>
						<tr><td><label>Slides: </label></td><td><input type="file" name="slides"></td></tr>
						<tr><td colspan=2><br><input class="center" type="submit" value="Submit"></td></tr>
					</form> 
				</table>
			</body>
</html>