<?php require("head.php"); 
if (isset($_SESSION["typ"]) && $_SESSION["typ"] >= 100) {

if (isset($_GET["usunop_id"])) {
	$sql = "DELETE FROM dodatkowe_oplaty WHERE oplata_id = '{$_GET["usunop_id"]}';";
	if ($conn->query($sql) === TRUE) { ?>
		<h4>Pomyślnie usunięto opłatę</h4> <?php
	}
	else { ?>
		<h4>Nie udało się usunąć opłaty</h4> <?php
	}
}
		
if (isset($_POST["submit"])) {
	$sql = "INSERT INTO dodatkowe_oplaty (obiekt_id,nazwa,kwota,kiedy) VALUES ('{$_POST["obiekt"]}','{$_POST["nazwa"]}','{$_POST["kwota"]}','{$_POST["kiedy"]}');";
	if ($conn->query($sql) === TRUE) { ?>
		<h4>Pomyślnie wprowadzono opłatę</h4> <?php
	}
	else { ?>
		<h4>Wprowadzenie opłaty nie powiodło się</h4> <?php
	}
} 

if (isset($_POST["edytuj"])) {
	$sql = "UPDATE dodatkowe_oplaty SET nazwa='{$_POST["nazwa"]}',kwota='{$_POST["kwota"]}',kiedy='{$_POST["kiedy"]}' WHERE oplata_id = '{$_POST["oplata_id"]}';";
	if ($conn->query($sql) === TRUE) { ?>
		<h4>Pomyślnie edytowano</h4> <?php
	} 
	else { ?>
		<h4>Nie udało się edytować</h4> <?php
	}
}

if (isset($_GET["data_od"])) {
	$sql = "SELECT oplata_id,o.nazwa AS 'obiekt', do.nazwa, kwota, kiedy FROM dodatkowe_oplaty do INNER JOIN obiekty o ON (do.obiekt_id = o.obiekt_id) WHERE kiedy >= '{$_GET["data_od"]}' AND kiedy <= '{$_GET["data_do"]}' ORDER BY kiedy DESC;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) { ?>
		<table><tr><th>Obiekt</th><th>Opłata</th><th>Kwota</th><th>Data</th><th colspan='2'>Akcja</th></tr> <?php
		while ($row = $result->fetch_assoc()) { 
			if (isset($_GET["editop_id"]) && ($_GET["editop_id"] == $row["oplata_id"])) { ?>
				<form method='post' action='oplaty.php?data_od=<?php echo $_GET["data_od"]; ?>
				&data_do=<?php echo $_GET["data_do"]; ?>'>
				<input type='hidden' name='oplata_id' value='<?php echo $row["oplata_id"]; ?>'
				<tr><td><?php echo $row["obiekt"]; ?></td><td><input type='text' name='nazwa' maxlength='32' value='<?php echo $row["nazwa"]; ?>'></td><td><input type='number' name='kwota' value='<?php echo $row["kwota"]; ?>'></td><td><input type='date' name='kiedy' value='<?php echo $row["kiedy"]; ?>'></td><td colspan='2'><input type='submit' name='edytuj' value='Zatwierdź'></td></tr></form> <?php
			} else { ?>
				<tr><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["kwota"]; ?> PLN</td><td><?php echo $row["kiedy"]; ?></td><td><a href='oplaty.php?data_od=<?php echo $_GET["data_od"]; ?>&data_do=<?php echo $_GET["data_do"]; ?>&editop_id=<?php echo $row["oplata_id"]; ?>'>Edytuj</a></td><td><a href='oplaty.php?data_od=<?php echo $_GET["data_od"]; ?>&data_do=<?php echo $_GET["data_do"]; ?>&usunop_id=<?php echo $row["oplata_id"]; ?>'>Usuń</a></td></tr> <?php
			}
		}
	}
} 
?>
<form method='get'>
Wyświetl opłaty: <br>Okres: <input type='date' name='data_od' value='<?php echo date("Y-m-d"); ?>'> - <input type='date' name='data_do' value='<?php echo date("Y-m-d"); ?>'><br><input type='submit' value='Wyświetl'><br></form><br>

<form method='post'>
<b>Wprowadzanie nowej opłaty:</b><br>
Obiekt: <select name='obiekt'> <?php 
$sql = "SELECT obiekt_id, nazwa FROM obiekty WHERE obiekt_id > 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) { ?>
		<option value='<?php echo $row["obiekt_id"]; ?>'><?php echo $row["nazwa"]; ?></option> <?php
	}
} ?>
</select><br>Nazwa opłaty: <input type='text' name='nazwa' maxlength='32'><br>Kwota: <input type='number' name='kwota' value='0'><br>Data: <input type='date' name='kiedy' value='<?php echo date("Y-m-d"); ?>'><br><input type='submit' name='submit' value='Dodaj'></form>
</body></html> <?php
}
