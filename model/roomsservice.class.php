<?php

require_once 'db.class.php';
require_once 'reservations.class.php';
require_once 'rooms.class.php';

/* Klasa za upravljanje prostorijama i rezervacijama. */
class RoomsService
{

  /* Funkcija ne prima ništa, a vraća polje svih prostorija iz baze.*/
  function getallRooms()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT room_number FROM rooms' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
			$arr[] = new Room( $row['room_number'] );

		return $arr;
	}

  /* Funkcija ne prima ništa, a vraća polje svih rezervacija iz baze.*/
  function getallReservations()
  {
    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, room_number, res_start, res_end, info FROM reservations' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
			$arr[] = new Reservation( $row['username'], $row['room_number'], $row['res_start'], $row['res_end'], $row['info'] );

		return $arr;
  }

 /* Funkcija prima broj prostorije i vraća polje svih rezervacija te prostorije */
  function getReservationsByRoom( $room_number )
  {
    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, room_number, res_start, res_end, info
                           FROM reservations WHERE room_number = :room_number' );
			$st->execute( array( 'room_number' => $room_number));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr = array();
		while( $row = $st->fetch() )
			$arr[] = new Reservation(  $row['username'], $row['room_number'], $row['res_start'], $row['res_end'], $row['info'] );

    return $arr;
  }

    /* Funkcija prima datum oblika YYYY-MM-DD, a vraća polje svih rezervacija tog dana. */
  function getReservationsByDate( $date )
  {
    $date_begin = $date . ' 00:00:00';
    $date_end = $date . ' 23:59:59';

    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, room_number, res_start, res_end, info
                           FROM reservations WHERE res_start >= :date_begin AND res_start <= :date_end' );
			$st->execute( array( 'date_begin' => $date_begin, 'date_end' => $date_end));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr = array();
    while( $row = $st->fetch() )
      $arr[] = new Reservation(  $row['username'], $row['room_number'], $row['res_start'], $row['res_end'], $row['info'] );

    return $arr;
  }

  /* Funkcija prima datum oblika YYYY-MM-DD, i broj prostorije i vraća polje svih rezervacija te prostorije na taj dan. */
  function getReservationsByDateRoom( $date, $room)
  {
    $date_begin = $date . ' 00:00:00';
    $date_end = $date . ' 23:59:59';

    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, room_number, res_start, res_end, info
                           FROM reservations WHERE res_start >= :date_begin AND res_start <= :date_end
                           AND room_number = :room' );
			$st->execute( array( 'date_begin' => $date_begin, 'date_end' => $date_end, 'room' => $room));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr = array();
    while( $row = $st->fetch() )
      $arr[] = new Reservation(  $row['username'], $row['room_number'], $row['res_start'], $row['res_end'], $row['info'] );

    return $arr;
  }

  /* Funkcija prima datume oblika YYYY-MM-DD i broj prostorije, vraća polje svih rezervacija te prostorije na između 2 datuma */
  function getReservationsFromTo($dateFrom, $dateTo, $room_number)
  {
    $date_begin = $dateFrom . ' 00:00:00';
    $date_end = $dateTo . ' 23:59:59';

    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, room_number, res_start, res_end, info
                           FROM reservations WHERE res_start >= :date_begin AND res_start <= :date_end
                           AND room_number = :room_number' );
			$st->execute( array( 'date_begin' => $date_begin, 'date_end' => $date_end, 'room_number' => $room_number));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr = array();
    while( $row = $st->fetch() )
      $arr[] = new Reservation(  $row['username'], $row['room_number'], $row['res_start'], $row['res_end'], $row['info'] );

    return $arr;
  }


  /* Funkcija provjerava dal su termin i prostorija slobodni, prima Reservation objekt
   *   i vraća true ako jesu, a false ako nisu, koristi se unutar addReservation. */
  function checkReservation( $reservation)
  {

    $date = substr ($reservation->res_start, 0, 10); //dohvaća samo datum iz datetime

    $res_array = array();
    $res_array = $this->getReservationsByDate($date); //sad imamo u polju sve rezervacije tog dana

    foreach($res_array as $res)
    {

      if ($res->room_number === $reservation->room_number)
      {

        $date1_start = DateTime::createFromFormat("Y-m-d h:i:s", $res->res_start);
        $date1_end = DateTime::createFromFormat("Y-m-d h:i:s", $res->res_end);
        $date2_start = DateTime::createFromFormat("Y-m-d h:i:s", $reservation->res_start);
        $date2_end = DateTime::createFromFormat("Y-m-d h:i:s", $reservation->res_end);

        /* Provjeramo ako se preklapaju */
        if ( ($date1_start <= $date2_start && $date1_end > $date2_start) ||
             ($date2_start <= $date1_start && $date2_end > $date1_start) )
                      return false; //postoji presjek
      }
    }
    return true; //nismo našli nijedan presjek
  }

  /* Prima sve potrebne informacije i dodaje rezervaciju u bazu ako je termin slobodan, a vraća false ako nije.   */
  function addReservation($username, $room_number, $res_start, $res_end, $info = '')
  {
    $username = $_POST["username"];
    $room_number = $_POST["room_number"];
    $res_start = $_POST["res_start"];
    $res_end = $_POST["res_end"];
    $info = $_POST["info"];

    $res = new Reservation($username, $room_number, $res_start, $res_end, $info);
    if ( !($this->checkReservation($res)) )
    {
      echo "Termin je zauzet.<br>";
      return false;
    }

    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO reservations( username, room_number, res_start, res_end, info)
                           VALUES (:username, :room_number, :res_start, :res_end, :info )' );
			$st->execute( array( 'username' => $username, 'room_number' => $room_number, 'res_start' => $res_start,
                            'res_end' => $res_end, 'info' => $info ));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		return true;
  }

  /* Funkcija dodaje novu prostoriju u bazu. */
  function addRoom($room_number)
  {
    $room = new Room( $room_number );

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'INSERT INTO rooms ( room_number)
                           VALUES ( :room_number )' );
      $st->execute( array( 'room_number' => $room_number ) );
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return true;
  }

  /* Funkcija prima broj prostorije, početak i kraj rezervacije i briše takvu rezervaciju ako ona postoji. */
  function removeReservation($room_number, $res_start, $res_end)
  {
    $room_number = $_POST["room_number"];
    $res_start = $_POST["res_start"].":00";
    $res_end = $_POST["res_end"].":00";
 
    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'DELETE FROM reservations WHERE room_number =:room_number
                           AND res_start = :res_start AND res_end = :res_end' );
			$st->execute( array( 'room_number' => $room_number, 'res_start' => $res_start, 'res_end' => $res_end));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return true;
  }

}

//Provjera
/*
$rs = new RoomsService();

print_r( $rs->getallRooms() );
echo "<br><br>";
print_r( $rs->getallReservations());
echo "<br><br>";
print_r( $rs->getReservationsByRoom('PR2') );
echo "<br><br>";
print_r( $rs->getReservationsByDate('2017-06-28'));
echo "<br><br>";
print_r( $rs->addReservation('korisnik1', '001', '2017-07-07 08:00:00', '2017-07-07 10:00:00', 'test' ) );
echo "<br><br>";
print_r( $rs->addReservation('korisnik1', '006', '2017-06-28 08:00:00', '2017-07-07 10:00:00', 'test' ) );

print_r( $rs->getReservationsByDateRoom('2017-06-28', '006'));
print_r($rs->addRoom('A303'));

print_r($rs->removeReservation('001', '2017-07-07 08:00:00', '2017-07-07 10:00:00'));

*/
