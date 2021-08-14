<?php require("head.php"); 
if (isset($_POST["submit"])) {
	if (isset($_SESSION["typ"])) {
		if ($_SESSION["typ"] >= 100) {
			$sql = "INSERT INTO sale (obiekt_id,nazwa,miejsca,cena) VALUES ('{$_POST["obiekt"]}','{$_POST["nazwa"]}','{$_POST["miejsca"]}','{$_POST["cena"]}');";
			if ($conn->query($sql) === TRUE) { ?>
				<h4>Pomyślnie dodano salę. Aby dodać obiekty przejdź do <a href='obiekty.php'>obiektów</a></h4> <?php
			}
			else { ?>
				<h4>Błąd dodawania sali</h4> <?php
			}
		}
	}
}

if (isset($_SESSION["typ"]) && isset($_GET["usuns_id"])) {
	if ($_SESSION["typ"] >= 100) {
		$sql = "DELETE FROM sale WHERE sala_id = '{$_GET["usuns_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie usunięto obiekt</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas usuwania obiektu</h4> <?php
		}
	}
	else { ?>
		<h4>Brak odpowiednich uprawnień</h4> <?php
	}
}

if (isset($_POST["edytuj"])) {
	if (!isset($_SESSION["typ"]) || $_SESSION["typ"] < 100) { ?>
		<h4>Brak odpowiednich uprawnień</h4> <?php
	}
	else {
		$sql = "UPDATE sale SET obiekt_id='{$_POST["obiekt"]}',nazwa='{$_POST["nazwa"]}',miejsca='{$_POST["miejsca"]}',cena='{$_POST["cena"]}' WHERE sala_id = '{$_POST["s_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie edytowano salę.</h4> <?php
		}
		else { ?>
			<h4>Błąd edycji sali</h4> <?php
		}
	}
}

$sql = "SELECT sala_id, s.obiekt_id, s.nazwa, o.nazwa AS 'obiekt', miejsca, cena, m.nazwa AS 'miejscowosc' FROM sale s INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id);";
$result = $conn->query($sql); ?>
<table>
	<tr><th>ID</th><th>Nazwa</th><th>Obiekt</th><th>Miejscowość</th><th>Miejsca</th><th>Cena</th><th colspan='2'>Akcja</th></tr> <?php
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) { 
		if (isset($_SESSION["typ"]) && isset($_GET["edits_id"]) && $_SESSION["typ"] >= 100 && ($_GET["edits_id"] == $row["sala_id"])) { ?>
			<form action='sale.php' method='post'>
			<input type='hidden' name='s_id' value='<?php echo $row["sala_id"]; ?>'>
			<tr><td><?php echo $row["sala_id"]; ?></td><td><input type='text' name='nazwa' maxlength='32' value='<?php echo $row["nazwa"]; ?>'>
			<td colspan='2'><select name='obiekt'><?php
			$sql = "SELECT obiekt_id, o.nazwa, m.nazwa AS 'miejscowosc' FROM obiekty o INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id) WHERE o.obiekt_id != 1;";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while ($row2 = $result2->fetch_assoc()) { ?>
					<option value='<?php echo $row2["obiekt_id"]; ?>' <?php if ($row2["obiekt_id"] == $row["obiekt_id"]) echo 'selected'; ?>><?php echo $row2["nazwa"] . ", " . $row2["miejscowosc"]; ?></option> <?php
				}
			}	?>
			</select></td><td><input type='number' min='0' name='miejsca' value='<?php echo $row["miejsca"]; ?>' required></td><td><input type='number' min='0' name='cena' value='<?php echo $row["cena"]; ?>' required></td><td colspan='2'><input type='submit' name='edytuj' value='Zatwierdź zmiany'></td></tr></form> <?php
		}
		else { ?>
		<tr><td><?php echo $row["sala_id"]; ?></td><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejscowosc"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["cena"] . " PLN/H"; ?></td><td><a href='sale.php?edits_id=<?php echo $row["sala_id"]; ?>'>Edytuj</a></td><td><a href='sale.php?usuns_id=<?php echo $row["sala_id"]; ?>'>Usuń</a></td></tr> 
<?php	} 
	}
} ?>
<form method='post'>
<tr><td></td><td><input type='text' name='nazwa' maxlength='32'></td>
<td colspan='2'><select name='obiekt'><?php
$sql = "SELECT obiekt_id, o.nazwa, m.nazwa AS 'miejscowosc' FROM obiekty o INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id) WHERE o.obiekt_id != 1;";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) { ?>
		<option value='<?php echo $row["obiekt_id"]; ?>'><?php echo $row["nazwa"] . ", " . $row["miejscowosc"]; ?></option> <?php
	}
}	?>
</select></td><td><input type='number' min='0' name='miejsca' required></td><td><input type='number' min='0' name='cena' value='50' required></td><td colspan='2'><input type='submit' name='submit' value='Dodaj'></td></tr></form>
</table>