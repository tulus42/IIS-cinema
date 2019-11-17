<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewPerformerFormFactory
{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    
    /** @var Model\PerformerManager */
    private $performerManager;
    
    public function __construct(FormFactory $factory, Model\PerformerManager $performerManager)
	{
		$this->factory = $factory;
		$this->performerManager = $performerManager;
    }

    public function createPerformer(callable $onSuccess): Form
    {
        $form = $this->factory->create();
        
		$form->addText('name', '*Krstné meno:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired('Prosím vyplňte krstné meno účinkujúceho');

        $form->addText('surname', '*Priezvisko:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired('Prosím vyplňte priezvisko účinkujúceho');

        $form->addSubmit('send', 'Vytvoriť účinkujúceho')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $this->performerManager->addPerformer($values->name, $values->surname);
            $onSuccess();
        };

        return $form;
    }
}