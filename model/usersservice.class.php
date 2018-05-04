<?php

require_once 'db.class.php';
require_once 'reservations.class.php';
require_once 'rooms.class.php';
require_once 'users.class.php';

/* Klasa za upravljanje korisnicima, regisriranjem i logiranjem. */
class UsersService
{

  /* Funkcija ne prima ništa, a vraća polje svih korisnika iz baze.*/
  function getallUsers()
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT username, email, pass, level FROM users' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
			$arr[] = new User( $row['username'], $row['email'], $row['pass'], $row['level'] );

		return $arr;
	}

  /* Funkcija prima username, a vraća objekt tipa User. */
  function getUser( $username )
  {
    try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT email, pass, level FROM users WHERE username = :username' );
			$st->execute( array('username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		$row = $st->fetch();
		return new User( $username, $row['email'], $row['pass'], $row['level'] );

  }


  /* Funkcija dodaje korisnika u bazu, po defaultu je razina ovlasti 0 i pass već hashiran
   * vraća true ako je uspjelo */
  function addUser($username, $email, $pass, $level = 0)
  {

    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'INSERT INTO users(username, email, pass, level)
                           VALUES (:username, :email, :pass, :level)' );
      $st->execute( array( 'username' => $username, 'email' => $email, 'pass' => $pass,
                           'level' => $level) );
    }
    catch( PDOException $e ) {  exit( 'PDO error ' . $e->getMessage() ); }

    return true;
  }

  /* Funkcija mijenja razinu ovlasti korisnika, prima username i željenu razinu ovlasti, vraća true ako je uspjelo */
  function setUserLevel($username, $newlevel)
  {
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'UPDATE users SET level = :newlevel WHERE username=:username' );
      $st->execute( array( 'username' => $username, 'newlevel' => $newlevel ) );
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return true;
  }

  /* Funkcija prima string i postavlja razinu ovlasti na 1 ako je kliknuto na registracijski niz u mailu. */
  function approveRegistration($reg_niz)
  {
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'UPDATE users SET level = 1 WHERE reg = :reg_niz' );
      $st->execute(array( 'reg_niz' => $reg_niz ));
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    echo "Vaša registracija je potvrđena.<br>";
    sleep(2);
    header("Location: index.php?rt=users");

  }

  /* Funkcija prima username i password i vraća true ako postoje u bazi. */
  function checkLogin($username, $password)
  {
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare( 'SELECT username, pass, level FROM users' ); //korisnici s level = 0 se ne mogu logirati
      $st->execute();
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    while( $row = $st->fetch() )
    {
       if ($row['username'] == $username && $row['level'] != 0 && password_verify($password, $row['pass'])) return true;
    }
    return false;
  }

  /* Funkcija prima username i tom useru šalje na mail link za potvrdu registracije, vraća true ako je uspjelo. */
  // ne radi slanje maila iz localhosta, promjeniti link na RP2 server poslije
  function sendRegistration( $username )
  {

    $reg = '';
    for ( $i = 0; $i < 10; $i++)
        $reg .= chr(rand(97,122));

      //prvo dodamo tom korisniku u bazu njegov registracijski niz
      try
      {
        $db = DB::getConnection();
        $st = $db->prepare( 'UPDATE users SET reg = :reg WHERE username = :username');
        $st->execute( array( 'reg' => $reg, 'username' => $username  ));

        //sada nađemo njegov email
        $st2 = $db->prepare( 'SELECT email FROM users WHERE username = :username');
        $st2->execute( array( 'username' => $username ));
      }
      catch( PDOException $e ) {  exit( 'PDO error ' . $e->getMessage() ); }

      $link = 'http://localhost/rooms/index.php?rt=users/approve&niz=' . $reg;
      $row = $st2->fetch();
      $to = $row['email'];
      $subject = "Link za aktivaciju registracije na forumu";
      $body = "Pozdrav," . $username . ".U nastavku je vaš link za aktivaciju registracije." . $link;
       if (!mail($to, $subject, $body))
       {
         echo "Slanje nije uspjelo.";
         return false;
       }
       echo "Link za potvrdu registracij je poslan na vaš mail.";
       sleep(2);
       header("Location: index.php?");
      return true;
    }


}
