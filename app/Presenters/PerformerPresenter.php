<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class PerformerPresenter extends Nette\Application\UI\Presenter
{
    /** @var Model\PerformerManager */
    private $performerManager;

    /** @var Nette\Database\Context */
    private $database;
    
    /** @var Forms\NewPerformerFormFactory */
    private $newPerformerFormFactory;

    public function __construct(Model\PerformerManager $performerManager, Nette\Database\Context $database, Forms\NewPerformerFormFactory $newPerformerFormFactory)
    {
        $this->performerManager = $performerManager;
        $this->database = $database;
        $this->newPerformerFormFactory = $newPerformerFormFactory;
    }

    public function renderList()
    {
        $this->template->performers = $this->performerManager->allPerformers();
    }

    public function renderProfile(int $id)
    {
        $this->template->one_performer = $this->performerManager->getOnePerformer($id);
        
        $this->template->performer_movies = $this->database->query('SELECT cultural_piece_of_work.id_piece_of_work, cultural_piece_of_work.name
        FROM cultural_piece_of_work
        JOIN stars_in ON cultural_piece_of_work.id_piece_of_work=stars_in.id_piece_of_work where stars_in.performer_id=' . $id .';');
    }

    public function createComponentAddForm(): Form
    {
        return $this->newPerformerFormFactory->createPerformer(function (): void {
			$this->redirect('Performer:list');
		});
    }

    public function renderDelete(int $id)
    {
        $this->template->performer = $this->performerManager->getOnePerformer($id);
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
        $performer_id = $this->getParameter('id');
        $this->performerManager->deletePerformer($performer_id);
        $this->redirect("Performer:list");
    }
    
    public function formCancelled(): void
	{
		$this->redirect("Performer:list");
	}
}