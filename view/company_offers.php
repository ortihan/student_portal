<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_button_choice">

	<div class="transparent_div">
		<h1 class="boldaj" align="center"> Moje prakse </h1>
		<button class="unvisible_button" type="submit" name="button" value="make_new">Ponudi novu praksu</button>
	</div>

	<?php foreach($company_offers as $i=>$ponuda) { ?>
		<div class="prikaz_ponuda" id="pponuda_<?php echo $i; ?>">
			<h2 align="center" id="oboji_<?php echo $i; ?>" class="naslov"> <?php echo $ponuda->name; ?> </h2>
			<button class="my_button middle_button" type="submit" name="button" value="students_in_offer_<?php echo $ponuda->id ?>"> Prijavljeni studenti  </button> 
			<p align="center" class="hide" id="hide_<?php echo $i; ?>" >
				<?php 
				echo '<br><span class="boldaj">Opis prakse: </span> <br>', $ponuda->description, '<br><br>';
				echo '<span class="boldaj">Adresa: </span> <br>', $ponuda->adress, '<br><br>';
				echo '<span class="boldaj">Period rada: </span> <br>', $ponuda->period;
				?>
			</p>		
		</div>		
	<?php } ?>

</form>

<script type="text/javascript">
	$("document").ready(function() {
		$(".hide").hide();
		var opened_offer = [];

		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=company/all_offers'
			};
			console.log(loc1);
			window.location.assign(loc1+loc2.url);
		});
		$(".naslov").on("click", function(){
			var i = $(this).attr("id");
			i = i.substr(6); //izvuci indeks
			if( typeof(opened_offer[i])  === "undefined" || opened_offer[i] === false ){
				var make_id = "#hide_" + i;
				$(make_id).show();
				opened_offer[i] = true;

				//css
				var ponuda = "#pponude_"+i;
				$(ponuda).css("background-color", "white")
					   .css("border", "4px solid #40e0d0");
				var naslov = "#oboji_" + i;
				$(naslov).css("font-size","30px").css("color","#40e0d0")
			}
			else{
				var make_id = "#hide_" + i;
				$(make_id).hide();
				opened_offer[i] = false;

				//css
				var ponuda = "#pponude_"+i;
				$(ponuda).css("background-color", "rgb(255,255,255,0.8)")
					   .css("border", "0.5px solid #40e0d0");
				var naslov = "#oboji_" + i;
				$(naslov).css("font-size","24px").css("color","black")
			}
			
		});
	} )
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>