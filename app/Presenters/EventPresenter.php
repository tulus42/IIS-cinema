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
}