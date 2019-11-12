<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewWorkFormFactory{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    
    /** @var Model\WorkManager */
    private $work;
    
    public function __construct(FormFactory $factory, Model\WorkManager $work){
        $this->factory = $factory;
        $this->work = $work;
    }

    public function createWorkForm(): Form
    {
        $form = $this->factory->create();
        return $form;
    }
}