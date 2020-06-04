<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<!-- prikazuje se ako si logiran-->
<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=student/search_results">
	<div id="div_search" class="transparent_div">
		<input type="text" name="search" class="nice_input" placeholder="pretraži"/>
		<button type="submit" class="search_button"> &#187; </button><br><br>
		<?php if( isset($message) && strlen($message) ) echo $message . ' <br> '; ?>
	</div>
</form>

<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=student/check_button_choice">


	<button class="my_button button_group lijevi" type="submit" name="button" value="applications">Moje prijave </button>
	<button class="my_button button_group" type="submit" name="button" value="update_profile"> Uredi profil </button>

	<br><br><br><br>
	

	<?php 
	foreach($offers as $i=>$ponuda) { ?>
		<div class="prikaz_ponuda" id="pponude_<?php echo $i; ?>">
			<h2 align="center" id="oboji_<?php echo $i; ?>" class="naslov"> <?php echo $ponuda->name; ?> </h2>
			<h3 align="center" id="grey_<?php echo $i; ?>"> <?php echo $ponuda->company; ?> </h3>
			<p align="center" class="sakr" id="sakr_<?php echo $i; ?>" >
				<?php 
				echo '<br><span class="boldaj">Opis prakse: </span> <br>', $ponuda->description, '<br><br>';
				echo '<span class="boldaj">Adresa: </span> <br>', $ponuda->adress, '<br><br>';
				echo '<span class="boldaj">Period rada: </span> <br>', $ponuda->period;
				?>
				<br><br><br>
				<!-- Klikom na ovaj gumb se student prijavljuje za praksu -->
				<button class="my_button sredina" type="submit" id="prijava_<?php echo $i; ?>" name="button" value="application_in_offer_<?php echo $ponuda->id; ?>">Prijavi se!</button> 
			</p>
		</div>		
	<?php } ?>


</form>

<script type="text/javascript">
	var opened_offers = [];
	$(".sakr").hide();

	var js_offers_applied = <?php echo json_encode($offers_applied); ?>;

	$("document").ready(function() {
		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=student/all_offers'
			};
			console.log(loc1);
			window.location.assign(loc1+loc2.url);
		});

		

		for(let i = 0; i <= js_offers_applied.length; i++) {
			if (js_offers_applied[i]){
    			$('#prijava_' + i).hide();
    			$("#oboji_"+i).css("color","grey");
    			$("#grey_"+i).css("color","grey");
			}
			
  			$('#prijava_' + i).click( function(){
    			alert('Prijava je poslana!');
  			});
		}


		
		$(".naslov").on("click", function(){ 
			var i = $(this).attr("id").substr(6);
			var id = "#sakr_" + i;

			if( typeof(opened_offers[i]) === "undefined" || opened_offers[i] === false ){
				$(id).show();
				opened_offers[i] = true;
				//css
				var ponuda = "#pponude_"+i;
				$(ponuda).css("background-color", "white")
					   .css("border", "4px solid #40e0d0");
				var naslov = "#oboji_" + i;
				if( !js_offers_applied[i] ) $(naslov).css("font-size","30px").css("color","#40e0d0");
			}
			else{
				opened_offers[i] = false;
				$(id).hide();
				//css
				var ponuda = "#pponude_"+i;
				$(ponuda).css("background-color", "rgb(255,255,255,0.8)")
					   .css("border", "0.5px solid #40e0d0");
				var naslov = "#oboji_" + i;
				if ( !js_offers_applied[i] ) $(naslov).css("font-size","24px").css("color","black");
			}
		});

	} )
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>