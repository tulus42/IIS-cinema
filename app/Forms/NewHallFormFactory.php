<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewHallFormFactory{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var Model\HallManager */
    private $hallManager;

    public function __construct(FormFactory $factory, Model\HallManager $hallManager)
    {
        $this->factory = $factory;
        $this->hallManager = $hallManager;
    }

    public function createHallForm(callable $onSuccess): Form
    {
        $form = $this->factory->create();

        $form->addText('hall_num', '*Názov sály:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $form->addInteger('row', '*Počet radov:')
            ->setHtmlAttribute('class', 'form-text')
            ->addRule(Form::MIN, 'Počet radov musí byť aspoň 1', 1)
            ->setRequired();
            
        $form->addInteger('column', '*Počet stĺpcov:')
            ->setHtmlAttribute('class', 'form-text')
            ->addRule(Form::RANGE, 'Počet stĺpcov musí byť v rozmedzí 1 až 20', [1,20])
            ->setRequired();

        $form->addText('address', '*Adresa:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $form->addSubmit('send', 'Pridať')
            ->setHtmlAttribute('class', 'form-button');

        
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            try{
                $this->hallManager->addHall($values->hall_num, $values->row, $values->column, $values->address);
                $onSuccess();
            } catch(Model\DuplicateNameException $e) {
                $form['hall_num']->addError('Sála s týmto názvom už existuje');
            }
        };

        return $form;
    }
}