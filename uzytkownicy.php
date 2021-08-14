<?php require("head.php");
if (isset($_SESSION["typ"])) {
	if ($_SESSION["typ"] >= 10) { 
	if (isset($_GET["usunu_id"])) {
		$sql = "SELECT typ_konta FROM uzytkownicy WHERE uzytk_id = '{$_GET["usunu_id"]}' AND typ_konta < '{$_SESSION["typ"]}';";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$sql = "DELETE FROM uzytkownicy WHERE uzytk_id = '{$_GET["usunu_id"]}';";
			echo $_GET["usunu_id"];
			if ($conn->query($sql) === TRUE) { ?>
				<h4>Usunięto użytkownika</h4> <?php
			}
			else { ?>
				<h4>Błąd podczas usuwania</h4> <?php
			}
		}
		else { ?>
			<h4>Brak uprawnień</h4> <?php
		}
	}?>
		<table>
			<tr><th>ID</th><th>Rejestracja</th><th>Rezerwacje</th><th>Nick</th><th>Imię</th><th>Nazwisko</th><th>E-Mail</th><th>Telefon</th><th>Data urodzenia</th><th colspan="2">Akcja</th></tr>
<?php	$sql = "SELECT COUNT(r.uzytk_id) AS 'ile',u.uzytk_id,u.nick,u.imie,u.nazwisko,u.haslo,u.email,u.telefon,u.data_ur,u.typ_konta,u.data_rejestracji FROM uzytkownicy u LEFT JOIN rezerwacje r ON (u.uzytk_id = r.uzytk_id) WHERE typ_konta < 10 GROUP BY (u.uzytk_id);";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) { ?>
				<tr><td><?php echo $row["uzytk_id"]; ?></td><td><?php echo $row["data_rejestracji"]; ?></td><td><?php echo $row["ile"]; ?></td><td><?php echo $row["nick"]; ?></td><td><?php echo $row["imie"]; ?></td><td><?php echo $row["nazwisko"]; ?></td><td><?php echo $row["email"]; ?></td><td><?php echo $row["telefon"]; ?></td><td><?php echo $row["data_ur"]; ?></td>
				<td><a href='edytujDane.php?id=<?php echo $row["uzytk_id"]; ?>'>Edytuj</a></td><td><a href='uzytkownicy.php?usunu_id=<?php echo $row["uzytk_id"]; ?>'>Usuń</a></td></tr>
<?php		}
		}
		
	}
}