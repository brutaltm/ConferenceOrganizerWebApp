<?php 
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
if (!($row["czynny_dni"] & pow(2,$dayofweek-1) == pow(2,$dayofweek-1))) {
	echo "Nieczynne";
	exit;
}
if (date('Y-m-d') > $_POST["kiedy"]) {
	echo "Termin już minął";
	exit;
}
$sql = "SELECT czas_od,czas_do FROM rezerwacje WHERE sala_id = '{$_POST["s_id"]}' AND kiedy = '{$_POST["kiedy"]}' AND oplacona = 1 ORDER BY czas_od";
$result = $conn->query($sql);
$od = $czynny_od;

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if (strtotime("+30 minutes", strtotime($od)) <= strtotime($row["czas_od"]))
			echo $od . " - " . $row["czas_od"] . "<br>";
		$od = $row["czas_do"];
	}
	if (strtotime("+30 minutes", strtotime($od)) <= strtotime($czynny_do))
		echo $od . " - " . $czynny_do . "<br>";
}
else
	echo date('H:i',strtotime($czynny_od)) . " - " . date('H:i',strtotime($czynny_do)) . "<br>";

?>