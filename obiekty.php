<?php require("head.php"); 
if (isset($_POST["submit"])) {
	if (isset($_SESSION["typ"])) {
		if ($_SESSION["typ"] >= 100) {
			$czynny_dni = 0;
			for ($i=1; $i<8; $i++) {
				if (isset($_POST[("D" . $i)]))
					$czynny_dni += $_POST[("D" . $i)];
			}
			//$czynny_dni = $_POST["Pn"] + $_POST["Wt"] + $_POST["Sr"] + $_POST["Cz"] + $_POST["Pt"] + $_POST["Sb"] + $_POST["Nd"]; 
			$sql = "INSERT INTO obiekty (miejscowosc_id,nazwa,adres,kod_pocztowy,czynny_od,czynny_do,czynny_dni) VALUES ('{$_POST["miejscowosc"]}','{$_POST["nazwa"]}','{$_POST["adres"]}','{$_POST["kod_pocztowy"]}','{$_POST["czynny_od"]}','{$_POST["czynny_do"]}','{$czynny_dni}');";
			if ($conn->query($sql) === TRUE) { ?>
				<h4>Pomyślnie dodano obiekt. Aby dodać sale przejdź do <a href='sale.php'>sal</a></h4> <?php
			}
			else { ?>
				<h4>Błąd dodawania obiektu</h4> <?php
			}
		}
	}
}
if (isset($_POST["dodMiejscowosc"])) {
	if (isset($_SESSION["typ"])) {
		if ($_SESSION["typ"] >= 100) {
			if ($_POST["woj_id"] == "Dodaj") {
				$sql = "INSERT INTO wojewodztwa (nazwa) VALUES ('{$_POST["nazwaWojewodztwo"]}');";
				if ($conn->query($sql) === TRUE) { 
					$sql = "SELECT wojewodztwo_id FROM wojewodztwa WHERE nazwa = '{$_POST["nazwaWojewodztwo"]}';";
					$result = $conn->query($sql);
					if ($result->num_rows > 0)
						$row = $result->fetch_assoc();
					$sql = "INSERT INTO miejscowosci (nazwa,wojewodztwo_id) VALUES ('{$_POST["nazwaMiejscowosc"]}','{$row["wojewodztwo_id"]}');";
					if ($conn->query($sql) === TRUE) { ?>
						<h4>Pomyślnie dodano miejscowość oraz województwo</h4> <?php
					}
					else { ?>
						<h4>Błąd dodawania miejscowości po pomyślnym dodaniu województwa</h4> <?php
					}
				}
				else { ?>
					<h4>Błąd dodawania województwa</h4> <?php
				}
			}
			else {
				$sql = "INSERT INTO miejscowosci (nazwa,wojewodztwo_id) VALUES ('{$_POST["nazwaMiejscowosc"]}','{$_POST["woj_id"]}');";
				if ($conn->query($sql) === TRUE) { ?>
					<h4>Pomyślnie dodano miejscowość.</h4> <?php
				}
				else { ?>
					<h4>Błąd dodawania miejscowości</h4> <?php
				}
			}
		}
	}
}
if (isset($_SESSION["typ"]) && isset($_GET["usuno_id"])) {
	if ($_SESSION["typ"] >= 100) {
		$sql = "DELETE FROM obiekty WHERE obiekt_id = '{$_GET["usuno_id"]}';";
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
		$czynny_dni = 0;
		for ($i=1; $i<8; $i++) {
			if (isset($_POST[("D" . $i)]))
				$czynny_dni += $_POST[("D" . $i)];
		}
		$sql = "UPDATE obiekty SET miejscowosc_id='{$_POST["miejscowosc"]}',nazwa='{$_POST["nazwa"]}',adres='{$_POST["adres"]}',kod_pocztowy='{$_POST["kod_pocztowy"]}',czynny_od='{$_POST["czynny_od"]}',czynny_do='{$_POST["czynny_do"]}',czynny_dni='{$czynny_dni}' WHERE obiekt_id = '{$_POST["o_id"]}';";
		if ($conn->query($sql) === TRUE) { ?>
			<h4>Pomyślnie edytowano obiekt.</h4> <?php
		}
		else { ?>
			<h4>Błąd edytowania obiektu</h4> <?php
		}
	}
}

$sql = "SELECT obiekt_id, o.miejscowosc_id, m.nazwa AS 'miejscowosc', o.nazwa, adres, kod_pocztowy, czynny_od, czynny_do, czynny_do, czynny_dni FROM obiekty o INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id);";
$result = $conn->query($sql); ?>
<table>
	<tr><th>ID</th><th>Nazwa</th><th>Adres</th><th>Kod pocztowy</th><th>Miejscowość</th><th>Czynny</th><th>Od</th><th>Do</th><th colspan='2'>Akcja</th></tr>
<?php
if ($result->num_rows > 0) { 
while ($row = $result->fetch_assoc()) {
		$czynny = "";
		if (($row["czynny_dni"] & 1) == 1)
			$czynny = "Pn";
		if (($row["czynny_dni"] & 2) == 2)
			$czynny .= ",Wt";
		if (($row["czynny_dni"] & 4) == 4)
			$czynny .= ",Śr";
		if (($row["czynny_dni"] & 8) == 8)
			$czynny .= ",Cz";
		if (($row["czynny_dni"] & 16) == 16)
			$czynny .= ",Pt";
		if (($row["czynny_dni"] & 32) == 32)
			$czynny .= ",Sb";
		if (($row["czynny_dni"] & 64) == 64)
			$czynny .= ",Nd";  
		
		if (isset($_SESSION["typ"]) && isset($_GET["edito_id"]) && $_SESSION["typ"] >= 100 && ($_GET["edito_id"] == $row["obiekt_id"])) { ?>
			<form action='obiekty.php' method='post'>
			<input type='hidden' name='o_id' value='<?php echo $row["obiekt_id"]; ?>'>
			<tr><td><?php echo $row["obiekt_id"]; ?></td><td><input type='text' name='nazwa' maxlength='32' value='<?php echo $row["nazwa"]; ?>'></td><td><input type='text' name='adres' maxlength='32' value='<?php echo $row["adres"]; ?>'></td><td><input type="text" name='kod_pocztowy' value='<?php for ($i=0;$i<(5-ceil(log10($row["kod_pocztowy"]+1))); $i++) echo "0"; echo $row["kod_pocztowy"]; ?>' pattern="[0-9]{5}"></td>
			<td><select name='miejscowosc'><?php
			$sql = "SELECT miejscowosc_id, nazwa FROM miejscowosci;";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while ($row2 = $result2->fetch_assoc()) { ?>
					<option value='<?php echo $row2["miejscowosc_id"]; ?>' <?php if ($row2["miejscowosc_id"] == $row["miejscowosc_id"]) echo 'selected'; ?>><?php echo $row2["nazwa"]; ?></option> <?php
				}
			}	?>
			</select></td>
			<td><input type="checkbox" name="D1" value="1" <?php if (($row["czynny_dni"] & 1) == 1) echo 'checked'; ?>><input type="checkbox" name="D2" value="2" <?php if (($row["czynny_dni"] & 2) == 2) echo 'checked'; ?>><input type="checkbox" name="D3" value="4" <?php if (($row["czynny_dni"] & 4) == 4) echo 'checked'; ?>><input type="checkbox" name="D4" value="8" <?php if (($row["czynny_dni"] & 8) == 8) echo 'checked'; ?>><input type="checkbox" name="D5" value="16" <?php if (($row["czynny_dni"] & 16) == 16) echo 'checked'; ?>><input type="checkbox" name="D6" value="32" <?php if (($row["czynny_dni"] & 32) == 32) echo 'checked'; ?>><input type="checkbox" name="D7" value="64" <?php if (($row["czynny_dni"] & 64) == 64) echo 'checked'; ?>></td>
			<td><input type="time" name="czynny_od" value='<?php echo $row["czynny_od"]; ?>' required></td><td><input type="time" name="czynny_do" value='<?php echo $row["czynny_do"]; ?>' required></td><td colspan='2'><input type='submit' name='edytuj' value='Zatwierdź zmiany'></td></tr></form> <?php
		}
		else {
		?>
		<tr><td><?php echo $row["obiekt_id"]; ?></td><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["adres"]; ?></td><td><?php for ($i=0;$i<(5-ceil(log10($row["kod_pocztowy"]+1))); $i++) echo "0"; echo $row["kod_pocztowy"]; ?></td><td><?php echo $row["miejscowosc"]; ?></td><td><?php echo $czynny; ?></td><td><?php echo $row["czynny_od"]; ?></td><td><?php echo $row["czynny_do"]; ?></td><td><a href='obiekty.php?edito_id=<?php echo $row["obiekt_id"]; ?>'>Edytuj</a></td><td><a href='obiekty.php?usuno_id=<?php echo $row["obiekt_id"]; ?>'>Usuń</a></td></tr>
<?php	}
	}
} ?>
<form method='post'>
<tr><td></td><td><input type='text' name='nazwa' maxlength='32'></td><td><input type='text' name='adres' maxlength='32'></td><td><input type="text" name='kod_pocztowy' pattern="[0-9]{5}"></td>
<td><select name='miejscowosc'><?php
$sql = "SELECT miejscowosc_id, nazwa FROM miejscowosci;";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) { ?>
		<option value='<?php echo $row["miejscowosc_id"]; ?>'><?php echo $row["nazwa"]; ?></option> <?php
	}
}	?>
</select></td>
<td><input type="checkbox" name="D1" value="1"><input type="checkbox" name="D2" value="2"><input type="checkbox" name="D3" value="4"><input type="checkbox" name="D4" value="8"><input type="checkbox" name="D5" value="16"><input type="checkbox" name="D6" value="32"><input type="checkbox" name="D7" value="64"></td>
<td><input type="time" name="czynny_od" value='09:00' required></td><td><input type="time" name="czynny_do" value='20:00' required></td><td colspan='2'><input type='submit' name='submit' value='Dodaj'></td></tr></form>
</table>
<script>
function pokazFormMiej() {
	if (document.getElementById("dodMiejsc").style.display == "none")
		document.getElementById("dodMiejsc").style.display = "block"
	else
		document.getElementById("dodMiejsc").style.display = "none"
}
function poZmianie(value) {
	if (value == "Dodaj")
		document.getElementById("dodWoj").style.display = "";
	else
		document.getElementById("dodWoj").style.display = "none";
}
</script>
<a href="#" id="przyciskMiej" style="text-decoration: none;" onclick='pokazFormMiej()'>Dodaj miejscowość</a><br>
<div id='dodMiejsc' style="display: none;">
	<form method="post">
	<table>
		<tr><th colspan='2'>Dodawanie miejscowości</th></tr>
		<tr><td>Województwo: </td><td><select name='woj_id' onchange='poZmianie(value);'><?php
		$sql = "SELECT wojewodztwo_id, nazwa FROM wojewodztwa;";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) { ?>
				<option value='<?php echo $row["wojewodztwo_id"]; ?>'><?php echo $row["nazwa"]; ?></option> <?php
			}
		} ?>
		<option value='Dodaj'>Dodaj województwo</option></select></td></tr>
		<tr id='dodWoj' style="display: none;"><td>Nazwa województwa: </td><td><input type='text' name='nazwaWojewodztwo' maxlength='32'></td></tr>
		<tr><td>Nazwa miejscowości: </td><td><input type='text' name='nazwaMiejscowosc' maxlength='32' required></td></tr>
		<tr><td colspan='2'><input type='submit' name='dodMiejscowosc' value='Dodaj miejscowość'></td></tr>
	</table>
	</form>
</div>
 <?php /*
if (isset($_SESSION["typ"]) && isset($_GET["edito_id"])) {
	if ($_SESSION["typ"] >= 100) { ?>
		<form method="post">
		<table>
			<tr><th>
		
	}
	else { ?>
		<h4>Brak odpowiednich uprawnień</h4> <?php
	}
}*/ ?>
</body>
</html>