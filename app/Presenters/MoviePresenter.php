<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;

class MoviePresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
	private $database;

    /** @persistent */
	public $backlink = '';

	/** @var Forms\NewWorkFormFactory */
	private $newWorkFactory;

    public function __construct(Nette\Database\Context $database, Forms\NewWorkFormFactory $newWorkFactory)
    {
        $this->database = $database;
        $this->newWorkFactory = $newWorkFactory;
    }

    public function renderShow(int $id_piece_of_work): void
    {
        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;
        $this->template->events = $piece_of_work->related('cultural_event');
        $this->template->starsIn = $piece_of_work->related('stars_in', 'id_piece_of_work');
    }

    /**
	 * New work form factory.
	 */
    protected function createComponentNewWorkForm(): Form
    {
        return $this->newWorkFactory->createWorkForm(function (): void{
            $this->restoreRequest($this->backlink);
            $this->redirect('Homepage:default');
        });
    }

    public function renderDelete(): void
    {
        //$piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
        //$this->template->piece_of_work = $piece_of_work;
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

    }
    
    public function formCancelled(): void
	{
		
	}
}


