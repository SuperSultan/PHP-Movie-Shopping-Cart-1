<?php

processPageRequest();

function authenticateUser($username, $password) {
	
	$data = file_get_contents("data/credentials.db");
	$array = explode(",", $data);

	if ($array[0] === $_POST['username'] && $array[1] === $_POST['password']) {	
		session_start();	
		$_SESSION['display_name'] = $array[2];
		$_SESSION['email'] = $array[3];
		header('Location: ./index.php'); // Redirect to index.php
		return;
	} else {	
		$msg = "<p style='color:red;'>Error! Invalid username and/or password!</p>";	
		displayLoginForm($msg);
		return;
	} 

}

function displayLoginForm($message="") {	
	if ($_SESSION['result']) {
		echo $_SESSION['result'];
	}
	echo $message;
	require_once('templates/logon_form.html');
}

function processPageRequest() {
	session_destroy();
	if ( !empty($_POST) ) {
		authenticateuser($_POST['username'], $_POST['password']);
	}
	else 
		displayLoginForm();
} 

?>
