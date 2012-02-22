<?php
// Connect to the database
include_once("constants.php");

// Load the initial data from the database
if(isset($_GET['acronym'])){
	$acronym = strtoupper($_GET['acronym']);
}
else{// If there is no acronym, create one
	// 3 - 7 characters
	$size = rand(3, 7);
	$acronym = "";
	$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); 
	for($i = 1; $i <= $size; $i++){
		$acronym .= strtoupper($abc[rand(0,25)]);
	}
}

// Get phrases for that acronym
$res = mysql_query("SELECT * FROM acro WHERE acronym = '$acronym'");

// Prepare the html for the list item
$phrases = "";
while($phrase = mysql_fetch_assoc($res)){
	$phrases .= "<tr><td>$phrase[id]</td><td>$phrase[body]</td><td>$phrase[votes]</td></tr>";
}

// Get previous rounds
$res = mysql_query("SELECT acronym FROM acro GROUP BY acronym");
$previousAcronyms = "";
while($a = mysql_fetch_assoc($res)){
	$previousAcronyms .= "<a href='game.php?acronym=$a[acronym]'>$a[acronym]</a> ";
}

// Prepare the instructions
if($twilio_number != "XXX-XXX-XXXX")
	$instructions = "Text $twilio_number a phrase that matches this acronym,<br>then text the ID number of the best one to vote for it!";
else
	$instructions = "Use the SMS Simulator to send a phrase that matches this acronym,<br>then send the ID number of the best one to vote for it!";

// include_once('Pubnub.php');
// $pubnub = new Pubnub( $pn_pubkey, $pn_subkey );
// print_r( $pubnub->history(array(
// 	"channel"=>"$acronym",
// 	"limit"=>1
// )));


?><html>
<head>
	<title>Acro</title>
	<link href='styles.css' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
	<div id="header-bar">Acro (<a href="https://github.com/smashcubed/Acro">GitHub</a>)</div>
	<div id="page-wrapper">
		<div class="acronym-wrapper">
			<span align="center" class="acronym"><?php echo $acronym; ?></span>
		</div>
		<div class="instructions-wrapper">
			<span class="instructions"><?php echo $instructions; ?></span>
		</div>
		<div class="table-wrapper">
			<table>
				<thead class="grad"><tr><th>ID</th><th>Phrase</th><th>Votes</th></tr></thead>
				<tbody id="phrases"><?php echo $phrases; ?></tbody>
			</table>
		</div>
		<button onClick="var loc = window.location.href; window.location = loc.split('?')[0];">New phrase</button>
		<div class="previousAcronyms-wrapper">
			<div class="previousAcronyms"><?php echo $previousAcronyms; ?></div>
		</div>
	</div>
	<div class="smsBox">
		<span id="smsTitle">SMS Simulator</span><br>
		<input id="smsBody" type="text" /><br>
		<button id="smsSubmit" onClick="sendSMS()">Send SMS</button><br>
		<span id="smsResponse"></span>
	</div>
	<script>
	function sendSMS(){
		var Body = $("#smsBody").val();
		var From = "web" + Math.floor(Math.random()*100);
		$.ajax({
		  url: "sms.php?From=" + From + "&Body=" + encodeURIComponent(Body),
		  context: document.body,
		  success: function(data){
			  console.log(data);
			  $("#smsBody").val("")
			  $("#smsResponse").html(data)
		  }
		});
	}
	function getParameterByName(name) {
		var match = RegExp('[?&]' + name + '=([^&]*)')
                    .exec(window.location.search);
    	return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
	}

	</script>
	<div pub-key="<?php echo $pn_pubkey; ?>" sub-key="<?php echo $pn_subkey; ?>" ssl="off" origin="pubsub.pubnub.com" id="pubnub"></div>
	<script src="http://cdn.pubnub.com/pubnub-3.1.min.js"></script>
	<script>(function(){

		var acronym = "<?php echo $acronym; ?>";
		// Update phrases from the most recent message from the history
		// PUBNUB.history({
		// 	channel: acronym,
		// 	limit: 1
		// }, function(messages){
		// 	console.log(messages)
		// 	if(messages[0])
		// 		$("#phrases").html(messages[0].phrases)
		// })

	    // LISTEN FOR MESSAGES
	    PUBNUB.subscribe({
		    channel  : acronym,
		    callback : function(message) { 
				$("#phrases").html(message.phrases)
			}
		})
	})();</script>
</body>
</html>