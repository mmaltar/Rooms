<?php

/* Klasa za prostorije */
class Room implements \JsonSerializable
{
    private $room_number;

	function __construct( $room_number )
	{

		$this->room_number = $room_number;

        }

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }

        /*
         * ovo je implementacija funkcije iz JsonSerializable interface-a
         * defaultno json_encode funkcija, koju koristimo u RoomsController::allrooms,
         * zanemaruje one propertije koji imaju pravo pristupa "private", kao u slučaju
         * klase Room i propertia room_number. Kada bi room_number bio public ovo ne bi trebali
         */
        public function jsonSerialize()
        {
            return get_object_vars($this);
        }
}

?>
