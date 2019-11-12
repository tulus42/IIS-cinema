<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Hall management.
 */
class HallManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'hall',
        COLUMN_HALL_NUM = 'hall_num',
        COLUMN_ROWS = 'number_of_rows',
        COLUMN_COLUMNS = 'number_of_columns',
        COLUMN_ADDRESS = 'address';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }

    /**
     * Adds new hall
     */
    public function addHall(string $hall_num, int $rows, int $columns, string $address){
        try{
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_HALL_NUM => $hall_num,
                self::COLUMN_ROWS => $rows,
                self::COLUMN_COLUMNS => $columns,
                self::COLUMN_ADDRESS => $address,
            ]);
        } catch (Nette\Database\UniqueConstraintViolationException $e){
            throw new DuplicateNameException;
        }
    }

    /**
     * Edits existing hall
     */
    public function editHall(string $hall_num, int $rows, int $columns, string $address){
        $this->database->table(self::TABLE_NAME)->where($hall_num)->update([
            self::COLUMN_ROWS => $rows,
            self::COLUMN_COLUMNS => $columns,
            self::COLUMN_ADDRESS => $address,
        ]);
    }

    /**
     * Removes existing hall
     */
    public function deleteHall(string $hall_num){
        $this->database->table(self::TABLE_NAME)->where($hall_num)->delete();
    }
}