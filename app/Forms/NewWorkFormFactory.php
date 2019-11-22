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
            ->setItems($allGenres, true);

        $form->addSelect('type', '*Typ', [
            'film' => 'film',
            'prednáška' => 'prednáška',
            'divadlo' => 'divadlo',
            'iné' => 'iné'
        ])
            ->setHtmlAttribute('class', 'form-text');

        $form->addUpload('poster', '*Plagát')
            //->setRequired(true)
            ->addRule(Form::IMAGE, 'Plagát musí byť JPEG, PNG')
            ->setHtmlAttribute('class', 'form-file');

        $form->addText('picture', '*URL obrázka')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired();

        $form->addTextArea('description', 'Popis:')
            ->setHtmlAttribute('class', 'form-text-description');

        $form->addInteger('duration', 'Dĺžka trvania:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired()
            ->addRule(Form::MIN, 'Dĺžka nesmie byť záporné číslo', 0);

        $form->addInteger('rating', 'Hodnotenie:')
            ->setHtmlAttribute('class', 'form-text')
            ->addRule(Form::RANGE, 'Hodnotenie musí byť v rozmedzí 0 až 100', [0, 100]);


        $form->addSubmit('send', 'Pridať')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $current_poster = $values->poster;
            try{
                //$values->poster = $this->imageStorage->SaveUpload($current_poster);
            }
            catch(\Exception $e){
                dump('ERROR!');
            }

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

            $this->workManager->addWork($values->name, $allGenres[$values->genre], $values->type, $values->picture, $values->description, $values->duration, $values->rating);
            $onSuccess();
        };

        return $form;
    }
}