<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class HallListPresenter extends BasePresenter
{
    private $database;

    /** @var Forms\NewHallFormFactory */
    private $newHallFactory;

    /** @var Model\HallManager */
    private $hallManager;

    /** @var Model\EventManager */
    private $eventManager;

    public function __construct(Nette\Database\Context $database, Forms\NewHallFormFactory $newHallFactory, Model\HallManager $hallManager, Model\EventManager $eventManager)
    {
        $this->database = $database;
        $this->newHallFactory = $newHallFactory;
        $this->hallManager = $hallManager;
        $this->eventManager = $eventManager;
    }

    public function renderHallList(): void
    {
        $this->template->halls = $this->database->table('hall');
    }

    public function renderOneHall(string $hall_num): void
    {
        $hall = $this->database->table('hall')->get($hall_num);
        $this->template->hall = $hall;
        
    }

    public function createComponentNewHallForm()
    {
        return $this->newHallFactory->createHallForm(function (): void{
            $this->redirect('HallList:hallList');
        });
    }

    public function renderDelete(string $hall_num)
    {
        $hall = $this->database->table('hall')->get($hall_num);
        $this->template->hall = $hall;
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
        $hall_number = $this->getParameter('hall_num');
        $this->eventManager->deleteEventsInHall($hall_number);
        $this->hallManager->deleteHall($hall_number);
        $this->redirect('HallList:hallList');
    }
    
    public function formCancelled(): void
	{
        $hall_number = $this->getParameter('hall_num');
		$this->redirect("HallList:oneHall", $hall_number);
	}
}
