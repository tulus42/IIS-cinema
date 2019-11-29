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
        $this->checkPrivileges();
        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;
    }

    public function checkPrivileges()
    {
        if (!$this->user->isLoggedIn() or !($this->user->isInRole('admin') or $this->user->isInRole('redactor'))){
            throw new \Nette\Application\BadRequestException(404);
        }
    }

    public function renderEdit(int $id_piece_of_work): void
    {
        $this->checkPrivileges();
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
        return $this->newWorkFactory->createWorkForm($this, function (): void{
            $this->redirect('Movie:allMovies');
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

    public function renderAdd()
    {
        $this->checkPrivileges();
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
        $this->checkPrivileges();

        $this->template->already_stars_in = $this->database->query('SELECT performer.performer_id, performer.name, performer.surname
        FROM performer
        JOIN stars_in ON stars_in.performer_id=performer.performer_id where stars_in.id_piece_of_work=' . $id_piece_of_work . ' ORDER BY surname ASC, name ASC;;');

        
        $this->template->stars_in_count = $this->template->already_stars_in->getRowCount();
        

        $this->template->doesnt_star_in = $this->database->query('SELECT *
        FROM performer
        WHERE performer_id NOT IN (
            SELECT performer_id
            FROM stars_in
            WHERE stars_in.id_piece_of_work = ' . $id_piece_of_work . ')
            ORDER BY surname ASC, name ASC;');


        $this->template->doesnt_star_in_count = $this->template->doesnt_star_in->getRowCount();

        $this->template->work = $this->getParameter('id_piece_of_work');
        
    }

    public function renderAllMovies(): void
    {
        $this->template->movies = $this->database->table('cultural_piece_of_work')->order('name ASC');
    }

    public function createComponentAddPerf()
    {
        $form = new Form;
        $form->addSubmit('delete', 'Áno')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'AddP'];
        $form->addSubmit('cancel', 'Nie')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'goBack'];
		$form->addProtection();
		return $form;
    }

    public function createComponentRemovePerf()
    {
        $form = new Form;
        $form->addSubmit('delete', 'Áno')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'RemoveP'];
        $form->addSubmit('cancel', 'Nie')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'goBack'];
		$form->addProtection();
		return $form;
    }


    public function renderRemovePerf($work_id, $perf_id)
    {
        $this->checkPrivileges();
    }

    public function renderAddPerf($work_id, $perf_id)
    {
        $this->checkPrivileges();
    }

    public function goBack()
    {
        $param = $this->getParameter('work_id');
        $this->redirect('AddPerformer', $param);
    }

    public function AddP()
    {
        $work_id = $this->getParameter('work_id');
        $perf_id = $this->getParameter('perf_id');
        $this->starsInManager->addPerformer((int) $perf_id, (int) $work_id);
        $this->goBack();
    }
    
    public function RemoveP()
    {
        $work_id = $this->getParameter('work_id');
        $perf_id = $this->getParameter('perf_id');
        $this->starsInManager->removePerformer((int) $perf_id, (int) $work_id);
        $this->goBack();
    }


    public function renderGallery($workID, $pictureNum): void
    {
        $work = $this->database->table('cultural_piece_of_work')->get($workID);
        
        $gallery = $this->workManager->getGallery($work);

        $actualPic = $this->workManager->getActualPicture($gallery, $pictureNum);

        $nextPic = $this->workManager->getNextPic($gallery, $pictureNum);
        $prevPic = $this->workManager->getPrevPic($gallery, $pictureNum);

        $this->template->actualPic = $actualPic;
        $this->template->nextPic = $nextPic;
        $this->template->prevPic = $prevPic;
        $this->template->workID = $workID;
    }

}


