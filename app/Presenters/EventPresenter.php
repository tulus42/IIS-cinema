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
        return $this->newEventFactory->createEventForm();
    }

    public function renderShowEvent($event_id): void
    {
        // $hall = $this->database->table('hall')->get($hall_num);
        // $this->template->hall = $hall;

        $event = $this->database->table('cultural_event')->get($event_id);
        $this->template->event = $event;

      
    }
}