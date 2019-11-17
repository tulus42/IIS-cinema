<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class UserManagmentPresenter extends BasePresenter
{
    private $database;

    /** @var Forms\NewUserFormFactory */
    private $newUserFormFactory;

    /** @var Forms\editUserProfileFormFactory */
    private $editUserProfileFormFactory;
    
    /** @var Model\UserManager */
    private $userManager;

    public function __construct(Nette\Database\Context $database, Model\UserManager $userManager, Forms\NewUserFormFactory $newUserFormFactory, Forms\editUserProfileFormFactory $editUserProfileFormFactory)
    {
        $this->database = $database;
        $this->newUserFormFactory = $newUserFormFactory;
        $this->userManager = $userManager;
        $this->editUserProfileFormFactory = $editUserProfileFormFactory;
    }


    public function startup(): void
    {
        parent::startup();

        if (!$this->user->isLoggedIn() or !$this->user->isInRole('admin')){
            throw new \Nette\Application\BadRequestException(403);
        }
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

    public function renderEdit(string $username)
    {
        ;
    }

    protected function createComponentEditForm(): Form
    {
        $username = $this->getParameter('username');
        return $this->editUserProfileFormFactory->createEditUser($username, function (): void {
            $username = $this->getParameter('username');
			$this->redirect('UserManagment:profile', $username);
		});
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
        $this->userManager->deleteUser($userID);
        $this->redirect('UserManagment:showGroup', $role_list);
    }
    
    public function formCancelled(): void
	{
        $role_list = $this->getParameter('role');
        $this->redirect('UserManagment:showGroup', $role_list);
	}

    public function createComponentAddForm(): Form
    {
        return $this->newUserFormFactory->createUser(function (): void {
			$this->redirect('UserManagment:showGroup', 'all');
		});
    }

    public function renderProfile(string $username)
    {
        $this->template->this_profile = $this->database->table('user')->get($username);
    }
}