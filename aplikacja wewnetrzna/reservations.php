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
<div id="container">
<div id="header">
<center>
<h1>Pension Manager 1.0</h1><BR><BR>
</div>
<BR>
<div id="menu" align="center">
<a href="Clients.php">Klienci</a> |
<b href="Reservations.php?action=browse">Wynajęcia</b> |
<a href="Rooms.php">Pokoje</a><BR>
</div>
</div>
<div id="table">
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
		
		$pension->execute_query($clients, $reservations);
		$reservations = $pension->get_all_reservations();
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
	
	function execute_query($clients, $reservations)
	{
		if ($_GET)
		{
			$action = $_GET['action'];
			$clientsfname = null;
			$clientslname = null;
			if ($action == "browse" || $action == "edit")
			{
				return;
			}
			else if ($action == "delete")
			{
				$id = $_GET['id'];
				foreach ($reservations as $reservation) 
				{
					if ($reservation->get_reservationid() == $id)
					{
						$reservation->delete_from_db();
						break;
					}
				}
				return;
			}
			else if ($action == "add") 
			{
				foreach ($clients as $client)
				{
					if ($client->get_clientsid() == $_POST['clientsid'])
					{
						$clientsfname = $client->get_fname();
						$clientslname = $client->get_lname();
						break;
					}
				}
				$newreservation = new reservation();
				$newreservation->set_values_from_form($clientsfname, $clientslname);
				$newreservation->add_to_db();
				return;
			}
			else if ($action == "update")
			{
				$id = $_GET['id'];
				foreach ($clients as $client)
				{
					if ($client->get_clientsid() == $_POST['clientsid'])
					{
						$clientsfname = $client->get_fname();
						$clientslname = $client->get_lname();
						break;
					}
				}
				foreach ($reservations as $reservation)
				{
					if ($reservation->get_reservationid() == $id)
					{
						$reservation->set_values_from_form($clientsfname, $clientslname);
						$reservation->update_in_db();
						break;
					}
				}
			}
		}
	}
	
	function display_reservations($reservations)
	{
		echo "<div class='DbTable'>";
		echo "<table width=100%>";
		echo "<tr>";
        echo "<td>Numer wynajęcia</td>";
		echo "<td>Numer klienta</td>";
		echo "<td>Imie klienta</td>";
		echo "<td>Nazwisko klienta</td>";
		echo "<td>Numer pokoju</td>";
		echo "<td>Pojemność pokoju</td>";
		echo "<td>Data przyjazdu</td>";
		echo "<td>Data wyjazdu</td>";
		echo "<td>Stan</td>";
		echo "<td width=20%>Działania</td>";
    	echo "</tr>";
		foreach($reservations as $reservation) 
		{
			$reservation->generate_tablerow();
		}
		if ($_GET && $_GET['action']=="edit" && $_GET['id'] == "0")
		{
			$newreservation = new reservation();
			$newreservation->set_reservationid("0");
			$newreservation->generate_tablerow();
		}
		echo "</table>";
		echo "<table>";
		echo "<tr>";
        echo "<td><form style='display: inline' method = 'post' action = 'reservations.php?action=browse'>
						<input type ='submit' value='   Odśwież   ' /></form>";
		echo "<form style='display: inline' method = 'post' action = 'reservations.php?action=edit&id=0'>
						<input type ='submit' value='   Dodaj nowe wynajęcie   ' /></form></td>";
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
		if ($_GET && $_GET['action'] == 'edit' && $_GET['id'] == $this->get_reservationid())
		{
			if ($this->get_reservationid() == "0")
			{
				echo "<form name = 'edit' action='reservations.php?action=add' method='post'>";
				echo "<td></td>";
				echo "<td><input size=10 type='text' name='clientsid' value='".$this->get_clientsid()."'></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td><input size=2 type='text' name='roomsno'></td>";
				echo "<td><input size=1 type='text' name='roomscapacity'></td>";
				echo "<td><input size=10 type='text' name='arrivaldate'></td>";
				echo "<td><input size=10 type='text' name='departuredate'></td>";
				echo "<td><input type='text' name='status'></td>";
				echo "<td><input type='submit' name='save' value='   Dodaj   '</td>";
			}
			else
			{
				echo "<form name = 'edit' action='reservations.php?action=update&id=".$this->get_reservationid()."' method='post'>";
				echo "<td>" . $this->get_reservationid() . "</td>";
				echo "<td><input size=10 type='text' name='clientsid' value='".$this->get_clientsid()."'></td>";
				echo "<td>" . $this->get_clientsfname() . "</td>";
				echo "<td>" . $this->get_clientslname() . "</td>";
				echo "<td><input size=2 type='text' name='roomsno' value='".$this->get_roomsno()."'></td>";
				echo "<td><input size=1 type='text' name='roomscapacity' value='".$this->get_roomscapacity()."'></td>";
				echo "<td><input size=10 type='text' name='arrivaldate' value='".$this->get_arrivaldate()."'></td>";
				echo "<td><input size=10 type='text' name='departuredate' value='".$this->get_departuredate()."'></td>";
				echo "<td><input type='text' name='status' value='".$this->get_status()."'></td>";
				echo "<td><input type='submit' name='save' value='   Zapisz   '</td>";
			}
			echo "</form>";
			echo "<form style='display: inline' method = 'post' action = 'reservations.php?action=browse'>
						<input type ='submit' value='   Anuluj   ' /></form></td>";
		}
		else 
		{
			echo "<td>" . $this->get_reservationid() . "</td>";
			echo "<td>" . $this->get_clientsid() . "</td>";
			echo "<td>" . $this->get_clientsfname() . "</td>";
			echo "<td>" . $this->get_clientslname() . "</td>";
			echo "<td>" . $this->get_roomsno() . "</td>";
			echo "<td>" . $this->get_roomscapacity() . "</td>";
			echo "<td>" . $this->get_arrivaldate() . "</td>";
			echo "<td>" . $this->get_departuredate() . "</td>";
			echo "<td>" . $this->get_status() . "</td>";
			echo "<td><form style='display: inline' method = 'post' action = 'reservations.php?action=edit&id=".$this->get_reservationid()."'>
				<input type ='submit' value='Edytuj wynajęcie' /></form>
					<form style='display: inline' method = 'post' action = 'reservations.php?action=delete&id=".$this->get_reservationid()."'>
						<input type ='submit' value='Usuń wynajęcie' onClick='return confirmDelete()' /></form></td>";
		}
		echo "</tr>";
	}
	
	function set_values_from_form($clientsfname, $clientslname)
	{
		$this->set_clientsid($_POST['clientsid']);
		$this->set_clientsfname($clientsfname);
		$this->set_clientslname($clientslname);
		$this->set_roomsno($_POST['roomsno']);
		$this->set_roomscapacity($_POST['roomscapacity']);
		$this->set_arrivaldate($_POST['arrivaldate']);
		$this->set_departuredate($_POST['departuredate']);
		$this->set_status($_POST['status']);
	}
	
	function add_to_db()
	{
		$clientsfnameforsql = $this->get_clientsfname();
		$clientslnameforsql = $this->get_clientslname();
		$clientsidforsql = $this->get_clientsid();
		$roomsnoforsql= $this->get_roomsno();
		$roomscapacityforsql = $this->get_roomscapacity();
		$arrivaldateforsql = $this->get_arrivaldate();
		$departuredateforsql = $this->get_departuredate();
		$statusforsql = $this->get_status();
		$sql = "INSERT INTO wynajecia 
			(nr_klienta, imie_klienta, nazwisko_klienta, nr_pokoju, pojemnosc_pokoju, data_przyjazdu, data_wyjazdu, stan) VALUES 
				('$clientsidforsql', '$clientsfnameforsql', '$clientslnameforsql', '$roomsnoforsql', '$roomscapacityforsql', '$arrivaldateforsql',
	 				'$departuredateforsql', '$statusforsql')";
		$newreservation = mysql_query($sql) or die 
				("Could not execute query : $query." . mysql_error());
	}

	function update_in_db()
	{
		$reservationidforsql = $this->get_reservationid();
		$clientsidforsql= $this->get_clientsid();
		$clientsfnameforsql = $this->get_clientsfname();
		$clientslnameforsql = $this->get_clientslname();
		$roomscapacityforsql = $this->get_roomscapacity();
		$roomsnoforsql = $this->get_roomsno();
		$arrivaldateforsql = $this->get_arrivaldate();
		$departuredateforsql = $this->get_departuredate();
		$statusforsql = $this->get_status();
		$sql = "UPDATE wynajecia SET nr_klienta='$clientsidforsql', imie_klienta='$clientsfnameforsql',
			nazwisko_klienta='$clientslnameforsql', nr_pokoju='$roomsnoforsql', pojemnosc_pokoju='$roomscapacityforsql',
				data_przyjazdu='$arrivaldateforsql', data_wyjazdu='$departuredateforsql', stan='$statusforsql'
					WHERE nr_rezerwacji='$reservationidforsql'";
		$updatereservation = mysql_query($sql) or die 
				("Could not execute query : $query." . mysql_error());
	}
	
	function delete_from_db()
	{
		$idforsql = $this->get_reservationid();
		$sql = "DELETE FROM wynajecia where nr_rezerwacji='$idforsql'";
		$deletereservation = mysql_query($sql) or die 
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
</div>

</body>

</html>