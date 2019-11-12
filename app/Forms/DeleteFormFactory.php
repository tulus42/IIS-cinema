<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

class DeleteFormFactory
{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    
    /** @var Model\UserManager */
    private $userManager;

    public function __construct(FormFactory $factory){
        $this->factory = $factory;
    }

    public function createDeleteForm(): Form{
        $form = $this->factory->create();
        $form->addSubmit('delete', 'Áno');
        $form->addSubmit('cancel', 'Nie');
        return $form;
    }    
}