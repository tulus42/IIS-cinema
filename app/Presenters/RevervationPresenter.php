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
        $this->template->events = $this->eventManager->allEvents();
    }

    public function renderEventList(int $id_work)
    {
        $this->template->events = $this->eventManager->getEventByWork($id_work);
    }

    public function renderOneEvent(int $id_event)
    {
        $this->template->reservations = $this->reservationManager->allEventReservation($id_event);
    }
}