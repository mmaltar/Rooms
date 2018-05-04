<?php

session_start();

require_once 'model/usersservice.class.php';
//require_once __DIR__.'/../view/rooms_index.php';

function sendJSONandExit( $message )
{
    // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
    header( 'Content-type:application/json;charset=utf-8' );
    echo json_encode( $message );
    flush();
    exit( 0 );
}

function sendErrorAndExit( $messageText )
{
	$message = [];
	$message[ 'error' ] = $messageText;
	sendJSONandExit( $message );
}


class UsersController {

    public function index() {
      require_once 'view/login.php';

    }

    public function reg_index() {
      require_once 'view/register.php';

    }

    //poziva se kad se klikne na login u login.php
    public function login() {

        //poslani su username i password, provjeri ih
        if (isset($_POST['logusername']) && isset($_POST['logpass'])) {
            $username = $_POST['logusername'];
            $password = $_POST['logpass'];
            $us = new UsersService();
            if ($us->checkLogin($username, $password)) {
                $_SESSION['login'] = $username;
                $userData = $us->getUser($username);
                $res = array(
                    "username"=> $userData->username,
                    "email"=> $userData->email,
                    "level"=> $userData->level,
                );
                sendJSONandExit(array("success"=>true, "data"=>$res));
            } else
                sendJSONandExit(array("success"=>false, "message"=>"Wrong username or password."));
        }

    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?rt=rooms");
    }

    //pozove se kad se klikne na link u mailu, postavlja level od usera na 1 i sad se on može ulogirati
    public function approve() {
        print_r($_GET);
        print_r("tuuusa");
        die();
        //kliknuto je na registracijski link koji je poslan na mail
        if (isset($_GET['niz'])) {
            $us = new UsersService();
            $us->approveRegistration($_GET['niz']);
        }
    }

    //poziva se iz register.php
    public function register() {
        if (isset($_POST['username']) && isset($_POST['pass']) && isset($_POST['mail'])) {
            $username = $_POST['username'];
            $password = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $email = $_POST['mail'];

            $us = new UsersService();
            $us->addUser($username, $email, $password);
            if ($us->sendRegistration($username))
                sendJSONandExit("Na vaš mail je poslan link za potvrdu registracije.");
            else sendErrorandExit("Registracija nije uspjela.");
        }

        require_once 'view/register.php';
    }
}

;
?>
