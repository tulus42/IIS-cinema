<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Tracy\Debugger;

use App\Model;

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

    /** @var Model\seatManager */
    private $seatManager;

    public function __construct(Nette\Database\Context $database, Model\SeatManager $seatManager)
	{
        $this->database = $database;
        $this->seatManager = $seatManager;
    }

    /**
     * Adds new event
     */
    public function addEvent(string $date, string $time, float $price, int $piece_of_work, string $hall_num)
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
        $id_event = $this->findEventId($date, $time, $hall_num);
        $this->seatManager->addSeatsToEvent($hall_num, $id_event);
    }

    /**
     * Edits existing event
     */
    public function editEvent(int $event_id, string $date, string $time, float $price)
    {
        $date = date('Y-m-d', strtotime($date));
        //$time = date('H:M', strtotime($time));
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EVENT_ID, $event_id)->update([
            self::COLUMN_DATE => $date,
            self::COLUMN_TIME => $time,
            self::COLUMN_PRICE => $price,
        ]);
    }

    /**
     * Deletes existing event
     */
    public function deleteEvent(int $id)
    {
        $hall = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EVENT_ID, $id)->fetch();
        $this->seatManager->deleteSeatsToEvent($hall->hall_num,$id);
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EVENT_ID, $id)->delete();
    }

    public function getEvent(int $id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EVENT_ID, $id)->fetch();
    }

    public function getAllEvents(string $id): array
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_WORK, $id)->fetchAll();
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

    public function deleteEventsInHall(string $hall_num)
    {
        $events = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_HALL, $hall_num)->fetchAll();
        if($events){
            foreach($events as $one_event){
                $this->deleteEvent($one_event->id_cultural_event);
            }
        }
    }

    public function allEvents()
    {
        $events = $this->database->query('SELECT DISTINCT cultural_event.id_piece_of_work, cultural_piece_of_work.name 
        FROM cultural_event 
        JOIN cultural_piece_of_work ON cultural_event.id_piece_of_work=cultural_piece_of_work.id_piece_of_work ORDER BY cultural_piece_of_work.name ASC');
        return $events;
    }

    public function getEventByWork(int $work_id)
    {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_DATE . ' ASC')->order(self::COLUMN_TIME . ' ASC')->where(self::COLUMN_WORK, $work_id)->fetchAll();
    }

    public function findDuplicateEvent(string $date, string $time, string $hall_num)
    {
        $events = $this->database->table(self::TABLE_NAME)->select('*')->where(self::COLUMN_DATE, $date)->where(self::COLUMN_TIME, $time)->where(self::COLUMN_HALL, $hall_num)->fetchAll();
        return count($events);
    }

    public function getReservationEvent($reservationID) 
    {
        $eventID = $this->database->table(self::TABLE_NAME)->where('id_cultural_event', $this->database->table('seat')->where('seat_id', $this->database->table('reservation')->where('reservation_id = ?', $reservationID)->select('seat1'))->select('cultural_event_id'))->fetch();
        return $this->database->table(self::TABLE_NAME)->get($eventID);
    }
}