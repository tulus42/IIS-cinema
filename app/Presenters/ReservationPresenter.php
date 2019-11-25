<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class ReservationPresenter extends BasePresenter
{
    use Nette\SmartObject;

    /** @var Nette\Database\Context */
    private $database;

    /** @var Model\EventManager */
    private $eventManager;

    /** @var Model\ReservationManager */
    private $reservationManager;

    public function __construct(Nette\Database\Context $database, Model\EventManager $eventManager, Model\ReservationManager $reservationManager)
    {
        $this->database = $database;
        $this->eventManager = $eventManager;
        $this->reservationManager = $reservationManager;
    }

    public function renderMovieEventList()
    {
        $events = $this->eventManager->allEvents();
        $this->template->event_count = $events->getRowCount();
        $this->template->events = $events;
    }

    public function renderEventList(int $id_work)
    {
        $events = $this->eventManager->getEventByWork($id_work);
        //$this->template->event_count = $events->getRowCount();
        //dump($this->template->event_count);
        $this->template->events = $events;
    }

    public function renderOneEvent(int $id_event)
    {
        $reservations = $this->reservationManager->allEventReservation($id_event);
        $this->template->res_count = $reservations->getRowCount();
        $this->template->reservations = $reservations;
        $this->template->id_event = $id_event;
    }

    public function renderOneReservation(int $id_reservation, int $id_event)
    {
        $one_res = $this->reservationManager->getOneReservation($id_reservation);
        $this->template->one_res = $one_res;
        $this->template->id_event = $this->getParameter('id_event');

        $seats = [];
        array_push($seats, $this->database->table('seat')->get($one_res->seat1));

        if ($one_res->seat2 != NULL) {
            array_push($seats, $this->database->table('seat')->get($one_res->seat2));
        }

        if ($one_res->seat3 != NULL) {
            array_push($seats, $this->database->table('seat')->get($one_res->seat3));
        }

        if ($one_res->seat4 != NULL) {
            array_push($seats, $this->database->table('seat')->get($one_res->seat4));
        }

        if ($one_res->seat5 != NULL) {
            array_push($seats, $this->database->table('seat')->get($one_res->seat5));
        }

        if ($one_res->seat6 != NULL) {
            array_push($seats, $this->database->table('seat')->get($one_res->seat6));
        }

        $this->template->seats = $seats;
    }

    public function renderDeleteReservation(int $red_id, int $id_event)
    {
        $this->reservationManager->removeReservation($red_id);
        $this->template->event_id = $id_event;
    }

    public function renderPayReservation(int $red_id, int $id_event)
    {
        $this->reservationManager->payReservation($red_id);
        $this->template->reservation_id = $red_id;
        $this->template->event_id = $id_event;
    }
}