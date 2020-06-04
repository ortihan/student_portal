<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<div class="transparent_div">
	<h2 class="boldaj" align="center"> 
		Nova ponuda
	</h2>
	<p align="center"><?php if( isset($message_not_filled) && strlen($message_not_filled) ) echo$message_not_filled; ?> </p>
</div>

<div>
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_new_offer">
		<table>

			<tr>
				<td class="boldaj"> Unesi ime prakse:</td>
				<td> <input class="nice_input_reg" type="text" name="new_offer_name" /> </td>
			</tr>
			<tr>
				<td class="boldaj"> <br> Dodaj opis prakse: </td>
				<td> <br><textarea class="textarea" name="new_offer_description" rows="2" cols="18"></textarea> </td>
			</tr>			
			<tr>
				<td class="boldaj">  Adresa: </td>
				<td> <input class="nice_input_reg" type="text" name="new_offer_adress" /> </td>
			</tr>
			<tr>
				<td class="boldaj"> Vremenski period rada: </td>
				<td> <input class="nice_input_reg" type="text" name="new_offer_period" /> </td>
			</tr>

		</table>
		<button class="my_button right_button" type="submit" name="stvori">Stvori novu ponudu</button>

	</form>
	<br><br>
</div>

<script type="text/javascript">
	$("document").ready(function() {
		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=company/all_offers'
			};
			console.log(loc1);
			window.location.assign(loc1+loc2.url);
		});
	} )
</script>


<?php require_once __SITE_PATH . '/view/_footer.php'; ?>