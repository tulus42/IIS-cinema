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

    public $array;
    
    public function __construct(FormFactory $factory, Model\WorkManager $workManager){
        $this->factory = $factory;
        $this->workManager = $workManager;
        $this->array = [];
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

        $form->addText('picture', '*URL obrázka')
            ->setHtmlAttribute('class', 'form-text');
            // ->setRequired();
        $form->addSubmit('pridatURL', 'Potvrdiť URL obrázka')
            ->setHtmlAttribute('class', 'form-button')
            ->setValidationScope([])
            ;
    

        $form->addHidden('hidden1', null);
        $form->addHidden('hidden2', null);
        $form->addHidden('hidden3', null);
        $form->addHidden('hidden4', null);
        $form->addHidden('hidden5', null);
        $form->addHidden('hidden6', null);
            
        $form->addTextArea('description', 'Popis:')
            ->setHtmlAttribute('class', 'form-text-description');

        $form->addInteger('duration', '*Dĺžka trvania:')
            ->setHtmlAttribute('class', 'form-text')
            ->setRequired()
            ->addRule(Form::MIN, 'Dĺžka nesmie byť záporné číslo', 0);

        $form->addInteger('rating', 'Hodnotenie:')
            ->setHtmlAttribute('class', 'form-text')
            ->addRule(Form::RANGE, 'Hodnotenie musí byť v rozmedzí 0 až 100', [0, 100]);


        $form->addSubmit('send', 'Pridať')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            if ($form['send']->isSubmittedBy()) {
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

                $this->workManager->addWork($values->name, $allGenres[$values->genre], $values->type, $values->hidden1, $values->hidden2, $values->hidden3, $values->hidden4, $values->hidden5, $values->hidden6, $values->description, $values->duration, $values->rating);
                $onSuccess();
            } else {

                if ($form['hidden1']->getValue() == null) {
                    $form['hidden1']->setValue($form['picture']->getValue());
                } elseif ($form['hidden2']->getValue() == null) {
                    $form['hidden2']->setValue($form['picture']->getValue());
                } elseif ($form['hidden3']->getValue() == null) {
                    $form['hidden3']->setValue($form['picture']->getValue());
                } elseif ($form['hidden4']->getValue() == null) {
                    $form['hidden4']->setValue($form['picture']->getValue());
                } elseif ($form['hidden5']->getValue() == null) {
                    $form['hidden5']->setValue($form['picture']->getValue());
                } elseif ($form['hidden6']->getValue() == null) {
                    $form['hidden6']->setValue($form['picture']->getValue());
                } else {

                }



                $form['picture']->setValue('');
                
                
                dump($form['hidden1']->getValue());
                dump($form['hidden2']->getValue());
                dump($form['hidden3']->getValue());
                dump($form['hidden4']->getValue());
                dump($form['hidden5']->getValue());
                dump($form['hidden6']->getValue());
            }
        };

        

        return $form;
    }


}