<?php

/*

CyberSci Regionals 2023/24

CFP Service by 0xd13a

*/

// Prepares folder with slides
function get_slides_folder($uid) {
	return 'slides/' . $uid . "/";
}

// Decode and check the token
function decode_token($token) {
	$arr = json_decode(base64_decode($token), true);

	if ($arr !== NULL) {
		$to_check = $arr['username'] . ":" . $arr['fullname'] . ":" . $arr['nonce'];

		// Verify the signature
		if ($arr['signature'] === hash_hmac('sha256', $to_check, 'IASJDIAJ9DSJ9ADJ9AJDJA29DJA2')) {
			return $arr;
		}
	}
	return NULL;
}

// Create and sign the token
function build_token($username, $fullname) {
	$nonce = bin2hex(random_bytes(16));

	$to_sign = $username . ":" . $fullname . ":" . $nonce;

	// Sign the token so that it can't be forged
	$signature = hash_hmac('sha256', $to_sign, 'IASJDIAJ9DSJ9ADJ9AJDJA29DJA2');

	$arr = array('username' => $username, 'fullname' => $fullname, 'nonce' => $nonce, 'signature' => $signature);

	return base64_encode(json_encode($arr));
}

// Create new user record
function register_user($username, $password, $fullname, $email) {
	$db = open_database();

	$statement = $db->prepare('REPLACE INTO users (username, password, fullname, email)
    	VALUES (:username, :password, :fullname, :email)');
	$statement->bindValue(':username', $username);
	$statement->bindValue(':password', $password);
	$statement->bindValue(':fullname', $fullname);
	$statement->bindValue(':email', $email);
	$result = $statement->execute();
	if ($result === false) {
		return FALSE;
	}

	close_database($db);
	
	return TRUE;
}

// Find user ID from database
function get_user_id($username) {
	$db = open_database();
	
	$statement = $db->prepare('SELECT id FROM users WHERE username = ?');
	$statement->bindValue(1, $username);
	$result = $statement->execute();
	if ($result === false) {
		return NULL;
	}

	$resultArray = $result->fetchArray(SQLITE3_ASSOC);
	
	if ($resultArray === false) {
		$result->finalize();
		close_database($db);
		return NULL;
	}

	$id = $resultArray['id'];
	
	unset($resultArray);

	$result->finalize();

	close_database($db);

	return $id;
}

// Sign into the portal
function sign_in($username, $password) {
	$db = open_database();	
	
	$statement = $db->prepare('SELECT * FROM users WHERE username = :username AND pwd = :password');
	$statement->bindValue(':username', $username);
	$statement->bindValue(':password', $password);

	$result = $statement->execute();

	if ($result === false) {
		close_database($db);
		return NULL;
	}

	$resultArray = $result->fetchArray(SQLITE3_ASSOC);

	// get fullname
	$fullname = $resultArray['fullname'];
	
	$token = build_token($username, $fullname);

	unset($resultArray);

	$result->finalize();
	close_database($db);

	return $token;
}

// Create a new presentation record
function add_presentation($user, $title, $abstract, $filename) {
	$db = open_database();

	$statement = $db->prepare('INSERT INTO presentations (userid, title, abstract, slides)
    	VALUES (:userid, :title, :abstract, :slides)');
	$statement->bindValue(':userid', $user);
	$statement->bindValue(':title', $title);
	$statement->bindValue(':abstract', $abstract);
	$statement->bindValue(':slides', $filename);
	$result = $statement->execute();
	if ($result === false) {
		return FALSE;
	}

	close_database($db);

	return TRUE;
}

// Load a list of presentations
function get_presentations($userid) {
	$db = open_database();
	
	$statement = $db->prepare('SELECT * FROM presentations WHERE userid = ?');
	$statement->bindValue(1, $userid);
	$result = $statement->execute();
	if ($result === false) {
		return array();
	}

	$resultArray = $result->fetchArray(SQLITE3_ASSOC);
	$multiArray = array();
	
	while ($resultArray !== false){
		array_push($multiArray, $resultArray);
		$resultArray = $result->fetchArray(SQLITE3_ASSOC);
	}
	
	unset($resultArray);

	$result->finalize();

	close_database($db);

	return $multiArray;
}

// Delete the presentation
function delete_presentation($userid, $presid) {
	$db = open_database();

	$statement = $db->prepare('SELECT * FROM presentations WHERE userid = ? and id = ?');
	$statement->bindValue(1, $userid);
	$statement->bindValue(2, $presid);
	$result = $statement->execute();
	if ($result === false) {
		return FALSE;
	}

	$resultArray = $result->fetchArray(SQLITE3_ASSOC);

	// Remove presentation slides
	unlink(get_slides_folder($userid) . $resultArray['slides']);

	$result->finalize();

	$statement = $db->prepare('DELETE FROM presentations WHERE userid = ? and id = ?');
	$statement->bindValue(1, $userid);
	$statement->bindValue(2, $presid);
	$result = $statement->execute();
	if ($result === false) {
		return FALSE;
	}

	$result->finalize();

	close_database($db);
	return TRUE;
}

// Create and open database
function open_database() {
	$db = new SQLite3('cfp.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

	$db->enableExceptions(true);

	$db->query('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    	username VARCHAR UNIQUE, password VARCHAR, fullname VARCHAR, email VARCHAR)');

	$db->query('CREATE TABLE IF NOT EXISTS presentations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    	userid INTEGER, title VARCHAR, abstract VARCHAR, slides VARCHAR)');

	return $db;
}

// Close database
function close_database($db) {
	$db->close();
}

?>