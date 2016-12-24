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
		
		<style>
			<!-- Colorazione link -->
			a:link {
			color : #6495ED; }
			
		</style>
	</head>
<body>
		
	<?php include 'navbar.html'; ?>
	
	<form action="ricerca_show.php" id="ricerca" class="form-style">
		<h2> Cerca il tuo oggetto </h2><br>
		<div class="row">
			<div class="span3"><input type="text" class="input" name="keywords" placeholder="Cerca"></div>
			<div class="span3">
				<select name="categoria">
					<option value="0">Tutte la categorie</option>
					<?php
					//Apertura database
					include 'db_connect.php';
					
					$strSQL = "SELECT * FROM Categoria";
					$query_result = mysql_query($strSQL);
					
					// Creazione categorie
					for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
						$tmp = mysql_fetch_array($query_result);
						echo "<option value=".$tmp['IDCategoria'].">".$tmp['Nome']."</option>";
						}
					mysql_close($db);
					?>
				</select>
			</div>
                        <div class="span3">
				<select name="city">
					<option value="0">Tutte le provincie</option>
					<?php
					//Apertura database
					include 'db_connect.php';
					
					$strSQL = "SELECT DISTINCT prov FROM Italia ORDER BY prov";
					$query_result = mysql_query($strSQL);
					
					// Creazione categorie
					for ($i = 0; $i < mysql_num_rows($query_result); $i++) {
						$tmp2 = mysql_fetch_array($query_result);
						echo "<option value=".$tmp2['prov'].">".$tmp2['prov']."</option>";
						}
					mysql_close($db);
					?>
				</select>
			</div>
			<div class="span2">
				<button type="submit" class="btn" name="Bsrch" style="width: 80px">Cerca</button> <span id="loading"></span>
			</div>
		</div>
		<br>
		<table class="table">
			<tbody id="results">
			
			</tbody>
		</table>
	</form>
	
</body>

<script type="text/javascript" src="js/scripts.js"></script>

<script type="application/javascript">
	
	// Visualizza alcuni oggetti
	$(document).ready(showList());
	
	// Ricerca
	$("#ricerca").submit(function() {
		$("#loading").html("<img src='img/loading.gif' style='height:20px; width:20px;'>");
		showList();
		return false;
		});
		
	// Visualizzazione lista
	$("#ricerca").submit(function() {
		showList();	
		return false;
	});
	
	// Visualizzazione lista
	function showList() {
		$("#results").html("");
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $("#ricerca").attr("action"),
			data: $("#ricerca").serialize(),
			success: function(response) {
				if (response[0].ID == 'vuoto') {
					$("#results").html("").append("<div align='center'><h4>Nessun oggetto individuato!</h4></div>");	
					}
				else {
					$("#results").html("");
					for (var i = 0, len = response.length; i < len; i++) {
						var code = "<tr><td rowspan='2' width='160' height='150'><a style='text-decoration: none' href=ricerca_showobject.php?objID=" + response[i].ID + "><img src='" + response[i].Immagine + "' class='img-rounded' style='max-height:150px'></a></td>";
						code += "<td height='37' colspan='3'><b><a style='text-decoration: none' href=ricerca_showobject.php?objID=" + response[i].ID + ">" + response[i].Titolo + "</a></b></td>";
						code += "<td align=right><div align=right>" + response[i].Data + " , " + response[i].Ora + "</div></td></tr>";
						code += "<tr><td style='line-height: 3' width='150'>EUR &nbsp;"  + response[i].Prezzo + " &euro; </td>";
						code += "<td style='line-height: 3' width='170'>" + response[i].Categoria + "</td>";
                                            if (response[i].Luogo != "")
						code += "<td style='line-height: 3' width='170'>Luogo " + response[i].Luogo + "</td>";
                                            else
                                                code += "<td style='line-height: 3' width='170'></td>";
						code += "<td style='line-height: 3'><div align=right>Inserito da <i>" + response[i].User + "</i></div></td></tr>"; 
						$("#results").append(code);	
						}
					}
				$("#loading").html("");
				}
			})
		}
</script>

</html>