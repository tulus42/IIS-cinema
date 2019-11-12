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

        

        $form->addSubmit('send', 'Pridať');
        return $form;
    }
}