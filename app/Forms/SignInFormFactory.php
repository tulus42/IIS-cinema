<?php

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


final class SignInFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;


	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	public function create(callable $onSuccess): Form
	{
		$form = $this->factory->create();
		$form->addText('username', '*Užívateľské meno:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vložte svoje užívateľské meno');

		$form->addPassword('password', '*Heslo:')
			->setHtmlAttribute('class', 'form-text')
			->setRequired('Prosím vložte svoje heslo');

		$form->addCheckbox('remember', 'Zapamätaj si ma');

		$form->addSubmit('send', 'Prihlásiť sa')
			->setHtmlAttribute('class', 'form-button');

		$form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				$this->user->setExpiration($values->remember ? '14 days' : '30 minutes');
				$this->user->login($values->username, $values->password);
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('The username or password you entered is incorrect.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
