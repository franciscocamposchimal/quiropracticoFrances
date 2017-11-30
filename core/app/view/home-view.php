<?php
$thejson=null;
$array=null;
$events = ReservationData::getEvery();
//$pacients = ReservationData::getPacients();
foreach($events as $event){
	$thejson[] = array("title"=>$event->title,"url"=>"./?view=editreservation&id=".$event->id,"start"=>$event->date_at."T".$event->time_at,"end"=>$event->date_at."T".$event->end_time);
	$pacients= ReservationData::getPacients($event->pacient_id);
	$DateArray[] = array("date"=>$event->date_at."T".$event->time_at, "nombre"=>$pacients[0]->name);
}
?>
<script>
	$(document).ready(function() {


		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'agendaDay,agendaWeek,month'
			},
			locale: 'es',
			defaultDate: '<?php echo date('Y-m-d');?>',
			monthNames:['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
						 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			dayNames:['Domingo', 'Lunes', 'Martes', 'Miércoles',
					  'Jueves', 'Viernes', 'Sábado'],
			dayNamesShort:['Dom', 'Lun', 'Mar', 'Mier', 'Jue', 'Vier', 'Sab'],
			navLinks: true,
			editable: true,
			selectable: true,
			eventLimit: true, // allow "more" link when too many events
			events: <?php echo json_encode($thejson); ?>
		});

		var arrayPacient = <?php echo json_encode($DateArray); ?>;

		const date = new Date();
		const timeToday = date.toLocaleTimeString();
		const dateTodays = date.toLocaleDateString();

		function sumarDias(fecha, dias){
            fecha.setDate(fecha.getDate() + dias);
            return fecha;
		}

		const diaAnterior = sumarDias(date, -1);

		arrayPacient.forEach(element => {
			console.log('Dia: ', element.date);
			console.log('Nombre: ', element.nombre);
		});

	});

</script>


<div class="row">
<div class="col-md-12">
<div class="card">
  <div class="card-header" data-background-color="blue">
      <h4 class="title">Calendario de Citas</h4>
  </div>
  <div class="card-content table-responsive">
<div id="calendar"></div>
</div>
</div>
</div>
</div>
