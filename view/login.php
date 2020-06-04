<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=index/check_login_type">

	<div class="transparent_div">
		<h3 class="boldaj">Prijavi se kao student ili tvrtka?</h3>

		<input type="radio" name="odabir" value="student" id="student">Student</input>
		<input type="radio" name="odabir" value="company" id="company">Tvrtka</input>
	</div>

</form>

<div id="log_student">
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=student/check_login">
		<table>
			<!--poruka u slucaju loseg log in - -->
			<caption> <?php if( isset($message_student) && strlen($message_student) ) echo $message_student; ?> <caption> 
			<tr> 
				<td class="boldaj"> Korisničko ime:</td>
				<td> <input class="nice_input_reg" type="text" name="username" /> </td>
			</tr>
			<tr> 
				<td class="boldaj"> <br> Lozinka: </td>
				<td> <br> <input class="nice_input_reg" type="Password" name="pass" /></td>
			</tr>

		</table>

		<button class="my_button right_button" type="submit" name="posalji">Prijavi se</button> <br><br>
	</form>
</div>

<div id="log_company">
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=company/check_login">
		<table>
		<caption> <?php if( isset($message_company) && strlen($message_company) ) echo $message_company; ?> </caption> 
			<tr>
				<td class="boldaj"> OIB:  </td>
				<td> <input class="nice_input_reg" type="text" name="oib" /> </td>
			</tr>
			<tr>
				<td class="boldaj"> Lozinka:</td>
				<td> <input class="nice_input_reg" type="Password" name="pass" /> </td>
			</tr>

		</table>
		
		<button class="my_button right_button"  type="submit" name="posalji">Prijavi se</button> <br><br>
	</form>
</div>


<script type="text/javascript">
//Kada odaberemo jesmo li student ili tvrtka, pokazuje se forma za login za odgovarajuc odabir
$("document").ready(function() {

	$("#log_student").hide();
	$("#log_company").hide();
	$("#login").hide();
	$("#register").hide();

	var log_type = "<?php echo $login_type; ?>";
	console.log ( log_type );
	//u slucaju krivog logina, javlja poruku s greskom, ali izbrana forma ostaje otvorena
	if ( log_type === "student" ){
		$("#log_company").hide();
		$("#log_student").show();
	}
	if ( log_type === "company" ){
		$("#log_company").show();
		$("#log_student").hide();
	}

	$('input:radio[name="odabir"]').change( function() {
		if( document.getElementById("student").checked ) {
			$("#log_company").hide();
			$("#log_student").show();
		}
		else if( document.getElementById("company").checked ) {
			$("#log_student").hide();
			$("#log_company").show();
		}
	});
	$('#header').on( "click", function() {
		var loc1 = window.location.pathname;
		var loc2 = {
			url : '?rt=index/all_offers'
		};
		console.log(loc1);
		window.location.assign(loc1+loc2.url);
	});
} )
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>




