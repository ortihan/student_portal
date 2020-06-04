<?php

class Member{
	
	protected $id_student, $id_offer, $status;

	function __construct( $id_student, $id_offer, $status ){
		$this->id_student = $id_student;
    	$this->id_offer = $id_offer;
		$this->status = $status;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
