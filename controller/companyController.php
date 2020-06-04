<?php 

class CompanyController extends BaseController{

	public function index() {}

	//obradi logout
	public function logout(){
		//kontroler za provođenje logout-a
		unset($_SESSION['login']); 
		unset($_SESSION['offer']);
		unset($_SESSION['student']);
		unset($_SESSION['student_profil']);

		//ako već nije, unset
		unset($_POST['oib']);
		unset($_POST['password']);
		unset($_POST['name']);
		unset($_POST['email']);
		unset($_POST['phone']);
		unset($_POST['adress']);
		unset($_POST['description']);

		$who = false; //da u viewu znamo da nitko nije logiran -- ako je false, nitko nije logiran

		session_unset(); 
		session_destroy();

		//prikupi sve ponude i ispiši ih na dashboard
		$spp = new studentplus_service();
		$offers = $spp->get_all_offers();
		$this->registry->template->who = $who;
		$this->registry->template->offers = $offers;

		header( 'Location: ' . __SITE_URL . '/index.php?rt=index/all_offers' );
		exit();
	}


	//obradi login
	public function check_login(){
		$spp = new studentplus_service();
		$login = "company";
		$login_message = "";

		$who = false;
		$this->registry->template->who = $who;
		$this->registry->template->login_type = $login;

		if(isset($_POST['oib']) && $_POST['oib'] !== "" ){ //napisan je oib
			
			//sanitizacija - oib mora biti broj
			if( !is_numeric($_POST['oib'])){
				$login_message = "OIB mora biti desetoznamenkasti broj!";
				$this->registry->template->message_company = $login_message;
				$this->registry->template->show( 'login' );
				exit();
			}

			//provjeri je li oib u bazi
			if( $spp->get_company_by_oib($_POST['oib']) === null ){
				$login_message = "Ne postoji registrirana tvrtka s navedenim OIB-om!";
				$this->registry->template->message_company = $login_message;
				$this->registry->template->show( 'login' );
				exit();
			}

			//oib je u bazi
			//dohvati lozinku te tvrtke
			$pass = $spp->get_password_by_oib( $_POST['oib'] );

			if(isset( $_POST['pass'] )){
				if( password_verify($_POST['pass'], $pass) ){
					//lozinka je dobra
					//if (!isset($_SESSION)) session_start(); MAKNI OVO

					//zapamti ulogiranog korisnika
					$_SESSION['login'] = $_POST['oib'];

					//spremi neke info o korisniku za ljepši view
					$who = 'company';
					$this->registry->template->who = $who;
					$logedin = $spp->get_companyname_by_oib($_POST['oib']);
					$this->registry->template->logedin = $logedin;

					//odi prikupi info o svim ponudama
					$this->all_offers();
					exit();
				}
				else{
					$login_message = "Kriva lozinka!";
					$this->registry->template->message_company = $login_message;
					$this->registry->template->show( 'login' );
					exit();
				} 
			}
		}
		else {
			$login_message = "Popunite sva polja za prijavu!";
			$this->registry->template->message_company = $login_message;
			$this->registry->template->show( 'login' );
		}
	}


	//obradi register
	public function check_register(){
		$spp = new studentplus_service();
		$who = false;
		$this->registry->template->who = $who;
		$reg_type = "company";
		$this->registry->template->reg_type = $reg_type;
		$reg_message = "";

		if( isset($_POST['new_company_oib']) && isset($_POST['new_company_password']) && isset($_POST['new_company_name']) && isset($_POST['new_company_email']) && isset($_POST['new_company_adress']) && isset($_POST['new_company_phone']) && isset($_POST['new_company_description']) ){

			if( $_POST['new_company_oib'] === '' || $_POST['new_company_password'] === '' || $_POST['new_company_name'] === '' ||  $_POST['new_company_email'] === '' || $_POST['new_company_adress'] === '' || $_POST['new_company_phone'] === '' || $_POST['new_company_description'] === '' ){
				//nesto nismo unijeli
				$reg_message = "Popunite sva navedena polja!";
				$this->registry->template->reg_message_company = $reg_message;
				$this->registry->template->show( 'register' );
				exit();
			}

			//sanitizacija
			$oib = filter_var( $_POST['new_company_oib'], FILTER_SANITIZE_NUMBER_INT );
			$password_hash = password_hash($_POST['new_company_password'], PASSWORD_DEFAULT);
			$name = filter_var($_POST['new_company_name'], FILTER_SANITIZE_STRING);
			$email = filter_var($_POST['new_company_email'], FILTER_SANITIZE_EMAIL);
			$adress = filter_var( $_POST['new_company_adress'] , FILTER_SANITIZE_STRING );
			$phone = filter_var( $_POST['new_company_phone'], FILTER_SANITIZE_NUMBER_INT);
			$description = filter_var($_POST['new_company_description'], FILTER_SANITIZE_STRING );

			if( $spp->get_company_by_oib($oib) !== null ){
				$reg_message = "Tvrtka s navedenim oib-om je vec registrirana!";
				$this->registry->template->reg_message_company = $reg_message;
				$this->registry->template->show( 'register' );
				exit();
			}

			$spp->add_company($oib, $password_hash, $name, $email, $adress, $phone, $description );


			//zapamti ulogiranog korisnika
			$_SESSION['login'] = $_POST['new_company_oib'];
			$who = 'company';
			$this->registry->template->who = $who;
			$logedin = $spp->get_companyname_by_oib($_POST['new_company_oib']);
			$this->registry->template->logedin = $logedin;

			//odi prikupi info o svim ponudama
			$this->all_offers();
			exit();

		}
	}


	//provjerava na koje smo dugme stisnuli
	public function check_button_choice(){
		$spp = new studentplus_service();
		$who = 'company';
		$this->registry->template->who = $who;
		$logedin = $spp->get_companyname_by_oib($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		if(isset($_POST['logout'])){
			$this->logout();
			exit();
		}
		if(isset($_POST['button'])){
			if($_POST['button'] === 'ours'){
				//hoće prikazati naše ponude
				unset($_SESSION['offer']);

				//prikaži ponude koje je tvrtka objavila
				$this->company_offers();
				exit();
			}
			if($_POST['button'] === 'make_new'){
				//želi stvoriti novu ponudu
				unset($_SESSION['offer']);

				//prikaži formu za pravljenje nove ponude
				$this->registry->template->show( 'new_offer' );
				exit();
			}
			if( substr($_POST['button'], 0, 18 ) === 'students_in_offer_' ){
				//želi vidjeti prijavljene studente neke ponude
				$_SESSION['offer'] = substr($_POST['button'], 18 );
				//stisnuli smo na neki button da želimo vidjeti sve studente koji su se prijavili na tu ponudu
				$this->show_students();
				exit();
			}
			if( substr($_POST['button'], 0, 7) === 'accept_'){
				$spp = new studentplus_service();
				$student_id = substr($_POST['button'], 7);

				$spp->change_status( '1', $student_id, $_SESSION['offer'] );
				$this->show_students();
			}
			if( substr($_POST['button'], 0, 7) === 'reject_'){
				$spp = new studentplus_service();
				$student_id = substr($_POST['button'], 7);
 
				$spp->change_status( '-1', $student_id, $_SESSION['offer'] );
				$this->show_students();
			}
		}
		if( isset($_POST['download']) ){
			//ako želimo downlodati životopis
			$id = $_POST['download'];

            $spp = new studentplus_service();
            $file = $spp->get_file_by_id($id);

            $filepath = realpath(dirname(__FILE__) . '/..').'/uploads/' . $file['name'];

            if (file_exists($filepath)) {
                echo 'doshao';
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($filepath));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));
                readfile($filepath);

                //za kraj ponovno prikaži studentov profil
                $this->show_profil();
                exit();
            }
		}

	}


	//dohvaća sve ponude
	public function all_offers(){
		$spp = new studentplus_service();

		$offers = $spp->get_all_offers();
		$this->registry->template->offers = $offers;
		//unset($_SESSION['offer']); MAKNI OVO

		//da znamo u viewu
		$who = 'company';
		$this->registry->template->who = $who;
		$logedin = $spp->get_companyname_by_oib($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		//sad znaš koje su sve ponude i koji je user(sve potrebne info za obični dashboard)  -- odi na logdash_index.php
		$this->registry->template->title = 'Company Dashboard!';
		$this->registry->template->show( 'logdash_index_company' );
	}


	//ide kad stisnemo da želimo vidjeti sve ponude koje je ta tvrtka napravila
	public function company_offers(){
		$spp = new studentplus_service();

		//da znamo u viewu
		$who = 'company';
		$this->registry->template->who = $who;
		$logedin = $spp->get_companyname_by_oib($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$company_offers = $spp->get_offers_by_oib( $_SESSION['login'] );
		$this->registry->template->company_offers = $company_offers;
		unset($_SESSION['offer']); //da je session čist 

		//sad znaš koje su sve ponude i koji je user(sve potrebne info za obični dashboard)  -- odi na logdash_index.php
		$this->registry->template->title = 'Company Offers!';
		$this->registry->template->show( 'company_offers' );	
	}


	//ide poslije ispunjavanja forme za pravljenje nove ponude
	public function check_new_offer(){
		$spp = new studentplus_service();

		$who = 'company';
		$this->registry->template->who = $who;
		$logedin = $spp->get_companyname_by_oib($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		if( isset($_POST['new_offer_name']) && isset($_POST['new_offer_description']) && isset($_POST['new_offer_adress']) && isset($_POST['new_offer_period']) ){

			if( $_POST['new_offer_name'] === '' || $_POST['new_offer_description'] === '' || $_POST['new_offer_adress'] === '' || $_POST['new_offer_period'] === '' ){
				//nismo nista napisali o praksi
				$message_not_filled = "Niste popunili sva polja!";
				$this->registry->template->message_not_filled = $message_not_filled;
				$this->registry->template->title = 'Try again!';
				$this->registry->template->show( 'new_offer' );
				exit();
			}
			$company = $_SESSION['login'];
			$name = filter_var($_POST['new_offer_name'], FILTER_SANITIZE_STRING ); 
			$description = filter_var($_POST['new_offer_description'], FILTER_SANITIZE_STRING ); 
			$adress = filter_var($_POST['new_offer_adress'], FILTER_SANITIZE_STRING ); 
			$period = filter_var($_POST['new_offer_period'], FILTER_SANITIZE_STRING );

			$spp = new studentplus_service();
			$spp->add_offer($company, $name, $description, $adress, $period);

			$this->all_offers();
			exit();
			
		}
	}


	//svi studenti koji su se prijavili na neku ponudu
	public function show_students(){
		unset($_SESSION['student_profil']);
		$spp = new studentplus_service();

		$who = 'company';
		$this->registry->template->who = $who;
		$logedin = $spp->get_companyname_by_oib($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$spp = new studentplus_service();

		if( $spp->get_offer_by_id($_SESSION['offer']) === null ){
			echo 'Offer Id not valid.';
			$this->all_offers();
			exit();
		}

		//dohvati sve studente u ponudama koji su prihvaćeni/čekaju
		$accepted_students_in_offer = $spp->get_accepted_students_in_offer($_SESSION['offer']);
		$this->registry->template->accepted_students_in_offer = $accepted_students_in_offer;

		$pending_students_in_offer = $spp->get_pending_students_in_offer($_SESSION['offer']);
		$this->registry->template->pending_students_in_offer = $pending_students_in_offer;

		//dohvati ime ponude
		$offer = $spp->get_offer_by_id($_SESSION['offer']);
		$this->registry->template->offer = $offer;

		$this->registry->template->title = 'Offer Students!';
		$this->registry->template->show('offer_students');
	}

	public function search_results() {
		$spp = new studentplus_service();

		$who = 'company';
		$this->registry->template->who = $who;
		$logedin = $spp->get_companyname_by_oib($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$offers = $spp->get_offers_by_podstring_name($_POST['search']);

		if( count($offers) === 0 ){
			$message = 'Ne postoje ponude koje sadrže '. $_POST['search'] . ' u svom imenu.';
			$offers = $spp->get_all_offers();
		} 
		else $message = '';

		$this->registry->template->offers = $offers;
		$this->registry->template->message = $message;
		$this->registry->template->show( 'logdash_index_company' );
		exit();
	}
}; 

