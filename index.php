<?php

session_start(); // Connect to the existing session
require_once '/home/common/mail.php'; // Add email functionality to program
processPageRequest(); // Call the processPageRequest() function

function addMovieToCart($movieID) {
	$array = readMovieData();	
	$array[] = $movieID;
	writeMovieData($array);
	displayCart();
}

function checkout($display_name, $address) {	
	$array = readMovieData();
	require_once('templates/cart_form.html');
	$message = "";
	$message .= "<strong>This is your receipt for the ". count($movies) . " movie(s) you purchased from myMovies Express!</strong><br><br<br>
		<br><table width='100px' cellpadding='2' cellspacing='0'>
	        <tr>
			<th><strong>Poster</strong></th>
			<th><strong>Title(year)</strong></th>	
		</tr><br>";
	foreach($movies as $movie) {
			$movie = file_get_contents('http://www.omdbapi.com?apikey=4d502d07&i=' . $movie . '&type=movie&r=json');
                	$array = json_decode($movie, true);
                	$id_string = $array["imdbID"];
                	$title = $array['Title'];
                	$poster = $array['Poster'];
                	$year = $array['Year'];
		$message .= "<tr>";	
		$message .= "<td><img src=" .  $poster . " height='100px'></td><br>";
		$message .= "<td><a href='https://www.imdb.com/title/" . "$id_string" . "/'" . "target='_blank'>" . $title . " " . "(" . $year . ")" . "</a></td><br>";	
		$message .= "<br>";
		$message .= "</tr>";
	}
	$message .= "</table><br>";
	
	$result = sendMail(342376999, $address, $display_name, "Your Receipt from myMovies!", $message); 		
	switch ($result) {
		case 0:
			echo "<p style='color:red;'>The email message was sent to </p>" . $address . "<p style='color:red;' succesfully </p>";
			break;
		case ($result > 0 && $result < 60):
			echo $result . " time (in seconds) that remains before the next email can be sent";
			break;
		case -1:
			echo "An error occured while sending the email message (email not sent)";
			break;
		case -2:
			echo "An invalid " . $address . "  was provided (email not sent)";
			break;
		case -3:
			echo "An error occured while accessing the database (email not sent)";
			break;
	}	

}

function displayCart() {	
	$array = readMovieData();
	require_once('templates/cart_form.html');	
}

function processPageRequest() {
	
	if (isset($_GET) && isset($_GET['action'])) {		
		if ($_GET['action'] === 'add')
			addMovieToCart($_GET['movie_id']);
		if ($_GET['action'] === 'checkout')
			checkout($_SESSION['display_name'], $_SESSION['email']);
		if ($_GET['action'] === 'remove')
			removeMovieFromCart($_GET['movie_id']);
	}
	else
		displayCart();	
}

function readMovieData() {
	$array = array_map('str_getcsv', file('data/cart.db'));
	return $array != null ? $array[0] : [];
}

function removeMovieFromCart($movieID) {
	$movies = readMovieData();
	for($i=0; $i<count($movies); $i++) {
		if ($movies[$i] === $movieID)
			unset($movies[$i]);
	}
	writeMovieData($movies);
	displayCart();
}

function writeMovieData($array) {	
	$csv = implode(",", $array);
	file_put_contents("./data/cart.db", $csv);
}

?>
