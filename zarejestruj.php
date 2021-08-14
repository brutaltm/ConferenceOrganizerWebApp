<?php
session_start();
if (!($_POST["nick"]==="" || $_POST["haslo"]==="" || $_POST["email"]==="")) {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "projektzbaz";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		//die("Connection failed: " . $conn->connect_error);
		header("Location: index.php?#conn_err");
	}
	else {
	$sql = "SET NAMES 'UTF8'";
	$conn->query($sql);
	//$haslo = password_hash($_POST["haslo"], PASSWORD_DEFAULT);
	}
	// SPRAWDZENIE
	$sql = "SELECT nick FROM uzytkownicy WHERE nick = '{$_POST["nick"]}'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$conn->close();
		header("Location: index.php?#nick_taken");
	}
	else {
		$sql = "SELECT email FROM uzytkownicy WHERE email = '{$_POST["email"]}'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$conn->close();
			header("Location: index.php?#email_taken");
		}
		else {
			//$haslo = password_hash($_POST["haslo"], PASSWORD_DEFAULT);
			$sql = "INSERT INTO uzytkownicy (nick,imie,nazwisko,haslo,email,telefon,data_ur) " .
			"VALUES ('{$_POST["nick"]}','{$_POST["imie"]}','{$_POST["nazwisko"]}','{$_POST["haslo"]}','{$_POST["email"]}','{$_POST["telefon"]}','{$_POST["data_ur"]}')";
			if ($conn->query($sql) === TRUE) {
				$sql = "SELECT uzytk_id FROM uzytkownicy WHERE nick = '{$_POST["nick"]}';";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$_SESSION["id"] = $row["uzytk_id"];
					$_SESSION["nick"] = $_POST["nick"];
					$_SESSION["typ"] = 1;
					echo $_SESSION["id"] . $_SESSION["nick"];
					$conn->close();
				}
				header('Location: ' . $_SERVER['HTTP_REFERER']);
			} 
			else {
				$conn->close();
				header("Location: index.php?#reg_error");
			}
		}
	}
}
?>