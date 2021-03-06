<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Utils\Validators;
use App\Model;

/**
 * Users management.
 */
final class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	private const
		TABLE_NAME = 'user',
		COLUMN_USERNAME = 'username',
		COLUMN_NAME = 'name',
		COLUMN_SURNAME = 'surname',
		COLUMN_DATE_OF_BIRTH = 'date_of_birth',
		COLUMN_PHONE_NUMBER = 'phone_number',
		COLUMN_EMAIL = 'e_mail',
		COLUMN_ROLE = 'rights',
		COLUMN_PASSWORD_HASH = 'password';


	/** @var Nette\Database\Context */
	private $database;

	/** @var Passwords */
	private $passwords;

	/** @var Model\ReservationManager */
	private $reservationManager;

	public function __construct(Nette\Database\Context $database, Passwords $passwords, Model\ReservationManager $reservationManager)
	{
		$this->database = $database;
		$this->passwords = $passwords;
		$this->reservationManager = $reservationManager;
	}


	/**
	 * Performs an authentication.
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		[$username, $password] = $credentials;

		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_USERNAME, $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!$this->passwords->verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif ($this->passwords->needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
			]);
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_USERNAME], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user
	 * @throws DuplicateNameException
	 */
	public function addUser(string $username, string $name, string $surname, string $email, string $dateOfBirth, string $phone_number, string $role, string $password): void
	{
		Nette\Utils\Validators::assert($email, 'email');
		$dateOfBirth = date('Y-m-d', strtotime($dateOfBirth));
		try {
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_USERNAME => $username,
				self::COLUMN_NAME => $name,
				self::COLUMN_SURNAME => $surname,
				self::COLUMN_DATE_OF_BIRTH => $dateOfBirth,
				self::COLUMN_PHONE_NUMBER => $phone_number,
				self::COLUMN_EMAIL => $email,
				self::COLUMN_ROLE => $role,
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	/**
	 * Edits existing user
	 */
	public function editUser(string $username, string $name, string $surname, string $email, string $dateOfBirth, string $phone_number): void
	{
		Nette\Utils\Validators::assert($email, 'email');
		$dateOfBirth = date('Y-m-d', strtotime($dateOfBirth));
		$this->database->table(self::TABLE_NAME)->where(self::COLUMN_USERNAME, $username)->update([
			self::COLUMN_NAME => $name,
			self::COLUMN_SURNAME => $surname,
			self::COLUMN_DATE_OF_BIRTH => $dateOfBirth,
			self::COLUMN_PHONE_NUMBER => $phone_number,
			self::COLUMN_EMAIL => $email
			
		]);
	}

	/**
	 * Edits existing user
	 */
	public function editUserAdmin(string $username, string $name, string $surname, string $email, string $dateOfBirth, string $phone_number, string $role): void
	{
		Nette\Utils\Validators::assert($email, 'email');
		$dateOfBirth = date('Y-m-d', strtotime($dateOfBirth));
		$this->database->table(self::TABLE_NAME)->where(self::COLUMN_USERNAME, $username)->update([
			self::COLUMN_NAME => $name,
			self::COLUMN_SURNAME => $surname,
			self::COLUMN_DATE_OF_BIRTH => $dateOfBirth,
			self::COLUMN_PHONE_NUMBER => $phone_number,
			self::COLUMN_EMAIL => $email,
			self::COLUMN_ROLE => $role
		]);
	}

	/**
	 * Deletes existing user
	 */
	public function deleteUser(string $username)
	{
		$current_user = $this->getUser($username);
		if($current_user->rights == 'admin'){
			$admin_count = $this->getAdminCount();
			// there always has to be 1 admin
			if($admin_count > 1){
				$this->database->table(self::TABLE_NAME)->where(self::COLUMN_USERNAME, $username)->delete();
			}
		}
		else{
			$this->reservationManager->deleteUser($username);
			$this->database->table(self::TABLE_NAME)->where(self::COLUMN_USERNAME, $username)->delete();
		}
	}

	public function getAdminCount()
	{
		$result = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ROLE, 'admin')->fetchAll();
		return count($result);
	}

	public function getUsers(string $role)
	{
		if($role == "all")
		{
			return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_USERNAME . ' ASC')->select('*')->fetchAll();
		}
		else
		{
			return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_USERNAME . ' ASC')->where(self::COLUMN_ROLE, $role)->select('*')->fetchAll();
		}
	}

	public function getUser(string $username)
	{
		return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_USERNAME, $username)->select('*')->fetch();
	}

}

class DuplicateNameException extends \Exception
{
}
