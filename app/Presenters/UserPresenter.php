<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class UserPresenter extends Nette\Application\UI\Presenter
{
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderProfile(): void
    {
        $userID = $this->getUser()->id;
        $this->template->this_profile = $this->database->table('user')->get($userID);
    }

    
}