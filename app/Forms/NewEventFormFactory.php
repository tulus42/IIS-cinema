<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

class NewEventFormFactory
{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    
    /** @var Model\EventManager */
    private $eventManager;

    /** @var Model\HallManager */
    private $hallManager;

    public function __construct(FormFactory $factory, Model\EventManager $eventManager, Model\HallManager $hallManager){
        $this->factory = $factory;
        $this->eventManager = $eventManager;
        $this->hallManager = $hallManager;
    }

    public function createEventForm(): Form
    {
        $form = $this->factory->create();

        $form->addText('dateOfEvent', '*Dátum konania:')
			->setType('Date')
			->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $form->addText('timeOfEvent', '*Čas konania:')
			->setType('Time', 'HOUR:MINUTE')
			->setHtmlAttribute('class', 'form-text')
            ->setRequired();
            
        $form->addText('price', '*Jednotková cena (€):')
            ->setHtmlAttribute('class', 'form-text')
            ->addRule(Form::MIN, 'Cena nesmie byť záporné číslo', 0)
            ->addRule(Form::FLOAT, 'Cena musí byť číslo')
            ->setRequired();

        $allHalls = $this->hallManager->getAllHalls();

        
        $form->addSelect('hall', '*Hala:')
            ->setHtmlAttribute('class', 'form-text')
            ->setItems($allHalls, false);

        $form->addSubmit('send', 'Pridať')
            ->setHtmlAttribute('class', 'form-button');
        return $form;
    }
}