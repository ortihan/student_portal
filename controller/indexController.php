<?php 


class IndexController extends BaseController
{

	//samo preusmjeri na dashboard
	public function index() {
		unset($_SESSION['checked']);
		$who = false;
		$this->registry->template->who = $who;
		//$this->registry->template->show( '404_index' );
		header( 'Location: ' . __SITE_URL . '/index.php?rt=index/all_offers' );	
		exit();
	}

	//dohvaća sve ponude
	public function all_offers(){
		$spp = new studentplus_service();

		$offers = $spp->get_all_offers();
		$this->registry->template->offers = $offers;

		unset($_SESSION['offer']);

		$who = false;
		$this->registry->template->who = $who;

		$this->registry->template->title = 'Dashboard!';
		$this->registry->template->show( 'dashboard_index' );
	} 

	//ako se stisnuo button login/register
	public function check_button_choice(){
		$who = false;
		$this->registry->template->who = $who;
		$type = "nije izabrano";
		if( isset($_POST['login']) ){
			$this->registry->template->login_type = $type;
			$this->registry->template->title = 'Login!';
			$this->registry->template->show( 'login' );
			
		}
		if( isset($_POST['register']) ){
			$this->registry->template->reg_type = $type;
			$this->registry->template->title = 'Register!';
			$this->registry->template->show( 'register' );
		}
	}

	//procesuiraj login
	public function check_login_type(){
		$who = false;
		$this->registry->template->who = $who;

		if( isset($_POST['odabir']) ){
			if ($_POST['odabir'] === 'student'){
				header( 'Location: ' . __SITE_URL . '/index.php?rt=student/check_login' );
				exit();
			}
			else if( $_POST['odabir'] === 'company' ){
				header( 'Location: ' . __SITE_URL . '/index.php?rt=company/check_login' );
				exit();
			}

			//inače
			echo 'Login failed!';
			$this->all_offers();
			exit();
		}
	}

	//procesuiraj register
	public function check_register_type(){
		$who = false;
		$this->registry->template->who = $who;

		if( isset($_POST['odabir']) ){
			if( $_POST['odabir'] === "student"){
				header( 'Location: ' . __SITE_URL . '/index.php?rt=student/check_register' );
				exit();
			}
			else if( $_POST['odabir'] === "company" ){
				header( 'Location: ' . __SITE_URL . '/index.php?rt=company/check_register' );
				exit();
			}

			//dosli do oovdje 
			echo 'Registration failed!';
			$this->all_offers();
		}
	}

	public function search_results() {
		$who = false;
		$this->registry->template->who = $who;

		$spp = new studentplus_service();
		$offers = $spp->get_offers_by_podstring_name($_POST['search']);

		if( count($offers) === 0 ){
			if( strlen($_POST['search']) === 0 ) $message = "Niste unijeli ime ponude.";
			else $message = 'Ne postoje ponude koje sadrže '. $_POST['search'] . ' u svom imenu.';			
			$offers = $spp->get_all_offers();
		} 
		else $message = '';

		$this->registry->template->offers = $offers;
		$this->registry->template->message = $message;
		$this->registry->template->show( 'dashboard_index' );
		exit();
	}
}; 

?>