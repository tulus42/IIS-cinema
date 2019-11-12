<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;

class MoviePresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow(int $id_piece_of_work): void
    {
        $piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
        $this->template->piece_of_work = $piece_of_work;
        $this->template->events = $piece_of_work->related('cultural_event');

    

        $this->template->starsIn = $piece_of_work->related('stars_in', 'id_piece_of_work');
    }
}

