<?php require "head.php"; 
if (isset($_SESSION["id"])) { 
	if ($_SESSION["typ"] < 10) { 
	if (isset($_GET["usunr_id"])) {
		$sql = "SELECT uzytk_id FROM rezerwacje WHERE rez_id = '{$_GET["usunr_id"]}' AND uzytk_id = '{$_SESSION["id"]}' AND oplacona = 0;";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$sql = "DELETE FROM zam_uslugi WHERE rez_id = '{$_GET["usunr_id"]}';";
			if ($conn->query($sql) === TRUE) {
				$sql = "DELETE FROM rezerwacje WHERE rez_id = '{$_GET["usunr_id"]}';";
				if ($conn->query($sql) === TRUE) { ?>
					<h4>Usunięto pomyślnie</h4> <?php
				}
			}
			else { ?>
				<h4>Nie udało się usunąć</h4> <?php
			}
		}
	} ?>
	Aktywne rezerwacje: <br>
	<table>
		<tr><th>ID</th><th>Sala</th><th>Obiekt</th><th>Miejsc</th><th>Data</th><th>Od</th><th>Do</th><th>Wartość</th><th colspan='3'>Akcja</th></tr>
<?php 
	//$sql = "SELECT rez_id,kiedy,czas_od,czas_do,oplacona,wartosc,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE r.uzytk_id = '{$_SESSION["id"]}' AND kiedy >= DATE(NOW());";
	$sql = "SELECT r.rez_id,s.sala_id,kiedy,czas_od,czas_do,oplacona,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca,s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS 'wartosc' FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE uzytk_id = '{$_SESSION["id"]}' AND kiedy >= DATE(NOW()) GROUP BY r.rez_id ORDER BY kiedy,czas_od;";
	$result = $conn->query($sql);
	$ile = $result->num_rows;
	if ($ile > 0) {
		while($row = $result->fetch_assoc()) { ?>
		<tr><td><?php echo $row["rez_id"]; ?></td><td><?php echo $row["sala"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["kiedy"]; ?></td><td><?php echo $row["czas_od"]; ?></td><td><?php echo $row["czas_do"]; ?></td><td><?php echo $row["wartosc"] . " PLN"; ?></td><?php if ($row["oplacona"] == 1) echo "<td colspan='3'>Opłacono</td>"; else echo "<td><a href='rezerwuj.php?editr_id=" . $row["rez_id"] . "'>Edytuj</a></td><td><a href='oplac.php?id=" . $row["rez_id"] . "'>Opłać</a></td><td><a href='rezerwacje.php?usunr_id=" . $row["rez_id"] . "'>Usuń</a></td>"; ?></tr>
	<?php 
			$sql = "SELECT ilosc, nazwa, opis FROM zam_uslugi z INNER JOIN uslugi u ON (z.usluga_id = u.usluga_id) WHERE rez_id = '{$row["rez_id"]}';";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) { ?>
					<tr><td><td><td colspan='6'><?php echo "+ " . $row2["nazwa"] . " x" . $row2["ilosc"]; ?></td></tr>
	<?php		}
			}
		}
	}?>
	</table></body></html><br>
<?php
	//$sql = "SELECT rez_id,kiedy,czas_od,czas_do,oplacona,wartosc,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE r.uzytk_id = '{$_SESSION["id"]}' AND kiedy < DATE(NOW()) AND oplacona = true;";
	$sql = "SELECT r.rez_id,s.sala_id,kiedy,czas_od,czas_do,oplacona,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca,s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS 'wartosc' FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE uzytk_id = '{$_SESSION["id"]}' AND kiedy < DATE(NOW()) AND oplacona = 1 GROUP BY r.rez_id ORDER BY kiedy DESC, czas_od DESC;";
	$result = $conn->query($sql);
	$ile = $result->num_rows;
	if ($ile > 0) { ?>
		Historia rezerwacji: <br>
		<table>
		<tr><th>ID</th><th>Sala</th><th>Obiekt</th><th>Miejsc</th><th>Data</th><th>Od</th><th>Do</th><th>Opłacona</th></tr>
<?php
		while($row = $result->fetch_assoc()) { ?>
		<tr><td><?php echo $row["rez_id"]; ?></td><td><?php echo $row["sala"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["kiedy"]; ?></td><td><?php echo $row["czas_od"]; ?></td><td><?php echo $row["czas_do"]; ?></td><td><?php echo $row["wartosc"] . " PLN"; ?></td><td><?php echo "Opłacona";?></td></tr>
	<?php 
			$sql = "SELECT ilosc, nazwa, opis FROM zam_uslugi z INNER JOIN uslugi u ON (z.usluga_id = u.usluga_id) WHERE rez_id = '{$row["rez_id"]}';";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) { ?>
					<tr><td><td><td colspan='6'><?php echo "+ " . $row2["nazwa"] . " x" . $row2["ilosc"]; ?></td></tr>
	<?php		}
			}
		}
	}?>
	</table></body></html>
<?php
	} 
else if ($_SESSION["typ"]>=100) { 
	if (isset($_GET["usunr_id"])) {
		$sql = "DELETE FROM zam_uslugi WHERE rez_id = '{$_GET["usunr_id"]}';";
		if ($conn->query($sql) === TRUE) { 
			$sql = "DELETE FROM rezerwacje WHERE rez_id = '{$_GET["usunr_id"]}';";
			if ($conn->query($sql) === TRUE) { ?>
				<h4>Usunięto pomyślnie</h4> <?php
			}
			else { ?>
				<h4>Nie udało się usunąć rezerwacji</h4> <?php
			}
		}
		else { ?>
			<h4>Nie udało się usunąć powiązanych usług</h4> <?php
		}
	} 
	if (isset($_POST['submit'])) { ?>
		<form method='post'>
		<input type='date' name='data' value='<?php echo $_POST["data"]; ?>'>
		<input type='submit' name='submit' value='Wyświetl'>
		</form>
		Zamówienia użytkowników: <br>
		<table>
		<tr><th>ID</th><th>Rezerwacja</th><th>Sala</th><th>Obiekt</th><th>Miejsc</th><th>Data</th><th>Od</th><th>Do</th><th>Wartość</th><th colspan='3'>Akcja</th></tr>
		<?php 
		//$sql = "SELECT uzytk_id,rez_id,kiedy,czas_od,czas_do,oplacona,wartosc,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE kiedy = '{$_POST["data"]}'";
		$sql = "SELECT r.rez_id,r.uzytk_id,s.sala_id,kiedy,czas_od,czas_do,oplacona,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca,s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS 'wartosc' FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE kiedy = '{$_POST["data"]}' GROUP BY r.rez_id ORDER BY czas_od;";
		$result = $conn->query($sql);
		$ile = $result->num_rows;
		if ($ile > 0) {
			while($row = $result->fetch_assoc()) { ?>
			<tr><td><?php echo $row["uzytk_id"]; ?></td><td><?php echo $row["rez_id"]; ?></td><td><?php echo $row["sala"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["kiedy"]; ?></td><td><?php echo $row["czas_od"]; ?></td><td><?php echo $row["czas_do"]; ?></td><td><?php echo $row["wartosc"] . " PLN"; ?></td><?php if ($row["oplacona"] == 1) echo "<td>Opłacona</td><td><a href='rezerwacje.php?usunr_id=" . $row["rez_id"] . "'>Usuń</a></td>"; else echo "<td><a href='rezerwuj.php?editr_id=" . $row["rez_id"] . "'>Edytuj</a></td><td><a href='oplac.php?id=" . $row["rez_id"] . "'>Opłać</a></td><td><a href='rezerwacje.php?usunr_id=" . $row["rez_id"] . "'>Usuń</a></td>"; ?></tr>
		<?php 
				$sql = "SELECT ilosc, nazwa, opis FROM zam_uslugi z INNER JOIN uslugi u ON (z.usluga_id = u.usluga_id) WHERE rez_id = '{$row["rez_id"]}';";
				$result2 = $conn->query($sql);
				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) { ?>
						<tr><td><td><td colspan='6'><?php echo "+ " . $row2["nazwa"] . " x" . $row2["ilosc"]; ?></td></tr>
		<?php		}
				}
			}
		}?>
		</table></body></html><br>
	<?php 
	}
	else { ?>
		<form method='post'>
		<input type='date' name='data' value='<?php echo date("Y-m-d",time()); ?>'>
		<input type='submit' name='submit' value='Wyświetl'>
		</form>
		Zamówienia użytkowników: <br>
		<table>
		<tr><th>Użytkownik</th><th>Rezerwacja</th><th>Sala</th><th>Obiekt</th><th>Miejsc</th><th>Data</th><th>Od</th><th>Do</th><th>Wartość</th><th colspan='3'>Akcja</th></tr>
	<?php 
	//$sql = "SELECT uzytk_id,rez_id,kiedy,czas_od,czas_do,oplacona,wartosc,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE kiedy >= DATE(NOW());";
	$sql = "SELECT r.rez_id,r.uzytk_id,s.sala_id,kiedy,czas_od,czas_do,oplacona,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca,s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS 'wartosc' FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE kiedy = DATE(NOW()) AND oplacona = 1 GROUP BY r.rez_id ORDER BY czas_od;";
	$result = $conn->query($sql);
	$ile = $result->num_rows;
	if ($ile > 0) {
		while($row = $result->fetch_assoc()) { ?>
		<tr><td><?php echo $row["uzytk_id"]; ?></td><td><?php echo $row["rez_id"]; ?></td><td><?php echo $row["sala"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["kiedy"]; ?></td><td><?php echo $row["czas_od"]; ?></td><td><?php echo $row["czas_do"]; ?></td><td><?php echo $row["wartosc"] . " PLN"; ?></td><?php if ($row["oplacona"] == 1) echo "<td><a href='rezerwuj.php?editr_id=" . $row["rez_id"] . "'>Edytuj</a></td><td>Opłacona</td><td><a href='rezerwacje.php?usunr_id=" . $row["rez_id"] . "'>Usuń</a></td>"; else echo "<td><a href='rezerwuj.php?editr_id=" . $row["rez_id"] . "'>Edytuj</a></td><td><a href='oplac.php?id=" . $row["rez_id"] . "'>Opłać</a></td><td><a href='rezerwacje.php?usunr_id=" . $row["rez_id"] . "'>Usuń</a></td>"; ?></tr>
	<?php 
			$sql = "SELECT ilosc, nazwa, opis FROM zam_uslugi z INNER JOIN uslugi u ON (z.usluga_id = u.usluga_id) WHERE rez_id = '{$row["rez_id"]}';";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) { ?>
					<tr><td><td><td colspan='6'><?php echo "+ " . $row2["nazwa"] . " x" . $row2["ilosc"]; ?></td></tr>
	<?php		}
			}
		}
	}?>
	</table></body></html><br>
<?php 
	}
} 
else {
	if (isset($_POST['submit'])) { ?>
		<form method='post'>
		<input type='date' name='data' value='<?php echo $_POST["data"]; ?>'>
		<input type='submit' name='submit' value='Wyświetl'>
		</form>
		Zamówienia użytkowników: <br>
		<table>
		<tr><th>Użytkownik<th>Rezerwacja</th><th>Sala</th><th>Obiekt</th><th>Miejsc</th><th>Data</th><th>Od</th><th>Do</th><th>Wartość</th><th>Opłacona</th></tr>
		<?php 
		//$sql = "SELECT uzytk_id,rez_id,kiedy,czas_od,czas_do,oplacona,wartosc,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE kiedy = '{$_POST["data"]}' AND s.obiekt_id = (SELECT obiekt_id FROM pracownicy WHERE uzytk_id = '{$_SESSION["id"]}') AND oplacona = 1";
		$sql = "SELECT r.rez_id,r.uzytk_id,s.sala_id,kiedy,czas_od,czas_do,oplacona,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca,s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS 'wartosc' FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE s.obiekt_id = (SELECT obiekt_id FROM pracownicy WHERE uzytk_id = '{$_SESSION["id"]}') AND kiedy = '{$_POST["data"]}' AND oplacona = 1 GROUP BY r.rez_id ORDER BY czas_od;";
		$result = $conn->query($sql);
		$ile = $result->num_rows;
		if ($ile > 0) {
			while($row = $result->fetch_assoc()) { ?>
			<tr><td><?php echo $row["uzytk_id"]; ?></td><td><?php echo $row["rez_id"]; ?></td><td><?php echo $row["sala"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["kiedy"]; ?></td><td><?php echo $row["czas_od"]; ?></td><td><?php echo $row["czas_do"]; ?></td><td><?php echo $row["wartosc"] . " PLN"; ?></td><?php if ($row["oplacona"] == 1) echo "<td>Opłacona</td>"; ?></tr>
		<?php 
				$sql = "SELECT ilosc, nazwa, opis FROM zam_uslugi z INNER JOIN uslugi u ON (z.usluga_id = u.usluga_id) WHERE rez_id = '{$row["rez_id"]}';";
				$result2 = $conn->query($sql);
				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) { ?>
						<tr><td><td><td colspan='6'><?php echo "+ " . $row2["nazwa"] . " x" . $row2["ilosc"]; ?></td></tr>
		<?php		}
				}
			}
		}?>
		</table></body></html><br>
	<?php 
	}
	else { ?>
		<form method='post'>
		<input type='date' name='data' value='<?php echo date("Y-m-d",time()); ?>'>
		<input type='submit' name='submit' value='Wyświetl'>
		</form>
		Zamówienia użytkowników: <br>
		<table>
		<tr><th>Użytkownik<th>Rezerwacja</th><th>Sala</th><th>Obiekt</th><th>Miejsc</th><th>Data</th><th>Od</th><th>Do</th><th>Wartość</th><th>Opłacona</th></tr>
	<?php 
	//$sql = "SELECT uzytk_id,rez_id,kiedy,czas_od,czas_do,oplacona,wartosc,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE kiedy >= DATE(NOW()) AND s.obiekt_id = (SELECT obiekt_id FROM pracownicy WHERE uzytk_id = '{$_SESSION["id"]}') AND oplacona = 1;";
	$sql = "SELECT r.rez_id,r.uzytk_id,s.sala_id,kiedy,czas_od,czas_do,oplacona,s.nazwa AS 'sala',o.nazwa AS 'obiekt',s.miejsca,s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS wartosc FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE s.obiekt_id = (SELECT obiekt_id FROM pracownicy WHERE uzytk_id = '{$_SESSION["id"]}') AND kiedy = DATE(NOW()) AND oplacona = 1 GROUP BY r.rez_id ORDER BY czas_od;";
	$result = $conn->query($sql);
	$ile = $result->num_rows;
	if ($ile > 0) {
		while($row = $result->fetch_assoc()) { ?>
		<tr><td><?php echo $row["uzytk_id"]; ?></td><td><?php echo $row["rez_id"]; ?></td><td><?php echo $row["sala"]; ?></td><td><?php echo $row["obiekt"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["kiedy"]; ?></td><td><?php echo $row["czas_od"]; ?></td><td><?php echo $row["czas_do"]; ?></td><td><?php echo $row["wartosc"] . " PLN"; ?></td><?php echo "<td>Opłacona</td></tr>"; ?>
	<?php 
			$sql = "SELECT ilosc, nazwa, opis FROM zam_uslugi z INNER JOIN uslugi u ON (z.usluga_id = u.usluga_id) WHERE rez_id = '{$row["rez_id"]}';";
			$result2 = $conn->query($sql);
			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) { ?>
					<tr><td><td><td colspan='6'><?php echo "+ " . $row2["nazwa"] . " x" . $row2["ilosc"]; ?></td></tr>
	<?php		}
			}
		}
	}?>
	</table></body></html><br>
<?php 
	}
}
}
?>

