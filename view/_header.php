<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<title>Student++</title>
	<link rel="stylesheet" href="<?php echo __SITE_URL;?>/css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	 <link href="https://fonts.googleapis.com/css?family=Bungee+Shade|Just+Another+Hand|Montserrat|Rajdhani&display=swap" rel="stylesheet">    
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
	<p id="gore_skroz">
		<?php
		if( $who !== false ){
			?> <span id="ispisi_ime"><?php echo $logedin; ?> </span>
			<?php
		}
		?>
	</p>
	<div id="header">
		<h1 id="naslov">Student++</h1> <br>
		<?php 
		//ako nitko nije logiran, ponudi login i registraciju
		if($who === false) { 
		?>	
			<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=index/check_button_choice" >
				<button id="login" type="submit" name="login" class="my_button button_group lijevi" >Prijava</button>
				<button id="register" type="submit" name="register" class="my_button button_group" >Registracija</button>
			</form> 
			<br><br>

		<?php }

		//ako je netko logiran, ponudi logout
		if( $who !== false ){ 
			if( $who === 'student' ){
			?>
				<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=student/logout">
					<button id="logout" type="submit" name="logout" class="my_button middle_button">Odjava</button>
				</form>	
			<?php			
			}
			else if( $who === 'company' ){
			?>
				<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/logout">
					<button id="logout" type="submit" name="logout" class="my_button middle_button">Odjava</button>
				</form>
				<br><br>
			<?php
			}
			?>
		<?php 
		} ?>

	</div>

<!-- 	<script type="text/javascript">
		$("document").ready(function() {
			var v = window.innerWidth;
			v *= 0.75;
			v /= 2;
			$("#logout").css("margin-left", v-80);
			v -= 190;
			$("#login").css("margin-left", v);
			

			$(window).resize(function() {
				var v = window.innerWidth;
				v *= 0.75;
				v /= 2;
				$("#logout").css("margin-left", v-80);
				v -= 190;
				$("#login").css("margin-left", v);
			});
			
		} )
	</script>
 -->