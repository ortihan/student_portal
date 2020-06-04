<?php require_once __SITE_PATH . '/view/_header.php'; ?>
<div class="transparent_div">
	<h2 align="center"> Moje prijave </h2>
</div>

<div id="div_waiting">
	<h3 class="boldaj">Poslani zahtjevi za prakse: </h3> <br>
	<?php foreach( $waiting as $i=>$zahtjev ) { ?>
		<table class="table_waiting"> <?php
			echo '<caption class="boldaj">', $zahtjev->name, '</caption>'; 
			echo '<tr><td class="boldaj">Tvrtka: </td><td> ', $zahtjev->company, '</td></tr>';
			echo '<tr><td class="boldaj">Opis <br> prakse: </td><td> ', $zahtjev->description, '</td></tr>';
			echo '<tr><td class="boldaj">Adresa: </td><td> ', $zahtjev->adress, '</td></tr>';
			echo '<tr><td class="boldaj">Period <br> rada: </td><td> ', $zahtjev->period, '</td></tr>';
		?>
		</table> <?php
	} ?>
</div>

<div id="div_accepted">
	<h3 class="boldaj">Prihvaćeni zahtjevi za prakse: </h3> <br>
	<?php foreach( $accepted as $i=>$zahtjev ) { 
		?><table class="table_accepted"><?php
			echo '<caption class="boldaj">', $zahtjev->name, '</caption>'; 
			echo '<tr><td class="boldaj">Tvrtka: </td><td> ', $zahtjev->company, '</td></tr>';
			echo '<tr><td class="boldaj">Opis <br> prakse: </td><td> ', $zahtjev->description, '</td></tr>';
			echo '<tr><td class="boldaj">Adresa: </td><td> ', $zahtjev->adress, '</td></tr>';
			echo '<tr><td class="boldaj">Period <br> rada: </td><td> ', $zahtjev->period, '</td></tr>';
		?>
		</table> <?php
	} ?>
</div>

<div id="div_rejected" class="odbijeni">
	<h3 class="boldaj" >Odbijeni zahtjevi za praksu: </h3> <br>
	<?php foreach( $rejected as $i=>$zahtjev ) { 
		?><table class="table_rejected" ><?php
		echo '<caption class="boldaj">', $zahtjev->name, '</caption>'; 
		echo '<tr><td class="boldaj">Tvrtka: </td><td> ', $zahtjev->company, '</td></tr>';
		echo '<tr><td class="boldaj">Opis <br> prakse: </td><td> ', $zahtjev->description, '</td></tr>';
		echo '<tr><td class="boldaj">Adresa: </td><td>  ', $zahtjev->adress, '</td></tr>';
		echo '<tr><td class="boldaj">Period <br> rada: </td><td> ', $zahtjev->period, '</td></tr>';
		?>
		</table> <?php
	} ?>
</div>

<script type="text/javascript">
	$("document").ready(function() {
		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=student/all_offers'
			};
			console.log(loc1);
			window.location.assign(loc1+loc2.url);
		});
	} )
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>