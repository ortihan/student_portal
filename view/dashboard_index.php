<?php require_once __SITE_PATH . '/view/_header.php'; ?>


<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=index/search_results">
	<div id="div_search" class="transparent_div">
		<input type="text" name="search" class="nice_input" placeholder="pretraži"/>
		<button type="submit" class="search_button"> &#187; </button><br><br>
		<?php if( isset($message) && strlen($message) ) echo $message . ' <br> '; ?>
	</div>
</form>

<!--<h>Prikaz ponuda nelogiranog korisnika</h><br>-->
<?php
foreach($offers as $i=>$ponuda) { ?>
	<div class="prikaz_ponuda" id="pponude_<?php echo $i; ?>">
		<h2 align="center" id="oboji_<?php echo $i; ?>" class="naslov"> <?php echo $ponuda->name; ?> </h2>
		<p align="center" id="tvrtka_<?php echo $i; ?>" > <?php echo $ponuda->company; ?> </p>
		<p align="center" class="hide" id="hide_<?php echo $i; ?>" >
			<?php 
			echo '<br><span class="boldaj">Opis prakse: </span> <br>', $ponuda->description, '<br><br>';
			echo '<span class="boldaj">Adresa: </span> <br>', $ponuda->adress, '<br><br>';
			echo '<span class="boldaj">Period rada: </span> <br>', $ponuda->period;
			?>
			<br><br><br>
			<hr class="hide" id="hr_<?php echo $i; ?>">
		</p>
	</div>	
<?php } ?>

<script type="text/javascript">
	$(".hide").hide();
	var opened_offer = [];

	$("document").ready(function() {
		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=index/all_offers'
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