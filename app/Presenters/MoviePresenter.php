<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class MoviePresenter extends BasePresenter
{
    /** @var Nette\Database\Context */
	private $database;

    /** @persistent */
	public $backlink = '';

	/** @var Forms\NewWorkFormFactory */
    private $newWorkFactory;

    /** @var Forms\EditWorkFormFactory */
    private $editWorkFactory;
    
    /** @var Model\WorkManager */
    private $workManager;

    /** @var Model\StarsInManager */
    private $starsInManager;

    public function __construct(Nette\Database\Context $database, Forms\NewWorkFormFactory $newWorkFactory, Model\WorkManager $workManager, Forms\EditWorkFormFactory $editWorkFactory, Model\StarsInManager $starsInManager)
    {
        $this->database = $database;
        $this->newWorkFactory = $newWorkFactory;
        $this->workManager = $workManager;
        $this->editWorkFactory = $editWorkFactory;
        $this->starsInManager = $starsInManager;
    }

    public function renderShow(int $id_piece_of_work): void
    {
        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;
        $this->template->events = $piece_of_work->related('cultural_event');
        $this->template->starsIn = $piece_of_work->related('stars_in', 'id_piece_of_work');
    }

    public function renderDelete(int $id_piece_of_work): void
    {
        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;
    }

    public function renderEdit(int $id_piece_of_work): void
    {

    }

    /**
	 * New work form factory.
	 */
    protected function createComponentEditWorkForm(): Form
    {
        $work_id = (int) $this->getParameter('id_piece_of_work');
        return $this->editWorkFactory->createEditWorkForm($work_id, function (): void{
            $work_id = (int) $this->getParameter('id_piece_of_work');
            $this->redirect('Movie:show', $work_id);
        });
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
        $workId = (int) $this->getParameter('id_piece_of_work');
        $this->workManager->deleteWork($workId);
        $this->redirect('Homepage:default');
    }
    
    public function formCancelled(): void
	{
        $workId = (int) $this->getParameter('id_piece_of_work');
		$this->redirect("Movie:show", $workId);
    }
    
    public function renderAddPerformer(int $id_piece_of_work)
    {
        $this->template->already_stars_in = $this->database->query('SELECT performer.performer_id, performer.name, performer.surname
        FROM performer
        JOIN stars_in ON stars_in.performer_id=performer.performer_id where stars_in.id_piece_of_work=' . $id_piece_of_work . ' ORDER BY surname ASC, name ASC;;');

        $this->template->doesnt_star_in = $this->database->query('SELECT *
        FROM performer
        WHERE performer_id NOT IN (
            SELECT performer_id
            FROM stars_in
            WHERE stars_in.id_piece_of_work = ' . $id_piece_of_work . ')
            ORDER BY surname ASC, name ASC;');

        $this->template->starsInManager = $this->starsInManager;

        $this->template->work = $this->getParameter('id_piece_of_work');
    }

    public function renderAllMovies(): void
    {
        $this->template->movies = $this->database->table('cultural_piece_of_work')->order('name ASC');
    }
}


