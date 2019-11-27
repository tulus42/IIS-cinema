<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class EditWorkFormFactory{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;
    
    /** @var Model\WorkManager */
    private $workManager;
    
    public function __construct(FormFactory $factory, Model\WorkManager $workManager){
        $this->factory = $factory;
        $this->workManager = $workManager;
    }

    public function createEditWorkForm(int $workId, callable $onSuccess): Form
    {
        $form = $this->factory->create();

        $form->addText('name', '*Názov:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $allGenres = array(
            'akčný',
            'dobrodružný',
            'dráma',
            'fantasy',
            'filozofický',
            'historický',
            'horor',
            'komédia',
            'krimi',
            'mysteriózny',
            'politický',
            'romantický',
            'triller',
            'vedecký',
            'western'
        );

        $form->addSelect('genre', '*Žáner:')
            ->setHtmlAttribute('class', 'form-text')
            ->setItems($allGenres, false);

        $form->addSelect('type', '*Typ', [
            'film' => 'film',
            'prednáška' => 'prednáška',
            'divadlo' => 'divadlo',
            'iné' => 'iné'
        ])
            ->setHtmlAttribute('class', 'form-text');

        $form->addText('picture', '*URL obrázka')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $form->addTextArea('description', 'Popis:')
            ->setHtmlAttribute('class', 'form-text-description');

        $form->addInteger('duration', '*Dĺžka trvania:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired()
            ->addRule(Form::MIN, 'Dĺžka nesmie byť záporné číslo', 0);

        $form->addInteger('rating', 'Hodnotenie:')
            ->setHtmlAttribute('class', 'form-text')
            ->addRule(Form::RANGE, 'Hodnotenie musí byť v rozmedzí 0 až 100', [0, 100]);

        
        $this_work = $this->workManager->getWork($workId);

        $form->setDefaults([
            'name' => $this_work->name,
            'genre' => $this_work->genre,
            'type' => $this_work->type,
            'picture' => $this_work->picture,
            'description' => $this_work->description,
            'duration' => $this_work->duration,
            'rating' => $this_work->rating
        ]);

        $form->addSubmit('send', 'Uložiť')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($workId, $onSuccess): void {
            $this->workManager->editWork($workId, $values->name, $values->genre, $values->type, $values->picture, $values->description, $values->duration, $values->rating);
            $onSuccess();
        };

        return $form;
    }
}