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
} 

if (isset($_GET["editr_id"])) {
	$ileczasu = $_POST["dlugosc"] * 60;
	$czasod = date('H:i:s',strtotime($_POST["czas_od"]));
	$czasdo = date('H:i:s',strtotime("+{$ileczasu} minutes",strtotime($czasod)));
	$sql = "UPDATE rezerwacje SET kiedy='{$_POST["kiedy"]}',czas_od='{$czasod}',czas_do='{$czasdo}' WHERE rez_id = '{$_GET["editr_id"]}';";
	$conn->query($sql);
	$sql = "DELETE FROM zam_uslugi WHERE rez_id = '{$_GET["editr_id"]}';";
	$conn->query($sql);
	$sql = "SELECT du.usluga_id, du.cena FROM dost_uslugi du INNER JOIN uslugi u ON (du.usluga_id = u.usluga_id) WHERE sala_id = {$_POST['s_id']}";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) { 
			$a = "us" . $row['usluga_id'];
			if ($_POST[$a] > 0) {
				$sql = "INSERT INTO zam_uslugi (usluga_id,rez_id,ilosc) VALUES ({$row["usluga_id"]},{$_GET["editr_id"]},{$_POST[$a]});";
				$conn->query($sql);
			}
		}
	} 
	header("Location: rezerwacje.php");
	exit;
}

$sql = "SELECT czynny_od, czynny_do, czynny_dni FROM sale,obiekty WHERE sale.sala_id = {$_POST["s_id"]} AND sale.obiekt_id = obiekty.obiekt_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
}
$czynny_od = $row["czynny_od"];
$czynny_do = $row["czynny_do"];
$czynny_dni = $row["czynny_dni"];
$dayofweek = date('w', strtotime($_POST["kiedy"]));
if ($dayofweek == 0) 
	$dayofweek = 7;
$currentDateTime = date('Y-m-d');
if ($currentDateTime > $_POST["kiedy"]) {
	exit;
}
if ($row["czynny_dni"] & pow(2,$dayofweek-1) == pow(2,$dayofweek-1)) {
	
$ileczasu = $_POST["dlugosc"] * 60;
$wstawiono = false;
$niemozna = false;
$czasod = date('H:i:s',strtotime($_POST["czas_od"]));
$czasdo = date('H:i:s',strtotime("+{$ileczasu} minutes",strtotime($czasod)));
//$czasdo = strtotime("+" . $ileczasu . " minutes",$czynny_od);

if (isset($_SESSION["id"])) {
	$sql = "SELECT czas_od,czas_do FROM rezerwacje WHERE sala_id = '{$_POST["s_id"]}' AND kiedy = '{$_POST["kiedy"]}' AND oplacona = 1 ORDER BY czas_od";
	$result = $conn->query($sql);
	$do = strtotime($czynny_od);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$od = strtotime($row["czas_od"]);
			if (strtotime($_POST["czas_od"]) >= $do && strtotime($czasdo) <= $od) {
				$sql = "INSERT INTO rezerwacje (uzytk_id,sala_id,kiedy,czas_od,czas_do) VALUES ({$_SESSION["id"]},{$_POST["s_id"]},'{$_POST["kiedy"]}','{$czasod}','{$czasdo}');";
				if ($conn->query($sql) === TRUE) {
					$sql = "SELECT rez_id FROM rezerwacje WHERE uzytk_id = {$_SESSION["id"]} ORDER BY rez_id DESC LIMIT 1";
					$result = $conn->query($sql);
					if ($result->num_rows > 0)
						$row = $result->fetch_assoc();
					$rez_id = $row["rez_id"];
					$sql = "SELECT du.usluga_id, du.cena FROM dost_uslugi du INNER JOIN uslugi u ON (du.usluga_id = u.usluga_id) WHERE sala_id = {$_POST['s_id']}";
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) { 
							$a = "us" . $row['usluga_id'];
							if ($_POST[$a] > 0) {
								$sql = "INSERT INTO zam_uslugi (usluga_id,rez_id,ilosc) VALUES ({$row["usluga_id"]},{$rez_id},{$_POST[$a]});";
								$conn->query($sql);
							}
						}
					} 
					header("Location: rezerwacje.php");
					exit;
				}
				else {
					echo("Error description: " . $conn -> error);
					echo "Dlaczego? {$czasdo}";
					header("Location: rezerwuj.php?s_id=" . $POST["s_id"]);
					exit;
				}
				$wstawiono = true;
				break;
			}
			$do = strtotime($row["czas_do"]);
		}
		if (!$niemozna && !$wstawiono) {
			if (strtotime($_POST["czas_od"]) >= $do && strtotime($czasdo) <= strtotime($czynny_do)) {
				$sql = "INSERT INTO rezerwacje (uzytk_id,sala_id,kiedy,czas_od,czas_do) VALUES ({$_SESSION["id"]},{$_POST["s_id"]},'{$_POST["kiedy"]}','{$czasod}','{$czasdo}');";
				if ($conn->query($sql) === TRUE) {
					$sql = "SELECT rez_id FROM rezerwacje WHERE uzytk_id = {$_SESSION["id"]} ORDER BY rez_id DESC LIMIT 1";
					$result = $conn->query($sql);
					if ($result->num_rows > 0)
						$row = $result->fetch_assoc();
					$rez_id = $row["rez_id"];
					$sql = "SELECT du.usluga_id, du.cena FROM dost_uslugi du INNER JOIN uslugi u ON (du.usluga_id = u.usluga_id) WHERE sala_id = {$_POST['s_id']}";
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) { 
							$a = "us" . $row['usluga_id'];
							if ($_POST[$a] > 0) {
								$sql = "INSERT INTO zam_uslugi (usluga_id,rez_id,ilosc) VALUES ({$row["usluga_id"]},{$rez_id},{$_POST[$a]});";
								$conn->query($sql);
							}
						}
						header("Location: rezerwacje.php");
						$wstawiono = true;
						exit;
					} 
					header("Location: rezerwacje.php");
					exit;
				}
				else {
					echo("Error description: " . $conn -> error);
				}
			}
			else {
				header("Location: rezerwuj.php?s_id=" . $POST["s_id"]);
				exit;
			}
		}
	}
	else {
		$sql = "INSERT INTO rezerwacje (uzytk_id,sala_id,kiedy,czas_od,czas_do) VALUES ({$_SESSION["id"]},{$_POST["s_id"]},'{$_POST["kiedy"]}','{$czasod}','{$czasdo}');";
		if ($conn->query($sql) === TRUE) {
			$sql = "SELECT rez_id, s.cena FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) WHERE uzytk_id = {$_SESSION["id"]} ORDER BY rez_id DESC LIMIT 1";
			$result = $conn->query($sql);
			if ($result->num_rows > 0)
				$row = $result->fetch_assoc();
			$rez_id = $row["rez_id"];
			$sql = "SELECT du.usluga_id, du.cena FROM dost_uslugi du INNER JOIN uslugi u ON (du.usluga_id = u.usluga_id) WHERE sala_id = {$_POST['s_id']}";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) { 
					$a = "us" . $row['usluga_id'];
					if ($_POST[$a] > 0) {
						$sql = "INSERT INTO zam_uslugi (usluga_id,rez_id,ilosc) VALUES ({$row["usluga_id"]},{$rez_id},{$_POST[$a]});";
						$conn->query($sql);
					}
				}
			} 
			header("Location: rezerwacje.php");
			$wstawiono = true;
			exit;
		}
		else {
			echo("Error description: " . $conn -> error);
			echo "Dlaczego? {$czasdo}";
		}
	}
}
}
else {
	header("Location: rezerwuj.php?s_id=" . $POST["s_id"]);
}
