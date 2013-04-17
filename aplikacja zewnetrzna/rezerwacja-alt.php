<?php 

class reservation
{
	static function main()
	{
		$reservation = new reservation;
		$rooms_capacity = 1;	
		$reservation->connect_to_db();
		$reservation->add_client();
		$clients_id = $reservation->get_clients_id();
		while ($rooms_capacity <= 4)
		{
			$reservation->add_reservation($rooms_capacity, $clients_id);
			$rooms_capacity = $rooms_capacity + 1;
		}
		$reservation->confirm_reservation();
		$reservation->disconnect_from_db();
	}
	
	function connect_to_db()
	{
		$hostname='localhost'; 
		$user='kpituch'; 
		$pass='pensjonat'; 
		$dbase='pensjonat';
		$connection = mysql_connect("$hostname", "$user", "$pass") 
			or die ("Can't connect to MySQL");
		mysql_select_db($dbase , $connection) or die ("Can't select database.");
	}
	
	function add_client()
	{
		$space = ' ';
		$fname = $_POST['imie'];
		$lname = $_POST['nazwisko'];
		$address = $_POST['ulica'].$space.$_POST['nrdomu'].$space.$_POST['kod'].$space.$_POST['miasto'];
		$telephone = $_POST['tel'];
		$email = $_POST['mail'];
		$sql1 = "INSERT INTO klienci (imie, nazwisko, adres, telefon, e_mail)
	 		VALUES	('$fname', '$lname', '$address', '$telephone', '$email')";
		$newclient = mysql_query($sql1) or die 
		("Could not execute query : $query." . mysql_error());
	}
	
	function get_clients_id()
	{
		$space = ' ';
		$fname = $_POST['imie'];
		$lname = $_POST['nazwisko'];
		$address = $_POST['ulica'].$space.$_POST['nrdomu'].$space.$_POST['kod'].$space.$_POST['miasto'];
		$telephone = $_POST['tel'];
		$email = $_POST['mail'];
		$sql2 = "SELECT nr_klienta	FROM	klienci	WHERE
	    	(imie='$fname' AND nazwisko='$lname' AND adres='$address' AND telefon='$telephone' AND e_mail='$email')";
		$getclientsid = mysql_query($sql2) or die 
			("Could not execute query : $query." . mysql_error());
		$row = mysql_fetch_row($getclientsid);
		return $row[0];
	}
	
	function add_reservation($rooms_capacity, $clientsid)
	{
		$rooms_quantity;
		$fname = $_POST['imie'];
		$lname = $_POST['nazwisko'];
		$arrival = $_POST['from'];
		$departure = $_POST['to'];
		$status = 'rezerwacja';
		if ($rooms_capacity == 1)
		{
			$rooms_quantity = $_POST['jednos'];
		}
		if ($rooms_capacity == 2)
		{
			$rooms_quantity = $_POST['dwuos'];
		}
		if ($rooms_capacity == 3)
		{
			$rooms_quantity = $_POST['trzyos'];
		}
		if ($rooms_capacity == 4)
		{
			$rooms_quantity = $_POST['czteros'];
		}
		while ($rooms_quantity > 0)
		{
			$sql3 = "INSERT INTO wynajecia (nr_klienta, imie_klienta, nazwisko_klienta, pojemnosc, data_przyjazdu, data_wyjazdu, stan)
	 			VALUES	('$clientsid', '$fname', '$lname', '$rooms_capacity', '$arrival', '$departure', '$status')";
		$newclient = mysql_query($sql3) or die 
		("Could not execute query : $query." . mysql_error());
		$rooms_quantity = $rooms_quantity - 1;
		}
	}	
	
	function confirm_reservation()
	{
		echo "Rezerwacja została zarejestrowana! Dziękujemy!";
	}	
	
	function disconnect_from_db()
	{ 
		mysql_close();
	}
}

reservation::main();

?>
<br>
<a href="index.html">Powrót na stronę pensjonatu</a>
