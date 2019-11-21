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
        COLUMN_USER = 'username',
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

    public function __construct(Nette\Database\Context $database, Model\StarsInManager $seatManager)
	{
        $this->database = $database;
        $this->seatManager = $seatManager;
    }

    public function createReservation(string $username, int $work, string $status, $seat1, $seat2, $seat3, $seat4, $seat5, $seat6)
    {
        $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_USER => $username,
            self::COLUMN_WORK => $status,
            self::COLUMN_STATE => $state,
            self::COLUMN_SEAT_1 => $seat1,
            self::COLUMN_SEAT_2 => $seat2,
            self::COLUMN_SEAT_3 => $seat3,
            self::COLUMN_SEAT_4 => $seat4,
            self::COLUMN_SEAT_5 => $seat5,
            self::COLUMN_SEAT_6 => $seat6,
        ]);
    }
}