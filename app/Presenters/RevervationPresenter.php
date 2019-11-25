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
    }

    public function renderOneReservation(int $id_reservation)
    {
        $this->template->one_res = $this->reservationManager->getOneReservation($id_reservation);
    }
}