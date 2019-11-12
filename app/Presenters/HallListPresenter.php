<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

class HallListPresenter extends Nette\Application\UI\Presenter
{
    private $database;
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderHallList(): void
    {
        $this->template->halls = $this->database->table('hall');
    }

    public function renderOneHall(string $hall_num): void
    {
        $hall = $this->database->table('hall')->get($hall_num);
        $this->template->hall = $hall;
        
    }
}
