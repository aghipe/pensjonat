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
		
		$areroomsavailable = $pension->check_rooms_availability($rooms, $reservations);
		
		if ($areroomsavailable == true)
		{
			$newclient = new client;
			$newclient->set_values_from_form();
			$isclientregistered = $pension->check_if_client_is_registered($clients, $newclient);
			
			if ($isclientregistered == false)
			{
				$newclient->add_to_db();
			}
			$newclient->set_id_from_db();
			$clientsid = $newclient->get_clientsid();
			if ($isclientregistered == true)
			{
				$newclient->update_db_entry($clientsid);
			}
		
			$roomscapacity = 1;
			while ($roomscapacity <= 4)
			{
				$pension->process_reservations_from_form($clientsid, $roomscapacity);
				$roomscapacity = $roomscapacity + 1;
			}
			$pension->confirm_reservation();
		}
		else 
		{
			$pension->deny_reservation();
		}
		
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
    		$allreservations[$index] = $newreservation;
    		$index = $index + 1;
		}
		return $allreservations;
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
	
	function check_rooms_availability($rooms, $reservations)
	{
		$arrival = $_POST['from'];
		$departure = $_POST['to'];
		$capacity = 1;
		while ($capacity <= 4)
		{
			if ($capacity == 1)
			{
				$quantity = $_POST['jednos'];
			}
			if ($capacity == 2)
			{
				$quantity = $_POST['dwuos'];
			}
			if ($capacity == 3)
			{
				$quantity = $_POST['trzyos'];
			}
			if ($capacity == 4)
			{
				$quantity = $_POST['czteros'];
			}
			$roomstotal = 0;
			$roomsbooked = 0;
			foreach ($rooms as $room)
			{
				if ($room->get_capacity() == $capacity)
				{
					$roomstotal = $roomstotal + 1;
				}
			}
			foreach ($reservations as $reservation)
			{
				if ($reservation->get_roomscapacity() == $capacity)
				{
					$bookedarrivaldatetoint = str_replace("-", "", $reservation->get_arrivaldate());
					$bookeddeparturedatetoint = str_replace("-", "", $reservation->get_departuredate());
					$arrivaldatetochecktoint = str_replace("-", "", $arrival);
					$departuredatetochecktoint = str_replace("-", "", $departure);
					if (($bookedarrivaldatetoint >= $arrivaldatetochecktoint) && ($bookedarrivaldatetoint <= $departuredatetochecktoint))
					{
						$roomsbooked = $roomsbooked + 1;
						continue;		
					}
					if (($bookeddeparturedatetoint >= $arrivaldatetochecktoint) && ($bookeddeparturedatetoint <= $departuredatetochecktoint))
					{
						$roomsbooked = $roomsbooked + 1;
						continue;		
					}
					if (($bookedarrivaldatetoint < $arrivaldatetochecktoint) && ($bookeddeparturedatetoint > $departuredatetochecktoint))
					{
						$roomsbooked = $roomsbooked + 1;
					}
				}
			}
			if ($roomsbooked + $quantity > $roomstotal)
			{
				return false;
			}
			$capacity = $capacity + 1;
		}
		return true;
	}

	function check_if_client_is_registered($clients, $newclient)
	{
		foreach ($clients as $client)
		{
			if ($client->confirm_identity
				($newclient->get_fname(), $newclient->get_lname(), $newclient->get_address(), $newclient->get_telephone()) == true)
			{
				return true;	
			}
		}
		return false;
	}
	
	function process_reservations_from_form($clientsid, $roomscapacity)
	{
		$roomsquantity;
		if ($roomscapacity == 1)
		{
			$roomsquantity = $_POST['jednos'];
		}
		if ($roomscapacity == 2)
		{
			$roomsquantity = $_POST['dwuos'];
		}
		if ($roomscapacity == 3)
		{
			$roomsquantity = $_POST['trzyos'];
		}
		if ($roomscapacity == 4)
		{
			$roomsquantity = $_POST['czteros'];
		}
		while ($roomsquantity > 0)
		{
			$newreservation = new reservation;
			$newreservation->set_values_from_form($clientsid, $roomscapacity);
			$newreservation->add_to_db($clientsid, $roomscapacity);
			$roomsquantity = $roomsquantity - 1;
		}
	}

	function confirm_reservation()
	{
		echo "Rezerwacja została zarejestrowana! Dziękujemy!";
	}
	
	function deny_reservation()
	{
		echo ("Przepraszamy, ale Twoja rezerwacja nie została zarejestrowana
					z powodu braku dostatecznej liczby wolnych pokoi.");
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
	
	function confirm_identity($fnametocompare, $lnametocompare, $addresstocompare, $telephonetocompare)
	{
		if (($this->get_fname() == $fnametocompare) && ($this->get_lname() == $lnametocompare))
		{
			if ($this->get_address() == $addresstocompare)
			{
				return true;
			}
			if ($this->get_telephone() == $telephonetocompare)
			{
				return true;
			}
		}
		return false;
	}
	
	function set_values_from_form()
	{
		$space = ' ';
		$this->set_fname($_POST['imie']);
		$this->set_lname($_POST['nazwisko']);
		$this->set_address($_POST['ulica'].$space.$_POST['nrdomu'].$space.$_POST['kod'].$space.$_POST['miasto']);
		$this->set_telephone($_POST['tel']);
		$this->set_email($_POST['mail']);
	}
	
	function add_to_db()
	{
		$fnameforsql = $this->get_fname();
		$lnameforsql = $this->get_lname();
		$addressforsql = $this->get_address();
		$telephoneforsql = $this->get_telephone();
		$emailforsql = $this->get_email();
		$sql = "INSERT INTO klienci (imie, nazwisko, adres, telefon, e_mail)
	 		VALUES	('$fnameforsql', '$lnameforsql', '$addressforsql', '$telephoneforsql', '$emailforsql')";
		$newclient = mysql_query($sql) or die 
			("Could not execute query : $query." . mysql_error());
	}
	
	function set_id_from_db()
	{
		$fnameforsql = $this->get_fname();
		$lnameforsql = $this->get_lname();
		$addressforsql = $this->get_address();
		$telephoneforsql = $this->get_telephone();
		$sql = "SELECT nr_klienta FROM klienci WHERE
	    	(imie='$fnameforsql' AND nazwisko='$lnameforsql' AND (adres='$addressforsql' OR telefon='$telephoneforsql'))";
		$getclientsid = mysql_query($sql) or die 
			("Could not execute query : $query." . mysql_error());
		$row = mysql_fetch_row($getclientsid);
		$this->set_clientsid($row[0]);
	}
	
	function update_db_entry($clientsid)
	{
		$addressforsql = $this->get_address();
		$telephoneforsql = $this->get_telephone();
		$emailforsql = $this->get_email();
		$sql = "UPDATE	klienci SET	
			adres='$addressforsql', telefon='$telephoneforsql', e_mail='$emailforsql'
				WHERE nr_klienta='$clientsid'";
		$updateclient = mysql_query($sql) or die 
			("Could not execute query : $query." . mysql_error());
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
<br>
<a href="home.html">Powrót na stronę pensjonatu</a>
