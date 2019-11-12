<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


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


	public function __construct(Nette\Database\Context $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
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
	 * Adds new user.
	 * @throws DuplicateNameException
	 */
	public function add(string $username, string $name, string $surname, string $email, string $dateOfBirth, string $phone_number, string $role, string $password): void
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

	public function delete(string $username)
	{
		/*
		try{
			//$this->database->table(self::TABLE_NAME)->delete()
		}*/
		
	}
}

class DuplicateNameException extends \Exception
{
}
