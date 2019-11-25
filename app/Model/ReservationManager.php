<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use App\Model;

/**
 * Reservation management.
 */
class ReservationManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'reservation',
        COLUMN_ID = 'reservation_id',
        COLUMN_WORK = 'id_piece_of_work',
        COLUMN_STATE = 'paid',
        COLUMN_SEAT_1 = 'seat1',
        COLUMN_SEAT_2 = 'seat2',
        COLUMN_SEAT_3 = 'seat3',
        COLUMN_SEAT_4 = 'seat4',
        COLUMN_SEAT_5 = 'seat5',
        COLUMN_SEAT_6 = 'seat6';


    /** @var Nette\Database\Context */
    private $database;

    /** @var Model\SeatManager*/
    private $seatManager;

    /** @var Model\UserReservesManager */
    private $userReservesManager;

    public function __construct(Nette\Database\Context $database, Model\StarsInManager $seatManager, Model\UserReservesManager $userReservesManager)
	{
        $this->database = $database;
        $this->seatManager = $seatManager;
        $this->userReservesManager = $userReservesManager;
    }

    public function createReservation(int $work, string $status, $seat1, $seat2, $seat3, $seat4, $seat5, $seat6)
    {
        
        return $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_WORK => $work,
            self::COLUMN_STATE => $status,
            self::COLUMN_SEAT_1 => $seat1,
            self::COLUMN_SEAT_2 => $seat2,
            self::COLUMN_SEAT_3 => $seat3,
            self::COLUMN_SEAT_4 => $seat4,
            self::COLUMN_SEAT_5 => $seat5,
            self::COLUMN_SEAT_6 => $seat6,
        ]);
    }

    public function allEventReservation(int $id_event)
    {
        $res = $this->database->query('SELECT *
        FROM reservation
        WHERE seat1 IN (
            SELECT seat_id
            FROM seat
            WHERE cultural_event_id = '.$id_event.'
        )
        ;');


        return $res;
    }


    public function getOneReservation(int $id_reservation)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id_reservation)->fetch();
    }

    public function deleteReservation(int $seatId)
    {
        $reservationId = $this->getReservationId($seatId);
        if(is_object($reservationId)){
            $this->userReservesManager->deleteUserReservers($reservationId->reservation_id);
        }
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_SEAT_1, $seatId)->delete();
    }

    public function getReservationId(int $seat1)
    {
        $id = $this->database->table(self::TABLE_NAME)->select('*')->where(self::COLUMN_SEAT_1, $seat1)->fetch();
        return $id;
    }
}