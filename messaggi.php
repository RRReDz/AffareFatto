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

<!--Messaggi-->
<div class="tab-pane active" id="messaggi">
    <div class="form-style">
        <h2>Messaggi</h2><br>
        <div class="row">
            <div class="span2">
                <a href="#new" id="newButton" class="open-new btn" data-toggle="modal"><i class="icon-pencil"></i> Nuovo</a>
            </div>
            <div class="span">
                <a id="deleteButton" class="btn" disabled><i class="icon-trash"></i> Elimina</a>
            </div>
            <div id="replySpan" class="span"></div>
        </div>
        <br><br>
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#inbox" id="inboxToggle" class="boxToggle" data-toggle="tab" data-id="inbox"
                       style="width:130px;">Posta in arrivo </a>
                </li>
                <li><a href="#invio" class="boxToggle" data-toggle="tab" data-id="outbox">Posta inviata</a></li>
                <li><a href="#cestino" class="boxToggle" data-toggle="tab" data-id="bin">Cestino</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="inbox">
                    <form action="messaggi_inbox.php" id="inboxform">
                        <table class="table">
                            <tbody id="inboxResults">

                            </tbody>
                            <tr>
                                <td colspan="4">
                                    <div align="left" class="pagination">
                                        <ul id="inboxPage">

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="tab-pane" id="invio">
                    <form action="messaggi_outbox.php" id="outboxform">
                        <table class="table">
                            <tbody id="outboxResults">

                            </tbody>
                            <tr>
                                <td colspan="4">
                                    <div align="left" class="pagination">
                                        <ul id="outboxPage">

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="tab-pane" id="cestino">
                    <form action="messaggi_bin.php" id="binform">
                        <table class="table">
                            <tbody id="binResults">

                            </tbody>
                            <tr>
                                <td colspan="4">
                                    <div align="left" class="pagination">
                                        <ul id="binPage">

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal nuovo messaggio -->
<div id="new" class="modal hide fade">
    <form action="messaggi_nuovo.php" id="newmessage">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
            <div class="row">
                <div class="span6">
                    <font size="3"><b>A: </b></font>
                    <div id="nome">
                        <input type="text" name="nome" class="input-level" style="width:400px;" placeholder="Nome">
                    </div>
                </div>
                <div class="span6">
                    <font size="3"><b>Oggetto: </b></font>
                    <div id="oggetto">
                        <input type="text" name="oggetto" class="input-level" style="width:400px;"
                               placeholder="Oggetto">
                    </div>
                </div>
                <div class="span6">
                    <font size="3"><b>Messaggio: </b></font>
                    <div id="descrizione">
                        <textarea name="descrizione" rows="6" style="min-width:450px; max-width:450px;"
                                  placeholder="Inserisci il messaggio..."></textarea>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div class="modal-footer">
            <button class='btn' data-dismiss='modal' aria-hidden='true'>Annulla</button>
            <button type='submit' class='btn btn-primary'> Invia</button>
        </div>
    </form>
</div>

</body>

<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>

<script type="text/javascript">

    // Messaggi selezionati
    var cSelected = "";

    // Visualizzazione lista messaggi
    $(document).ready(function () {
        showBadge();
        showBox("inbox", 0, "inbox");
    });

    // Visualizza i messaggi
    $(".boxToggle").click(function () {
        var id = $(this).data('id');
        showBox(id, 0, id);
    });

    // Visualizza quanti messaggi ci sono da leggere
    function showBadge() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "messaggi_check_inbox.php",
            success: function (response) {
                if (response[0].nMessaggi != '0') {
                    $("#inboxToggle").children("span").remove();
                    $("#inboxToggle").append("<span class='badge badge-inverse'>" + response[0].nMessaggi + "</span>");
                }
                if (response[0].nMessaggi == '0')
                    $("#inboxToggle").children("span").remove();
            }
        });
    }

    // Selezione pagina messaggi
    $(document).on("click", ".pageMessage", function () {
        var pagina = $(this).data("id");
        var form = $(this).parents("form").attr("id").split("form");
        showBox(form[0], pagina, form[0]);
    });

    // Messaggi in arrivo, messaggi inviati e nel cestino
    function showBox(form, page, ul) {
        showBadge();
        $("#deleteButton").attr("disabled", "disabled");
        $("#" + ul + "Page").html("");
        $("#replySpan").html("");
        $("#" + form + "Results").html("");
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $("#" + form + "form").attr("action"),
            data: "page=" + page,
            success: function (response) {
                $("#" + form + "Results").html("");
                if (response.data[0].ID == 'vuoto') {
                    $("#" + form + "Results").append("<tr><td colspan='4'><div align='center'><h4>Nessun messaggio presente!</h4></div></td></tr>");
                }
                else {
                    for (var i = 0, len = response.data.length; i < len; i++) {
                        var html = "<tr class='messageRow'>";
                        if (response.data[i].Letto == 0)
                            html += "<td><label class='checkbox'><input type='checkbox' data-id=" + response.data[i].Codice + " class='checkboxM'><i class='icon-envelope'></i></label></td>";
                        else
                            html += "<td><label class='checkbox'><input type='checkbox' data-id=" + response.data[i].Codice + " class='checkboxM'></label></td>";
                        html += "<td>" + response.data[i].Utente + "</td>";
                        html += "<td width='500'><a href='#' data-id=" + response.data[i].Codice + " data-box=" + form + " class='clickShow' style='color: black;'><b>" + response.data[i].Oggetto + "</a></td>";
                        html += "<td>" + response.data[i].Data + "</td>";
                        html += "</tr></tr>";
                        $("#" + form + "Results").append(html);
                    }
                    for (var i = 0; i < Math.ceil(response.num / 10); i++) {
                        if (i == response.page)
                            $("#" + ul + "Page").append("<li class='active'><a href='#'>" + (i + 1) + "</a></li>");
                        else
                            $("#" + ul + "Page").append("<li><a href='#' data-id='" + i + "' class='pageMessage'>" + (i + 1) + "</a></li>");
                    }
                }
            }
        });
    }

    // Visualizzazione messaggio
    $("tbody").on('click', '.clickShow', function () {
        var body = $(this).closest("tbody").attr("id");
        var id = $(this).data("id");
        var box = $(this).data("box");
        $("#" + box + "Page").html("");
        $("#" + body).html("");
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "messaggi_showmessage.php",
            data: "id=" + id,
            success: function (response) {
                if (box == "inbox") {
                    var html = "<a href='#new' id='replyButton' class='btn' data-toggle='modal'><i class='icon-share-alt'></i> Rispondi</a>";
                    $("#replySpan").html(html);
                }
                var html = "<tr><td><div align='right'><input type='checkbox' id='checkM' data-id=" + response[0].Codice + " class='checkboxM'>Il " + response[0].Data + " alle " + response[0].Ora + "</div>";
                html += "<font size='6'><span id='obj'>" + response[0].Oggetto + "</span></font></td></tr>";
                html += "<tr><td>Da: <span id='dest'>" + response[0].Mittente + "</span><br>A: " + response[0].Destinatario + "</td></tr>";
                html += "<tr><td><div class='messageView'><font size='4'>" + response[0].Contenuto + "</font></div></td></tr>";
                $("#" + body).html(html);
                showBadge();
                $("#checkM").hide().trigger("click");
                ;
            }
        });

    });

    // Pulizia del modal form nuovo messaggio
    $("#newButton").click(function () {
        $(':input', '#newmessage').val("");
        $("#output").html("");
    });

    // Risposta precompilata
    $(document).on("click", "#replyButton", function () {
        var utente = $("#dest").html();
        $("input[name=nome]").val(utente);
        var oggetto = $("#obj").html();
        $("input[name=oggetto]").val("RE: " + oggetto);
        $("input[name=descrizione]").val("");
    });

    // Invio dati con Ajax (NUOVO MESSAGGIO)
    $("#newmessage").submit(function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: $("#newmessage").attr("action"),
            data: $("#newmessage").serialize(),
            success: function (response) {
                if (response[0].campo == "output" && response[0].valore == "true") {
                    $('#new').modal('hide')
                    showBox("outbox", 0, "outboxPage");
                }
                else if (response[0].campo == "output" && response[0].valore == "false") {
                    $(".modal-body").children("span").remove();
                    $(".modal-body").append("<span><font color='red'>Errore nell'invio del messaggio.</font></span>");
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

    // Abilita il pulsante elimina
    $("tbody").on("click", ".checkboxM", function () {
        if ($("input.checkboxM:checked").length < 1)
            $("#deleteButton").attr("disabled", "disabled");
        else
            $("#deleteButton").removeAttr("disabled");
    });

    // Elimina messaggi
    $("#deleteButton").click(function () {
        cSelected = "";
        $('input:checked').each(function () {
            if (cSelected != "")
                cSelected += "&IDMess[]=" + $(this).data("id");
            else
                cSelected = "IDMess[]=" + $(this).data("id");
        });
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "messaggi_delete.php",
            data: cSelected,
            success: function (response) {
                if (response[0].campo == "true")
                    $("#deleteButton").attr("disabled", "disabled");
                showBox("inbox", 0, "inboxPage");
                showBox("outbox", 0, "outboxPage");
                showBox("bin", 0, "binPage");
            }
        });
    });

</script>

</html>