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
        COLUMN_ID = 'stars_in_id',
        COLUMN_PERFORMER = 'performer_id',
        COLUMN_WORK = 'id_piece_of_work';

    public function __construct(Nette\Database\Context $database)
	{
        $this->database = $database;
    }

    public function addStarsIn(int $performer, int $work)
    {

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