<?php

class studentplus_service {

	/*
	---------------------DOHVATI PODATKE O PONUDAMA ------------------------
	*/
	//vraća polje svih ponuda (od svih tvrtki)
	function get_all_offers(){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_offers ORDER BY id' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ) ; } 

		//polje za spremanje svih ponuda
		while( $row = $st->fetch() ){
			//pronađimo ime tvrtke koja oglašava ovu ponudu
			$name = $this->get_companyname_by_oib($row['oib']);
			$offers[] = new Offer( $row['id'], $name, $row['name'], $row['description'], $row['adress'], $row['period'] );
		}
		return $offers;
	}

	//vraća ponudu s poslanim id-om
	function get_offer_by_id($id){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_offers WHERE id=:id ORDER BY id' );
			$st->execute( array('id' => $id ));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return null;
		else{
			$name = $this->get_companyname_by_oib($row['oib']);
			return new Offer( $row['id'], $name, $row['name'], $row['description'], $row['adress'], $row['period'] );
		} 
	}

	//vraća sve ponude koje u imenu sadržavaju podstring $ime 
	//kod pretraživanja
	function get_offers_by_podstring_name($ime){
		$offers = $this->get_all_offers();
		$found = array();
		if( strlen($ime) === 0 ) return $found;
		
		for( $i = 0; $i<count($offers); $i++ ){
			//pokušaj sa imenom ponude
			if( strpos( strtolower($offers[$i]->name), strtolower($ime) ) !== false ){
				$found[] = $offers[$i];
				continue;
			} 
			//pokušaj sa imenom tvrtke
			$tvrtka = $offers[$i]->company;
			if( strpos( strtolower($tvrtka), strtolower($ime) ) !== false ) $found[] = $offers[$i];

		}
		return $found;
	}

	//vraća sve ponude čije ime je JEDNAKO varijabli $name 
	//kod pretraživanja
	function get_offers_by_name($name){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_offers WHERE name=:name ORDER BY id' );
			$st->execute( array( 'name' => $name ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		//polje za spremanje svih ponuda
		$offers = array();
		while( $row = $st->fetch() ){
			//pronađimo ime tvrtke koja oglašava ovu ponudu
			$company = $this->get_companyname_by_oib($row['oib']);
			$offers[] = new Offer( $row['id'], $company, $row['name'], $row['description'], $row['adress'], $row['period'] );
		}
		return $offers;
	}
	
	//vraća sve ponude tvrtke s poslanim oib-om (tj dohvati sve ponude neke tvrtke)
	function get_offers_by_oib($oib){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_offers WHERE oib=:oib ORDER BY id' );
			$st->execute( array('oib' => $oib ));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		//polje za spremanje svih ponuda
		$offers = array();
		//pronađimo ime tvrtke koja oglašava ovu ponudu
		$name = $this->get_companyname_by_oib($oib);
		while( $row = $st->fetch() ){
			$offers[] = new Offer( $row['id'], $name, $row['name'], $row['description'], $row['adress'], $row['period'] );
		}
		return $offers;
	}

	//vraća polje svih ponuda kojima sam poslao zahtjev (bez obzira na status)
	function get_accepted_offers_by_id($id_student){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_student=:id_student ORDER BY id_student' );
			$st->execute( array( 'id_student' => $id_student ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$offers = array();
		while( $row = $st->fetch() ){
			if ($row['status'] !== '1') continue;
			$jedna_ponuda = $this->get_offer_by_id($row['id_offer']);
			if( $jedna_ponuda === null ) continue;
			$offers[] = $jedna_ponuda;
		}
		return $offers;
	}	

	//vraća polje svih ponuda kojima sam poslao zahtjev (bez obzira na status)
	function get_pending_offers_by_id($id_student){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_student=:id_student ORDER BY id_student' );
			$st->execute( array( 'id_student' => $id_student ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$offers = array();
		while( $row = $st->fetch() ){
			if ($row['status'] !== '0') continue;
			$jedna_ponuda = $this->get_offer_by_id($row['id_offer']);
			if( $jedna_ponuda === null ) continue;
			$offers[] = $jedna_ponuda;
		}
		return $offers;
	}

	//vraća polje svih ponuda kojima sam poslao zahtjev (bez obzira na status)
	function get_rejected_offers_by_id($id_student){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_student=:id_student ORDER BY id_student' );
			$st->execute( array( 'id_student' => $id_student ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$offers = array();
		while( $row = $st->fetch() ){
			if ($row['status'] !== '-1') continue;
			$jedna_ponuda = $this->get_offer_by_id($row['id_offer']);
			if( $jedna_ponuda === null ) continue;
			$offers[] = $jedna_ponuda;
		}
		return $offers;
	}



	/*
	---------------------DOHVATI PODATKE O TVRTKI------------------------
	*/
	//vraća tvrtku (klasa) s određenim oib-om
	function get_company_by_oib($oib){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_companies WHERE oib=:oib ORDER BY oib' );
			$st->execute( array( 'oib' => $oib ) );
		}
		catch( PDOException $e ) { 
			exit( 'PDO error ' . $e->getMessage() ); 
		}

		$row = $st->fetch();
		if( $row === false ) return null;
		else{
			return new Company( $row['oib'], $row['password'], $row['name'], $row['email'], $row['adress'], $row['phone'], $row['description'] );
		} 
	}

	//vraća ime tvrtke s poslanim oib-om
	function get_companyname_by_oib($oib){
		//probaj naći oib među tvrtkama
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT name FROM studentplus_companies WHERE oib=:oib' );
			$st->execute( array( 'oib' => $oib ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return null;
		else return $row['name'];
	}

	//vraća ime tvrtke koja je postavila određenu ponudu
	function get_companyname_by_offerid($id){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT oib FROM studentplus_offers WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return null;
		else return $this->get_companyname_by_oib($row['oib']);
	}

	//vraća lozinku od neke tvrtke
	function get_password_by_oib($oib){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT password FROM studentplus_companies WHERE oib=:oib' );
			$st->execute( array( 'oib' => $oib ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return null;
		else return $row['password'];
	}



	/*
	-------------------DOHVATI PODATKE O STUDENTIMA--------------------
	*/
	//vraća studenta(klasa) s određenim id-om
	function get_student_by_id($id){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id ORDER BY id' );
			$st->execute( array('id' => $id ) );
		}
		catch( PDOException $e ) { 
			exit( 'PDO error ' . $e->getMessage() ); 
		}

		$row = $st->fetch();
		if( $row === false ) return null;
		else{
			return new Student( $row['id'], $row['username'], $row['password'], $row['name'], $row['surname'], $row['email'], $row['phone'], $row['school'], $row['grades'], $row['free_time'], $row['cv'] );
		} 
	}


	//vrati studentovo ime ako znamo username
	function get_studentname_by_username($username){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE username=:username ORDER BY username' );
			$st->execute( array('username' => $username ) );
		}
		catch( PDOException $e ) { 
			exit( 'PDO error ' . $e->getMessage() ); 
		}

		$row = $st->fetch();
		if( $row === false ) return null;
		else{
			$vrati = $row['name'] .' '.$row['surname'];
			return $vrati;
		} 
	}


	// vraća id studenta s određenim username-om
	function get_id_by_username($username){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id FROM studentplus_students WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return null;
		else return $row['id'];
	}

	//vraća lozinku nekog studenta
	function get_password_by_username($username){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT password FROM studentplus_students WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return null;
		else return $row['password'];
	}



	/*
	-------------------DOHVATI PODATKE O MEMBERIMA--------------------
	*/
	//vraća polje svih članova (-1/0/1) neke ponude čiji id šaljemo
	function get_students_in_offer_by_id($id_offer){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_offer=:id_offer ORDER BY id_offer' );
			$st->execute( array( 'id_offer' => $id_offer ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		//polje za spremanje svih ponuda
		$members = array();

		while( $row = $st->fetch() ){
			//pronađimo studenta s danim id-om
			$student = $this->get_student_by_id($row['id_student']);
			if( $student === null ) continue;
			$members[] = $student;
		}
		return $members;
	}

	//vracamo je li student prijavljen za neku praksu
	function is_student_applied($id_student, $id_offer){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_offer=:id_offer AND id_student=:id_student' );

			$st->execute( array( 'id_offer' => $id_offer, 'id_student' => $id_student ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return false;
		else return true;
		
		
	}
	//vraća polje svih  članova koji su podnijeli zahtjev (za koje još nismo odlučili što ćemo s njima)
	function get_pending_students_in_offer($id_offer){
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_offer=:id_offer ORDER BY id_offer' );
			$st->execute( array( 'id_offer' => $id_offer ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		//polje za spremanje svih ponuda
		$pending = array();

		while( $row = $st->fetch() ){
			//ako nije pending preskoči
			if( $row['status'] !== '0' ) continue;

			//pronađimo studenta s danim id-om
			$student = $this->get_student_by_id($row['id_student']);
			if( $student === null ) continue;
			$pending[] = $student;
		}
		return $pending;
	}

	//vraća polje svih  članova koji su podnijeli zahtjev (i koje je tvrtka prihvatila)
	function get_accepted_students_in_offer($id_offer){
		//polje za spremanje svih ponuda
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_offer=:id_offer ORDER BY id_offer' );
			$st->execute( array( 'id_offer' => $id_offer ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
		$accepted = array();

		while( $row = $st->fetch() ){
			//ako nije accepted preskoči
			if( $row['status'] !== '1' ) continue;

			//pronađimo studenta s danim id-om
			$student = $this->get_student_by_id($row['id_student']);
			if( $student === null ) continue;
			$accepted[] = $student;
		}
		return $accepted;
	}
	
	//vraća polje svih  članova koji su podnijeli zahtjev (i koje je tvrtka odbila)
	function get_rejected_students_in_offer($id_offer){
		//polje za spremanje svih ponuda
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_offer=:id_offer ORDER BY id_offer' );
			$st->execute( array( 'id_offer' => $id_offer ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		$rejected = array();

		while( $row = $st->fetch() ){
			//ako nije rejected preskoči
			if( $row['status'] !== '-1' ) continue;

			//pronađimo studenta s danim id-om
			$student = $this->get_student_by_id($row['id_student']);
			if( $student === null ) continue;
			$rejected[] = $student;
		}
		return $rejected;
	}

	/*
	------------------- UPDATE AND INSERT IN DB --------------------
	*/
	//obradi prijavu studenta za neku ponudu
	function asign_student_to_offer($id_student, $id_offer){
		//dodajmo studenta u member
		$tvrtka = $this->get_companyname_by_offerid($id_offer);
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO studentplus_members(id_student, id_offer, status) VALUES (:id_student, :id_offer, :status)' );
			$st->execute( array( 'id_student' => $id_student, 'id_offer' => $id_offer, 'status' => '0' ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); 
		}

	}

	//obradi prihvaćanje/odbijanje studenta
	function change_status( $status, $id_student, $id_offer ){
		//moramo actually mjenjati nešto
		if( $status !== '-1' && $status !== '1' ) throw new Exception( 'change_status :: Status value is not valid.' );

		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_members WHERE id_student=:id_student AND id_offer=:id_offer' );
			$st->execute( array( 'id_student' => $id_student,  'id_offer' => $id_offer ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Member with the given id in the given Offer does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_members SET status=:status WHERE id_student=:id_student AND id_offer=:id_offer' );
			$st->execute( array( 'status' => $status, 'id_student' => $id_student,  'id_offer' => $id_offer ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	//dodaj novu ponudu u bazu
	function add_offer($company, $name, $description, $adress, $period){
		//dodajmo ponudu u studentplus_offers
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO studentplus_offers(oib, name, description, adress, period) VALUES (:oib, :name, :description, :adress, :period)' );
			$st->execute( array( 'oib' => $company, 'name' => $name, 'description' => $description, 'adress' => $adress, 'period' => $period ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	//registracija tvrtke
	function add_company($oib, $password_hash, $name, $email, $adress, $phone, $description ){
		//registracija tvrtke
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO studentplus_companies(oib, password, name, email, adress, phone, description ) VALUES (:oib, :password, :name, :email, :adress, :phone, :description)' );
			$st->execute( array( 'oib' => $oib, 'password' => $password_hash, 'name' => $name, 'email' => $email, 'adress' => $adress, 'phone' => $phone, 'description' => $description) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	//registracija studenta
	function add_student($username, $password_hash, $name, $surname, $email, $phone, $school, $grades, $free_time, $cv ){
		if( $cv === false ) throw new Exception( 'add_student :: File was not uploaded properly.' );

		//registriramo usera
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO studentplus_students(username, password, name, surname, email, phone, school, grades, free_time, cv) VALUES (:username, :password, :name, :surname, :email, :phone, :school, :grades, :free_time, :cv)' );
			$st->execute( array('username' => $username, 'password' => $password_hash, 'name' => $name, 'surname' => $surname, 'email' => $email, 'phone' => $phone, 'school' => $school, 'grades' => $grades, 'free_time' => $free_time, 'cv' => $cv ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}



	/*
	------------------- FILES --------------------
	*/
	//dodaj file u bazu
	function upload_file(){
		$filename = $_FILES['new_student_cv']['name'];
		$destination = realpath(dirname(__FILE__) . '/..').'/uploads/' . $filename;
		$extension = pathinfo( $filename, PATHINFO_EXTENSION );

		$file = $_FILES['new_student_cv']['tmp_name'];
		$size = $_FILES['new_student_cv']['size'];

		//kreiramo novi jedinstveni id
		$id = 1;
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id FROM studentplus_files' );
			$st->execute( );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		while( $row = $st->fetch() ) $id++;	
			

		//upload
	    if(!in_array($extension, ['zip', 'pdf', 'docx', 'txt'])) echo "Your file extension must be .zip,.txt, .pdf or .docx";
	    elseif ($_FILES['new_student_cv']['size'] > 1000000) echo "File too large!"; //ne više od 1mb
	    else{
	        // move the uploaded (temporary) file to the specified destination
	        if (move_uploaded_file($file, $destination) ) {
	            try{
					$db = DB::getConnection();
					$st = $db->prepare( 'INSERT INTO studentplus_files (id, name, size) VALUES (:id, :name, :size)' );
					$st->execute( array( 'id' => $id, 'name' => $filename, 'size' => $size) );

					//echo "File uploaded successfully";
	            	return $id;
				}
				catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	        } 
	        else {
	        	echo "Failed to upload file.";
	        	echo "filename " . $filename;
	        	echo " extension" . $extension;
	        	echo $_FILES['new_student_cv']['error'];
	        }
	        return false;
	    }
	}

	//dohvati file po njegovom id-u (id je spremljen u studenta)
	function get_file_by_id($id){
	    try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_files WHERE id=:id' );
			$st->execute( array( 'id' => $id) );

			$row = $st->fetch();
			if( $row === false ) return null;
			else return $row;
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}


	// ---------------------  UPDATE PROFIL  -------------------------
	function update_student_password($id, $pass){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		//pass je već hashiran
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET password=:password WHERE id=:id' );
			$st->execute( array( 'password' => $pass, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_username($id, $username){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET username=:username WHERE id=:id' );
			$st->execute( array( 'username' => $username, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_name($id, $name){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute(array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET name=:name WHERE id=:id' );
			$st->execute( array( 'name' => $name, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_surname($id, $surname){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET surname=:surname WHERE id=:id' );
			$st->execute( array( 'surname' => $surname, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_email($id, $email){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET email=:email WHERE id=:id' );
			$st->execute( array( 'email' => $email, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_phone($id, $phone){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET phone=:phone WHERE id=:id' );
			$st->execute( array( 'phone' => $phone, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_school($id, $school){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET school=:school WHERE id=:id' );
			$st->execute( array( 'school' => $school, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_grades($id, $grades){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET grades=:grades WHERE id=:id' );
			$st->execute( array( 'grades' => $grades, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_free_time($id, $free_time){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET free_time=:free_time WHERE id=:id' );
			$st->execute( array( 'free_time' => $free_time, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}

	function update_student_cv($id, $cv){
		//provjeri postoji li takav member
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT * FROM studentplus_students WHERE id=:id');
			$st->execute( array( 'id' => $id)  );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		if( $st->rowCount() !== 1 ) throw new Exception( 'change_status :: Student with the given id  does not exist.' );

		//update db
		try{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE studentplus_students SET cv=:cv WHERE id=:id' );
			$st->execute( array( 'cv' => $cv, 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }		
	}


};

?>