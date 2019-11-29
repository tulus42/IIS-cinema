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


        $sub1 = $form->addContainer('url');
        $sub1->addText('picture', '*URL plagátu')
            ->setRequired()
            ->setHtmlAttribute('class', 'form-text');

        
        $form->addSubmit('pridatURL', 'Pridať ďalší obrázok')
            ->setHtmlAttribute('class', 'form-button')
            ->setValidationScope([]);
        
            
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
            if ($form['url']['picture']->getValue() != null) {
                $form['url']->addText('picture2', 'URL obrázka č. 1')
                ->setHtmlAttribute('class', 'form-text');

                if ($form['url']['picture2']->getValue() != null) {
                    $form['url']->addText('picture3', 'URL obrázka č. 2')
                    ->setHtmlAttribute('class', 'form-text');

                    if ($form['url']['picture3']->getValue() != null) {
                        $form['url']->addText('picture4', 'URL obrázka č. 3')
                        ->setHtmlAttribute('class', 'form-text');

                        if ($form['url']['picture4']->getValue() != null) {
                            $form['url']->addText('picture5', 'URL obrázka č. 4')
                            ->setHtmlAttribute('class', 'form-text');

                            if ($form['url']['picture5']->getValue() != null) {
                                $form['url']->addText('picture6', 'URL obrázka č. 5')
                                ->setHtmlAttribute('class', 'form-text');

                                if ($form['url']['picture6']->getValue() != null) {
                                    $form['url']['picture6']->addError('Môžete pridať maximálne 5 obrázkov');
                                } else {

                                }
                            }
                        }
                    }
                }
            }
            
            
            
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

                $pictureArr = [];
                array_push($pictureArr, $values->url->picture);
  
    
                if ($form['url']['picture2']->getValue() != null) {
                    array_push($pictureArr, $form['url']['picture2']->getValue());

                    if ($form['url']['picture3']->getValue() != null) {
                        array_push($pictureArr, $form['url']['picture3']->getValue());

                        if ($form['url']['picture4']->getValue() != null) {
                            array_push($pictureArr, $form['url']['picture4']->getValue());

                            if ($form['url']['picture5']->getValue() != null) {
                                array_push($pictureArr, $form['url']['picture5']->getValue());

                                if ($form['url']['picture6']->getValue() != null) {
                                    array_push($pictureArr, $form['url']['picture6']->getValue());
                                } else {

                                }
                            }
                        }
                    }
                }
        
                while(count($pictureArr) < 6) {
                    array_push($pictureArr, null);
                }

                $can_return = false;

                if(strlen($values->description) > 2048){
                    $form['description']->addError('Dĺžka popisu môže byť maximálne 2048 znakov');
                    $can_return = true;
                }

                if(strlen($values->url->picture) > 2048){
                    $form['url']['picture']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                    $can_return = true;
                }

                if ($form['url']['picture2']->getValue() != null) {
                    $current_pic = $form['url']['picture2']->getValue();
                    if(strlen($current_pic) > 2048){
                        $form['url']['picture2']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                        $can_return = true;
                    }

                    if ($form['url']['picture3']->getValue() != null) {
                        $current_pic = $form['url']['picture3']->getValue();
                        if(strlen($current_pic) > 2048){
                            $form['url']['picture3']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                            $can_return = true;
                        }

                        if ($form['url']['picture4']->getValue() != null) {
                            $current_pic = $form['url']['picture4']->getValue();
                            if(strlen($current_pic) > 2048){
                                $form['url']['picture4']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                                $can_return = true;
                            }

                            if ($form['url']['picture5']->getValue() != null) {
                                $current_pic = $form['url']['picture5']->getValue();
                                if(strlen($current_pic) > 2048){
                                    $form['url']['picture5']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                                    $can_return = true;
                                }

                                if ($form['url']['picture5']->getValue() != null) {
                                    $current_pic = $form['url']['picture5']->getValue();
                                    if(strlen($current_pic) > 2048){
                                        $form['url']['picture5']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                                        $can_return = true;
                                    }
                                }
                            }
                        }
                    }
                }
                /*
                $current_pic = $form['url']['picture']->getValue();
                if($current_pic != null){
                    if(strlen($current_pic) > 2048){
                        $form['url']['picture']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                        return;
                    }
                }

                $current_pic = $form['url']['picture2']->getValue();
                if($current_pic != null){
                    if(strlen($current_pic) > 2048){
                        $form['url']['picture2']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                        return;
                    }
                }


                $current_pic = $form['url']['picture6']->getValue();
                if($current_pic != null){
                    if(strlen($current_pic) > 2048){
                        $form['url']['picture6']->addError('Dĺžka URL môže byť maximálne 2048 znakov');
                        return;
                    }
                }
                */

                if($can_return){
                    return;
                }

                $this->workManager->addWork($values->name, $allGenres[$values->genre], $values->type, $pictureArr[0], $pictureArr[1], $pictureArr[2], $pictureArr[3], $pictureArr[4], $pictureArr[5], $values->description, $values->duration, $values->rating);
                $onSuccess();
            }
        };

        

        return $form;
    }


}