<?php require "head.php"; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		$('#dost').load("wolneterminy.php", {s_id: $('input[name=s_id]').val(), kiedy: $('input[name=kiedy]').val()});
		console.log($('input[name=s_id]').val());
		console.log($('input[name=kiedy]').val());
		$('#kiedy').on('change', function() {
			$('#dost').load("wolneterminy.php", {s_id: $('input[name=s_id]').val(), kiedy: $('input[name=kiedy]').val()});
		});
		updatecena();
	});
</script>
<h2>Tworzenie rezerwacji:</h4> 
<?php 
if (isset($_GET["s_id"]))
	$sql = "SELECT nazwa, miejsca, cena FROM sale WHERE sala_id = {$_GET["s_id"]};";
else {
	$sql = "SELECT nazwa, miejsca, cena FROM sale WHERE sala_id = (SELECT sala_id FROM rezerwacje WHERE rez_id = '{$_GET["editr_id"]}');";
}
$result = $conn->query($sql);
if ($result->num_rows > 0)
	$row = $result->fetch_assoc(); ?>
Sala: <?php echo $row["nazwa"]; ?> <br>
Miejsca: <?php echo $row["miejsca"]; ?> <br>
Cena: <?php $cena = $row["cena"]; echo $row["cena"] . " PLN/H"; ?> <br>
<script>
var cena = <?php echo $cena; ?>;
function updatecena() {
	sum=0;
    $("input[name^='cena_']").each(function(){
      sum+=Number($(this).val() * $("input[name='us"+this.id+"']").val());
    });
	$('#cena').text( $('#dl').val() * cena + sum);
}
</script> 
<?php
if (isset($_GET["editr_id"]) && isset($_SESSION["typ"])) { 
	if ($conn->query("SELECT uzytk_id FROM rezerwacje WHERE rez_id = '{$_GET["editr_id"]}' AND uzytk_id = '{$_SESSION["id"]}';") === FALSE && $_SESSION["typ"] < 100) {
		header("Location: rezerwacje.php");
		exit;
	} ?> 
	<form id='form' method="post" action="zarezerwuj.php?editr_id=<?php echo $_GET["editr_id"]; ?>"> <?php
	$sql = "SELECT * FROM rezerwacje WHERE rez_id = '{$_GET["editr_id"]}';";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
		$row = $result->fetch_assoc();
	else
		header("Location: rezerwacje.php"); 
	if (!(($_SESSION["id"] == $row["uzytk_id"]) || ($_SESSION["typ"]>=100)))
		header("Location: rezerwacje.php"); ?>
	<input type='hidden' name='s_id' value='<?php echo $row["sala_id"]; ?>'>
	Data: <input type='date' id='kiedy' name='kiedy' value='<?php echo $row["kiedy"]; ?>'> <br>
	Dostępne terminy: <br>
	<div id='dost'></div>
	Rezerwuj od: <input type="time" name="czas_od" value='<?php echo $row["czas_od"]; ?>' required> <br>
	Długość rezerwacji: <select name="dlugosc" id='dl' form="form" onchange="updatecena();"> 
	  <option value="0.5">0.5 H</option>
	  <option value="1">1 H</option>
	  <option value="1.5">1.5 H</option>
	  <option value="2" selected>2 H</option>
	  <option value="2.5">2.5 H</option>
	  <option value="3">3 H</option>
	  <option value="3.5">3.5 H</option>
	  <option value="4">4 H</option>
	  <option value="4.5">4.5 H</option>
	  <option value="5">5 H</option>
	</select> <br>
	Dodatkowe usługi: <br>
	<?php 
	$sql = "SELECT du.usluga_id, max_ilosc, nazwa, cena FROM dost_uslugi du INNER JOIN uslugi u ON (du.usluga_id = u.usluga_id) WHERE sala_id = {$row['sala_id']}";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) { ?>
			<input type='hidden' id='<?php echo $row["usluga_id"]; ?>' name='cena_<?php echo $row["usluga_id"]; ?>' value='<?php echo $row["cena"]; ?>'> <?php echo $row["nazwa"] . ": "; ?> <input type='number' name='us<?php echo $row["usluga_id"]; ?>' min='0' max=<?php echo $row["max_ilosc"]; ?> value='0' onchange="updatecena();"> <br>
	<?php	}
	} ?>
	Cena końcowa: <span id='cena'></span><br>
	<input type='submit' name='submit' value='Zarezerwuj'>
	</form> 
<?php
} 
else { ?>
<form id='form' method="post" action="zarezerwuj.php">
<input type='hidden' name='s_id' value=<?php echo $_GET["s_id"]; ?>>
Data: <input type='date' id='kiedy' name='kiedy' min='<?php echo date("Y-m-d"); ?>' value='<?php echo date("Y-m-d",time()); ?>'> <br>
Dostępne terminy: <br>
<div id='dost'></div>
<?php if (isset($_SESSION["typ"])) { ?>
Rezerwuj od: <input type="time" name="czas_od" value='10:00' required> <br>
Długość rezerwacji: <select name="dlugosc" id='dl' form="form" onchange="updatecena();"> 
  <option value="0.5">0.5 H</option>
  <option value="1">1 H</option>
  <option value="1.5">1.5 H</option>
  <option value="2" selected>2 H</option>
  <option value="2.5">2.5 H</option>
  <option value="3">3 H</option>
  <option value="3.5">3.5 H</option>
  <option value="4">4 H</option>
  <option value="4.5">4.5 H</option>
  <option value="5">5 H</option>
</select> <br>
Dodatkowe usługi: <br>
<?php 
$sql = "SELECT du.usluga_id, max_ilosc, nazwa, cena FROM dost_uslugi du INNER JOIN uslugi u ON (du.usluga_id = u.usluga_id) WHERE sala_id = {$_GET['s_id']}";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) { ?>
		<input type='hidden' id='<?php echo $row["usluga_id"]; ?>' name='cena_<?php echo $row["usluga_id"]; ?>' value='<?php echo $row["cena"]; ?>'> <?php echo $row["nazwa"] . ": "; ?> <input type='number' name='us<?php echo $row["usluga_id"]; ?>' min='0' max=<?php echo $row["max_ilosc"]; ?> value='0' onchange="updatecena();"> <br>
<?php	}
} ?>
Cena końcowa: <span id='cena'></span><br>
<input type='submit' name='submit' value='Zarezerwuj'>
<?php } else {?>
Zaloguj się aby mieć możliwość tworzenia rezerwacji.
<?php } ?>
</form> 
<?php 
} ?>
