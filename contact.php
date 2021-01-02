<?php
class Contact {

	public $id;
	public $firstName;
	public $lastName;
	public $numbers;

	public function __construct($id, $firstName, $lastName, $numbers) {
		$this->id = $id;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->numbers = $numbers;
	}
}
