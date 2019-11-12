<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Event management.
 */
class EventManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'cultural_event',
        COLUMN_DATE = 'date',
        COLUMN_TIME = 'time',
        COLUMN_PRICE = 'price',
        COLUMN_WORK = 'id_piece_of_work',
        COLUMN_HALL = 'hall_num';


    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }

    /**
     * Adds new event
     */
    public function addEvent(string $date, string $time, int $price, string $piece_of_work, string $hall_num)
    {
        $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_DATE => $date,
            self::COLUMN_TIME => $time,
            self::COLUMN_PRICE => $price,
            self::COLUMN_WORK => $piece_of_work,
            self::COLUMN_HALL => $hall_num,
        ]);
    }

    /**
     * Edits existing event
     */
    public function editEvent(string $id, string $date, string $time, int $price, string $piece_of_work, string $hall_num)
    {
        $this->database->table(self::TABLE_NAME)->where($id)->update([
            self::COLUMN_DATE => $date,
            self::COLUMN_TIME => $time,
            self::COLUMN_PRICE => $price,
            self::COLUMN_WORK => $piece_of_work,
            self::COLUMN_HALL => $hall_num,
        ]);
    }

    /**
     * Deletes existing event
     */
    public function deleteEvent(string $id)
    {
        $this->database->table(self::TABLE_NAME)->where($id)->delete();
    }
}