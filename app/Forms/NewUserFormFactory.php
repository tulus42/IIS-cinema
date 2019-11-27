<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewUserFormFactory
{
    use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, Model\UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
    }
    
    public function createUser(callable $onSuccess): Form
    {
        $form = $this->factory->create();
		$form->addText('username', '*Prihlasovacie meno:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vyplňte prihlasovacie meno');

		$form->addText('name', '*Krstné meno:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vyplňte Vaše krstné meno');

		$form->addText('surname', '*Priezvisko:')
			->setHtmlAttribute('class', 'form-text')
            ->setRequired('Prosím vyplňte Vaše priezvisko');
            
        $form->addSelect('role', '*Právomoc', [
            'viewer' => 'divák',
            'admin'  => 'administrátor',
            'redactor' => 'redaktor',
            'cashier' => 'pokladník'
        ])
            ->setHtmlAttribute('class', 'form-text');

		$form->addEmail('email', '*E-mail:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vložte Váš e-mail');

		$form->addText('dateOfBirth', '*Dátum narodenia:')
			->setType('Date')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vyberte Váš dátum narodenia');

		$form->addText('phoneNumber', 'Telefónne číslo:')
			->setHtmlAttribute('class', 'form-text');

		$form->addPassword('password', '*Heslo:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vložte Vaše heslo');

		$form->addPassword('passwordConfirm', '*Potvrdenie hesla:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vložte Vaše heslo');

		
		$form->addSubmit('send', 'Vytvoriť užívateľa')
			->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
			$today = date("Y-m-d");
			if($values->dateOfBirth > $today){
				$form['dateOfBirth']->addError('Dátum narodenia musí byť v minulosti');
				return;
			}
			try {
                //$values->dateOfBirth = date('Y-m-d', strtotime($values->dateOfBirth));
                $this->userManager->addUser($values->username, $values->name, $values->surname, $values->email, $values->dateOfBirth, $values->phoneNumber, $values->role, $values->password);
                $onSuccess();
            } catch (Model\DuplicateNameException $e) {
                $form['username']->addError('Užívateľské meno už existuje.');
            }	
        };

        return $form;
    }
}