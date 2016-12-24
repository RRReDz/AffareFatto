<?php 
	include 'session_check.php';
	
	if (!isset($_GET['objID']))
		header("location:preferiti.php");
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title> Benvenuto </title>
		
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/homepage.css" rel="stylesheet">
		<script type="text/javascript" src="jquery.js"></script>
		<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
		
	</head>
	
	<body>

	<?php include 'navbar.html'; ?>
			
			<div class="container form-style">
				<div class="row">
					<div class="span7">
						
						<?php 
							//Apertura database
							include 'db_connect.php';
							
							//Selezione attributi Oggetto
							$strSQL = "SELECT NomeUtente, Indirizzo, Paese, Provincia, CAP, U.Email AS EUtente, U.Telefono AS TUtente, O.Nome AS NomeOBJ, Prezzo, Descrizione, ";
							$strSQL .= "O.Immagine AS ImmagineOBJ, O.Data AS OData, O.Ora AS OOra, O.Telefono AS YNTel, O.EMail AS YNMail, O.Mappa AS YNMap, C.Nome AS NomeCategoria ";
							$strSQL .= "FROM Autenticazione A, Utente U, Oggetto O, Categoria C ";
							$strSQL .= "WHERE O.IDUtente = A.IDUtente AND O.IDUtente = U.IDUtente AND O.Categoria = C.IDCategoria AND CodOgg = '".$_GET['objID']."'";
							$query_result = mysql_query($strSQL);
							
							$result = mysql_fetch_array($query_result);
								
							if (mysql_num_rows($query_result) == 0)
								header("location:preferiti.php");
								
							echo"<div id=title><h2> $result[NomeOBJ] </h2></div>";
							echo"<h6> [$result[NomeCategoria]] </h6> <br><br>";
							/* Immagine dell'Oggetto */
							echo"<img id='imagePreview' src='pics/".$result['ImmagineOBJ']."' class='img-rounded' style='height:260px'><br>";
						?>
						
					</div>
					<div class="span5">
						<?php
						
							echo"<br><div id=prezzo align=right><h3> $result[Prezzo] &euro; </h3></div>";  
							echo"<div id=nomeUtente align=right><small> Inserito il $result[OData], alle $result[OOra], dall'utente <a id=sendmessage href=# style='text-decoration: none'>$result[NomeUtente]</a> </small></div><br><br>";
							
							//Visualizzazione informazioni annuncio e relativi controlli di esistenza							
							echo"<div align=right><h4> Ulteriori informazioni sull'Utente </h4>";
							if($result['YNMail'] == 1)
								echo"<div id=Mail>E-Mail: <em>$result[EUtente]</em> </div>";
							if($result['YNMap'] == 1){
								if($result['Indirizzo'] != "" || $result['Indirizzo'] != NULL)
									echo"Indirizzo: <em><span id=address>$result[Indirizzo]</span></em>";
								if(($result['Paese'] != "" || $result['Paese'] != NULL) && ($result['CAP'] != "" || $result['CAP'] != NULL))
									echo"<em>$result[Paese]</em>, <em><span id=paese>$result[CAP]</span></em> ";
								}
								if($result['Provincia'] != "" || $result['Provincia'] != NULL)
								echo"Provincia: <em>$result[Provincia]</em>";
								
							if($result['YNTel'] == 1 && ($result['TUtente'] != NULL || $result['TUtente'] != ""))
								echo"Numero di telefono: <em>$result[TUtente]</em></div>";
								
							mysql_close($db);
						?>
					</div>
				</div>
				<div class="row" style="padding-left:20px;">
					<div class="span12">
						<?php
							echo"<br><div align=left><h4> Descrizione dell'Oggetto: </h4>";
							echo"<div id=descrizione> ".htmlentities($result['Descrizione'], ENT_QUOTES, 'UTF-8')." </div></div><br><br>";
						?>
					</div>
				</div>
				<div class="row" style="padding-left:20px;">
					<div class="span5">
						<?php
							echo"<form id=form_message action=messaggi_nuovo.php method=post>
							<div style='border:solid; border-color:#d44413; border-radius:5px; padding:25px 29px 25px; width:400px'>
							<h4>Contatta il venditore</h4><br>
							<table>
								<tr><td width=100 style='vertical-align: top;'>Oggetto:</td> <td><div id=oggetto><input type=text name='oggetto' class='input-level' style='width:300px;' placeholder='Oggetto'> 
								<tr><td></td> <td></div></td>
								<tr><td style='vertical-align: top;'>Testo:</td> <td><div id=descrizione><textarea name='descrizione' rows=6 style='min-width:300px; max-width:300px;' placeholder='Inserisci il messaggio...'></textarea></td>
								<tr><td></td> <td></div></td>	
							</table>
							<input type=hidden name=nome value='".$result['NomeUtente']."'>
							<br><div align=right><button class='btn btn-large btn-primary' style='width:200px;' type='submit'>Invia</button></div>
							</div></form>";
						?>
					</div>
					<div class="span1"></div>
					<div class="span5" style="padding: 0px 30px 0px">
						<?php
							if($result['YNMap'] == 1){
								echo"<div id='map-canvas' style='width: 464px; height: 368px;'></div>";
								}	
						?>
					</div>
				</div>
			</div>
			
			
			<!-- Modal inserimento preferiti-->
			<div id="fav" class="modal hide">
					<div id='modal-body-delete' class="modal-body"></div>
					<div id='modal-footer-delete' class='modal-footer'></div>
			</div>
			
	</body>
	
	<script type="text/javascript" src="js/bootstrap.js"></script>
		
	<script type="application/javascript">
	$('#Sname').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 600);
		return false;
	});
	
	// Invio dati con Ajax (Contatta il venditore)
	$("#form_message").submit(function() {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $("#form_message").attr("action"),
			data: $("#form_message").serialize(),
			success: function(response) {
				if (response[0].campo == "output" && response[0].valore == "true") {
					$("#form_message :input").val("");
					$(document).scrollTop(0);
					}
				else 
					for (var i = 0, len = response.length; i < len; i++) {
						$("#" + response[i].campo).children("span").remove();
						$("#" + response[i].campo).append("<span class='help-inline' style='margin-top:-1em;'><font color='red'>" + response[i].valore + "</font></span>");	
						}
				}
			});
		return false;
		});
	
	$('#sendmessage').bind('click', function() {
		  $('html, body').animate({scrollTop:$('#form_message').position().top}, 'slow');
	});
		
	// Mappa Google Maps
	var geocoder;
	var map;
	$(document).ready(function() {
		if ($("#map-canvas").length > 0) {
			geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(0, 0);
			var mapOptions = {
				zoom: 15,
				center: latlng,
				disableDefaultUI: true,
				mapTypeId: google.maps.MapTypeId.HYBRID
				}
			map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
				
			var address = "address=";
			if ($("#address").html() != "")
				address += $("#address").html().replace(" ","+");
			if ($("#paese").html() != "")	
				address += "," + $("#paese").html().replace(" ","+");
			address += ",IT";
			geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
						map: map,
						position: results[0].geometry.location
						});
					} 
				else {
					alert("Geocode was not successful for the following reason: " + status);
					}
				});
			}
		});
	
	
		//Invio dati AJAX per inserimento/eliminazione preferito
		$('#ref').click(function (){
			if($("#flag").val() == 0) 
				$("#img").attr("src", "pics/starY.jpg");
			else 
				$("#img").attr("src", "pics/starW.jpg");
			$.ajax({ 
				type: 'POST', 
				dataType: 'json', 
				url: "preferiti_set.php", 
				data: {flag:$('#flag').val(),user:$('#user').val(),obj:$('#obj').val()},
				success: function(response){
							<!-- Conferma inserimento/rimozione preferito 
							$("#modal-body-delete").html("<div class='modal-body'>" + response[0].i + "</div>");
							var html = "<button class='btn' data-dismiss='modal' aria-hidden='true'>Chiudi</button>";
							$("#modal-footer-delete").html(html);
							$('#fav').modal('show');
							$("#flag").val(response[0].flag);
				}
			});
		});
		
	</script>	

</html>
