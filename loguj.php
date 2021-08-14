<?php
session_start();
if ( !($_POST["haslo"]==="" || $_POST["email"]==="") ) {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "projektzbaz";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		header("Location: index.php?#conn_err");
	}
	else {
		$sql = "SET NAMES 'UTF8'";
		$conn->query($sql);
		//$haslo = password_hash($_POST["haslo"], PASSWORD_DEFAULT);
		$sql = "SELECT uzytk_id, nick, haslo, typ_konta FROM uzytkownicy WHERE email = '{$_POST["email"]}'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				//if (password_verify($_POST["haslo"], $row["haslo"])) {
				if ($_POST["haslo"] === $row["haslo"]) {
					$_SESSION["id"] = $row["uzytk_id"];
					$_SESSION["nick"] = $row["nick"];
					$_SESSION["typ"] = $row["typ_konta"];
				}
				else
					header('Location: ' . $_SERVER['HTTP_REFERER'] . "?#bad_login");
			}
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} 
		else {
			header('Location: ' . $_SERVER['HTTP_REFERER'] . "?#bad_login");
		}
	}
}
else {
	header('Location: ' . $_SERVER['HTTP_REFERER'] . "?#brak_danych");
}
$conn->close();
exit;
?>