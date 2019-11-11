<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

class HomepagePresenter extends Nette\Application\UI\Presenter
{
	private $database;
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    public function renderDefault(): void
    {
        $this->template->movies = $this->database->table('cultural_piece_of_work');
    }
}