<?php require "head.php"; ?>

<?php 
if (isset($_POST['submit'])) {
	if (!isset($_GET["id"]))
		$result = $conn->query("SELECT email FROM uzytkownicy WHERE uzytk_id!='{$_SESSION["id"]}' AND (email='{$_POST["email"]}' OR nick='{$_POST["nick"]}');");
	else
		$result = $conn->query("SELECT email FROM uzytkownicy WHERE uzytk_id!='{$_GET["id"]}' AND (email='{$_POST["email"]}' OR nick='{$_POST["nick"]}');");
	$czyIstnieje = $result->num_rows > 0;
	if (!$czyIstnieje) {
		$sql = "UPDATE uzytkownicy SET nick='{$_POST["nick"]}',imie='{$_POST["imie"]}',nazwisko='{$_POST["nazwisko"]}',email='{$_POST["email"]}',telefon='{$_POST["telefon"]}',data_ur='{$_POST["data_ur"]}'";
		if (isset($_GET["id"])) {
			if ($_SESSION["typ"] >= $_POST["typ"] && $_SESSION["typ"] >= 10)
				$sql = $sql . ",typ_konta={$_POST["typ"]} WHERE uzytk_id='{$_GET["id"]}';";
			else
				header("Location: index.php");
			if ($conn->query($sql) === TRUE) {
				echo "<h4>Dane zmienione pomyślnie</h4>";
			} 
			else
				echo "<h4>Błąd edycji danych</h4>";
		}
		else  {
			$sql = $sql . " WHERE uzytk_id='{$_SESSION["id"]}';";
			if ($conn->query($sql) === TRUE) {
				$_SESSION["nick"] = $_POST["nick"];
				echo "<h4>Dane zmienione pomyślnie</h4>";
			} 
			else
				echo "<h4>Błąd edycji danych</h4>";
		}
		
	}
	else
		echo "<h4>Błąd edycji danychasd</h4>";
}
if (isset($_SESSION["id"])) {
	if (!isset($_GET["id"])) {
		$sql = "SELECT * FROM uzytkownicy WHERE uzytk_id = '{$_SESSION['id']}'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
			$row = $result->fetch_assoc();
	}
	else {
		if ($_SESSION["typ"]>=10) {
			$sql = "SELECT * FROM uzytkownicy WHERE uzytk_id = '{$_GET['id']}';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0)
				$row = $result->fetch_assoc();
		}
	}
}
?>
<form method="post">
<table>
	<tr><th colspan='2'>Edycja danych osobowych</th></tr>
	<tr><td><label>Email:</label></td><td><input type="email" name="email" value=<?php echo htmlspecialchars($row["email"]); ?>></td></tr>
	<tr><td><label>Nick:</label></td><td><input type="text" name="nick" value=<?php echo htmlspecialchars($row["nick"]); ?>></td></tr>
	<tr><td><label>Imie:</label></td><td><input type="text" minlength="3" name="imie" value=<?php echo htmlspecialchars($row["imie"]); ?>></td></tr>
	<tr><td><label>Nazwisko:</label></td><td><input type="text" minlength="3" name="nazwisko" value=<?php echo htmlspecialchars($row["nazwisko"]); ?>></td></tr>
	<tr><td><label>Telefon:</label></td><td><input type="number" minlength="9" name="telefon" value=<?php echo $row["telefon"]; ?>></td></tr>
	<tr><td><label>Data urodzenia:</label></td><td><input type="date" name="data_ur" value=<?php echo $row["data_ur"]; ?>></td></tr>
<?php if (isset($_GET["id"])) { ?>
	<tr><td><label>Typ konta</label></td><td><select name="typ"><option value='1'>Użytkownik</option><option value='10'>Obsługa</option><option value='100'>Moderator</option><option value='1000'>Admin</option></select></td></tr>
<?php } ?>
	<tr><td colspan="2"><input type="submit" name='submit' value="Zatwierdź"></td></tr>
</table>
</form>
</body>
</html>