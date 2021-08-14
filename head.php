<!doctype html>
<?php session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projektzbaz";

$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SET NAMES 'UTF8'";
$conn->query($sql);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} ?>
<html lang="pl">
	<head>
	<meta charset="utf-8">
	<title>Organizacja konferencji online</title>
	<style> 
		a:visited {
			color:blue;
		}
	</style>
	<script>
		function pokazLog() {
			document.getElementById("przycisklog").style = "text-decoration: underline; font-weight: bold;";
			document.getElementById("przyciskrej").style = "text-decoration: none; font-weight: normal;";
			document.getElementById("rejestracja").style.display = "none";
			document.getElementById("logowanie").style.display = "block";
		}
		
		function pokazRej() {
			document.getElementById("przycisklog").style = "text-decoration: none; font-weight: normal;";
			document.getElementById("przyciskrej").style = "text-decoration: underline; font-weight: bold;";
			document.getElementById("logowanie").style.display = "none";
			document.getElementById("rejestracja").style.display = "block";
		}
	</script>
	</head>
	<body>
	<h1><a href='index.php' style="text-decoration: none;">Organizacja konferencji</a></h1>
<?php 
if (isset($_SESSION["nick"])) {
	
	if ($_SESSION["typ"] == 1000)
		$t = "admin";
	else if ($_SESSION["typ"] == 100)
		$t = "moderator";
	else if ($_SESSION["typ"] == 10)
		$t = "obsługa";
	else
		$t = "uzytkownik";
	
	echo "Zalogowany jako <b>" . $t . "</b>: <span style='text-decoration: underline; color: green;'><i>" . $_SESSION["nick"] . "</i></span><br>
	<a id='wyloguj' href='wyloguj.php'>Wyloguj</a><br>";
} else { ?>
	Aby uzyskać dostęp do pełni funkcjonalności strony <br>
	<a href="#" id="przycisklog" style = "text-decoration: none;" onclick='pokazLog()'>Zaloguj się</a> lub 
	<a href="#" style = "text-decoration: none;" id="przyciskrej" onclick='pokazRej()'>Zarejestruj</a><br>
		<div id="logowanie" style="display: none;">
			<form action="loguj.php" method="post">
			<table>
				<tr><th colspan='2'>Logowanie</th></tr>
				<tr><td><label>Email:</label></td><td><span><input type="text" minlength="3" name="email" required></td></tr>
				<tr><td><label>Hasło:</label></td><td><span><input type="password" minlength="5" name="haslo" required></td></tr>
				<tr><td colspan="2"><input type="submit" value="Zaloguj"></td></tr>
			</table>
			</form>
		</div>
		<div id="rejestracja" style="display: none;">
			<form action="zarejestruj.php" method="post">
			<table>
				<tr><th colspan='2'>Rejestracja</th></tr>
				<tr><td><label>Email:</label></td><td><input type="email" name="email" required></td></tr>
				<tr><td><label>Nick:</label></td><td><input type="text" name="nick" required></td></tr>
				<tr><td><label>Hasło:</label></td><td><input type="password" minlength="5" name="haslo" required></td></tr>
				<tr><td><label>Imie:</label></td><td><input type="text" minlength="3" name="imie"></td></tr>
				<tr><td><label>Nazwisko:</label></td><td><input type="text" minlength="3" name="nazwisko"></td></tr>
				<tr><td><label>Telefon:</label></td><td><input type="number" minlength="9" name="telefon"></td></tr>
				<tr><td><label>Data urodzenia:</label></td><td><input type="date" name="data_ur"></td></tr>
				<tr><td colspan="2"><input type="submit" value="Zarejestruj"></td></tr>
			</table>
			</form>
		</div>
<?php } ?>