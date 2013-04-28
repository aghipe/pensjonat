<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8">
	    <link href="pensionmanager.css" rel="stylesheet" type="text/css" title="default">
	    <title>Pension Manager 1.0</title>
	    <script type="text/javascript">
		<!--
		function confirmDelete()
		{
			var agree=confirm("Wynajęcie zostanie usunięte z bazy danych. Czy na pewno chcesz kontynuować?");
			if (agree)
			return true ;
			else
			return false ;
		}
// -->
</script>
	</head>
	  <body>

<?php 

class pension
{
	private $reservations = array();
	private $clients = array();
	private $rooms = array();
	private $db_connection;
	
	static function main()
	{
		$pension = new pension;
		$db_connection = new db_connection;
		
		$db_connection->establish_connection();
		
		$reservations = $pension->get_all_reservations();
		$clients = $pension->get_all_clients();
		$rooms = $pension->get_all_rooms();
		
		$pension->display_reservations($reservations);
		
		$db_connection->close_connection();
	}
	
	function get_all_reservations()
	{
		$allreservations = array();
		$row = array();
		$sql = "SELECT * FROM wynajecia";
		$getallreservations = mysql_query($sql) or die 
			("Could not execute query : $query." . mysql_error());
		$index = 0;
		while($row = mysql_fetch_assoc($getallreservations))
		{
			$newreservation = new reservation;
			$newreservation->set_reservationid($row["nr_rezerwacji"]);
			$newreservation->set_clientsid($row["nr_klienta"]);
			$newreservation->set_clientsfname($row["imie_klienta"]);
			$newreservation->set_clientslname($row["nazwisko_klienta"]);
			$newreservation->set_roomsno($row["nr_pokoju"]);
			$newreservation->set_roomscapacity($row["pojemnosc_pokoju"]);
			$newreservation->set_arrivaldate($row["data_przyjazdu"]);
			$newreservation->set_departuredate($row["data_wyjazdu"]);
			$newreservation->set_status($row["stan"]);
    		$allreservations[$index] = $newreservation;
    		$index = $index + 1;
		}
		return $allreservations;
	}
	
	function display_reservations($reservations)
	{
		echo "<div class='DbTable'>";
		echo "<table width=100%>";
		echo "<tr>";
        echo "<td>Numer rezerwacji</td>";
		echo "<td>Numer klienta</td>";
		echo "<td>Imie klienta</td>";
		echo "<td>Nazwisko klienta</td>";
		echo "<td>Numer pokoju</td>";
		echo "<td>Pojemność pokoju</td>";
		echo "<td>Data przyjazdu</td>";
		echo "<td>Data wyjazdu</td>";
		echo "<td>Stan</td>";
		echo "<td>Działania</td>";
    	echo "</tr>";
		foreach($reservations as $reservation) 
		{
    		$reservation->generate_tablerow();
		}
		echo "</table>";
		echo "<table>";
		echo "<tr>";
        echo "<td width=50%>Odśwież</td>";
		echo "<td>Dodaj nową rezerwację</td>";
    	echo "</tr>";
		echo "</table>";
		echo "</div>";
	}
	
	function get_all_clients()
	{
		$allclients = array();
		$sql = "SELECT * FROM klienci";
		$getallclients = mysql_query($sql) or die 
			("Could not execute query : $query." . mysql_error());
		$index = 0;
		while($row = mysql_fetch_assoc($getallclients))
		{
			$newclient = new client;
			$newclient->set_fname($row["imie"]);
			$newclient->set_lname($row["nazwisko"]);
			$newclient->set_clientsid($row["nr_klienta"]);
			$newclient->set_address($row["adres"]);
			$newclient->set_telephone($row["telefon"]);
			$newclient->set_email($row["e_mail"]);
    		$allclients[$index] = $newclient;
    		$index = $index + 1;
		}
		return $allclients;
	}
	
	function get_all_rooms()
	{
		$allrooms = array();
		$sql = "SELECT * FROM pokoje";
		$getallrooms = mysql_query($sql) or die 
			("Could not execute query : $query." . mysql_error());
		$index = 0;
		while($row = mysql_fetch_assoc($getallrooms))
		{
			$newroom = new room;
			$newroom->set_roomno($row["nr_pokoju"]);
			$newroom->set_capacity($row["pojemnosc"]);
    		$allrooms[$index] = $newroom;
    		$index = $index + 1;
		}
		return $allrooms;
	}
}

class reservation
{
	private $reservationid;
	private $clientsid;
	private $clientsfname;
	private $clientslname;
	private $roomsno;
	private $roomscapacity;
	private $arrivaldate;
	private $departuredate;
	
	public function set_reservationid($reservationid)
	{
  		$this->reservationid = $reservationid;
	}

	public function get_reservationid() 
	{
  		return $this->reservationid;
	}
	
	public function set_clientsid($clientsid)
	{
  		$this->clientsid = $clientsid;
	}

	public function get_clientsid() 
	{
  		return $this->clientsid;
	}
	
	public function set_clientsfname($clientsfname)
	{
  		$this->clientsfname = $clientsfname;
	}

	public function get_clientsfname() 
	{
  		return $this->clientsfname;
	}
	
	public function set_clientslname($clientslname)
	{
  		$this->clientslname = $clientslname;
	}

	public function get_clientslname() 
	{
  		return $this->clientslname;
	}
	
	public function set_roomsno($roomsno)
	{
  		$this->roomsno = $roomsno;
	}

	public function get_roomsno() 
	{
  		return $this->roomsno;
	}
	
	public function set_roomscapacity($roomscapacity)
	{
  		$this->roomscapacity = $roomscapacity;
	}

	public function get_roomscapacity() 
	{
  		return $this->roomscapacity;
	}
	
	public function set_arrivaldate($arrivaldate)
	{
  		$this->arrivaldate = $arrivaldate;
	}

	public function get_arrivaldate() 
	{
  		return $this->arrivaldate;
	}
	
	public function set_departuredate($departuredate)
	{
  		$this->departuredate = $departuredate;
	}

	public function get_departuredate() 
	{
  		return $this->departuredate;
	}
	
	public function set_status($status)
	{
  		$this->status = $status;
	}

	public function get_status() 
	{
  		return $this->status;
	}
	
	
	function generate_tablerow()
	{
		echo "<tr>";
        echo "<td>" . $this->get_reservationid() . "</td>";
		echo "<td>" . $this->get_clientsid() . "</td>";
		echo "<td>" . $this->get_clientsfname() . "</td>";
		echo "<td>" . $this->get_clientslname() . "</td>";
		echo "<td>" . $this->get_roomsno() . "</td>";
		echo "<td>" . $this->get_roomscapacity() . "</td>";
		echo "<td>" . $this->get_arrivaldate() . "</td>";
		echo "<td>" . $this->get_departuredate() . "</td>";
		echo "<td>" . $this->get_status() . "</td>";
		echo "<td><form method = 'post' action = 'reservationedit.php?reservationid=Sthis->get_reservationid()'>
			<input type ='submit' value='Edytuj wynajęcie' />
				<form method = 'post' action = 'reservationdelete.php?reservationid=Sthis->get_reservationid()'>
					<input type ='submit' value='Usuń wynajęcie' onClick='return confirmDelete()' /></td>";
    	echo "</tr>";
	}
	
	function set_values_from_form($clientsid, $roomscapacity)
	{
		$this->set_clientsfname($_POST['imie']);
		$this->set_clientslname($_POST['nazwisko']);
		$this->set_clientsid($clientsid);
		$this->set_roomscapacity($roomscapacity);
		$this->set_arrivaldate($_POST['from']);
		$this->set_departuredate($_POST['to']);
	}
	
	function add_to_db($clientsid, $roomscapacity)
	{
		$clientsfnameforsql = $this->get_clientsfname();
		$clientslnameforsql = $this->get_clientslname();
		$clientsidforsql = $clientsid;
		$roomscapacityforsql = $roomscapacity;
		$arrivaldateforsql = $this->get_arrivaldate();
		$departuredateforsql = $this->get_departuredate();
		$statusforsql = 'rezerwacja';
		$sql = "INSERT INTO wynajecia (nr_klienta, imie_klienta, nazwisko_klienta, pojemnosc_pokoju, data_przyjazdu, data_wyjazdu, stan)
	 		VALUES	('$clientsidforsql', '$clientsfnameforsql', '$clientslnameforsql', '$roomscapacityforsql', '$arrivaldateforsql',
	 			'$departuredateforsql', '$statusforsql')";
		$newclient = mysql_query($sql) or die 
				("Could not execute query : $query." . mysql_error());
	}	
}	

class client
{
	private $fname;
	private $lname;
	private $clientsid;
	private $address;
	private $telephone;
	private $email;
	
	
	public function set_fname($fname)
	{
  		$this->fname = $fname;
	}

	public function get_fname() 
	{
  		return $this->fname;
	}
	
	public function set_lname($lname)
	{
  		$this->lname = $lname;
	}

	public function get_lname() 
	{
  		return $this->lname;
	}
	
	public function set_clientsid($clientsid)
	{
  		$this->clientsid = $clientsid;
	}

	public function get_clientsid() 
	{
  		return $this->clientsid;
	}
	
	public function set_address($address)
	{
  		$this->address = $address;
	}

	public function get_address() 
	{
  		return $this->address;
	}
	
	public function set_telephone($telephone)
	{
  		$this->telephone = $telephone;
	}

	public function get_telephone() 
	{
  		return $this->telephone;
	}
	
	public function set_email($email)
	{
  		$this->email = $email;
	}

	public function get_email() 
	{
  		return $this->email;
	}
}

class room
{
	private $roomno;
	private $capacity;
	
	public function set_roomno($roomno)
	{
  		$this->roomno = $roomno;
	}

	public function get_roomno() 
	{
  		return $this->roomno;
	}
	
	public function set_capacity($capacity)
	{
  		$this->capacity = $capacity;
	}

	public function get_capacity() 
	{
  		return $this->capacity;
	}
}

class db_connection
{
	public $hostname;
	public $user;
	public $pass;
	public $dbase;
	
	function establish_connection()
	{
		$hostname='localhost'; 
		$user='kpituch'; 
		$pass='pensjonat'; 
		$dbase='pensjonat';
		$connection = mysql_connect("$hostname", "$user", "$pass") 
			or die ("Can't connect to MySQL");
		mysql_select_db($dbase , $connection) or die ("Can't select database.");
	}
	
	function close_connection()
	{ 
		mysql_close();
	}
}

pension::main();

?>

	</body>
</html>