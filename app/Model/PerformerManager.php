<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use App\Model;

/**
 * Performer management.
 */
class PerformerManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'performer',
        COLUMN_ID = 'performer_id',
        COLUMN_NAME = 'name',
        COLUMN_SURNAME = 'surname';

    /** @var Nette\Database\Context */
    private $database;

    /** @var Model\StarsInManager*/
    private $starsInManager;

    public function __construct(Nette\Database\Context $database, Model\StarsInManager $starsInManager)
	{
        $this->database = $database;
        $this->starsInManager = $starsInManager;
    }

    public function addPerformer(string $name, string $surname)
    {
        $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_NAME => $name,
            self::COLUMN_SURNAME => $surname
        ]);
    }

    public function allPerformers()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_SURNAME . ' ASC')->select('*')->fetchAll();
    }

    public function getOnePerformer(int $id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->select('*')->fetch();
    }

    public function deletePerformer(int $performer_id)
    {
        $this->starsInManager->deletePerformer($performer_id);
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $performer_id)->delete();
    }
}