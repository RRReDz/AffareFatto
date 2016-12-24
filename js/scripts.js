// In fase di caricamento della pagina
$(document).load(function() {
	$("html").css("cursor", "progress");
	});

$(document).ajaxStart(function() {
	$("html").css("cursor", "progress");
	});	

$(document).ajaxStop(function() {
	$("html").removeAttr("style");
	});

// Seleziona la sezione della navbar 
$(document).ready(function() {
	var pathname = window.location.pathname;
	var url = pathname.split("/");
	$('a[href="' + url[url.length - 1] + '"]').parent("li").attr("class", "active");
	
	// Visualizza quanti messaggi ci sono da leggere
	if (url[url.length - 1] != "messaggi.php")
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "messaggi_check_inbox.php",
			success: function(response) {
				if (response[0].nMessaggi != '0') {
					$("#6").children("a").children("span").remove();
					$("#6").children("a").append(" <span class='badge badge-inverse'>" + response[0].nMessaggi + "</span>");	
					}
				if (response[0].nMessaggi == '0')
					$("#6").children("a").children("span").remove();
				}
			});
	});
	
// Segnalazione campi vuoti
$("input").focus(function() {
	$(this).parents("div:eq(0)").children("span").remove();
	});

// Segnalazione campi vuoti
$("textarea").focus(function() {
	$(this).parents("div:eq(0)").children("span").remove();
	});	
	
// Submit con tasto Invio
$("input").keypress(function(event) {
	if (event.which == 13) {
		event.preventDefault();
		$form = $(this).closest("form").attr("id");;
		$("#" + $form).submit();
		}
	});

// Scroll UP	
$('#Sname').on("click",function(){
	$("html, body").animate({ scrollTop: 0 }, 600);
	});
	