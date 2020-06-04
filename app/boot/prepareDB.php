<?php

require_once 'db.class.php';

echo "Nakon require db class", "<br>";

$db = DB::getConnection();

echo "Nakon db get connection", "<br>";


// --------------------- STUDENTPLUS_STUDENTS ----------------------
try{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS studentplus_students (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'username varchar(50) NOT NULL UNIQUE,' .
		'password varchar(255) NOT NULL,' .
		'name varchar(50) NOT NULL,' .
		'surname varchar(50) NOT NULL,' . 
		'email varchar(255) NOT NULL,' . 
		'phone varchar(50) NOT NULL,' . 
		'school varchar(255) NOT NULL,' .
		'grades decimal(3,2) NOT NULL,' .
		'free_time int(3) NOT NULL,' .
		'cv int NOT NULL)' //zivotopis spremamo kao id, a datoteku s tim id-om u mapu uploads
	);
	$st->execute();
}
catch( PDOException $e ){ exit( "PDO error #1: " . $e->getMessage() ); }

echo "Napravio tablicu studentplus_students.<br />";



// --------------------- STUDENTPLUS_COMPANIES ----------------------
try{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS studentplus_companies(' .
		'oib int NOT NULL PRIMARY KEY,' .
		'password varchar(255) NOT NULL,' .
		'name varchar(255) NOT NULL,' .
		'email varchar(255) NOT NULL,' . 
		'adress varchar(255) NOT NULL,' .
		'phone varchar(50) NOT NULL,' . 
		'description varchar(10000) NOT NULL)' 
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #1: " . $e->getMessage() ); }

echo "Napravio tablicu studentplus_companies.<br />";



// --------------------- STUDENTPLUS_OFFERS ----------------------
try{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS studentplus_offers(' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'oib int NOT NULL,' .
		'name varchar(255) NOT NULL,' .
		'description varchar(10000) NOT NULL,' . 
		'adress varchar(255) NOT NULL,' .
		'period varchar(100) NOT NULL)' 
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #1: " . $e->getMessage() ); }

echo "Napravio tablicu studentplus_offers.<br />";



// --------------------- STUDENTPLUS_MEMBER ----------------------
try{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS studentplus_members(' .
		'id_student int NOT NULL ,' .
		'id_offer int NOT NULL ,' .
		'status int(1) NOT NULL,' .
		'PRIMARY KEY (id_student, id_offer) )' 
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #1: " . $e->getMessage() ); }

echo "Napravio tablicu studentplus_members.<br />";




// --------------------- STUDENTPLUS_FILES ----------------------
try{
	$st = $db->prepare( 
		'CREATE TABLE IF NOT EXISTS studentplus_files(' .
		'id int NOT NULL PRIMARY KEY,' .
		'name varchar(255) NOT NULL ,' .
		'size int NOT NULL)' 
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error #1: " . $e->getMessage() ); }

echo "Napravio tablicu studentplus_files.<br />";






//ubaci neke tvrtke 
try{
	$st = $db->prepare( 'INSERT INTO studentplus_companies(oib, password, name, email, adress, phone, description ) VALUES (:oib, :password, :name, :email, :adress, :phone, :description)' );


	//ericsson
	$opis_tvrtke ='Ericsson Nikola Tesla je vodeći regionalni isporučitelj komunikacijskih proizvoda i usluga u operatorskom segmentu te je isto tako isporučitelj inovativnih informacijsko-komunikacijskih rješenja vezanih uz zdravstvenu zaštitu, promet, državnu upravu, komunalne djelatnosti i multimedijsku komunikaciju.';
	$st->execute( array( 'oib' => '84214771175', 'password' => password_hash( 'ericsson123', PASSWORD_DEFAULT ), 'name' => 'Ericsson Nikola Tesla', 'email' => 'etk.company@ericsson.com', 'adress' => 'Krapinska 45, Zagreb', 'phone' => '+385 (0)1 365 35 35', 'description' => $opis_tvrtke) );


	//trikoder
	$opis_tvrtke ='U suradnji s našim klijentima oživljavamo njihovu poslovnu viziju zahvaljujući napredcima u tehnologiji. Kao tim stručnjaka za različita područja razvoja internet sustava, služimo se širokim spektrom alata i vještina potrebnih za izradu visoko kvalitetnih proizvoda koji se koriste u nizu online poslovnih okruženja, od izdavaštva do prodaje i oglasnika.';
	$st->execute( array( 'oib' => '54608519877', 'password' => password_hash( 'trikoder123', PASSWORD_DEFAULT ), 'name' => 'Trikoder', 'email' => 'info@trikoder.net', 'adress' => 'Ivana Lučića 2a, Zagreb', 'phone' => '+385 (0)1 201 62 00', 'description' => $opis_tvrtke) );



	//erste
	$opis_tvrtke ='U svom poslovanju banka je prvenstveno usmjerena na građanstvo te malo i srednje poduzetništvo, no istodobno i na tradicionalne velike partnere s kojima posluje u regijama u kojima je snažno prisutna - Rijeci, Zagrebu i Bjelovaru. Banka se posebno ističe inovativnošću i brigom za klijente, a ujedno kontinuirano radi na proširenju usluga za klijente kao i stalnom podizanju razine kvalitete. ';
	$st->execute( array( 'oib' => '23057039320', 'password' => password_hash( 'erste123', PASSWORD_DEFAULT ), 'name' => 'Erste&Steiermärkische Bank', 'email' => 'erstebank@erstebank.hr', 'adress' => 'I. Lučića 2, 10000 Zagreb, Hrvatska', 'phone' => '0800 7890', 'description' => $opis_tvrtke) );
}
catch( PDOException $e ) { exit( "PDO error #4: " . $e->getMessage() ); }

echo "Ubacio tvrtke u tablicu studentplus_companies.<br />";


//ubaci neke ponude
try{
	$st = $db->prepare( 'INSERT INTO studentplus_offers( oib, name, description, adress, period) VALUES (:oib, :name, :description, :adress, :period)' );

	$opis_ponude = 'Math_Lab program je stručne prakse koji studentima prirodoslovno-matematičkog usmjerenja omogućuje primjenu teoretskih znanja stečenih na fakultetu u jednom od najvećih bankarskih sustava u regiji. Najmlađi u obitelji Erste praksi, Math_Lab pruža jedinstvenu priliku za rast i razvoj praćen programom mentorstva i rada u profesionalnom, ali prijateljskom okruženju vrhunskih stručnjaka u trima odjelima banke.';
	$st->execute( array('oib' => '23057039320', 'name' => 'Math_Lab', 'description' => $opis_ponude, 'adress' => 'I. Lučića 2, 10000 Zagreb, Hrvatska', 'period' => '3 mjeseca') );


	$opis_ponude = ' IT Lab je program tromjesečne plaćene stručne prakse koji od 2016. godine studentima tehničkih fakulteta omogućuje stjecanje praktičnih znanja i vještina u jednom od najvećih bankarskih IT sustava u regiji, i to u segmentu „Change the Bank“, zaduženom za razvoj programskih rješenja i konkretnih projekata te nadležnom za digitalno bankarstvo u Hrvatskoj. Studentima se omogućuje daljnji profesionalni razvoj kroz mentorski rad sa stručnjacima te program edukacija i radionica (stručnih i „mekih“ vještina). Oni studenti koji se iskažu u svom radu imaju mogućnost dobivanja stipendija do kraja studija.';
	$st->execute( array('oib' => '23057039320', 'name' => 'IT Lab', 'description' => $opis_ponude, 'adress' => 'I. Lučića 2, 10000 Zagreb, Hrvatska', 'period' => '3 mjeseca') );


	$opis_ponude = 'Ljetna radionica Ericssona Nikole Tesle prilika je za rano stjecanje praktičnog iskustva studenata u istraživanju i razvoju pod vodstvom stručnjaka. Ukupno trajanje radionice je 5 tjedana, a odvijat će se u organizaciji tvrtke Ericsson Nikola Tesla u Zagrebu i Splitu. Odabrani studenti su obavezni pripremiti se za sudjelovanje prema naputcima mentora. Očekivani rezultati sudjelovanja u radionici su softver, dokumentacija i tehničko izvješće na engleskom jeziku i prezentacija na završnom danu. Rad se honorira. Odabrani radovi s radionice pozivaju se za natjecanje za godišnje nagrade tvrtke Ericsson Nikola Tesla. Najbolji radovi pripremit će se i za objavu na stručnim i znanstvenim skupovima.';
	$st->execute( array('oib' => '84214771175', 'name' => 'Summer Camp 2019', 'description' => $opis_ponude, 'adress' => 'Krapinska 45, Zagreb', 'period' => '15.7. - 26.7. i 19.8.-6.9.' ) );

}
catch( PDOException $e ) { exit( "PDO error #5: " . $e->getMessage() ); }

echo "Ubacio ponude u tablicu studentplus_offers.<br />";


?> 
