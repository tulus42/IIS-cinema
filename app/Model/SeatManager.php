<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use App\Model;

/**
 * Seat management.
 */
class SeatManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'seat',
        COLUMN_SEAT_ID = 'seat_id',
        COLUMN_CULTURAL_EVENT = 'cultural_event_id',
        COLUMN_ROW = 'row',
        COLUMN_COLUMN = 'column',
        COLUMN_STATE = 'state';

    /** @var Nette\Database\Context */
    private $database;

    /** @var Model\HallManager */
    private $hallManager;

    /** @var Model\ReservationManager */
    private $reservationManager;

    /** @var */
    private $reservationArr;

    /** @var */
    private $reservationCnt;


    public function __construct(Nette\Database\Context $database, Model\HallManager $hallManager)
	{
        $this->database = $database;
        $this->hallManager = $hallManager;
        $this->reservationArr = array();
        $this->reservationCnt = 0;
    }

    public function addSeat(int $cultural_event, int $row, int $column, string $state)
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

    public function freeSeat(int $seat_id)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_SEAT_ID, $seat_id)->update([
            self::COLUMN_STATE => "available"
        ]);
    }

    public function getSeatId(int $cultural_event, int $row, int $column)
    {
        $id = $this->database->table(self::TABLE_NAME)->select('*')->where(self::COLUMN_CULTURAL_EVENT, $cultural_event)->where(self::COLUMN_ROW, $row)->where(self::COLUMN_COLUMN, $column)->fetch();
        return $id->seat_id;
    }

    public function deleteSeat(int $cultural_event, int $row, int $column)
    {
        $seatId = $this->getSeatId($cultural_event, $row, $column);
        //$this->reservationManager->deleteReservation($seatId);
        $reservationId = $this->getReservationId($seatId);
        if(is_object($reservationId)){
            $this->database->table('user_reserves')->where('reservation_id', $reservation_id)->delete();
        }
        $this->database->table('reservation')->where('seat1', $seatId)->delete();
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_CULTURAL_EVENT, $cultural_event)->where(self::COLUMN_ROW, $row)->where(self::COLUMN_COLUMN, $column)->delete();
    }

    public function getReservationId(int $seat1)
    {
        $id = $this->database->table('reservation')->select('*')->where('seat1', $seat1)->fetch();
        return $id;
    }

    public function findAll(): Nette\Database\Table\Selection
    {
        return $this->database->table(self::TABLE_NAME);
    }

    public function findById(string $id): Nette\Database\Table\ActiveRow{
        return $this->findAll()->get($id);
    }

    public function addSeatsToEvent(string $hall_num, int $id_event)
    {
        $rows = $this->hallManager->getRows($hall_num);
        $columns = $this->hallManager->getColumns($hall_num);
        for($one_row = 1; $one_row <= $rows; $one_row++){
            for($one_column = 1; $one_column <= $columns; $one_column++){
                $this->addSeat($id_event, $one_row, $one_column, "available");
            }
        }
    }

    public function deleteSeatsToEvent(string $hall_num,  int $id_event)
    {
        $rows = $this->hallManager->getRows($hall_num);
        $columns = $this->hallManager->getColumns($hall_num);
        for($one_row = 1; $one_row <= $rows; $one_row++){
            for($one_column = 1; $one_column <= $columns; $one_column++){
                $this->deleteSeat($id_event, $one_row, $one_column);
            }
        }
    }

    public function getSeat($seat_table, $row, $column){
        foreach($seat_table as $seat) {
            if ($seat->row == $row && $seat->column == $column)
                return $seat;
        }
    }


    public function clickOnSeat(){
        if ($this->reservationCnt < 3) {
            $this->reservationCnt += 1;
            return "#0000ff";
        } else {
            return "#ffffff";
        }
        
    }

    public function reserveSeats($seats)
    {
        foreach ($seats as $seat)
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_SEAT_ID, $seat->seat_id)->update([
            self::COLUMN_STATE => "reserved"
        ]);
    }

    public function deleteUserReservers(int $reservation_id)
    {
        ;
    }

    public function takeSeats($seats)
    {
        foreach ($seats as $seat)
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_SEAT_ID, $seat->seat_id)->update([
            self::COLUMN_STATE => "taken"
        ]);
    }

    public function getSeatsFromReservation($reservation)
    {
        $seats = [];
        array_push($seats, $this->database->table(self::TABLE_NAME)->get($reservation->seat1));

        if ($reservation->seat2 != NULL) {
            array_push($seats, $this->database->table(self::TABLE_NAME)->get($reservation->seat2));
        }

        if ($reservation->seat3 != NULL) {
            array_push($seats, $this->database->table(self::TABLE_NAME)->get($reservation->seat3));
        }

        if ($reservation->seat4 != NULL) {
            array_push($seats, $this->database->table(self::TABLE_NAME)->get($reservation->seat4));
        }

        if ($reservation->seat5 != NULL) {
            array_push($seats, $this->database->table(self::TABLE_NAME)->get($reservation->seat5));
        }

        if ($reservation->seat6 != NULL) {
            array_push($seats, $this->database->table(self::TABLE_NAME)->get($reservation->seat6));
        }

        return $seats;

    }
}