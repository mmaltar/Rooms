<?php

require_once 'model/roomsservice.class.php';
//require_once __DIR__.'/../model/roomsservice.class.php';


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


class RoomsController
{
	public function index()
	{

		require_once 'view/rooms_index.php';
	}
        /*
         * primjer funkcije za ajax
         * kod ajax poziva se obično dohvaćaju samo neki podaci
         * zato ne treba view, tj. html dio
         */
        public function allrooms() {
            $service = new RoomsService();
            $rooms = $service->getallRooms();
            //print_r($rooms);

          	sendJSONandExit($rooms);
        }
        
        public function removereservation() {
            $service = new RoomsService();
            $rooms = $service->removeReservation();
            //print_r($rooms);

            sendJSONandExit($rooms);
        }

				public function allreservations() {
            $service = new RoomsService();
            $reservations = $service->getallReservations();

            sendJSONandExit($reservations);
        }

				public function reservationsbyroom( $room_number ) {
            $service = new RoomsService();
            $reservations = $service->getReservationsByRoom( $room_number );

            sendJSONandExit($reservations);
        }

				public function reservationsbydate( $date ) {
						$service = new RoomsService();
						$reservations = $service->getReservationsByDate( $date );

						sendJSONandExit($reservations);
				}

				public function reservationsbydateroom( $date, $room_number ) {
						$service = new RoomsService();
						$reservations = $service->getReservationsByDateRoom( $date, $room_number );

						sendJSONandExit($reservations);
				}

        public function reservationsfromto( $dateFrom, $dateTo, $room_number ) {
						$service = new RoomsService();
						$reservations = $service->getReservationsFromTo( $dateFrom, $dateTo, $room_number );

						sendJSONandExit($reservations);
				}

				//sad ide obrnutim redom jer dodajemo u bazu?
				// jel nam treba to tu uopće?
				public function addreservation( $jsonReservation ) {

						$reservation = json_decode( $jsonReservation );

						$service = new RoomsService();
						$reservations = $service->addReservation( $reservation );
				}





};

?>
