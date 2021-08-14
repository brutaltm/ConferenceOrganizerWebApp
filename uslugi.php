<?php require("head.php"); 
if (isset($_POST["submit"])) {
	if (isset($_SESSION["typ"]) && $_SESSION["typ"] >- 100) {
		$sql = "INSERT INTO uslugi (nazwa,opis) VALUES ('{$_POST["nazwa"]}','{$_POST["opis"]}');";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie dodano usługę</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas dodawania usługi</h4> <?php
		}
	}
	else { ?>
		<h4>Brak uprawnień</h4> <?php
	}
}

if (isset($_POST["edytujUsluge"])) {
	if (isset($_SESSION["typ"]) && $_SESSION["typ"] >- 100) {
		$sql = "UPDATE uslugi SET nazwa='{$_POST["nazwa"]}',opis='{$_POST["opis"]}' WHERE usluga_id = '{$_POST["u_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie edytowano usługę</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas edytowania usługi</h4> <?php
		}
	}
	else { ?>
		<h4>Brak uprawnień</h4> <?php
	}
	
}

if (isset($_GET["usunu_id"])) {
	if (isset($_SESSION["typ"]) && $_SESSION["typ"] >- 100) {
		$sql = "DELETE FROM uslugi WHERE usluga_id = '{$_GET["usunu_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie usunięto usługę</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas usuwania usługi</h4> <?php
		}
	}
	else { ?>
		<h4>Brak uprawnień</h4> <?php
	}
}

if (isset($_POST["dodSale"])) {
	if (isset($_SESSION["typ"]) && $_SESSION["typ"] >- 100) {
		$sql = "INSERT INTO dost_uslugi (usluga_id,sala_id,max_ilosc,cena,marza) VALUES ('{$_POST["u_id"]}','{$_POST["sala"]}','{$_POST["max_ilosc"]}','{$_POST["cena"]}','{$_POST["marza"]}');";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie dodano usługę do sali</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas dodawania usługi do sali</h4> <?php
		}
	}
	else { ?>
		<h4>Brak uprawnień</h4> <?php
	}
}

if (isset($_POST["edytujSale"]) && isset($_GET["u_id"])) {
	if (isset($_SESSION["typ"]) && $_SESSION["typ"] >- 100) {
		if ($_POST["marza"]>$_POST["cena"]) $marza = $_POST["cena"]; else $marza = $_POST["marza"];
		$sql = "UPDATE dost_uslugi SET max_ilosc='{$_POST["max_ilosc"]}',cena='{$_POST["cena"]}',marza='{$marza}' WHERE usluga_id = '{$_GET["u_id"]}' AND sala_id = '{$_POST["s_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie edytowano usługę dla sali</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas edycji usługi dla sali</h4> <?php
		}
	}
	else { ?>
		<h4>Brak uprawnień</h4> <?php
	}
}

if (isset($_GET["usuns_id"]) && isset($_GET["u_id"])) {
	if (isset($_SESSION["typ"]) && $_SESSION["typ"] >- 100) {
		$sql = "DELETE FROM dost_uslugi WHERE usluga_id = '{$_GET["u_id"]}' AND sala_id = '{$_GET["usuns_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie usunięto usługę</h4> <?php
		}
		else { ?>
			<h4>Błąd podczas usuwania usługi</h4> <?php
		}
	}
	else { ?>
		<h4>Brak uprawnień</h4> <?php
	}
}

$sql = "SELECT * FROM uslugi";
$result = $conn->query($sql);
if ($result->num_rows > 0) { ?>
	<table>
	<tr><th>ID</th><th>Nazwa</th><th>Opis</th><th colspan='2'>Akcja</th></tr> <?php
	while ($row = $result->fetch_assoc()) {
		if (isset($_GET["editu_id"]) && $_GET["editu_id"] == $row["usluga_id"]) { ?>
			<form method='post' action='uslugi.php?u_id=<?php echo $row["usluga_id"]; ?>'>
			<input type='hidden' name='u_id' value='<?php echo $row["usluga_id"]; ?>'>
			<tr><td><?php echo $row["usluga_id"]; ?></td><td><input type='text' name='nazwa' value='<?php echo $row["nazwa"]; ?>'></td><td><input type='text' name='opis' value='<?php echo $row["opis"]; ?>' style='width:100%;'></td><td colspan='2'><input type='submit' name='edytujUsluge' value='Zatwierdź'></td></tr></form> <?php
		}
		else { ?>
			<tr><td><?php echo $row["usluga_id"]; ?></td><td><a href='uslugi.php?u_id=<?php echo $row["usluga_id"]; ?>'><?php echo $row["nazwa"]; ?></a></td><td><?php echo $row["opis"]; ?></td><td><a href='uslugi.php?u_id=<?php echo $row["usluga_id"] . "&editu_id=" . $row["usluga_id"]; ?>'>Edytuj</a></td><td><a href='uslugi.php?usunu_id=<?php echo $row["usluga_id"]; ?>'>Usuń</a></td></tr> <?php
		}
	} ?>
	<form method='post'>
	<tr><td></td><td><input type='text' name='nazwa'></td><td><input type='text' name='opis' style='width:100%;'></td><td colspan='2'><input type='submit' name='submit' value='Dodaj usługę'></td></tr></form>
	</table> <?php
}
mysqli_data_seek($result,0);
/*
if ($result->num_rows >0) { ?>
	<table>
	<tr><th>Sala</th><th>Dostępna</th><th>Cena</th><th>Marża</th></tr> <?php
	while ($row = $result->fetch_assoc()) {
		$sql = "SELECT s.nazwa,du.usluga_id,du.cena,du.marza FROM sale s LEFT JOIN dost_uslugi du ON (du.usluga_id = '{$row["usluga_id"]}' AND s.sala_id = du.sala_id);";
		$result2 = $conn->query($sql);
		if ($result2->num_rows > 0) {
			
		}
		
	}
}
*/
if ($result->num_rows > 0) {
	$i = 0;
	while ($row = $result->fetch_assoc()) { 
		if ((isset($_GET["u_id"]) && $_GET["u_id"] == $row["usluga_id"]) || (!isset($_GET["u_id"]) && $i == 0)) { 
		$i++; ?>
		<table>
		<tr><th colspan='7'><?php echo "Dostępność: " . $row["nazwa"] . " - " . $row["opis"]; ?></th></tr>
		<tr><th>Sala</th><th>Obiekt</th><th>Cena</th><th>Marża</th><th>Maks. ilość</th><th colspan='2'>Akcja</th></tr> <?php
		$sql = "SELECT s.sala_id,s.nazwa,o.nazwa AS 'obiekt',du.cena,marza,max_ilosc FROM dost_uslugi du INNER JOIN sale s ON (du.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE usluga_id = '{$row["usluga_id"]}' GROUP BY du.sala_id;";
		$result2 = $conn->query($sql);
		if ($result2->num_rows > 0) { 
			while ($row2 = $result2->fetch_assoc()) { 
				if (isset($_GET["edits_id"]) && $_GET["edits_id"] == $row2["sala_id"]) { ?>
					<form method='post' action='uslugi.php?u_id=<?php echo $row["usluga_id"]; ?>'>
					<input type='hidden' name='s_id' value='<?php echo $row2["sala_id"]; ?>'>
					<tr><td><?php echo $row2["nazwa"]; ?></td><td><?php echo $row2["obiekt"]; ?></td><td><input type='number' name='cena' min='0' value='<?php echo $row2["cena"]; ?>' style='width:50px;'></td><td><input type='number' name='marza' min='0' value='<?php echo $row2["marza"]; ?>' style='width:50px;'></td><td><input type='number' name='max_ilosc' min='0' value='<?php echo $row2["max_ilosc"]; ?>' style='width:50px;'></td><td colspan='2'><input type='submit' name='edytujSale' value='Zatwierdź'></td></tr></form>
					<?php
				} 
				else {?>
					<tr><td><?php echo $row2["nazwa"]; ?></td><td><?php echo $row2["obiekt"]; ?></td><td><?php echo $row2["cena"]; ?></td><td><?php echo $row2["marza"]; ?></td><td><?php echo $row2["max_ilosc"]; ?></td><td><a href='uslugi.php?u_id=<?php echo $row["usluga_id"] . "&edits_id=" . $row2["sala_id"]; ?>'>Edytuj</a></td><td><a href='uslugi.php?u_id=<?php echo $row["usluga_id"] . "&usuns_id=" . $row2["sala_id"]; ?>'>Usuń</a></td></tr> <?php 
				}
			}
		}
		$sql = "SELECT sala_id,s.nazwa,o.nazwa AS 'obiekt' FROM sale s INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE s.sala_id != ALL(SELECT sala_id FROM dost_uslugi WHERE usluga_id = '{$row["usluga_id"]}');";
		$result2 = $conn->query($sql);
		if ($result2->num_rows > 0) { ?>
			<form method='post'>
			<input type='hidden' name='u_id' value='<?php echo $row["usluga_id"]; ?>'>
			<tr><td colspan='2'><select name='sala' style='width:100%;'> <?php
			while ($row2 = $result2->fetch_assoc()) { ?>
				<option value='<?php echo $row2["sala_id"]; ?>'><?php echo $row2["nazwa"] . " - " . $row2["obiekt"]; ?></option> <?php
			} ?>
			</select></td>
			<td><input type='number' name='cena' min='0' value='0' style='width:50px;'></td><td><input type='number' name='marza' min='0' value='0' style='width:50px;'></td><td><input type='number' name='max_ilosc' min='0' value='0' style='width:50px;'></td><td colspan='2'><input type='submit' name='dodSale' value='Dodaj'></td></tr></form>
			</table> <?php
		}
		}
	}
}