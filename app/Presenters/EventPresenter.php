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
    
    /** @var Model\EventManager */
    private $eventManager;

    

    public function __construct(Nette\Database\Context $database, Forms\NewEventFormFactory $newEventFactory, Model\EventManager $eventManager)
    {
        $this->database = $database;
        $this->newEventFactory = $newEventFactory;
        $this->eventManager = $eventManager;
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

    public function renderShow($event_id): void
    {
        // $hall = $this->database->table('hall')->get($hall_num);
        // $this->template->hall = $hall;

        $event = $this->database->table('cultural_event')->get($event_id);
        $this->template->event = $event;

      
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
}