<?php require_once __SITE_PATH . '/view/_header.php'; ?>
<div class="transparent_div">
	<h2 align="center"> Uredi profil</h2>
	<p align="center"> <?php if( isset($update_message_student) && strlen($update_message_student) ) echo $update_message_student; ?> </p>
</div>



<!-- 	ovdje ćemo ispisati formu koja izgleda kao registracija studenta -->
<div id="reg_student">
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=student/update_profil" enctype="multipart/form-data" >
		<h3 class="update" id="username_update"> Korisničko ime </h3>
		<p> <?php echo $student->username; ?> </p>
		<p class="hide" id="hide_username"> 
			<input type="text" name="new_student_username" class="nice_input_reg" font-size="100%"/> 
		</p> 

		<h3 class="update" id="pass_update"> Lozinka </h3>
		<p class="hide" id="hide_pass"> 
			Stara lozinka:<input class="nice_input_reg" type="password" name="old_student_password" /> <br>
			Nova lozinka:<input class="nice_input_reg" type="password" name="new_student_password" />
		</p> 

		<h3 class="update" id="name_update"> Ime </h3>
		<p> <?php echo $student->name; ?> </p>
		<p class="hide" id="hide_name"> 
			<input type="text" name="new_student_name" class="nice_input_reg" font-size="100%"/> 
		</p> 

		<h3 class="update" id="surname_update"> Prezime </h3>
		<p> <?php echo $student->surname; ?> </p>
		<p class="hide" id="hide_surname"> 
			<input type="text" name="new_student_surname" class="nice_input_reg" font-size="100%"/> 
		</p> 

		<h3 class="update" id="email_update"> E-mail </h3>
		<p> <?php echo $student->email; ?> </p>
		<p class="hide" id="hide_email"> 
			<input type="text" name="new_student_email" class="nice_input_reg" font-size="100%"/> 
		</p> 

		<h3 class="update" id="phone_update"> Broj mobitela </h3>
		<p> <?php echo $student->phone; ?> </p>
		<p class="hide" id="hide_phone"> 
			<input type="text" name="new_student_phone" class="nice_input_reg" font-size="100%"/> 
		</p> 

		<h3 class="update" id="school_update"> Fakultet </h3>
		<p> <?php echo $student->school; ?> </p>
		<p class="hide" id="hide_school"> 
			<input type="text" name="new_student_school" class="nice_input_reg" font-size="100%"/> 
		</p> 

		<h3 class="update" id="grades_update"> Prosjek ocjena </h3>
		<p> <?php echo $student->grades; ?> </p>
		<p class="hide" id="hide_grades"> 
			<input type="text" name="new_student_grades" class="nice_input_reg" font-size="100%"/> 
		</p>
		
		<h3 class="update" id="free_time_update"> Slobodno vrijeme </h3>
		<p> <?php echo $student->free_time; ?> </p>
		<p class="hide" id="hide_free"> 
			<input type="text" name="new_student_free_time" class="nice_input_reg" font-size="100%"/> 
		</p>

		<h3 class="update" id="cv_update"> Životopis </h3>
		<p> 
			<button id="file-download" class="my_button cv_accepted" type="submit" name="download" value="<?php echo $student->cv;?>"> <i class="fa fa-cloud-download"></i>  Skini životopis </button>
		</p>
		<p class="hide" id="hide_cv"> 
			<label for="file-upload" class="my_button">
			    <i class="fa fa-cloud-upload"></i>  Učitaj novi životopis
			</label>
			<input type="file" name="new_student_cv" id="file-upload"/> 
		</p>

		<button type="submit" class="my_button middle_button" > Uredi podatke </button><br><br>

	</form>
</div>

<script type="text/javascript">
	$(".hide").css("margin-bottom","5%");
	$(".hide").hide();
	$("p").css("text-align","center");
	opened = [];
	$("document").ready(function() {
		$('#header').on( "click", function() {
			var loc1 = window.location.pathname;
			var loc2 = {
				url : '?rt=student/all_offers'
			};
			console.log(loc1);
			window.location.assign(loc1+loc2.url);
		});
		$(".update").on("click", function(){
			var id = $(this).attr("id");
			var what = id.substr(0,id.indexOf('_'));
			var id2 = "#hide_" + what;

			if( typeof(opened[id]) === "undefined" || opened[id] === false ){
				opened[id] = true;
				$(id2).show();
				$('#'+id).css("color","#40e0d0").css("font-size","24px");
			}
			else{
				opened[id] = false;
				$(id2).hide();
				$('#'+id).css("color","#404040").css("font-size","19px");
			}

		});
	} )
</script>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>