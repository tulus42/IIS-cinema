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
        $stars_in = $this->database->table('stars_in')->where('performer_id', $id)->select('id_piece_of_work')->fetch();
        //$stars_in_work = $stars_in->related('id_piece_of_work');
        //$this->template->starsIn = $piece_of_work->related('stars_in', 'id_piece_of_work');
        //$stars_in_work = 
    }
}