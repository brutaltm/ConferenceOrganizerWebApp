<?php
session_start();
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
	exit;
}

if (!isset($_SESSION["id"])) {
	header("Location: rezerwacje.php");
	exit;
}
$sql = "SELECT uzytk_id,czas_od,czas_do,kiedy FROM rezerwacje WHERE rez_id = {$_GET["id"]} AND oplacona = 0";
$result = $conn->query($sql);
if ($result->num_rows > 0)
	$row = $result->fetch_assoc();
else {
	header("Location: rezerwacje.php");
	exit;
}
if (($row["uzytk_id"] != $_SESSION["id"]) && $_SESSION["typ"] < 100) {
	header("Location: rezerwacje.php");
	exit;
}
$sql = "SELECT czas_od FROM rezerwacje WHERE kiedy='{$row["kiedy"]}' AND oplacona = 1 AND ((czas_od >= '{$row["czas_od"]}' AND czas_od < '{$row["czas_do"]}') OR (czas_do > '{$row["czas_od"]}' AND czas_do <= '{$row["czas_do"]}'));";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	header("Location: rezerwacje.php");
	exit;
}
else {
}
$sql = "UPDATE rezerwacje SET oplacona = 1 WHERE rez_id = '{$_GET["id"]}'"; 
if ($conn->query($sql)=== TRUE) {
	header("Location: rezerwacje.php");
	exit;
}
else 
	echo "Czemu?";