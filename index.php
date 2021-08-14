<?php require "head.php"; ?>
	Co chcesz zrobić?<br>
	- Przejrzyj <a href='oferta.php'>ofertę</a> naszych sal konferencyjnych<br>
<?php if (isset($_SESSION["nick"])) { ?>
	- Edytuj <a href='edytujDane.php'>dane osobowe</a><br>
<?php if ($_SESSION["typ"] == 1) { ?>
	- Moje <a href='rezerwacje.php'>rezerwacje</a><br>
<?php } if ($_SESSION["typ"] >= 10) { ?>
	- Przeglądaj <a href='rezerwacje.php'>rezerwacje</a> klientów<br>
	- Wyświetl zarejestrowanych <a href='uzytkownicy.php'>użytkowników</a><br>
<?php } if ($_SESSION["typ"]>=100) { ?>
	- Zarządzaj <a href='obiekty.php'>obiektami</a><br>
	- Zarządzaj <a href='uslugi.php'>usługi</a><br>
	- Zarządzaj <a href='sale.php'>salami</a><br>
	- Zarządzaj <a href='oplaty.php'>opłatami</a><br>
<?php } if ($_SESSION["typ"]==1000) { ?>
	- Zarządzaj <a href='pracownicy.php'>personelem</a><br>
	- Wyświetl <a href='raporty.php'>raporty</a>
<?php }
} ?>
	</body>
</html>