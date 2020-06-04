<?php 

class StudentController extends BaseController{

	public function index() {}

	//obradi logout
	public function logout(){
		//kontroler za provođenje logout-a
		unset($_SESSION['login']); 
		unset($_SESSION['offer']);

		//ako treba
		unset($_POST['username']);
		unset($_POST['password']);
		unset($_POST['name']);
		unset($_POST['surname']);
		unset($_POST['email']);
		unset($_POST['phone']);
		unset($_POST['school']);
		unset($_POST['grades']);
		unset($_POST['free_time']);
		unset($_POST['cv']);

		unset($offers);
		$who = false; //ako je false nitko nije ulogiran
		$this->registry->template->who = $who;

		session_unset(); 
		session_destroy();

		$spp = new studentplus_service();
		$offers = $spp->get_all_offers();
		$this->registry->template->offers = $offers;

		header( 'Location: ' . __SITE_URL . '/index.php?rt=index/all_offers' );
		exit();
	}

	//obradi login
	public function check_login(){
		//kontroler za provođenje logina
		$spp = new studentplus_service();
		$login = "student";
		$login_message = "";

		$who = false;
		$this->registry->template->who = $who;
		$this->registry->template->login_type = $login;
		if( isset( $_POST['username']) && ( $_POST['username'] !== "") ){ //napisan je username

			//sanitizacija
			$username = filter_var($_POST['username'],FILTER_SANITIZE_STRING);

			//provjeri je li username u bazi
			if( $spp->get_id_by_username($username) === null ){
				$login_message = "Ne postoji korisnik s navedenim korisničkim imenom!";
				//vrati na login
				$this->registry->template->message_student = $login_message;
				$this->registry->template->show( 'login' );
				exit();
			}

			//dohvati lozinku tog studenta
			$pass = $spp->get_password_by_username( $username );

			if(isset( $_POST['pass'] )){
				if( password_verify($_POST['pass'], $pass) ){
					//lozinka je dobra
					
					//if (!isset($_SESSION)) session_start(); MAKNI OVO

					//zapamti ulogiranog korisnika
					$_SESSION['login'] = $username;
					$who = 'student';
					$this->registry->template->who = $who;
					$logedin = $spp->get_studentname_by_username($_SESSION['login']);
					$this->registry->template->logedin = $logedin;

					//odi prikupi info o svim ponudama
					$this->all_offers();
					exit();
				}
				else{
					$login_message = "Kriva lozinka!";
					//vrati na login
					$this->registry->template->message_student = $login_message;
					$this->registry->template->show( 'login' );
					exit();
				} 
			}
		}
		else{
			$login_message = "Popunite sva polja za prijavu!";
			$this->registry->template->message_student = $login_message;
			$this->registry->template->show( 'login' );
		}
	}

//obradi registraciju
	public function check_register(){
		$spp = new studentplus_service();
		$who = false;
		$this->registry->template->who = $who;
		$reg_type = "student";
		$this->registry->template->reg_type = $reg_type;
		$reg_message = "";
		//jesu li sva polja set
		if( isset($_POST['new_student_username']) && strlen($_POST['new_student_password']) && isset($_POST['new_student_name']) && isset($_POST['new_student_email']) && isset($_POST['new_student_surname']) && isset($_POST['new_student_phone']) && isset($_POST['new_student_school']) && isset($_POST['new_student_grades']) && isset($_POST['new_student_free_time']) ) {
			//jesu li neprazna
			if( $_POST['new_student_username'] !== '' || $_POST['new_student_password'] !== '' || $_POST['new_student_name'] !== '' ||  $_POST['new_student_email'] !== '' || $_POST['new_student_surname'] !== '' || $_POST['new_student_phone'] !== '' || $_POST['new_student_school'] !== '' || $_POST['new_student_grades'] !== '' || $_POST['new_student_free_time'] !== ''){
				//je li set file
				if( !isset($_FILES['new_student_cv']) || $_FILES['new_student_cv']['error'] !== UPLOAD_ERR_OK ){
					$reg_message = "Niste priložili svoj životopis!";
					$this->registry->template->reg_message_student= $reg_message;
					$this->registry->template->show( 'register' );
					exit();
				}
				if( $_FILES['new_student_cv']['error'] === UPLOAD_ERR_OK ){
					if( $_POST['new_student_username'] === '' || $_POST['new_student_password'] === '' || $_POST['new_student_name'] === '' ||  $_POST['new_student_email'] === '' || $_POST['new_student_surname'] === '' || $_POST['new_student_phone'] === '' || $_POST['new_student_school'] === '' || $_POST['new_student_grades'] === '' || $_POST['new_student_free_time'] === ''){
						//nesto nismo unijeli
						$reg_message = "Popunite sva navedena polja!";
						$this->registry->template->reg_message_student = $reg_message;
						$this->registry->template->show( 'register' );
						exit();
					}
					//sanitiziraj
					$username = filter_var($_POST['new_student_username'], FILTER_SANITIZE_STRING);
					$password_hash = password_hash($_POST['new_student_password'], PASSWORD_DEFAULT);
					$name = filter_var($_POST['new_student_name'], FILTER_SANITIZE_STRING); 
					$surname = filter_var($_POST['new_student_surname'], FILTER_SANITIZE_STRING); 
					$email = filter_var($_POST['new_student_email'], FILTER_SANITIZE_EMAIL);
					$phone = filter_var($_POST['new_student_phone'], FILTER_SANITIZE_STRING);
					$school = filter_var($_POST['new_student_school'], FILTER_SANITIZE_STRING);
					$grades = number_format((float)$_POST['new_student_grades'], 2, '.', '');
					$grades = filter_var($grades, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
					$free_time = filter_var($_POST['new_student_free_time'], FILTER_SANITIZE_NUMBER_INT);
				
					$cv = $spp->upload_file(); //id nam vrati
					//nije učitao file
					if( !$cv ){
						$reg_message = "Greška prilikom slanja životopisa!";
						$this->registry->template->reg_message_student = $reg_message;
						$this->registry->template->show( 'register' );
						exit();
					}
					if( $spp->get_id_by_username($username) !== null ){ //već postoji username
						$reg_message = "Navedeno korisničko ime već postoji!";
						$this->registry->template->reg_message_student = $reg_message;
						$this->registry->template->show( 'register' );
						exit();
					}
					//dodaj studenta u bazu
					$spp->add_student($username, $password_hash, $name, $surname, $email, $phone, $school, $grades, $free_time, $cv );
					//kao da je ulogiran
					//if (!isset($_SESSION)) session_start(); MAKNI OVO
					//zapamti ulogiranog korisnika
					$_SESSION['login'] = $_POST['new_student_username'];
					$who = 'student';
					$this->registry->template->who = $who;
					$logedin = $spp->get_studentname_by_username($_SESSION['login']);
					$this->registry->template->logedin = $logedin;
					//odi prikupi info o svim ponudama
					$this->all_offers();
					exit();
				}
			}
			else{
				//neko polje je ostalo prazno
				$reg_message = "Popunite sva navedena polja!";
				$this->registry->template->reg_message_student = $reg_message;			
				$this->registry->template->show( 'register' );
				exit();
			}
		}
		else{
		//neko polje je ostalo prazno
			$reg_message = "Popunite sva navedena polja!";
			$this->registry->template->reg_message_student = $reg_message;			
			$this->registry->template->show( 'register' );
			exit();
		}
}
	//studentov glavni dashboard
	public function all_offers(){
		$spp = new studentplus_service();

		$offers = $spp->get_all_offers();
		$this->registry->template->offers = $offers;
		unset($_SESSION['offer']);

		$who = 'student';
		$this->registry->template->who = $who;
		$logedin = $spp->get_studentname_by_username($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$id_student = $spp->get_id_by_username($_SESSION['login']);
		$offers_applied = array();
		foreach ($offers as $i => $ponuda) {
			$is_applied = $spp->is_student_applied($id_student, $ponuda->id);
			$offers_applied[] = $is_applied;
		}

		$this->registry->template->offers_applied = $offers_applied;

		//sad znaš koje su sve ponude i koji je user(sve potrebne info za obični dashboard)  -- odi na logdash_index.php
		$this->registry->template->title = 'Student Dashboard!';
		$this->registry->template->show( 'logdash_index_student' );
	}

	//koji button je stisnuo
	public function check_button_choice(){
		$spp = new studentplus_service();

		if(isset($_POST['logout'])){
			$this->logout();
			exit();
		}
		if(isset($_POST['button'])){
			
			if($_POST['button'] === 'applications'){
				//prikaži ponude na koje se student prijavio

				unset($_SESSION['offer']);
				$this->my_applications();
				exit();
			}
			if( $_POST['button'] === 'update_profile' ){
				$id = $spp->get_id_by_username($_SESSION['login']);
				$student = $spp->get_student_by_id($id);
				$this->registry->template->student = $student;
				$who = 'student';
				$this->registry->template->who = $who;
				$logedin = $spp->get_studentname_by_username($_SESSION['login']);
				$this->registry->template->logedin = $logedin;
				$this->registry->template->title = 'Uredi profil!';
				$this->registry->template->show( 'update_profil' );
			}
			if( substr($_POST['button'], 0, 21 ) === 'application_in_offer_' ){
				//student se želi prijaviti za neku ponudu

				$extract_offerid = substr($_POST['button'], 21);//npr application_in_offer_1 - vrati nam natrag 1
				
				//provjeri postoji li ta ponuda
				if( $spp->get_offer_by_id($extract_offerid) === null ){
					echo 'Id not valid.';
					$this->all_offers();
					exit();
				}
				$_SESSION['offer'] = $extract_offerid; //ponuda postoji, zapamti da si je odabrao

				//nije postavljen session od username-a ili username nije dobar
				$id = $spp->get_id_by_username($_SESSION['login']);
				if( !isset($_SESSION['login']) || $id === null ){
					echo 'Something is wrong!';
					header( 'Location: ' . __SITE_URL . '/index.php?rt=index/all_offers' );
					exit();
				} 

				if( $spp->get_offer_by_id($_SESSION['offer']) === null ){
					echo 'Offer does not exists!';
					$this->all_offers();
					exit();
				}

				$spp->asign_student_to_offer($id, $_SESSION['offer'] );
				$this->all_offers();
				exit();
			}
		}
	}

	//vidi studentove prijave
	public function my_applications(){
		//omogući ispisivanje svih ponuda na koje sam se prijavio
		$spp = new studentplus_service();

		$id_student = $spp->get_id_by_username($_SESSION['login']);

		//dohvati podatke o prijavama
		$waiting = $spp->get_pending_offers_by_id($id_student);
		$accepted = $spp->get_accepted_offers_by_id($id_student);
		$rejected = $spp->get_rejected_offers_by_id($id_student);	

		//omogući u viewu
		$who = 'student';
		$this->registry->template->who = $who;
		$logedin = $spp->get_studentname_by_username($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$this->registry->template->accepted = $accepted;	
		$this->registry->template->waiting = $waiting;
		$this->registry->template->rejected = $rejected;

		$this->registry->template->title = 'Student Applications!';
		$this->registry->template->show( 'applications' );
	}

	public function search_results() {
		$spp = new studentplus_service();

		$who = 'student';
		$this->registry->template->who = $who;
		$logedin = $spp->get_studentname_by_username($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$offers = $spp->get_offers_by_podstring_name($_POST['search']);
		
		if( count($offers) === 0 ){
			if( strlen($_POST['search']) === 0 ) $message = "Niste unijeli ime ponude.";
			else $message = 'Ne postoje ponude koje sadrže '. $_POST['search'] . ' u svom imenu.';
			$offers = $spp->get_all_offers();
		}
		else $message = '';

		$this->registry->template->offers = $offers;


		$id_student = $spp->get_id_by_username($_SESSION['login']);
		$offers_applied = array();
		foreach ($offers as $i => $ponuda) {
			$is_applied = $spp->is_student_applied($id_student, $ponuda->id);
			$offers_applied[] = $is_applied;
		}
		$this->registry->template->offers_applied = $offers_applied;
		
		$this->registry->template->message = $message;
		$this->registry->template->show( 'logdash_index_student' );
		exit();
	}


	public function update_profil(){
		$spp = new studentplus_service();

		$who = 'student';
		$this->registry->template->who = $who;
		$logedin = $spp->get_studentname_by_username($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		//provjeri jesmo ista dodali
		$id = $spp->get_id_by_username($_SESSION['login']);
		$student = $spp->get_student_by_id($id);
		$this->registry->template->student = $student;


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

		$test = true;
		if( isset($_POST['new_student_password']) && $_POST['new_student_password'] !== '' && !password_verify($_POST['new_student_password'], $student->password) ){

			$pass = $spp->get_password_by_username( $_SESSION['login'] );
			//$pass_hash = password_hash($student->password, PASSWORD_DEFAULT);
			if( password_verify($_POST['old_student_password'], $pass)  ){
				$password_hash = password_hash($_POST['new_student_password'], PASSWORD_DEFAULT);
				$spp->update_student_password( $id, $password_hash );
				$test = false;
			}
			else{
				//nije dobra stara lozinka
				$update_message_student = "Niste dobro napisali staru lozinku!";
				$this->registry->template->update_message_student = $update_message_student;
				$this->registry->template->show( 'update_profil' );
				exit();				
			}


		}
		if( isset($_POST['new_student_username']) && $_POST['new_student_username'] !== '' && $_POST['new_student_username'] !== $student->username ){
			$username = filter_var($_POST['new_student_username'], FILTER_SANITIZE_STRING);

			if( $spp->get_id_by_username($username) !== null ){ //već postoji username
				$update_message_student = "Navedeno korisničko ime već postoji!";
				$this->registry->template->update_message_student = $update_message_student;
				$this->registry->template->show( 'update_profil' );
				exit();
			}

			$spp->update_student_username( $id, $username );
			$test = false;
		}
		if( isset($_POST['new_student_name']) && $_POST['new_student_name'] !== '' && $_POST['new_student_name'] !== $student->name ){
			$name = filter_var($_POST['new_student_name'], FILTER_SANITIZE_STRING);
			$spp->update_student_name( $id, $name );
			$test = false;
		}
		if( isset($_POST['new_student_surname']) && $_POST['new_student_surname'] !== '' && $_POST['new_student_surname'] !== $student->surname ){
			$surname = filter_var($_POST['new_student_surname'], FILTER_SANITIZE_STRING);
			$spp->update_student_surname( $id, $surname );
			$test = false;
		}
		if( isset($_POST['new_student_email']) && $_POST['new_student_email'] !== '' && $_POST['new_student_email'] !== $student->email ){
			$email = filter_var($_POST['new_student_email'], FILTER_SANITIZE_EMAIL);
			$spp->update_student_email( $id, $email );
			$test = false;
		}
		if( isset($_POST['new_student_phone']) && $_POST['new_student_phone'] !== '' && $_POST['new_student_phone'] !== $student->phone ){
			$phone = filter_var($_POST['new_student_phone'], FILTER_SANITIZE_STRING);
			$spp->update_student_phone( $id, $phone );
			$test = false;
		}
		if( isset($_POST['new_student_school']) && $_POST['new_student_school'] !== '' && $_POST['new_student_school'] !== $student->school ){
			$school = filter_var($_POST['new_student_school'], FILTER_SANITIZE_STRING);
			$spp->update_student_school( $id, $school );
			$test = false;
		}
		if( isset($_POST['new_student_grades']) && $_POST['new_student_grades'] !== '' && $_POST['new_student_grades'] !== $student->grades ){
			$grades = number_format((float)$_POST['new_student_grades'], 2, '.', '');
			$grades = filter_var($grades, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);			
			$spp->update_student_grades( $id, $grades );
			$test = false;
		}
		if( isset($_POST['new_student_free_time']) && $_POST['new_student_free_time'] !== '' && $_POST['new_student_free_time'] !== $student->free_time ){
			$free_time = filter_var($_POST['new_student_free_time'], FILTER_SANITIZE_NUMBER_INT);
			$spp->update_student_free_time( $id, $free_time );
			$test = false;
		}
		if( $_FILES['new_student_cv']['error'] === UPLOAD_ERR_OK ){
			$cv = $spp->upload_file(); //id nam vrati

			//nije učitao file
			if( $cv === false ){
				$update_message_student = "Greška prilikom slanja životopisa!";
				$this->registry->template->update_message_student = $update_message_student;
				$this->registry->template->show( 'update_profil' );
				exit();
			}
			$spp->update_student_cv( $id, $cv );
			$test = false;
		}

		if( !$test ){ //ako smo ista izmjenili
			$update_message_student = "Uspješno ste izmjenili podatke.";
			$this->registry->template->update_message_student = $update_message_student;			
		}


		$student = $spp->get_student_by_id($id);
		$this->registry->template->student = $student;

		//update session
		$_SESSION['login'] = $student->username;
		$logedin = $spp->get_studentname_by_username($_SESSION['login']);
		$this->registry->template->logedin = $logedin;

		$this->registry->template->show( 'update_profil' );
		exit();
	}
}; 

?>
