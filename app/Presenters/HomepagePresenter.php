<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

class HomepagePresenter extends BasePresenter
{
    use Nette\SmartObject;
    
	private $database;
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        
        $all_movies_with_events = $this->database->query('SELECT *
        FROM cultural_piece_of_work
        WHERE id_piece_of_work IN (
            SELECT id_piece_of_work
            FROM cultural_event
            ); ');
        $this->template->movies = $all_movies_with_events;

        $this->template->number = (int) $all_movies_with_events->getRowCount();
    }
}
