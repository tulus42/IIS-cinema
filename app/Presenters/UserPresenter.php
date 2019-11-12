<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class UserPresenter extends Nette\Application\UI\Presenter
{
    private $database;

    /** @var Forms\DeleteFormFactory */
    private $deleteFormFactory;
    
    /** @var Model\UserManager */
    private $userManager;

    public function __construct(Nette\Database\Context $database, Model\UserManager $userManager)
    {
        $this->database = $database;
        $this->userManager = $userManager;
    }

    public function renderProfile(): void
    {
        $userID = $this->getUser()->id;
        $this->template->this_profile = $this->database->table('user')->get($userID);
    }

    public function renderDelete(): void
    {
        $userID = $this->getUser()->id;
        $this->template->current_user = $this->database->table('user')->get($userID);
    }
    
    
    protected function createComponentDeleteForm(): Form
    {
        $form = new Form;
        $form->addSubmit('delete', 'Áno')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'deleteFormSucceeded'];
        $form->addSubmit('cancel', 'Nie')
            ->setHtmlAttribute('class', 'form-button')
			->onClick[] = [$this, 'formCancelled'];
		$form->addProtection();
		return $form;
    }

    public function deleteFormSucceeded(): void
	{
        $userID = $this->getUser()->id;
        $this->getUser()->logout();
        $this->userManager->deleteUser($userID);
        $this->redirect('Homepage:default');
        
    }
    
    public function formCancelled(): void
	{
		$this->redirect('profile');
	}
    
}