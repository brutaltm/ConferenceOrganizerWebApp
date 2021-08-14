<?php require "head.php"; 
if (isset($_GET["w_id"])) { 
	$sql = "SELECT wojewodztwo_id,nazwa FROM wojewodztwa WHERE wojewodztwo_id = '{$_GET['w_id']}'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
		$row = $result->fetch_assoc();
?>
	<h4>Wyświetlanie wyników dla województwa: <?php echo $row["nazwa"]; ?> </h4>
<?php 
	$sql = "SELECT * FROM miejscowosci WHERE wojewodztwo_id = '{$_GET["w_id"]}' ORDER BY nazwa;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) { ?>
		Miejscowości: <br> 
<?php
		while($row = $result->fetch_assoc()) { ?>
			<a href="oferta.php?m_id=<?php echo $row["miejscowosc_id"]; ?>"><?php echo $row["nazwa"]; ?></a>
<?php	}
		echo "<br>";
	}
	$sql = "SELECT * FROM obiekty WHERE miejscowosc_id = ANY (SELECT miejscowosc_id FROM miejscowosci WHERE wojewodztwo_id = '{$_GET["w_id"]}') AND obiekty.obiekt_id > 1;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) { ?>
		Obiekty: <br>
<?php	
		while($row = $result->fetch_assoc()) { ?>
			<a href="oferta.php?o_id=<?php echo $row["obiekt_id"]; ?>"><?php echo $row["nazwa"]; ?></a>
<?php	}
		echo "<br>";
	} ?>
	<table>
		<tr><th>Sala</th><th>Miejsca</th><th>Cena</th><th>Obiekt</th><th>Adres</th><th>Miejscowość</th><th>Akcja</th></tr>
<?php	
	$sql = "SELECT s.sala_id, s.nazwa, s.miejsca, s.cena, o.nazwa AS 'o_nazwa', o.adres, m.nazwa AS 'm_nazwa' FROM sale s 
	INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) 
	INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id)
	WHERE m.wojewodztwo_id = '{$_GET["w_id"]}' ORDER BY s.miejsca,s.cena"; 
	$result = $conn->query($sql); 
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) { ?>
			<tr><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["cena"] . "PLN/h"; ?></td><td><?php echo $row["o_nazwa"]; ?></td><td><?php echo "ul. " . $row["adres"]; ?></td><td><?php echo $row["m_nazwa"]; ?></td>
				<td><a href=<?php if (isset($_SESSION["typ"])) { if ($_SESSION["typ"] < 100) echo "rezerwuj.php?s_id=" . $row["sala_id"]; else echo "rezerwuj.php?s_id=" . $row["sala_id"]; } else echo "rezerwuj.php?s_id=" . $row["sala_id"]; ?>>Rezerwuj</a></td>
			</tr> 
<?php
		}
		echo "</table>";
	}
}
else if (isset($_GET["m_id"])) {
	$sql = "SELECT miejscowosc_id,nazwa FROM miejscowosci WHERE miejscowosc_id = '{$_GET['m_id']}' ORDER BY nazwa;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
		$row = $result->fetch_assoc(); ?>
	<h4>Wyświetlanie wyników dla miejscowości: <?php echo $row["nazwa"]; ?> </h4>
<?php
	$sql = "SELECT * FROM obiekty WHERE miejscowosc_id = '{$_GET["m_id"]}' AND obiekt_id > 1;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) { ?>
		Obiekty: <br>
<?php	
		while($row = $result->fetch_assoc()) { ?>
			<a href="oferta.php?o_id=<?php echo $row["obiekt_id"]; ?>"><?php echo $row["nazwa"]; ?></a>
<?php	}
		echo "<br>";
	} ?>
	<table>
		<tr><th>Sala</th><th>Miejsca</th><th>Cena</th><th>Obiekt</th><th>Adres</th><th>Akcja</th></tr>
<?php	
	$sql = "SELECT s.sala_id, s.nazwa, s.miejsca, s.cena, o.nazwa AS 'o_nazwa', o.adres FROM sale s INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) WHERE o.miejscowosc_id = '{$_GET["m_id"]}' ORDER BY s.miejsca"; 
	$result = $conn->query($sql); 
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) { ?>
			<tr><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["cena"] . "PLN/h"; ?></td><td><?php echo $row["o_nazwa"]; ?></td><td><?php echo "ul. " . $row["adres"]; ?></td>
				<td><a href=<?php if (isset($_SESSION["typ"])) { if ($_SESSION["typ"] < 100) echo "rezerwuj.php?s_id=" . $row["sala_id"]; else echo "rezerwuj.php?s_id=" . $row["sala_id"]; } else echo "rezerwuj.php?s_id=" . $row["sala_id"]; ?>>Rezerwuj</a></td>
			</tr> 
<?php
		}
		echo "</table>";
	}
}
else if (isset($_GET["o_id"])) {
	$sql = "SELECT * FROM obiekty WHERE obiekt_id = '{$_GET["o_id"]}';";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
		$row = $result->fetch_assoc(); ?>
	<h4>Wyświetlanie wyników dla obiektu: <?php echo $row["nazwa"] . ", " . $row["adres"] . " - "; ?>
<?php
	$sql = "SELECT nazwa FROM miejscowosci WHERE miejscowosc_id = '{$row["miejscowosc_id"]}';";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
		$row = $result->fetch_assoc();
	echo $row["nazwa"] . "</h4>"; ?>
	<table>
		<tr><th>Sala</th><th>Miejsca</th><th>Cena</th><th>Akcja</th></tr>
<?php 
	$sql = "SELECT * FROM sale WHERE obiekt_id = '{$_GET["o_id"]}' ORDER BY miejsca"; 
	$result = $conn->query($sql); 
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) { ?>
			<tr><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["cena"] . "PLN/h"; ?></td>
				<td><a href=<?php if (isset($_SESSION["typ"])) { if ($_SESSION["typ"] < 100) echo "rezerwuj.php?s_id=" . $row["sala_id"]; else echo "rezerwuj.php?s_id=" . $row["sala_id"]; } else echo "rezerwuj.php?s_id=" . $row["sala_id"]; ?>>Rezerwuj</a></td>
			</tr> 
<?php
		}
		echo "</table>";
	}
}
else { ?>
	Województwa: <br>
<?php 
	$sql = "SELECT wojewodztwo_id,nazwa FROM wojewodztwa ORDER BY nazwa;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
		while($row = $result->fetch_assoc()) { ?>
			<a href="oferta.php?w_id=<?php echo $row["wojewodztwo_id"]; ?>"><?php echo $row["nazwa"]; ?></a>
<?php 	}
		echo "<br>";
	$sql = "SELECT * FROM miejscowosci ORDER BY nazwa;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) { ?>
		Miejscowości: <br> 
<?php
		while($row = $result->fetch_assoc()) { ?>
			<a href="oferta.php?m_id=<?php echo $row["miejscowosc_id"]; ?>"><?php echo $row["nazwa"]; ?></a>
<?php	}
		echo "<br>";
	}
	$sql = "SELECT * FROM obiekty WHERE obiekt_id > 1;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) { ?>
		Obiekty: <br>
<?php	
		while($row = $result->fetch_assoc()) { ?>
			<a href="oferta.php?o_id=<?php echo $row["obiekt_id"]; ?>"><?php echo $row["nazwa"]; ?></a>
<?php	}
		echo "<br>";
	} ?>
	<table>
		<tr><th>Sala</th><th>Miejsca</th><th>Cena</th><th>Obiekt</th><th>Adres</th><th>Miejscowość</th><th>Akcja</th></tr>
<?php	
	$sql = "SELECT s.sala_id, s.nazwa, s.miejsca, s.cena, o.nazwa AS 'o_nazwa', o.adres, m.nazwa AS 'm_nazwa' FROM sale s 
	INNER JOIN obiekty o ON (s.obiekt_id = o.obiekt_id) 
	INNER JOIN miejscowosci m ON (o.miejscowosc_id = m.miejscowosc_id)
	ORDER BY s.miejsca,s.cena"; 
	$result = $conn->query($sql); 
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) { ?>
			<tr><td><?php echo $row["nazwa"]; ?></td><td><?php echo $row["miejsca"]; ?></td><td><?php echo $row["cena"] . "PLN/h"; ?></td><td><?php echo $row["o_nazwa"]; ?></td><td><?php echo "ul. " . $row["adres"]; ?></td><td><?php echo $row["m_nazwa"]; ?></td>
				<td><a href=<?php if (isset($_SESSION["typ"])) { if ($_SESSION["typ"] < 100) echo "rezerwuj.php?s_id=" . $row["sala_id"]; else echo "rezerwuj.php?s_id=" . $row["sala_id"]; } else echo "rezerwuj.php?s_id=" . $row["sala_id"]; ?>>Rezerwuj</a></td>
			</tr> 
<?php
		}
		echo "</table>";
	}
}?>
</body></html>