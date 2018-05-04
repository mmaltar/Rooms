<?php

// Manualno inicijaliziramo bazu ako već nije.
require_once 'model/db.class.php';

$db = DB::getConnection();

try {
    $st = $db->prepare(
            'CREATE TABLE IF NOT EXISTS rooms (' .
            'room_number varchar(4) NOT NULL )'  //char zbog prostorija A001, A002 itd.
    );

    $st->execute();
} catch (PDOException $e) {
    exit("PDO error #1: " . $e->getMessage());
}

echo "Napravio tablicu rooms.<br>";

try {
    $st = $db->prepare(
            'CREATE TABLE IF NOT EXISTS users (' .
            'username varchar(30) NOT NULL PRIMARY KEY,' .
            'email varchar(50) NOT NULL,' .
            'pass varchar(255) NOT NULL,' .
            'level int(1),' . //razina ovlasti
            'reg varchar(255) )' //registracijski niz poslan na mail za provjeru
    );

    $st->execute();
} catch (PDOException $e) {
    exit("PDO error #2: " . $e->getMessage());
}

echo "Napravio tablicu users.<br>";

try {
    $st = $db->prepare(
            'CREATE TABLE IF NOT EXISTS reservations (' .
            'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' . //ID rezervacije
            'username varchar(30) NOT NULL,' .
            'room_number varchar(4) NOT NULL, ' . //char zbog prostorija A001, A002 itd.
            'res_start datetime NOT NULL,' .
            'res_end datetime NOT NULL,' .
            'info varchar(255) )'  //opis rezervacije, može biti prazan
    );

    $st->execute();
} catch (PDOException $e) {
    exit("PDO error #3: " . $e->getMessage());
}

echo "Napravio tablicu reservations.<br />";

// Ubaci neke prostorije unutra
try {
    $st = $db->prepare('INSERT INTO rooms(room_number) VALUES (:room_number)');

    $st->execute(array('room_number' => '001'));
    $st->execute(array('room_number' => '002'));
    $st->execute(array('room_number' => '003'));
    $st->execute(array('room_number' => '004'));
    $st->execute(array('room_number' => '005'));
    $st->execute(array('room_number' => '006'));
    $st->execute(array('room_number' => 'A001'));
    $st->execute(array('room_number' => 'A002'));
    $st->execute(array('room_number' => 'A101'));
    $st->execute(array('room_number' => 'A102'));
    $st->execute(array('room_number' => '101'));
    $st->execute(array('room_number' => '110'));
    $st->execute(array('room_number' => 'PR1'));
    $st->execute(array('room_number' => 'PR2'));
    $st->execute(array('room_number' => 'PR3'));
    $st->execute(array('room_number' => 'PR4'));
    $st->execute(array('room_number' => 'PR5'));
} catch (PDOException $e) {
    exit("PDO error #4: " . $e->getMessage());
}

echo "Ubacio prostorije u tablicu rooms.<br>";

// Ubaci neke korisnike unutra
try {
    $st = $db->prepare('INSERT INTO users(username, email, pass, level) VALUES (:username, :email, :pass, :level)');

    $st->execute(array('username' => 'admin', 'email' => 'adminovmail@mail.com',
        'pass' => password_hash('adminovpass', PASSWORD_DEFAULT), 'level' => 2));
    $st->execute(array('username' => 'korisnik1', 'email' => 'korisnikjedan@mail.com',
        'pass' => password_hash('korisnik1pass', PASSWORD_DEFAULT), 'level' => 1));
    $st->execute(array('username' => 'korisnik2', 'email' => 'korisnikdva@mail.com',
        'pass' => password_hash('korisnik2pass', PASSWORD_DEFAULT), 'level' => 1));
    $st->execute(array('username' => 'korisnikbezovlasti', 'email' => 'korisnikbezovlasti@mail.com',
        'pass' => password_hash('korisnikbezovlastipass', PASSWORD_DEFAULT), 'level' => 0));
} catch (PDOException $e) {
    exit("PDO error #4: " . $e->getMessage());
}

echo "Ubacio korisnike u tablicu users.<br>";

//Ubaci neke rezervacije unutra
try {
    $st = $db->prepare('INSERT INTO reservations(username, room_number, res_start, res_end, info)
											 VALUES (:username, :room_number, :res_start, :res_end, :info)');

    $st->execute(array('username' => 'korisnik1', 'room_number' => '003', 'res_start' => '2017-06-28 10:00:00',
        'res_end' => '2017-06-28 12:00:00', 'info' => ''));
    $st->execute(array('username' => 'korisnik2', 'room_number' => '003', 'res_start' => '2017-06-28 12:00:00',
        'res_end' => '2017-06-28 15:00:00', 'info' => ''));
    $st->execute(array('username' => 'korisnik1', 'room_number' => 'PR2', 'res_start' => '2017-07-03 09:00:00',
        'res_end' => '2017-07-03 11:00:00', 'info' => 'Kolokvij iz RP2'));
    $st->execute(array('username' => 'korisnik2', 'room_number' => '101', 'res_start' => '2017-07-24 10:00:00',
        'res_end' => '2017-07-24 12:00:00', 'info' => ''));
} catch (PDOException $e) {
    exit("PDO error #5: " . $e->getMessage());
}

echo "Ubacio rezervacije u tablicu reservations.<br>";
?>
