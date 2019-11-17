<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

class MovieListPresenter extends BasePresenter
{
    private $database;
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    public function renderAllMovies(): void
    {
        $this->template->movies = $this->database->table('cultural_piece_of_work')->order('name ASC');
    }
}
