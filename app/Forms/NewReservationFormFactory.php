<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class NewReservationFormFactory{
    use Nette\SmartObject;

    /** @var FormFactory */
    private $factory;

    /** @var Model\ReservationManager */
    private $reservationManager;

    /** @var Model\SeatManager */
    private $seatManager;

    /** @var Model\UserReservesManager */
    private $userReservesManager;

    public function __construct(FormFactory $factory, Model\ReservationManager $reservationManager, Model\SeatManager $seatManager, Model\UserReservesManager $userReservesManager)
    {
        $this->factory = $factory;
        $this->reservationManager = $reservationManager;
        $this->seatManager = $seatManager;
        $this->userReservesManager = $userReservesManager;
    }


    public function createReservationForm($work, $event, $seats, $userID, $logged, $cashier, $presenter, callable $onSuccess): Form
    {
        $form = $this->factory->create();

        if (!$logged) {
            $form->addEmail('email', 'E-mail')
            ->setHtmlAttribute('class', 'form-text')
            ->setOption('description', '(na tento e-mail Vám budú zasnlané informácie o rezervácií)')
            ->setRequired();
        } 

        if ($logged && $cashier) {
            $form->addRadioList ('paymentMethod', 'Zvoľte spôsob platby:', [
                'card' => 'Platba kartou',
                'cash' => 'V hotovosti pri prevzatí',
                'cashier' => 'Predaj na pokladni',
            ])->setRequired();
        } else {
            $form->addRadioList ('paymentMethod', 'Zvoľte spôsob platby:', [
                'card' => 'Platba kartou',
                'cash' => 'V hotovosti pri prevzatí',
            ])->setRequired();
        }
        
        
        $form->addSubmit('pay', 'Potvrdiť rezerváciu')
            ->setHtmlAttribute('class', 'form-button');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($work, $event, $seats, $userID, $presenter, $onSuccess): void {
                        
            $seatsID = [];
            foreach ($seats as $seat) {
                array_push($seatsID, $seat->seat_id);

                if ($seat->state != "available") {
                    $presenter->redirect('Event:reserveUnSuccess', $event);
                }
            }

            $paid = '';
            switch ($values->paymentMethod) {
                case 'card': 
                    $paid = 'paid';
                    $this->seatManager->takeSeats($seats);
                    break;
                case 'cash': 
                    $paid = 'unpaid';
                    $this->seatManager->reserveSeats($seats);
                    break;
                case 'cashier':
                    $paid = 'paid';
                    $this->seatManager->takeSeats($seats);
                    break;
            }

            

            while (count($seatsID) < 6) {
                array_push($seatsID, NULL);
            }

            try{
                $reservation = $this->reservationManager->createReservation($work->id_piece_of_work, $paid, $seatsID[0], $seatsID[1], $seatsID[2], $seatsID[3], $seatsID[4], $seatsID[5]);
                
                if ($userID != '') {
                    $this->userReservesManager->createUserReserves($userID, $reservation->reservation_id); 
                }

                switch($values->paymentMethod){
                    case 'card':
                        $presenter->redirect('Event:payByCard', $reservation->reservation_id);
                        break;
                    case 'cash':
                        $presenter->redirect('Event:reserveSuccess', $reservation->reservation_id);
                        break;
                    case 'cashier':
                        $presenter->redirect('Event:reserveSuccessByCashier', $reservation->reservation_id);
                        break;
                }
               

                $onSuccess();
            } catch(Model\DuplicateNameException $e) {
                $form['seat']->addError('Sála s týmto názvom už existuje');
            }
        };

        return $form;
    }
}