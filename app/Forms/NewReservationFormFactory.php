<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewReservationFormFactory{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory)
    {
        $this->factory = $factory;
    }


    public function createReservationForm(callable $onSuccess): Form
    {
        $form = $this->factory->create();

        $form->addRadioList ('paymentMethod', 'Zvoľte spôsob platby:', [
            'card' => 'Platba kartou',
            'cash' => 'V hotovosti pri prevzatí',
        ]);


        return $form;

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            try{
            
                
                $onSuccess();
            } catch(Model\DuplicateNameException $e) {
                $form['seat']->addError('Sála s týmto názvom už existuje');
            }
        };
    }
}