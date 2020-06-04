<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_button_choice">
	<div class="transparent_div" > 
		<h1 class="boldaj" align="center"><?php echo $offer->name; ?> </h1> 
		<button class="unvisible_button" type="submit" name="button" value="ours">Moje prakse </button>
		<br>
		<button class="unvisible_button" type="submit" name="button" value="make_new">Ponudi novu praksu</button>
	</div>
</form>


<!-- prvo ispisujemo studente cije su prijave jos otvorene -->
<?php if( count($pending_students_in_offer) !== 0 ){
	?>
	<div id="div_pending">
		<br>
		<h3 class="boldaj"> Prijavljeni studenti:</h3>
		<table id="table_studenti_pending">
		<!-- za svakog studenta prikazujem ime, prezime i ostatak profila koji se moze po zelji sakriti-->
		<?php foreach($pending_students_in_offer as $i=>$student) { ?>
		
			<tr>
				<td class="ime_pending studenti" id="<?php echo $i ?>" />
				<?php echo $student->name, " ", $student->surname, "<br>"; ?>
				</td>
				<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_button_choice">
					<td><button class="my_button prihvati_odbaci" type="submit" name="button" value="accept_<?php echo $student->id ?>">Prihvati </button></td>          
					<td><button class="my_button prihvati_odbaci" type="submit" name="button" value="reject_<?php echo $student->id ?>">Odbij </button></td>
				</form>
				
			</tr>
			
			<!-- dodatni podaci- profil studenta, po defaultu hidden -->
			<?php 
				echo '<tr class="tr_hide_p"  id="s1' . $i . '" hidden><td class="sakriveno"> Fakultet: </td><td>', $student->school, '</td></tr>';
				echo '<tr class="tr_hide_p"  id="p1' . $i . '" hidden><td class="sakriveno"> Broj mobitela:</td><td>', $student->phone, '</td></tr>';
				echo '<tr class="tr_hide_p"  id="e1' . $i . '" hidden><td class="sakriveno"> E-mail: </td><td>', $student->email, '</td></tr>';
				echo '<tr class="tr_hide_p"  id="g1' . $i . '" hidden><td class="sakriveno"> Ocjene: </td><td>', $student->grades, '</td></tr>';
				echo '<tr class="tr_hide_p"  id="f1' . $i . '" hidden><td class="sakriveno"> Slobodno vrijeme: </td><td>', $student->free_time, '</td></tr>';
			?>

			<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_button_choice">
				<tr><td><button class="cv_pending" id="<?php echo "cvp" . $i; ?>" 
					type="submit" name="download" value="<?php echo $student->cv;?>" hidden> <i class="fa fa-cloud-download"></i>  Skini životopis </button></td></tr>
			</form>
							
		<?php } ?>
		</table>
	</div> 
<?php } ?>




<!-- studenti cije su prijave odobrene -->
<?php if( count($accepted_students_in_offer) !== 0  ){?>
	<div id="div_accepted">
		<br>
		<h3 class="boldaj"> Primljeni studenti:</h3>
		<table id="table_studenti_accepted">
			<?php 

			foreach($accepted_students_in_offer as $i=>$student) { ?>

				<tr><td class="ime_accepted studenti" id="<?php echo $i; ?>" />
					<?php echo $student->name, " ", $student->surname, "<br>"; ?>
				</td></tr>
						
				<?php			
					echo '<tr class="tr_hide_a"  id="s2' . $i . '" hidden><td class="sakriveno"> Fakultet:</td><td>', $student->school, '</td></tr>';
					echo '<tr class="tr_hide_a"  id="p2' . $i . '" hidden><td class="sakriveno"> Broj mobitela:</td><td>', $student->phone, '</td></tr>';
					echo '<tr class="tr_hide_a"  id="e2' . $i . '" hidden><td class="sakriveno"> E-mail: </td><td>', $student->email, '</td></tr>';
					echo '<tr class="tr_hide_a"  id="g2' . $i . '" hidden><td class="sakriveno"> Ocjene: </td><td>', $student->grades, '</td></tr>';
					echo '<tr class="tr_hide_a"  id="f2' . $i . '" hidden><td class="sakriveno"> Slobodno vrijeme: </td><td>', $student->free_time, '</td></tr>';
				?> 	
				<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_button_choice">
					<tr><td><button class="cv_accepted" id="<?php echo "cva" . $i; ?>" 
						type="submit" name="download" value="<?php echo $student->cv;?>" hidden> Skini životopis </button></td></tr>
				</form>
										
			<?php } ?>

		</table>
	</div>	
<?php } ?>


<script type="text/javascript">
	var otvoren_profil_p = [];
	var otvoren_profil_a = [];

	$("document").ready(function() {
		
		//povratak na naslovnicu, klikom na header
		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=company/all_offers'
			};
			window.location.assign(loc1+loc2.url);
		});

		//pokdazi/sakrij profil studenta
		var ime_accepted= $( ".ime_accepted" );
		var ime_pending= $( ".ime_pending" );

		var cv_accepted = $( ".cv_accepted" );
		var cv_pending = $( ".cv_pending" );

		var tra = $( ".tr_hide_a" );
		var trp = $( ".tr_hide_p" );

		ime_pending.css("cursor","pointer");
		ime_accepted.css("cursor","pointer");
		
		//klikom na ime studenta prikaze se njegov profil
		ime_pending.on( "click", function(){

			var i = $( this ).attr( "id" );

			if( typeof(otvoren_profil_p[i]) === 'undefined' || otvoren_profil_p[i] === false ){
				for ( var j = 0; j < trp.length; j++ ){
					if ( trp.eq(j).attr( "id" ).substr( 2 ) === i ){
						trp.eq(j).show();
					}
				}
				cv_pending.eq(i).show();
				cv_pending.eq(i).css( "background-color", "#40e0d0")
						   		 .css("border","none")
						 	 	 .css("color","white")
						  	 	 .css("padding","15px 32px")
							     .css("text-align","center")
							     .css("display","inline-block")
							     .css("font-size","16px")
							     .css("cursor","pointer")
							     .css("float","center")
							     .css("margin-left","30px");
				otvoren_profil_p[i] = true;
			} 
			else { //otvoren je profil
				otvoren_profil_p[i] = false;

				for ( var j = 0; j < trp.length; j++ ){
					if ( trp.eq(j).attr( "id" ).substr( 2 ) === String(i) ){
						trp.eq(j).hide();
					}
				}

				cv_pending.eq( i ).hide();
			}

			cv_pending.on( "mouseover", function(){
				$( this ).css("background-color","#1fc7b9");
			});
			cv_pending.on( "mouseleave", function(){
				$( this ).css("background-color","#40e0d0");
			});
			cv_pending.on( "mousedown", function(){
				$( this ).css("background-color","#1fc7b9")
						 .css("transform","translateY(2px)")
						 .css("box-shadow","0 5px #15847b");
			});
			cv_pending.on( "mouseup", function(){
				$( this ).css("background-color","#40e0d0")
						 .css("transform","none")
						 .css("box-shadow","none");
			});

		});
		ime_accepted.on( "click", function(){

			var i = $( this ).attr( "id" );
			if( typeof(otvoren_profil_a[i]) === 'undefined' || otvoren_profil_a[i] === false ) {
				otvoren_profil_a[i] = true;
				for ( var j = 0; j < tra.length; j++ ){
					if ( tra.eq(j).attr( "id" ).substr( 2 ) === i ){
						tra.eq(j).show();
					}
				}
				cv_accepted.eq(i).show();
				cv_accepted.eq(i).css( "background-color", "#40e0d0")
						   		 .css("border","none")
						 	 	 .css("color","white")
						  	 	 .css("padding","15px 32px")
							     .css("text-align","center")
							     .css("display","inline-block")
							     .css("font-size","16px")
							     .css("cursor","pointer")
							     .css("float","center")
							     .css("margin-left","30px");
			}
			else{
				otvoren_profil_a[i] = false;

				for ( var j = 0; j < tra.length; j++ ){
					if ( tra.eq(j).attr( "id" ).substr( 2 ) === String(i) ){
						tra.eq(j).hide();
					}
				}

				cv_accepted.eq( i ).hide();
			}

		});

		cv_accepted.on( "mouseover", function(){
			$( this ).css("background-color","#1fc7b9");
		});
		cv_accepted.on( "mouseleave", function(){
			$( this ).css("background-color","#40e0d0");
		});
		cv_accepted.on( "mousedown", function(){
			$( this ).css("background-color","#1fc7b9")
					 .css("transform","translateY(2px)")
					 .css("box-shadow","0 5px #15847b");
		});
		cv_accepted.on( "mouseup", function(){
			$( this ).css("background-color","#40e0d0")
					 .css("transform","none")
					 .css("box-shadow","none");
		});

	} )
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>