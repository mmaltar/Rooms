<?php

/* Klasa za rezervacije */
class Reservation implements \JsonSerializable
{
	private $username, $room_number, $res_start, $res_end, $info;

	function __construct(  $username, $room_number, $res_start, $res_end, $info )
	{
		$this->username = $username;
		$this->room_number = $room_number;
    $this->res_start = $res_start;
    $this->res_end = $res_end;
		$this->info = $info;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }

  public function jsonSerialize()
  {
      return get_object_vars($this);
  }





}

?>
