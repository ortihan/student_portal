<?php

class Company {
	
	protected $oib , $password, $name, $email, $adress, $phone, $description;

	function __construct( $oib , $password, $name, $email, $adress, $phone, $description ){
		$this->oib = $oib;
		$this->name = $name;
		$this->email = $email;
    	$this->adress = $adress;
    	$this->phone = $phone;
		$this->description = $description;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
