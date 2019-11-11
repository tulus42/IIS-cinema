<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class MoviePresenter extends Nette\Application\UI\Presenter
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow(string $id_piece_of_work): void
    {
        $this->template->piece_of_work = $this->database->table('cultural_piece_of_work')->get($id_piece_of_work);
    }
}