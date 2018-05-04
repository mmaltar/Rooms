<?php

/* Klasa za korisnike */
class User implements \JsonSerializable
{
	private $username, $email, $pass, $level;

	function __construct( $username, $email, $pass, $level )
	{
		$this->username = $username;
		$this->email = $email;
                $this->pass = $pass;
                $this->level = $level;
	}

	public function jsonSerialize()
  {
      return get_object_vars($this);
  }


	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }



}

?>
