<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

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

    public function __construct(Nette\Database\Context $database)
	{
        $this->database = $database;
    }

    public function addPerformer($name, $surname)
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
}