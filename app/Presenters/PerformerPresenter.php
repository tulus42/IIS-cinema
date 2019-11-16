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

    public function __construct(Model\PerformerManager $performerManager)
    {
        $this->performerManager = $performerManager;
    }

    public function renderList()
    {
        $this->template->performers = $this->performerManager->allPerformers();
    }

    public function renderProfile(int $id)
    {
        $this->template->one_performer = $this->performerManager->getOnePerformer($id);
    }
}