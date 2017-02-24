<?php
require_once dirname(__FILE__) . "/../util.php";
require_once dirname(__FILE__) . "/../menu.php";
session_start();
if(isset($_SESSION['idPM'])) 
	$idPM = $_SESSION['idPM'];
if(isset($_SESSION['idProj']))	
	$idProj = $_SESSION['idProj'];
if(isset($_SESSION['modalita']))
	$mod = $_SESSION['modalita'];	
if(isset($_SESSION['error']))
	$error = $_SESSION['error'];
else
	$error=0;




include_once '../connDB/DB_API.php';
$db = new DB_API();
$chiusura = 2;
//prendo lo stato del progetto 
$progetto =$db->getProjectFromId($idProj); 
$stato = $progetto[0]['stato'];
$personeProgetto = $db->getProgrammerSign($idProj);
$programmatori[]=null;
if(count($personeProgetto)>0){
for($i = 0;$i<count($personeProgetto);$i++){

	$programmatori[$i] = $db->getProgrammatore($personeProgetto[$i]['Programmatore_idProgrammatore']);
	$programmatori[$i][0]['dataIscrizione'] = $personeProgetto[$i]['data'];
}
}

//print_r($programmatori);




?>



<html>

<head>
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<script type="text/javascript" src="../lib/popup.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
    $('#popup').hide();
});


function deleteProgrammer(event){
	
	var flag = false;
	var idprogrammatori;
	var programmatori = document.getElementsByName('programmer[]');
	for (i = 0; i < programmatori.length; i++) {
	    if (programmatori[i].checked == true) {
	        flag = true;
	    }
	}
	if(flag == false){
		window.alert("Nessun Programmatore selezionato");
		event.preventDefault();
	
	}else{
		r = confirm("Sei sicuro di eliminare i\\Il programmatori\\e selezionato\\i");
		if(r == false) {event.preventDefault();}
		
		
	}

}


function comeBack(){
	 var r = confirm("L'Operazione ti sta riportando alla pagina precedente");
	 if (r == true) {
		 window.location.assign("projects.php");
		}
}

var idProg;

function viewCompetence(event){
	var idprogrammatori;
	var programmatori = document.getElementsByName('programmer[]');
	var count = 0;
	for (i = 0; i < programmatori.length; i++) {
	    if (programmatori[i].checked == true) {
	        count ++;
	    }
	}

	if(count == 0){
		window.alert("Nessun Programmatore selezionato");
		event.preventDefault();
	}else if(count > 1){
		window.alert("Puoi vedere solo le competenze di un programmatore per volta\nHai selezionato " + count + " programmatori");
		event.preventDefault();
	}else{
		//prendo l'id del programmatore
		for(i=0; i < programmatori.length; i++){
			if(programmatori[i].checked)
					idProg=programmatori[i].value;
			
		}
 		 
		sendPostViewCompetenze(idProg);
	}
}

function sendPostViewCompetenze(id){
	//alert(id);
	$.post("response_competenze.php",
			{
			  id:id
			},
			function(data,status){
				//alert("Data: " + data + "\nStatus: " + status);
				if(data == "-1"){
					addStr("error");
				}else{
					var pushedData = jQuery.parseJSON(data);
				
					//alert(pushedData);
                	addStr(pushedData);
				}
			});
}
function addStr(data){
	//prendo il nome e il cognome del programmatore
	//cerco con nome+idprogrammatore	
	//alert(idProg);
	arr = document.getElementById('nome'+parseInt(idProg));
	nome = arr.innerHTML;
	arr = document.getElementById('cognome'+parseInt(idProg));
	cognome = arr.innerHTML;
	
	str = "<p>Nome: "+ nome + "</p>";
	str += "<p>Cognome: "+ cognome +"</p>";
	
	
	str += "<p><h2>Competenze:</h2></p>";
	
	if(data == "error"){
		addToDiv(str + "Il programmatore non ha competenze");	
		
	}else{
	
	for(var i in data){
		str += "<p>" + data[i] + "</p>";		
	}
	addToDiv(str);
	}
}
	

	

function viewTip(event){
	var flag = false;
	var idprogrammatori;
	var programmatori = document.getElementsByName('programmer[]');
	for (i = 0; i < programmatori.length; i++) {
	    if (programmatori[i].checked == true) {
	        flag = true;
	    }
	}
	if(flag == false){
		window.alert("Nessun Programmatore selezionato");
		event.preventDefault();
	
	}
	


}

function redirect(){
	document.location.href='closeSign.php';
	var error =  "<?php echo $error; ?>";
	if(error==1){
		window.alert("Non ci sono abbastanza persone iscritte al progetto");
	}
	
}


	</script>
<head>
<script src="scriptProjects.js"></script>
<title>Iscrizioni</title>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="main_style.css" title="wsite-theme-css" />


<style type="text/css"> 
#main-wrap tr:hover
{
	text-align: center;
	color: #000;
	background-color: #FFF;
}
#main-wrap tr{
	height: 80px;
}

.message {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #F00;
	width: 300px;
	height: 30px;
	text-align: center;
}
</style>

</head>

<body>
	<div id="popup">Lorem Ipsum Dolor sit</div>
	<div id="header-wrap">
    	<div class="container">
        	<table id="header">
        	<tr>
           		<td id="logo"><span class="wsite-logo">
					<a href="#">
					<span id="wsite-title">Ricerca Competenze</span>
					</a></span>
				</td>
           <td id="header-right">
                <?php 
			   	menu("../");
			   ?>
       		</tr>
       </table>   
    </div>
</div>

<div id="topnav-wrap">
	<?php
	 	$util = new Util();
			 menuPM("");
		
	?>
</div>
<div id="main-wrap">
    <div class="container">
        <div id="main">
        	<div id="content">
        	<div id='wsite-content' class='wsite-elements wsite-not-footer'>
<div style="text-align:left;">

<h1> Persone Iscritte</h1>


<form method="POST" name="moduloSign" action="delSignProgrammer.php">
	<input type="hidden" name="idProj" value="<?php echo $idProj ?>">
	<input class="button-large" <?php  if( $stato == $chiusura) echo "type='hidden'"; else echo"type='submit'"?> name="delSign" value="Elimina Iscrizione" onClick="deleteProgrammer(event)">
	<input class="button-large" type = "button" name="view" value="Visualizza Competenze" onClick="viewCompetence(event)">
	<input class="button-large" type = "button" name="canc" value="Indietro" onClick="comeBack()">

<table id="tabProgrammatori">


<tr style="background:#fff; color:#5f5f5f">
	<td><h3>*</h3></td>
	<td><h3>Nome</h3></td>
	<td><h3>Cognome</h3></td>
	<!--<td><h3>Data Iscrizione</h3></td>-->
	<td><h3>E-Mail</h3></td>
	<td><h3>Costo/Ora</h3></td>
    <td><h3>Esperienza</h3></td>

</tr>
<?php 




if(count($personeProgetto)>0){

	for($i=0;$i<count($programmatori);$i++){
		
		echo"<tr >";
		echo"<td><input type='checkbox' name='programmer[]' value = '
				".$programmatori[$i][0]['idProgrammatore']."'></td>";
		echo"<td id='nome".$programmatori[$i][0]['idProgrammatore']."'>".$programmatori[$i][1]['nome']."</td>";
		echo"<td id='cognome".$programmatori[$i][0]['idProgrammatore']."'>".$programmatori[$i][1]['cognome']."</td>";
		//echo"<td>".$programmatori[$i][0]['dataIscrizione']."</td>";
		echo"<td>".$programmatori[$i][1]['email']."</td>";
		echo"<td>".$programmatori[$i][0]['costo_ora']."</td>";
		
		echo '<td >'.getEsperienza($programmatori[$i][0]['esperienza']).'</td>';
		
	}
		echo "</tr>";
}else{
	echo "</table><table><tr><td>Nessuna Iscrizione Al progetto</td></tr>";
}
	
?>


</table>
<div style="margin-left:33%; margin-right:50%; margin-top:5%"><div class="message"><?php 
if(isset($_GET['message'])){
	if($_GET['message']=='4'){
	echo "non ci sono abbastanza persone iscritte al progetto";	
	}
}



?></div>
<input  class ="button-large" type ="button" name="tip" value="Calcola team" onClick="redirect()">
</div>
</form>

</div>
			</div>
		</div>
	</div>
	</div>

	<div style="padding-top:200px; background:#fff;"></div>	
	<div id="footer-wrap">
	<div class="container">
		<div id="footer"></div>
    </div>
	</div>
</div>
</body>
</html>