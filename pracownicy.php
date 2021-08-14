<?php require("head.php"); 
if (isset($_POST["submit"])) {
	if (isset($_SESSION["typ"])) {
		if ($_SESSION["typ"] >= 1000) {
			$sql = "INSERT INTO uzytkownicy (nick,imie,nazwisko,haslo,email,telefon,data_ur,typ_konta) VALUES ('{$_POST["nick"]}','{$_POST["imie"]}','{$_POST["nazwisko"]}','hasloTemp','{$_POST["email"]}','{$_POST["telefon"]}','{$_POST["data_ur"]}','{$_POST["prawa"]}');";
			if ($conn->query($sql) === TRUE) { 
				$sql = "SELECT uzytk_id FROM uzytkownicy WHERE nick = '{$_POST["nick"]}';";
				$result = $conn->query($sql);
				if ($result->num_rows > 0)
					$row = $result->fetch_assoc();
				$pesel = substr($_POST["pesel"], -5);
				$sql = "INSERT INTO pracownicy (uzytk_id,obiekt_id,pesel,stanowisko_id,szef_id,pensja) VALUES ('{$row["uzytk_id"]}','{$_POST["obiekt"]}','{$pesel}','{$_POST["stanowisko"]}','{$_POST["szef"]}','{$_POST["pensja"]}')";
				if ($conn->query($sql) === TRUE) { ?>
					<h4>Poprawnie dodano pracownika wraz z kontem użytkownika</h4> <?php
				}
				else { ?>
					<h4>Błąd dodawania konta pracownika</h4> <?php
				}
			}
			else { ?>
				<h4>Błąd dodawania użytkownika</h4> <?php
			}
		}
		else { ?>
			<h4>Brak odpowiednich uprawnień</h4> <?php
		}
	}
}

if (isset($_SESSION["typ"]) && isset($_GET["usunp_id"])) {
	if ($_SESSION["typ"] >= 1000) {
		$sql = "SELECT uzytk_id FROM pracownicy WHERE prac_id = '{$_GET["usunp_id"]}';";
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
			$row = $result->fetch_assoc();
		$sql = "DELETE FROM pracownicy WHERE prac_id = '{$_GET["usunp_id"]}';";
		if ($conn->query($sql) === TRUE) { 
			$sql = "DELETE FROM uzytkownicy WHERE uzytk_id = '{$row["uzytk_id"]}';";
			if ($conn->query($sql) === TRUE) { ?>
				<h4>Pomyślnie usunięto pracownika oraz użytkownika</h4> <?php
			}
			else { ?>
				<h4>Błąd usuwania pracownika</h4> <?php
			}
		}
		else { ?>
			<h4>Błąd podczas usuwania konta użytkownika</h4> <?php
		}
	}
	else { ?>
		<h4>Brak odpowiednich uprawnień</h4> <?php
	}
}

if (isset($_POST["edytuj"])) {
	if (!isset($_SESSION["typ"]) || $_SESSION["typ"] <= 100) { ?>
		<h4>Brak odpowiednich uprawnień</h4> <?php
	}
	else {
		$pesel = substr($_POST["pesel"], -5);
		$sql = "UPDATE pracownicy SET obiekt_id='{$_POST["obiekt"]}',pesel='{$pesel}',stanowisko_id='{$_POST["stanowisko"]}',szef_id='{$_POST["szef"]}',pensja='{$_POST["pensja"]}' WHERE prac_id = '{$_POST["prac_id"]}';";
		if ($conn->query($sql) === TRUE) { 
			$sql = "UPDATE uzytkownicy SET nick='{$_POST["nick"]}',imie='{$_POST["imie"]}',nazwisko='{$_POST["nazwisko"]}',email='{$_POST["email"]}',telefon='{$_POST["telefon"]}',data_ur='{$_POST["data_ur"]}',typ_konta='{$_POST["prawa"]}' WHERE uzytk_id = '{$_POST["uzytk_id"]}';"; 
			if ($conn->query($sql) === TRUE) { ?>
				<h4>Pomyślnie edytowano pracownika.</h4> <?php
			}
			else { ?>
				<h4>Błąd podczas edycji uzytkownika.</h4> <?php
			}
		}
		else { ?>
			<h4>Błąd podczas edycji pracownika</h4> <?php
		}
	}
}
if (isset($_SESSION["typ"]) && $_SESSION["typ"] >= 1000) {
$sql = "SELECT p.prac_id, p.uzytk_id, p.obiekt_id, o.nazwa AS 'obiekt', p.pesel, p.stanowisko_id, s.nazwa AS 'stanowisko', p.szef_id, uz.imie AS 'szimie', uz.nazwisko AS 'sznazwisko', p.pensja, u.nick, u.imie, u.nazwisko, u.email, u.telefon, u.data_ur, u.typ_konta FROM pracownicy p NATURAL JOIN uzytkownicy u INNER JOIN obiekty o ON (p.obiekt_id = o.obiekt_id) INNER JOIN stanowiska s ON (p.stanowisko_id = s.stanowisko_id) INNER JOIN pracownicy sz ON (p.szef_id = sz.prac_id) INNER JOIN uzytkownicy uz ON (sz.uzytk_id = uz.uzytk_id) WHERE p.szef_id IS NOT NULL ORDER BY p.obiekt_id,u.nazwisko;";
$result = $conn->query($sql); ?>
<table>
	<tr><th>ID</th><th>Nick</th><th>Imię</th><th>Nazwisko</th><th>Data urodzenia</th><th>Pesel</th><th>Email</th><th>Telefon</th><th>Stanowisko</th><th>Pensja</th><th>Obiekt</th><th>Szef</th><th>Uprawnienia</th><th colspan='2'>Akcja</th></tr> <?php
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) { 
		if (isset($_SESSION["typ"]) && isset($_GET["editp_id"]) && $_SESSION["typ"] >= 100 && ($_GET["editp_id"] == $row["prac_id"])) { ?>
			<form action='pracownicy.php' method='post'>
			<input type='hidden' name='prac_id' value='<?php echo $row["prac_id"]; ?>'><input type='hidden' name='uzytk_id' value='<?php echo $row["uzytk_id"]; ?>'>
			<tr><td><?php echo $row["prac_id"]; ?></td><td><input type='text' name='nick' maxlength='32' value='<?php echo $row["nick"]; ?>' size='10'></td><td><input type='text' name='imie' maxlength='32' value='<?php echo $row["imie"]; ?>' size='15'></td><td><input type='text' name='nazwisko' maxlength='32' value='<?php echo $row["nazwisko"]; ?>' size='15'></td><td><input type='date' name='data_ur' value='<?php echo $row["data_ur"]; ?>'></td><td><input type='text' name='pesel' minlength='11' maxlength='11' size='9' value='<?php echo date("ymd",strtotime($row["data_ur"])) . $row["pesel"]; ?>'></td><td><input type='email' name='email' maxlength='32' value='<?php echo $row["email"]; ?>' size='30'></td><td><input type='number' name='telefon' minlength='9' maxlength='9' size='9' value='<?php echo $row["telefon"]; ?>' style='width:85px;'></td>
			<td><select name='stanowisko'> <?php
			$sql = "SELECT stanowisko_id, nazwa FROM stanowiska;";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while ($row2 = $result2->fetch_assoc()) { ?>
					<option value='<?php echo $row2["stanowisko_id"]; ?>' <?php if ($row["stanowisko_id"] == $row2["stanowisko_id"]) echo "selected" ?>><?php echo $row2["nazwa"]; ?></option> <?php
				}
			} ?>
			</select></td><td><input type='number' min='1000' name='pensja' value='<?php echo $row["pensja"]; ?>' style='width:70px;'></td>
			<td><select name='obiekt'> <?php
			$sql = "SELECT obiekt_id, o.nazwa, m.nazwa AS 'miejscowosc' FROM obiekty o INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id);";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while ($row2 = $result2->fetch_assoc()) { ?>
					<option value='<?php echo $row2["obiekt_id"]; ?>' <?php if ($row["obiekt_id"] == $row2["obiekt_id"]) echo "selected" ?>><?php echo $row2["nazwa"] . ", " . $row2["miejscowosc"]; ?></option> <?php
				}
			} ?>
			</select></td><td><select name='szef'> <?php
			$sql = "SELECT prac_id, imie, nazwisko, nazwa AS 'stanowisko' FROM pracownicy p INNER JOIN uzytkownicy u ON (p.uzytk_id = u.uzytk_id) INNER JOIN stanowiska s ON (p.stanowisko_id = s.stanowisko_id);";
			$result2 = $conn->query($sql);
			if ($result->num_rows > 0) {
				while ($row2 = $result2->fetch_assoc()) { ?>
					<option value='<?php echo $row2["prac_id"]; ?>' <?php if ($row["szef_id"] == $row2["prac_id"]) echo "selected" ?>><?php echo $row2["imie"] . " " . $row2["nazwisko"] . " - " . $row2["stanowisko"]; ?></option> <?php
				}
			} ?>
			</select></td><td><input type='number' name='prawa' value='10' value='<?php echo $row["typ_konta"]; ?>' style='width:50px;'></td><td><input type='submit' name='edytuj' value='Zatwierdź zmiany'></td></tr></form>
			<?php
		}
		else {	?>
		<tr><td><?php echo $row["prac_id"]; ?></td><td><?php echo $row["nick"]; ?></td><td><?php echo $row["imie"]; ?></td><td><?php echo $row["nazwisko"]; ?></td><td><?php echo $row["data_ur"]; ?></td><td><?php echo date("ymd",strtotime($row["data_ur"])) . $row["pesel"]; ?></td><td><?php echo $row["email"]; ?></td><td><?php echo $row["telefon"]; ?></td><td><?php echo $row["stanowisko"]; ?></td><td><?php echo $row["pensja"] . " PLN"; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["szimie"] . " " . $row["sznazwisko"];?></td><td><?php echo $row["typ_konta"]; ?></td><td><a href='pracownicy.php?editp_id=<?php echo $row["prac_id"]; ?>'>Edytuj</a></td><td><a href='pracownicy.php?usunp_id=<?php echo $row["prac_id"]; ?>'>Usuń</a></td></tr> <?php
		}
	}
} ?>
<form method='post'>
<tr><td></td><td><input type='text' name='nick' maxlength='32' size='10'></td><td><input type='text' name='imie' maxlength='32' size='15'></td><td><input type='text' name='nazwisko' maxlength='32' size='15'></td><td><input type='date' name='data_ur'></td><td><input type='text' name='pesel' minlength='11' maxlength='11' size='9'></td><td><input type='email' name='email' maxlength='32' size='30'></td><td><input type='number' name='telefon' minlength='9' maxlength='9' size='9' style='width:85px;'></td>
<td><select name='stanowisko'> <?php
$sql = "SELECT stanowisko_id, nazwa FROM stanowiska;";
$result2 = $conn->query($sql);
if ($result2->num_rows > 0) {
	while ($row2 = $result2->fetch_assoc()) { ?>
		<option value='<?php echo $row2["stanowisko_id"]; ?>'><?php echo $row2["nazwa"]; ?></option> <?php
	}
} ?>
</select></td><td><input type='number' min='1000' name='pensja' style='width:70px;'></td>
<td><select name='obiekt'> <?php
$sql = "SELECT obiekt_id, o.nazwa, m.nazwa AS 'miejscowosc' FROM obiekty o INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id);";
$result2 = $conn->query($sql);
if ($result2->num_rows > 0) {
	while ($row2 = $result2->fetch_assoc()) { ?>
		<option value='<?php echo $row2["obiekt_id"]; ?>'><?php echo $row2["nazwa"] . ", " . $row2["miejscowosc"]; ?></option> <?php
	}
} ?>
</select></td><td><select name='szef'> <?php
$sql = "SELECT prac_id, imie, nazwisko, nazwa AS 'stanowisko' FROM pracownicy p INNER JOIN uzytkownicy u ON (p.uzytk_id = u.uzytk_id) INNER JOIN stanowiska s ON (p.stanowisko_id = s.stanowisko_id);";
$result2 = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row2 = $result2->fetch_assoc()) { ?>
		<option value='<?php echo $row2["prac_id"]; ?>'><?php echo $row2["imie"] . " " . $row2["nazwisko"] . " - " . $row2["stanowisko"]; ?></option> <?php
	}
} ?>
</select></td><td><input type='number' name='prawa' value='10' style='width:50px;'></td><td colspan='2'><input type='submit' name='submit' value='Dodaj'></tr>
</form>
</table> 
</body>
</html><?php 
}
