<?php

class Student{
	
	protected $id, $username, $password, $name, $surname, $phone, $email, $school, $grades, $free_time, $cv;

	function __construct( $id, $username, $password, $name, $surname, $email, $phone, $school, $grades, $free_time, $cv ){
		$this->id = $id;
    	$this->username = $username;
		$this->name = $name;
		$this->surname = $surname;
		$this->phone = $phone;
		$this->email = $email;
    	$this->school = $school;
    	$this->grades = $grades;
		$this->free_time = $free_time;
    	$this->cv = $cv;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
