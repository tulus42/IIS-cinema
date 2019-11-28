<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Security\IUserStorage;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public function startup()
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            if($this->user->getLogoutReason() === IUserStorage::INACTIVITY) {
                
            }
        }
        
    }
}
