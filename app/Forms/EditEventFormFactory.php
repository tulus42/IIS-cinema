<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use App\Presenters;
use Nette\Utils\DateTime;

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

    public function createEditEventForm(int $eventId, callable $onSuccess): Form
    {
        $form = $this->factory->create();

        $currentEvent = $this->eventManager->getEvent($eventId);

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
        
        $formating_date = $currentEvent->date;
        $result_date = $formating_date->format('Y-m-d');

        
        $formating_time = $currentEvent->time;
        $result_time = $formating_time->format('%h:%i');
     
        $form->setDefaults([
            'price' => (float) $currentEvent->price,
            'dateOfEvent' => Date($result_date),
            'timeOfEvent' => $result_time
        ]);
        

        $form->addSubmit('send', 'Uložiť')
            ->setHtmlAttribute('class', 'form-button');


        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($eventId, $onSuccess): void {
            try{
                $this->eventManager->editEvent($eventId, $values->dateOfEvent, $values->timeOfEvent, (int) $values->price);
                $onSuccess();
            } catch(Model\DuplicateNameException $e) {
                // TODO DUPLICATE ERROR
                //$form['hall_num']->addError('Sála s týmto názvom už existuje');
            }
        };

        return $form;
    }


}