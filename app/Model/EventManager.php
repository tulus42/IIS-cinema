<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Tracy\Debugger;

Debugger::enable();

/**
 * Event management.
 */
class EventManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'cultural_event',
        COLUMN_EVENT_ID = 'id_cultural_event',
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
    public function addEvent(string $date, string $time, int $price, int $piece_of_work, string $hall_num)
    {
        $date = date('Y-m-d', strtotime($date));
        //$time = date('H:M', strtotime($time));
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
    public function editEvent(string $date, string $time, int $price, string $piece_of_work, string $hall_num)
    {
        $date = date('Y-m-d', strtotime($date));
        $time = date('H:M', strtotime($time));
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

    public function findAll(): Nette\Database\Table\Selection
    {
        return $this->database->table(self::TABLE_NAME);
    }

    public function findById(int $id): Nette\Database\Table\ActiveRow{
        return $this->findAll()->get($id);
    }

    public function findEventId(string $date, string $time, string $hall_num): int
    {
        $current_obj = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_HALL, $hall_num)->where(self::COLUMN_DATE, $date)->where(self::COLUMN_TIME, $time)->select(self::COLUMN_EVENT_ID)->fetch();
        return (int) $current_obj->id_cultural_event;
    }
}