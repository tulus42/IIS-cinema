<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms;
use App\Model;

class UserPresenter extends BasePresenter
{
    private $database;

    /** @var Forms\DeleteFormFactory */
    private $deleteFormFactory;

    /** @var Forms\EditOwnProfileFormFactory */
    private $editOwnProfileFormFactory;
    
    /** @var Model\UserManager */
    private $userManager;

    public function __construct(Nette\Database\Context $database, Model\UserManager $userManager, Forms\EditOwnProfileFormFactory $editOwnProfileFormFactory)
    {
        $this->database = $database;
        $this->userManager = $userManager;
        $this->editOwnProfileFormFactory = $editOwnProfileFormFactory;
    }

    public function renderProfile(): void
    {
        if ($this->user->isLoggedIn()){
            $userID = $this->getUser()->id;
            $this->template->this_profile = $this->database->table('user')->get($userID);
        }
        else{
            throw new \Nette\Application\BadRequestException(403);
        }
    }

    public function renderDelete(): void
    {
        if ($this->user->isLoggedIn()){
        $userID = $this->getUser()->id;
        $this->template->current_user = $this->database->table('user')->get($userID);
        }
        else{
            throw new \Nette\Application\BadRequestException(403);
        }
    }
    
    public function renderEdit(): void
    {
        if (!$this->user->isLoggedIn()){
            throw new \Nette\Application\BadRequestException(403);
        }
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
    
    public function createComponentEditProfile(): Form
    {
        $userID = $this->getUser()->id;
        return $this->editOwnProfileFormFactory->createEdit($userID, function (): void {
			$this->redirect('User:profile');
		});
    }
    
}