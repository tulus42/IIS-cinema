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
    private $workManager;
    
    public function __construct(FormFactory $factory, Model\WorkManager $workManager){
        $this->factory = $factory;
        $this->workManager = $workManager;
    }

    public function createWorkForm(callable $onSuccess): Form
    {
        $form = $this->factory->create();

        $form->addText('name', '*Názov:')
            ->setRequired();

        $form->addText('genre', '*Žáner:')
            ->setRequired();

        $form->addSelect('type', '*Typ', [
            'film' => 'film',
            'prednáška' => 'prednáška',
            'divadlo' => 'divadlo',
            'iné' => 'iné'
        ]);

        $form->addText('picture', '*URL obrázka')
            ->setRequired();

        $form->addText('description', 'Popis:');

        $form->addInteger('duration', 'Dĺžka trvania:')
            ->addRule(Form::MIN, 'Dĺžka nesmie byť záporné číslo', 0);

        $form->addInteger('rating', 'Hodnotenie:')
            ->addRule(Form::RANGE, 'Hodnotenie musí byť v rozmedzí 0 až 100', [0, 100]);

        $form->addSubmit('send', 'Pridať')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $this->workManager->addWork($values->name, $values->genre, $values->type, $values->picture, $values->description, $values->duration, $values->rating);
            $onSuccess();
        };

        return $form;
    }
}