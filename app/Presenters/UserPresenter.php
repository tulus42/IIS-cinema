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

    public function renderMyReservations(): void
    {
        if ($this->user->isLoggedIn()){
            $userID = $this->getUser()->id;
            $this->template->this_profile = $this->database->table('user')->get($userID);

            $userReserves = $this->database->table('user_reserves')->where('username = ?', $userID);
            $reservations = $this->database->query('SELECT *
            FROM reservation
            WHERE reservation_id IN (
                SELECT reservation_id 
                FROM user_reserves
                WHERE username = "' . $userID . '"
            );');

            

            $resEventArr = [];
            foreach ($reservations as $res) {
                $seat = $this->database->table('seat')->get($res->seat1);
                $event = $this->database->table('cultural_event')->get($seat->cultural_event_id);
                $work = $this->database->table('cultural_piece_of_work')->get($res->id_piece_of_work);

                $numberOfSeats = 1;
                $numberOfSeats += ($res->seat2 == null ? 0 : 1);
                $numberOfSeats += ($res->seat3 == null ? 0 : 1);
                $numberOfSeats += ($res->seat4 == null ? 0 : 1);
                $numberOfSeats += ($res->seat5 == null ? 0 : 1);
                $numberOfSeats += ($res->seat6 == null ? 0 : 1);

                $tmpArr = [];
                array_push($tmpArr, $res);
                array_push($tmpArr, $event);
                array_push($tmpArr, $work);
                array_push($tmpArr, $numberOfSeats);

                array_push($resEventArr, $tmpArr);
            }

            $this->template->reservations = $resEventArr;
        }
        else{
            throw new \Nette\Application\BadRequestException(403);
        }
    }


    public function renderOneReservation($reservationID): void
    {
        if ($this->user->isLoggedIn()){
            $userID = $this->getUser()->id;

            $res = $this->database->table('reservation')->get($reservationID);

            


            
            $seat = $this->database->table('seat')->get($res->seat1);
            $event = $this->database->table('cultural_event')->get($seat->cultural_event_id);
            $work = $this->database->table('cultural_piece_of_work')->get($res->id_piece_of_work);

            $tmpArr = [];
            array_push($tmpArr, $res->seat1);
            $res->seat2 == null ? 0 : array_push($tmpArr, $res->seat2);
            $res->seat3 == null ? 0 : array_push($tmpArr, $res->seat3);
            $res->seat4 == null ? 0 : array_push($tmpArr, $res->seat4);
            $res->seat5 == null ? 0 : array_push($tmpArr, $res->seat5);
            $res->seat6 == null ? 0 : array_push($tmpArr, $res->seat6);

            
            $resArr = [];
            array_push($resArr, $res);
            array_push($resArr, $event);
            array_push($resArr, $work);
            array_push($resArr, $tmpArr);

            $this->template->reservation = $resArr;
        
        
        }else{
            throw new \Nette\Application\BadRequestException(403);
        }
    }
    
}