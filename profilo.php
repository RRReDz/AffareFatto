<?php 
	include 'session_check.php';
?>

	<!DOCTYPE html>
	<html lang="en">
		<head>
			<title> Benvenuto </title>
			
			<link href="css/bootstrap.css" rel="stylesheet">
			<link href="css/homepage.css" rel="stylesheet">
			<script type="text/javascript" src="jquery.js"></script>
			
		</head>
	<body>
		
	<?php include 'navbar.html'; ?>
	
	<form id="form1" action="profilo_updateData.php" class="form-style">
		<h2>Il mio account</h2><br><br>
		<div class="row">
				
				<?php
				
					//Apertura database
					include 'db_connect.php';
					
					//Query per selezionare Nome Cognome dell'utente
					$strSQL = "SELECT * FROM Utente WHERE IDUtente = ( SELECT IDUtente FROM Autenticazione WHERE NomeUtente = '".$_SESSION['username']."')";
					$result = mysql_query($strSQL) or die(mysql_error());
					$row = mysql_fetch_array($result) or die(mysql_error());
					
					echo "<div class='span5'>				
							<img id='imagePreview' src='picsUser/".$row['Immagine']."' class='img-rounded' style='height:300px'><br><br>	
							<input type='hidden' id='immagine' name='immagine' value='".$row['Immagine']."'>
							<input type='button' id='getFile' value='Sfoglia' class='btn' onclick='this.form.test.click()'>
						</div>
			
					<div class='row'> ";
					
					echo "<div class='span3'><strong> Nome </strong></div>
						<div class='span4'>".$row['Nome']."</div><br><br>";
					echo "<div class='span3'><strong> Cognome </strong></div>
						<div class='span4'>".$row['Cognome']."</div><br><br>";
					echo "<div class='span3'><strong> Indirizzo </strong></div>
						<div class='span4'><input value='$row[Indirizzo]' name='addr' type='text' placeholder='Indirizzo'></div>";
					echo "<div class='span3'><strong> CAP </strong></div>
						<div class='span4'><input value='$row[CAP]' name='CAP' id='CAP' type='text' placeholder='CAP' maxlength=5> <div id='loading' style='display:inline;'></div></div>";
					echo "<div class='span3'><strong> Paese </strong></div>
						<div class='span4'><input value='$row[Paese]' name='town' id='town' type='text' placeholder='Paese'></div>";
					echo "<div class='span3'><strong> Provincia </strong></div>
						<div class='span4'><input value='$row[Provincia]' name='distr' id='distr' type='text' placeholder='Provincia' maxlength='2'></div>";
					echo "<div class='span3'><strong> E-Mail </strong></div>
						<div class='span3' id='email'><input value='$row[EMail]' name='email' type='email' placeholder='E-Mail'></div>";
					echo "<div class='span3'><strong> Telefono </strong></div>
						<div class='span3'><input value='$row[Telefono]' name='tphone' type='text' placeholder='Telefono'></div><br><br>";
						
					echo "</div>";
					
					echo "<div class='row'>
						<div class='span6'></div>
						<div class='span3'><button id=sub type='submit' class='btn btn-primary btn-large'>Aggiorna</button></div>
						<div class='span3'><button class='btn btn-inverse btn-large' id='edit1' type='button'>Modifica</button></div>
						</div></div>";
						
					mysql_close($db);
					
				?>
				
			</div>
		</div> <!-- close ROW -->
	</form>
	
	<!--Inserimento immagine profilo-->
	<iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>
	<form action="profilo_imgupload.php" id="imgupload" target="upload_target" method="post" enctype="multipart/form-data">
		<input type="file" name="file" id="file" style="visibility:hidden;position:absolute;top:0;left:0">
		<button id="subImage" style="visibility:hidden;position:absolute;top:0;left:0" type="submit"></button>
	</form>
	
</body>

<script type="text/javascript" src="js/bootstrap.js"></script>	
<script type="text/javascript" src="jquery.js"></script>
<script src="js/scripts.js"></script>
	
<script type="application/javascript">

	// Rimuove attributo "disabilitato" per permetterne l'invio 
	$("#form1").submit(function() {
		$(":disabled").removeAttr('disabled');
	});	
	
	// Sblocca caselle per permettere la modifica
	$("#edit1").click(function(){
		$("#form1, :disabled").removeAttr('disabled');
	});	

	// Blocca caselle di input se valore già presente in database 		
	$("form#form1 input[type='text'],input[type='email']").each(function(){
		var input = $(this); 
		if (input.val() != "")
			input.attr('disabled', 'disabled');
	});

	//Valore maiuscolo al campo 'provincia'
	$('#distr').blur(function(){
		$(this).val($(this).val().toUpperCase());
	});
		
			
	// Gestione autocompletamento CAP
	$("#CAP").blur(function(){
		$("#loading").html(" <img src='img/loading.gif' style='height:20px; width:20px;'>");
		$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'profilo_capprov_control.php',
		data: "cap=" + $(this).val(),
		success: function(response) {
				if (response[0].i == "ok"){	
					if(response[0].prov != "")
						$("#distr").val(response[0].prov);
					if(response[0].com != "")
						$("#town").val(response[0].com); 
				}
				else if(response[0].i == "null"){
					$("#distr").val("").removeAttr('disabled');
					$("#town").val("").removeAttr('disabled');	
				}
			$("#loading").html("");
			}
		});
	});
	
	// Invio dati con Ajax (DATI PERSONALI) 
	$("#form1").submit(function() {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $("#form1").attr("action"),
			data: $("#form1").serialize(),
			success: function(response) { 
				if (response[0].campo == "ok")
					window.location = response[0].valore;
				else{
					$("#" + response[0].campo).children("span").remove();
					$("#" + response[0].campo).append("<span class='help-inline' style='margin-top: -1em;'><font color='red'>" + response[0].valore + "</font></span>");	
					}
				}
			});
		return false;
		});
		
		
	// Gestione bottone sfoglia	
	$("#getFile").click(function () {
		$("#file").trigger('click');
	});
		
	// Submit form quando si sceglie un'immagine	
	$("#file").change(function () {
		$("#imgupload").submit();
		});
		
	// Quando l'iframe si carica si attende la risposta dal server
	$("#upload_target").load(function (){
        var response = $("#upload_target").contents().find('body').text();
		$("#imagePreview").attr('src', "picsUser/" + response);
		$("#immagine").attr('value', response);
		});		
		

</script>

</html>
