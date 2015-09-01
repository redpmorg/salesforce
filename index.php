<?php

// CONNECT START

/*
 * REST API Interface for fetching Salesforce Data
 *
 * Author: Leonard LEPADATU [leonardlepadatu@yahoo.com]
 *
 * Formal intuition:
 * OAuth make several requesting and if everything is going well the API provide
 * a security access token who we will stored in session.
 * This is a good practice to optimize the app speed because the first part of this
 * code can be implemented into some connection class and can be called just at beginning.
 *
 * ATENTION !!
 * According the SF documentation we can't revoke the token through this method
 * But when we done ... we can call session_destroy(); ... though :)
 *
*/

if(!isset($_COOKIE["access_token"])) {

	// To connect please fill your SF credentials here
	// $username = "";
	// $initial_password = "";
	// $security_token = "";

	// This are proper to SF connected app - OAuth - made by me.
	// Change only if replace the SF app ...
	$consumer_key    = "";
	$consumer_secret = "";
	$request_url = "";

	// This is GRANT_TYPE according with server architecture
	$grant_type  = "password";

	// Don't touch this
	$username = urlencode($username);
	$generated_password = $initial_password . $security_token;

	$post_fields = "grant_type=" . $grant_type;
	$post_fields .= "&client_id=" . $consumer_key;
	$post_fields .= "&client_secret=" . $consumer_secret;
	$post_fields .= "&username=" . $username;
	$post_fields .= "&password=" . $generated_password;

	// Start the sync
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $request_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

	if( !$response = curl_exec($ch) )
	{
		trigger_error(curl_error($ch));
	}

	curl_close($ch);

	// SF messages are by default set to JSON. Can be change it ... easy as pie :)
	// $response - JSON
	// $_response - array()

	$_response = json_decode($response, true);

	// Some checks to be sure that response messages are OK
	if (!isset($_response['access_token']) || $_response['access_token'] == "") {

		die("Error - access token missing from response!");

	}

	if (!isset($_response['instance_url']) || $_response['instance_url'] == "") {

		die("Error - instance URL missing from response!");

	}

	$access_token = $_response["access_token"];
	$instance_url = $_response["instance_url"];

	// construct simple session and store token on it.
	// probably it's better to serialize first ?!

	session_name("GroupHN");

	setcookie('access_token', $access_token);
	setcookie('instance_url', $instance_url);

	// CONNECT END
}

// FETCHING THE DATA SERVICE
$instance_url = $_COOKIE["instance_url"];
$access_token = $_COOKIE["access_token"];


// change this according last REST version
$last_version = "v33.0/";

/*
	$q = "queryAll?q=";
	$query= "SELECT
			id,
			name,
			nom_de_la_mission__c,
			date_demarrage_mission__c,
			date_de_sortie_mission__c,
			Statut_de_la_mission__c,
			Commentaires_Mission__c,
			Opports__c,
			Matricule_Ressource_mission__c,
			ressource__r.id,
			ressource__r.name,
			ressource__r.prenom__c,
			ressource__r.type__c,
			compte__r.id,
			compte__r.name
		FROM
			Mission__c";
 */
 //WHERE Statut_de_la_mission__c <> 'Terminée'"

 /*
 $q = "queryAll?q=";
 $query="SELECT
			 Type__c,
			 Matricule_Salarie__c,
			 Prenom__c,
			 Telephone_mobile__c,
			 Email_Salarie__c,
			 Code_Postal__c,
			 Age_Candidat__c
		FROM
			 Ressources__c";
 */

 /*
  $q = "queryAll?q=";
  $query="SELECT
			 Type__c,
			 Matricule_Salarie__c,
			 Prenom__c,
			 Telephone_mobile__c,
			 Email_Salarie__c,
			 Code_Postal__c,
			 Age_Candidat__c
		FROM
			 Ressources__c";

 */


// SOME OTHER EXAPMPLE BELLOW...

// SOBJECTS DEBUG
// $q = "sobjects";
// $query = "";

// ACCOUNT DESCRIBE
// ================
// $q = "sobjects/Account/describe";
// $query = "";


/*
  $q = "queryAll?q=";
  $query="SELECT
			 Type,
			 Name,
			 Phone,
			 AccountNumber,
			 Nombre_opportunite__c
		FROM
			 Account";
 */

// USER DESCRIBE
// ================
// $q = "sobjects/User/describe";
// $query = "";

			 // $q = "queryAll?q=";
			 // $query="SELECT
			 // Username,
			 // LastName,
			 // FirstName,
			 // MiddleName,
			 // Name,
			 // Email,
			 // Phone,
			 // MobilePhone,
			 // UserType
			 // FROM
			 // User";




// MISSION__C DESCRIBE
// ===================
$q = "sobjects/Mission__c/describe";
$query="";

// RESSOURCE__C
 // $q = "queryAll?q=";
 // $query = "SELECT Id, Name, Type__c  FROM Ressources__c";

// RESSOURCE__C DESCRIBE
// =====================
// $q = "sobjects/Ressources__c/describe";
// $query = "";


// construct the request url
			 $url = $instance_url ;
			 $url .= "/services/data/";
			 $url .= $last_version;
			 $url .= $q;
			 $url .= urlencode($query);

// init the request
			 $curl = curl_init($url);

			 curl_setopt($curl, CURLOPT_HEADER, false);
			 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			 curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: OAuth $access_token"));

			 $responseJson = curl_exec($curl);

// decode the response
			 $response = json_decode($responseJson, true);

			 curl_close($curl);


// R&D part

// var_dump($response);

/*
// Data loop for return SF fields.
foreach($response as $arr){
	if(is_array($arr)){
		foreach($arr as $val){
			if(isset($val['name'])){
				var_dump($val['name']);
			}
		}
	}
}

*/


// Data loop for return raw data
$b = "<br/>";
foreach($response as $arr){
	if(is_array($arr)){
		foreach($arr as $val){
			unset($val['attributes']);
			var_dump($val);
		}
	}
}



die;


// FETCH THE DATA SERVICE END
?>

<!DOCTYPE html>
<html>
<!-- This part is for demonstration purpose only -->
<head>
	<meta charset="UTF-8" />
	<style>
		table {
			display: table;
			border: solid #333;
			border-width: 1px;
			border-collapse: collapse;
			font-family: verdana;
			font-size: 12px;
		}
		table td {
			border: solid #333;
			border-width: 1px;
			padding: 2px;
		}
		.hidden:nth-child(n+2) {
			min-width: 350px;
		}
		.gras{
			font-weight: bold;
		}
		.purple {
			color: #51006C;
		}
		.red{
			color: red;
		}
	</style>
</head>
<body>
	<table>
		<tr>
			<td rowspan="13" style="vertical-align:top">
				<select id='mission_select'>
					<?php foreach ($response["records"] as $r) : ?>
						<option
						value = "<?php echo $r['Id'] ?>"
						data-nameid = "<?php echo $r['Name'] ?>"
						data-name = "<?php echo $r['Nom_de_la_Mission__c'] ?>"
						data-start = "<?php echo $r['Date_demarrage_mission__c'] ?>"
						data-end = "<?php echo $r['Date_de_sortie_Mission__c'] ?>"
						data-status = "<?php echo $r['Statut_de_la_mission__c']?>"
						data-resid = "<?php echo $r['Ressource__r']['Id']?>"
						data-resname = "<?php echo $r['Ressource__r']['Name']?>"
						data-ressurname = "<?php echo $r['Ressource__r']['Prenom__c']?>"
						data-restype = "<?php echo $r['Ressource__r']['Type__c']?>"
						data-clientid = "<?php echo $r['Compte__r']['Id']?>"
						data-clientname = "<?php echo $r['Compte__r']['Name']?>"
						>
						<?php echo $r['Name']?>
					</option>
				<?php endforeach?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Id:</td>
		<td id="mission_id" class="hidden"></td>
	</tr>
	<tr>
		<td>Name:</td>
		<td id="mission_nameid" class="hidden"></td>
	</tr>
	<tr>
		<td>Nom_de_la_Mission__c:</td>
		<td id="mission_name" class="hidden"></td>
	</tr>
	<tr>
		<td>Date_demarrage_mission__c:</td>
		<td id="mission_start" class="hidden"></td>
	</tr>
	<tr>
		<td>Date_de_sortie_Mission__c:</td>
		<td id="mission_end" class="hidden"></td>
	</tr>
	<tr>
		<td>Statut_de_la_mission__c:</td>
		<td id="mission_statut" class="hidden gras red"></td>
	</tr>
	<tr>
		<td>Ressource__r Id:</td>
		<td id="mission_res_id" class="hidden gras"></td>
	</tr>
	<tr>
		<td>Ressource__r Name:</td>
		<td id="mission_res_name" class="hidden gras"></td>
	</tr>
	<tr>
		<td>Ressource__r Prenom__c:</td>
		<td id="mission_res_surname" class="hidden gras"></td>
	</tr>
	<tr>
		<td>Ressource__r Type__c:</td>
		<td id="mission_res_type" class="hidden gras"></td>
	</tr>
	<tr>
		<td>Compte__r Id:</td>
		<td id="mission_client_id" class="hidden gras purple"></td>
	</tr>
	<tr>
		<td>Compte__r Name:</td>
		<td id="mission_client_name" class="hidden gras purple"></td>
	</tr>
</table>



<script type="text/javascript">

	/* 	 JavaScript/Vanilla alternative for construct the select object option on client side */
		//
		// 	var mission_json = JSON.parse(JSON.stringify(<?php echo $responseJson?>));
		// 	var opt = document.createElement("option");

		// 	for (var val in mission_json['records'])  {
		// 		opt += "<option value="
		// 		+ mission_json['records'][val]['Id']
		// 		+ " data-name=" + mission_json['records'][val]['Name']
		// 		+ " data-start=" + mission_json['records'][val]['Date_demarrage_mission__c']
		// 		+ " data-end=" + mission_json['records'][val]['Date_de_sortie_Mission__c']
		// 		+ " data-status=" + mission_json['records'][val]['Statut_de_la_mission__c']
		// 		+ " data-resid=" + mission_json['records'][val]['Ressource__r']['Id']
		// 		+ " data-resname=" + mission_json['records'][val]['Ressource__r']['Name']
		// 		+ " data-ressurname" + mission_json['records'][val]['Ressource__r']['Prenom__c']
		// //		+ " data-clientid" + mission_json['records'][val]['Compte__r']['Id']
		// //		+ " data-clientname" + mission_json['records'][val]['Compte__r']['Name']
		// 		+ ">"
		// 		+ mission_json['records'][val]['Name']
		// 		+ "</option>"

		// 		// opt.value = mission_json['records'][val]['Name'];
		// 	}

		// 	document.getElementById("mission_select").innerHTML = opt;

		var mission_select = document.querySelector("#mission_select");
		var all_hidden = document.getElementsByClassName("hidden");

		mission_select.addEventListener("change", function(){
			for (var i=0; i<all_hidden.length; i++) {
				all_hidden[i].innerHtml = "";
			}
			document.querySelector("#mission_id").innerHTML = this.value;
			document.querySelector("#mission_nameid").innerHTML = this.options[this.selectedIndex].dataset.nameid;
			document.querySelector("#mission_name").innerHTML = this.options[this.selectedIndex].dataset.name;
			document.querySelector("#mission_start").innerHTML = this.options[this.selectedIndex].dataset.start;
			document.querySelector("#mission_end").innerHTML = this.options[this.selectedIndex].dataset.end;
			document.querySelector("#mission_statut").innerHTML = this.options[this.selectedIndex].dataset.status;
			document.querySelector("#mission_res_id").innerHTML = this.options[this.selectedIndex].dataset.resid;
			document.querySelector("#mission_res_name").innerHTML = this.options[this.selectedIndex].dataset.resname;
			document.querySelector("#mission_res_surname").innerHTML = this.options[this.selectedIndex].dataset.ressurname;
			document.querySelector("#mission_res_type").innerHTML = this.options[this.selectedIndex].dataset.restype;
			document.querySelector("#mission_client_id").innerHTML = this.options[this.selectedIndex].dataset.clientid;
			document.querySelector("#mission_client_name").innerHTML = this.options[this.selectedIndex].dataset.clientname;
		});

	</script>
</body>
</html>