<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class EventPresenter extends BasePresenter
{
    /** @var Nette\Database\Context */
    private $database;
    
    /** @var Forms\NewEventFormFactory */
    private $newEventFactory;

    /** @var Forms\EditEventFormFactory */
    private $editEventFactory;
    
    /** @var Model\EventManager */
    private $eventManager;

    /** @var Model\SeatManager */
    private $seatManager;

    /** @var Forms\newReservationFormFactory */
    private $newReservationFormFactory;

    

    public function __construct(Nette\Database\Context $database, Forms\NewEventFormFactory $newEventFactory, Model\EventManager $eventManager, Model\SeatManager $seatManager, Forms\EditEventFormFactory $editEventFactory, Forms\newReservationFormFactory $newReservationFormFactory)
    {
        $this->database = $database;
        $this->newEventFactory = $newEventFactory;
        $this->eventManager = $eventManager;
        $this->seatManager = $seatManager;
        $this->editEventFactory = $editEventFactory;
        $this->newReservationFormFactory = $newReservationFormFactory;
    }

    public function renderEdit(int $event_id)
    {

    }

    public function createComponentEditEventForm(): Form
    {
        $event_id = (int) $this->getParameter('event_id');
        return $this->editEventFactory->createEditEventForm($event_id, function (): void{
            $this->redirect('Event:show', (int) $this->getParameter('event_id'));
        });
    }

    public function createComponentNewEventForm(): Form
    {
        $work_id = (int) $this->getParameter('id_piece_of_work');
        return $this->newEventFactory->createEventForm($work_id, function (): void{
            $this->redirect('Movie:show', (int) $this->getParameter('id_piece_of_work'));
        });

    }

    public function renderAdd(string $id_piece_of_work)
    {
        ;
    }

    public function renderShow(int $event_id): void
    {
        $event = $this->database->table('cultural_event')->get($event_id);
        $this->template->event = $event;

        $hall = $this->database->table('hall')->get($event->hall_num);
        $this->template->hall = $hall;

        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($event->id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;

        $seat = $event->related('seat');
        $this->template->seatTable = $seat;

        $this->template->seatManager = $this->seatManager;
    }

    public function renderDelete(int $id_cultural_event): void
    {
        $cultural_event = $this->database->table('cultural_event')->get($id_cultural_event);
        $this->template->cultural_event = $cultural_event;

        $cultural_piece_of_work = $this->database->table('cultural_piece_of_work')->where('id_piece_of_work = ?', $cultural_event->id_piece_of_work)->fetch();
        $this->template->cultural_piece_of_work = $cultural_piece_of_work;
    }

    protected function createComponentDeleteForm(): Form
    {
        $form = new Form;
        $form->addSubmit('delete', 'Áno')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'deleteFormSucceeded'];
        $form->addSubmit('cancel', 'Nie')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'formCancelled'];
		$form->addProtection();
		return $form;
    }

    public function deleteFormSucceeded(): void
	{
        // get the id for the final redirect first
        $eventId = (int) $this->getParameter('id_cultural_event');
        $cultural_event = $this->database->table('cultural_event')->get($eventId);
        $cultural_piece_of_work = $this->database->table('cultural_piece_of_work')->where('id_piece_of_work = ?', $cultural_event->id_piece_of_work)->fetch();
        $this->eventManager->deleteEvent($eventId);
        $this->redirect('Movie:show', $cultural_piece_of_work->id_piece_of_work);
    }
    
    public function formCancelled(): void
	{
        $eventId = (int) $this->getParameter('id_cultural_event');
		$this->redirect("Event:show", $eventId);
    }
    
    public function renderReserve($reservationArray, $event): void
    {
        $event = $this->database->table('cultural_event')->get($event);
        $this->template->event = $event;       

        $seatArray = $this->parseReservationArray($reservationArray, $event->id_cultural_event);
        $this->template->seatArray = $seatArray;

        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($event->id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;
    }
   

    protected function createComponentNewReservationForm(): Form
    {
        $eventID = (int) $this->getParameter('event');
        $event = $this->database->table('cultural_event')->get($eventID);

        $work = $this->database->table('cultural_piece_of_work')->where('id_piece_of_work = ?', $event->id_piece_of_work)->fetch();

        $seats = $this->getparameter('reservationArray');
        $seats = $this->parseReservationArray($seats, $eventID);

        if ($this->user->isLoggedIn()){
            $userID = $this->getUser()->id;
            $this->template->this_profile = $this->database->table('user')->get($userID);
        } else {
            $userID = '';
        }
        
        $logged = false;
        if ($this->user->isLoggedIn()) {
            $logged = true;
        }

        return $this->newReservationFormFactory->createReservationForm($work, $eventID, $seats, $userID, $logged, $this, function (): void{
            $this->redirect('Event:reserveSuccess');
        });
    }


    public function parseReservationArray($reservationArray, $eventID){
        $tmpSeatArray = [];
        $seatArray = [];

        $tmpSeatArray=explode("q", $reservationArray);


        foreach($tmpSeatArray as $seat) {
            $tmpSeat = explode(":", $seat);
            $tmpSeat = $this->database->table('seat')->where('row = ? AND column = ? AND cultural_event_id = ?', $tmpSeat[0], $tmpSeat[1], $eventID)->fetch();
            array_push($seatArray, $tmpSeat);
        }

        return $seatArray;
    }

    public function renderReserveSuccess($reservation): void
    {
        $reservation = $this->database->table('reservation')->get($reservation);
        $this->template->reservation = $reservation;
        
        $seats = [];
        array_push($seats, $this->database->table('seat')->get($reservation->seat1));

        if ($reservation->seat2 != NULL) {
            array_push($seats, $this->database->table('seat')->get($reservation->seat2));
        }

        if ($reservation->seat3 != NULL) {
            array_push($seats, $this->database->table('seat')->get($reservation->seat3));
        }

        if ($reservation->seat4 != NULL) {
            array_push($seats, $this->database->table('seat')->get($reservation->seat4));
        }

        if ($reservation->seat5 != NULL) {
            array_push($seats, $this->database->table('seat')->get($reservation->seat5));
        }

        if ($reservation->seat6 != NULL) {
            array_push($seats, $this->database->table('seat')->get($reservation->seat6));
        }

        $this->template->seats = $seats;
    }

    public function renderReserveUnSuccess($event): void
    {
        $this->template->event = $event;
    }

    public function renderPayByCard($reservation): void
    {
        $this->template->reservation = $reservation;
    }
}