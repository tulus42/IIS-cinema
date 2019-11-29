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

        $form->addText('poster', '*URL plagátu')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $form->addText('picture_1', 'URL obrázka č. 1')
            ->setHtmlAttribute('class', 'form-text');

        $form->addText('picture_2', 'URL obrázka č. 2')
            ->setHtmlAttribute('class', 'form-text');

        $form->addText('picture_3', 'URL obrázka č. 3')
            ->setHtmlAttribute('class', 'form-text');

        $form->addText('picture_4', 'URL obrázka č. 4')
            ->setHtmlAttribute('class', 'form-text');

        $form->addText('picture_5', 'URL obrázka č. 5')
            ->setHtmlAttribute('class', 'form-text');
        

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
            'poster' => $this_work->picture,
            'picture_1' => $this_work->picture2,
            'picture_2' => $this_work->picture3,
            'picture_3' => $this_work->picture3,
            'picture_4' => $this_work->picture3,
            'picture_5' => $this_work->picture3,
            'description' => $this_work->description,
            'duration' => $this_work->duration,
            'rating' => $this_work->rating
        ]);

        $form->addSubmit('send', 'Uložiť')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($workId, $onSuccess): void {
            $this->workManager->editWork($workId, $values->name, $values->genre, $values->type, $values->poster, $values->picture_1, $values->picture_2, $values->picture_3, $values->picture_4, $values->picture_5, $values->description, $values->duration, $values->rating);
            $onSuccess();
        };

        return $form;
    }
}