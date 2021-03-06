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

    public function checkPrivileges()
    {
        if (!$this->user->isLoggedIn()){
            throw new \Nette\Application\BadRequestException(403);
        }
    }

    public function renderProfile(): void
    {
        $this->checkPrivileges();
        $userID = $this->getUser()->id;
        $this->template->this_profile = $this->database->table('user')->get($userID);
    }

    public function renderDelete(): void
    {
        $this->checkPrivileges();
        $userID = $this->getUser()->id;
        $this->template->current_user = $this->database->table('user')->get($userID);
    }
    
    public function renderEdit(): void
    {
        $this->checkPrivileges();
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
        $this->checkPrivileges();
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


    public function renderOneReservation($reservationID): void
    {
        $this->checkPrivileges();

        $userID = $this->getUser()->id;

        $res = $this->database->table('reservation')->get($reservationID);

        $userRes = $this->database->table('user_reserves')->where('reservation_id = ?', $reservationID)->fetch();
        if ($userRes->username != $userID) {
            throw new \Nette\Application\BadRequestException(403);
        }

        $seat1 = $this->database->table('seat')->get($res->seat1);
                    
        $event = $this->database->table('cultural_event')->get($seat1->cultural_event_id);
        $work = $this->database->table('cultural_piece_of_work')->get($res->id_piece_of_work);

        $tmpArr = [];
        array_push($tmpArr, $seat1);
        $res->seat2 == null ? 0 : array_push($tmpArr, $this->database->table('seat')->get($res->seat2));
        $res->seat3 == null ? 0 : array_push($tmpArr, $this->database->table('seat')->get($res->seat3));
        $res->seat4 == null ? 0 : array_push($tmpArr, $this->database->table('seat')->get($res->seat4));
        $res->seat5 == null ? 0 : array_push($tmpArr, $this->database->table('seat')->get($res->seat5));
        $res->seat6 == null ? 0 : array_push($tmpArr, $this->database->table('seat')->get($res->seat6));

        
        $resArr = [];
        array_push($resArr, $res);
        array_push($resArr, $event);
        array_push($resArr, $work);
        array_push($resArr, $tmpArr);

        $this->template->reservation = $resArr;
        
        
        
    }

    public function renderMySeenMovies(): void
    {
        $this->checkPrivileges();
        $userID = $this->getUser()->id;

        $movies = $this->database->query('SELECT *
        FROM cultural_piece_of_work
        WHERE id_piece_of_work IN (
            SELECT id_piece_of_work
            FROM reservation
            WHERE reservation_id IN (
                SELECT reservation_id
                FROM user_reserves
                WHERE username = "'.$userID.'"
            )
        )
        GROUP BY id_piece_of_work
        ;');

        $this->template->movies = $movies;


        $tmpMovies = $this->database->table('cultural_piece_of_work')->where('id_piece_of_work', $this->database->table('reservation')->where('reservation_id', $this->database->table('user_reserves')->where('username ?', $userID)->select('reservation_id'))->select('id_piece_of_work'))->select('genre');
        $genres = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        foreach ($tmpMovies as $movie) {
            switch ($movie->genre) {
                case "akčný": $genres[0]++;
                break;
                case "dobrodružný": $genres[1]++;
                break;
                case "dráma": $genres[2]++;
                break;
                case "fantasy": $genres[3]++;
                break;
                case "filozofický": $genres[4]++;
                break;
                case "historický": $genres[5]++;
                break;
                case "horor": $genres[6]++;
                break;
                case "komédia": $genres[7]++;
                break;
                case "krimi": $genres[8]++;
                break;
                case "mysteriózny": $genres[9]++;
                break;
                case "politický": $genres[10]++;
                break;
                case "romantický": $genres[11]++;
                break;
                case "triller": $genres[12]++;
                break;
                case "vedecký": $genres[13]++;
                break;
                case "western": $genres[14]++;
                break;
            }
        }
        
        $mostSeenGenreIndex = array_keys($genres, max($genres));
        $mostSeenGenre = $this->getGenreFromIndex($mostSeenGenreIndex[0]);
        

        $notSeenMovie = $this->database->query('SELECT *
        FROM cultural_piece_of_work
        WHERE id_piece_of_work NOT IN (
            SELECT id_piece_of_work
            FROM reservation
            WHERE reservation_id IN (
                SELECT reservation_id
                FROM user_reserves
                WHERE username = "'.$userID.'"   
            )
        ) AND genre = "'.$mostSeenGenre.'"
        AND id_piece_of_work IN (
            SELECT id_piece_of_work
            FROM cultural_event
        )
        GROUP BY id_piece_of_work
        ;');
        
        if ($notSeenMovie != null) {
            $notSeenMovie = $notSeenMovie->fetch();
        }

        $this->template->notSeen = $notSeenMovie;

    }

    public function getGenreFromIndex($i)
    {
        switch ($i) {
            case 0: return "akčný";
            case 1: return "dobrodružný";
            case 2: return "dráma";
            case 3: return "fantasy";
            case 4: return "filozofický";
            case 5: return "historický";
            case 6: return "horor";
            case 7: return "komédia";
            case 8: return "krimi";
            case 9: return "mysteriózny";
            case 10: return "politický";
            case 11: return "romantický";
            case 12: return "triller";
            case 13: return "vedecký";
            case 14: return "western";
        }
    }
    
}