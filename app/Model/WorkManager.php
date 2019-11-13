<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Work management.
 */
class WorkManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'cultural_piece_of_work',
        COLUMN_ID = 'id_piece_of_work',
        COLUMN_NAME = 'name',
        COLUMN_GENRE = 'genre',
        COLUMN_TYPE = 'type',
        COLUMN_IMAGE = 'picture',
        COLUMN_DESCRIPTION = 'description',
        COLUMN_DURATION = 'duration',
        COLUMN_RATING = 'rating';

    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
    /**
     * Adds new work
     */
    public function addWork(string $name, string $genre, string $type, string $image, string $description, int $duration, int $rating){
        try{
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_NAME => $name,
                self::COLUMN_GENRE => $genre,
                self::COLUMN_TYPE => $type,
                self::COLUMN_IMAGE => $image,
                self::COLUMN_DESCRIPTION => $description,
                self::COLUMN_DURATION => $duration,
                self::COLUMN_RATING => $rating
            ]);
        } catch (Nette\Database\UniqueConstraintViolationException $e){
            throw new DuplicateNameException;
        }
    }

    /**
     * Edits existing work
     */
    public function editWork(string $id, string $name, string $genre, string $type, string $image, string $description, int $duration, int $rating){
        $this->database->table(self::TABLE_NAME)->where($id)->update([
            self::COLUMN_NAME => $name,
            self::COLUMN_GENRE => $genre,
            self::COLUMN_TYPE => $type,
            self::COLUMN_IMAGE => $image,
            self::COLUMN_DESCRIPTION => $description,
            self::COLUMN_DURATION => $duration,
            self::COLUMN_RATING => $rating
        ]);
    }

    /**
     * Deletes existing work
     */
    public function deleteWork(string $id){
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->delete();
    }
}