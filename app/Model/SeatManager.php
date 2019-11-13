<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Seat management.
 */
class SeatManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'seat',
        COLUMN_CULTURAL_EVENT = 'cultural_event_id',
        COLUMN_ROW = 'row',
        COLUMN_COLUMN = 'column',
        COLUMN_STATE = 'state';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }

    public function addSeat(string $cultural_event, int $row, int $column, string $state)
    {
        try{
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_CULTURAL_EVENT => $cultural_event,
                self::COLUMN_ROW => $row,
                self::COLUMN_COLUMN => $column,
                self::COLUMN_STATE => $state
            ]);
        } catch (Nette\Database\UniqueConstraintViolationException $e){
            throw new DuplicateNameException;
        }
    }

    public function updateSeatStatus(string $cultural_event, int $row, int $column, string $new_state)
    {

    }

    public function deleteSeat(string $cultural_event, int $row, int $column)
    {

    }
}