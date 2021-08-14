<?php require("head.php"); ?>
<form method='post'>
Okres czasu:<input type='date' name='data_od' value='<?php if (isset($_POST["submit"])) echo $_POST["data_od"]; else echo "2019-01-01"; ?>'>
 - <input type='date' name='data_do' value='<?php if (isset($_POST["submit"])) echo $_POST["data_do"]; else echo date("Y-m-d"); ?>'>
<input type='submit' name='submit' value='Wyświetl'>
</form> <?php
if (isset($_POST["submit"])) {
	$koniec = $_POST["data_do"];
	$pocz = $_POST["data_od"];
}
else {
	$koniec = date("Y-m-d");
	$pocz = "2019-01-01";
}
$dni = ceil((strtotime($koniec) - strtotime($pocz))/60/60/24); 
$sql = "SELECT COUNT(*) AS 'rejestracji' FROM uzytkownicy WHERE data_rejestracji >= '{$pocz}' AND data_rejestracji <= '{$koniec}' AND typ_konta = 1;";
$result = $conn->query($sql);
if ($result->num_rows >0)
	$row = $result->fetch_assoc(); 
$rejestracji = $row["rejestracji"];
?>

<br><b>Dane z okresu <?php echo $pocz; ?> - <?php echo date("Y-m-d"); ?>: </b><br><br>
Ilość zarejestrowanych użytkowników: <?php echo $rejestracji; ?><br><?php
$sql = "SELECT SUM(wartosc) AS 'wartosc',COUNT(*) AS 'ilosc' FROM (SELECT s.cena*(TIME_TO_SEC(TIMEDIFF(czas_do,czas_od))/60)/60+IFNULL(SUM(du.cena*zu.ilosc),0) AS wartosc FROM rezerwacje r INNER JOIN sale s ON (r.sala_id = s.sala_id) LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id) LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE kiedy >= '{$pocz}' AND kiedy <= '{$koniec}' AND oplacona = 1 GROUP BY r.rez_id) AS tabela;";
$result = $conn->query($sql);
if ($result->num_rows >0)
	$row = $result->fetch_assoc(); 
$przychody = $row["wartosc"]; ?>
Całkowite przychody z <?php echo $row["ilosc"]; ?> rezerwacji: <?php echo $przychody; ?> PLN<br> <?php
$sql = "SELECT SUM(wartosc) AS 'wartosc' FROM (SELECT IFNULL(SUM((du.cena-du.marza)*zu.ilosc),0) AS wartosc FROM rezerwacje r
INNER JOIN sale s ON (r.sala_id = s.sala_id)
LEFT JOIN zam_uslugi zu ON (r.rez_id = zu.rez_id)
LEFT JOIN dost_uslugi du ON (zu.usluga_id = du.usluga_id AND r.sala_id = du.sala_id) WHERE kiedy >= '{$pocz}' AND kiedy <= '{$koniec}' AND oplacona = 1 
GROUP BY r.rez_id) AS tabela;";
$result = $conn->query($sql);
if ($result->num_rows >0)
	$row = $result->fetch_assoc(); 
$wydatki_uslugi = $row["wartosc"]; ?>

Wydatki na realizacje usług: <?php echo $wydatki_uslugi; ?> PLN <br> <?php
$sql = "SELECT SUM( pensja/30 * DATEDIFF(LEAST(p.umowa_do,'{$koniec}'),GREATEST(u.data_rejestracji,'{$pocz}')) ) AS 'pensje' FROM pracownicy p INNER JOIN uzytkownicy u ON (p.uzytk_id = u.uzytk_id) WHERE u.data_rejestracji < '{$koniec}';";
$result = $conn->query($sql);
if ($result->num_rows >0)
	$row = $result->fetch_assoc(); 
$pensje = $row["pensje"]; ?>

Pensje pracowników: <?php echo intval($pensje); ?> PLN <br> <?php
$sql = "SELECT SUM(kwota) AS 'oplaty' FROM dodatkowe_oplaty WHERE kiedy >= '{$pocz}' AND kiedy <= '{$koniec}'";
$result = $conn->query($sql);
if ($result->num_rows >0)
	$row = $result->fetch_assoc(); 
$oplaty = $row["oplaty"]; ?>
Dodatkowe opłaty: <?php echo $oplaty; ?> PLN <br>
Zysk: <?php echo ($przychody - $pensje - $oplaty - $wydatki_uslugi); ?> PLN <br>
