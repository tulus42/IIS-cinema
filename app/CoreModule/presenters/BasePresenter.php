<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

abstract class BasePresenter
{
    public function handleLogout(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('Odhlásenie prebehlo úspešne', 'success');
        
    }
}