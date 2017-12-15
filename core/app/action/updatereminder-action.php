<?php
if(count($_POST)>0){
	$user = ReservationData::getById($_POST["id"]);
	$user->reminder_id = $_POST["reminder_id"];

	$user->updateReminder();

Core::alert("Estado acutalizado");
}
?>