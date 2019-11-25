<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use App\Model;

/**
 * Reservation management.
 */
class UserReservesManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'user_reserves',
        COLUMN_USER = 'username',
        COLUMN_RESERVATION = 'reservation_id';


    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
	{
        $this->database = $database;
    }

    public function createUserReserves($user, $reservation)
    {
        
        $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_USER => $user,
            self::COLUMN_RESERVATION => $reservation,
        ]);
    }

    public function deleteUserReservers(int $reservation_id)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_RESERVATION, $reservation_id)->delete();
    }
    

    
}