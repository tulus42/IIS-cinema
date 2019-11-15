<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class EventPresenter extends Nette\Application\UI\Presenter
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
    }
}