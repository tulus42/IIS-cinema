<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use App\Presenters;

class EditEventFormFactory
{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    
    /** @var Model\EventManager */
    private $eventManager;

    /** @var Model\HallManager */
    private $hallManager;

    /** @var Model\SeatManager */
    private $seatManager;
    
    public function __construct(FormFactory $factory, Model\EventManager $eventManager, Model\HallManager $hallManager, Model\SeatManager $seatManager){
        $this->factory = $factory;
        $this->eventManager = $eventManager;
        $this->hallManager = $hallManager;
    }

    public function createEventForm(int $workId, callable $onSuccess): Form
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
            ->setItems($allHalls, true);

        $form->addSubmit('send', 'Pridať')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($workId, $onSuccess): void {
            try{
                $this->eventManager->addEvent($values->dateOfEvent, $values->timeOfEvent, (int) $values->price, $workId, $values->hall);
                $onSuccess();
            } catch(Model\DuplicateNameException $e) {
                // TODO DUPLICATE ERROR
                //$form['hall_num']->addError('Sála s týmto názvom už existuje');
            }
        };

        return $form;
    }


}