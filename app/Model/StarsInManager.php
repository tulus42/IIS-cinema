<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * StarsIn management.
 */
class StarsInManager
{
    use Nette\SmartObject;



    /** @var Nette\Database\Context */
    private $database;

    private const
        TABLE_NAME = 'stars_in',
        COLUMN_PERFORMER = 'performer_id',
        COLUMN_WORK = 'id_piece_of_work';

    public function __construct(Nette\Database\Context $database)
	{
        $this->database = $database;
    }

    public function addPerformer(int $performer, int $work)
    {
        
        $this->database->query("INSERT into stars_in values ($performer, $work);");
    }

    public function removePerformer(int $performer, int $work)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_PERFORMER, $performer)->where(self::COLUMN_WORK, $work)->delete();
    }

    public function deletePerformer(int $performer)
    {   
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_PERFORMER, $performer)->delete();
    }

    public function deleteMovie(int $work)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_WORK, $work)->delete();
    }
}