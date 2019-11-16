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

    public function __construct(Model\PerformerManager $performerManager, Nette\Database\Context $database)
    {
        $this->performerManager = $performerManager;
        $this->database = $database;
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
}