<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class EditUserProfileFormFactory
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
    
    public function createEditUser(string $username, callable $onSuccess): Form
    {
        $form = $this->factory->create();
        $current_user = $this->userManager->getUser($username);

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

		
		$form->addSubmit('send', 'Uložiť')
            ->setHtmlAttribute('class', 'form-button');
            
        $formating= $current_user->date_of_birth;
        $result = $formating->format('Y-m-d');

        $form->setDefaults([
            'name' => $current_user->name,
            'surname' => $current_user->surname,
            'role' => $current_user->rights,
            'email' => $current_user->e_mail,
            'phoneNumber' => $current_user->phone_number,
            'dateOfBirth' => Date($result)
        ]);

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($username, $onSuccess): void {
            // check for birth in the future
			$today = date("Y-m-d");
			if($values->dateOfBirth > $today){
				$form['dateOfBirth']->addError('Dátum narodenia musí byť v minulosti');
				return;
			}
			// check for invalid phone number format (only numbers are accepted)
			if(strlen($values->phoneNumber) != 0){
				if(!Nette\Utils\Validators::isNumericInt($values->phoneNumber)){
					$form['phoneNumber']->addError('Telefónne číslo môže obsahovať iba číslice bez medzier');
					return;
				}
			}
            
            $this->userManager->editUserAdmin($username, $values->name, $values->surname, $values->email, $values->dateOfBirth, $values->phoneNumber, $values->role);
            $onSuccess();
        };

        return $form;
    }
}