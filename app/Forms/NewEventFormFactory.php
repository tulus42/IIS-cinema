<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use App\Presenters;

class NewEventFormFactory
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
            $today = date("Y-m-d");
            if($today > $values->dateOfEvent){
                $form['dateOfEvent']->addError('Nie je možné vytvoriť udalosť v minulosti');
                return;
            }
            else if($today == $values->dateOfEvent){
                $now = date("H:I");
                if($now > $values->timeOfEvent){
                    $form['timeOfEvent']->addError('Nie je možné vytvoriť udalosť v minulosti');
                    return;
                }
            }

            $count = $this->eventManager->findDuplicateEvent($values->dateOfEvent, $values->timeOfEvent, $values->hall);
            if($count == 0){
                $this->eventManager->addEvent($values->dateOfEvent, $values->timeOfEvent, round($values->price, 2), $workId, $values->hall);
                
            }
            else{
                $form['dateOfEvent']->addError('Udalosť v tento čas a deň vo zvolenej sále už existuje');
                return;
            }
            $onSuccess();
        };

        return $form;
    }


}