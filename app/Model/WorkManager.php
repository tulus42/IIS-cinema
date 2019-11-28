<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use App\Model;

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

    /** @var Model\EventManager */
    private $eventManager;

    /** @var Model\StarsInManager */
    private $starsInManager;
    
    public function __construct(Nette\Database\Context $database, Model\EventManager $eventManager, Model\StarsInManager $starsInManager)
	{
		$this->database = $database;
        $this->eventManager = $eventManager;
        $this->starsInManager = $starsInManager;
    }
    
    /**
     * Adds new work
     */
    public function addWork(string $name, string $genre, string $type, $poster, string $image, string $description, int $duration, $rating)
    {
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
    public function editWork(int $id, string $name, string $genre, string $type, string $image, string $description, int $duration, $rating)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->update([
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
    public function deleteWork(string $id)
    {
        //$work = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->fetch();
        $this->starsInManager->deleteWork((int) $id);
        $events = $this->eventManager->getAllEvents($id);
        foreach($events as $event){
            $this->eventManager->deleteEvent($event->id_cultural_event);
        }
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->delete();
    }

    public function getWork(int $id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->select('*')->fetch();
    }


    

    public function getGallery($work){
        $gallery = [];
        array_push($gallery, $work->picture);
        if ($work->picture2 != null) {array_push($gallery, $work->picture2);}
        if ($work->picture3 != null) {array_push($gallery, $work->picture3);}
        if ($work->picture4 != null) {array_push($gallery, $work->picture4);}
        if ($work->picture5 != null) {array_push($gallery, $work->picture5);}
        if ($work->picture6 != null) {array_push($gallery, $work->picture6);}

        return $gallery;

    }

    public function getActualPicture($gallery, $num){
        return $gallery[$num];
    }

    public function getNextPic($gallery, $num) {
        if ($num < count($gallery) - 1) {
            return ($num + 1);
        } else {
            return 0;
        }
    }

    public function getPrevPic($gallery, $num) {
        if ($num > 0) {
            return ($num - 1);
        } else {
            return (count($gallery) - 1);
        }
    }
}