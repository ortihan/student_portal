<?php

class Offer{

	protected $id, $company, $name, $description, $adress, $period;

	function __construct( $id, $company, $name, $description, $adress, $period ){
		$this->id = $id;
    	$this->company = $company;
		$this->name = $name;
		$this->description = $description;
		$this->adress = $adress;
		$this->period = $period;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
