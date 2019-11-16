<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class UserManagmentPresenter extends Nette\Application\UI\Presenter
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

    public function renderShowGroup(string $role)
    {
        
        if($role == "all")
        {
            $this->template->title = "Všetci užívatelia";
        }
        else if($role == "cashier")
        {
            $this->template->title = "Pokladníci";
        }
        else if($role == "redactor")
        {
            $this->template->title = "Redaktori";
        }
        else if($role == "viewer")
        {
            $this->template->title = "Registrovaní diváci";
        }
        else if($role == "admin")
        {
            $this->template->title = "Administrátori";
        }
        else{
            $this->template->title = "Error";
            return;
        }
        $this->template->role = $role;
        $this->template->all_users = $this->userManager->getUsers($role);
    }

    public function renderDelete(string $role, string $username)
    {
        $this->template->one_user = $this->userManager->getUser($username);
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
        $userID = $this->getParameter('username');
        $role_list = $this->getParameter('role');
        $this->redirect('UserManagment:showGroup', $role_list);
        /*
        $this->userManager->deleteUser($userID);
        */
    }
    
    public function formCancelled(): void
	{
        $role_list = $this->getParameter('role');
        $this->redirect('UserManagment:showGroup', $role_list);
	}

}