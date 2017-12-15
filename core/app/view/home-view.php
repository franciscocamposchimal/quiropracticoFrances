<?php
$thejson=null;
$events = ReservationData::getEvery();
foreach($events as $event){
	$pacients= ReservationData::getPacients($event->pacient_id);

	$thejson[] = array("title"=>$pacients[0]->name,"url"=>"./?view=editreservation&id=".$event->id,"start"=>$event->date_at."T".$event->time_at,"end"=>$event->date_at."T".$event->end_time);
	
	$DateArray[] = array("id"=>$event->pacient_id,"date"=>$event->date_at."T".$event->time_at, "nombre"=>$pacients[0]->name, "tel"=>$pacients[0]->phone, "status"=>$event->status_id, "reminder"=>$event->reminder_id);
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

		function sumarDias(fecha, dias){
			fecha.toLocaleDateString();
            fecha.setDate(fecha.getDate() + dias);
            return fecha;
		}
        function dateConvert(date){
			const dateOut = new Date(date);
			return dateOut.toLocaleDateString(); 
		}

		function dateButtons(date,id,reminder){
			var btn = "";
			if(date === dateConvert(new Date())){
				if(parseInt(reminder) <= 2){
					return btn += `<button id="msnj" type="button" class="btn btn-success btn-sm btn-mnj"value="3" data-id="${id}">Enviar mensaje</button>`;
				}else{
					return btn += `<i class="fa fa-check fa-4x btn-call text-success" aria-hidden="true"></i>`;
				}
			
			}else{
				if(parseInt(reminder) <= 1){
				    return btn += `<button id="call" type="button" class="btn btn-primary btn-sm btn-call" value="2" data-id="${id}">Llamada pendiente</button>`;
				}else{
					return btn += `<i class="fa fa-check fa-4x btn-call text-success" aria-hidden="true"></i>`;
				}
			}
		}
          //console.log(arrayPacient);
		var citasTodayAndTomorrow = $.grep(arrayPacient, (v) => {
			return (dateConvert(v.date)  === dateConvert(new Date()) && parseInt(v.status ) <= 1) || (dateConvert(v.date) === dateConvert(sumarDias(new Date (),1)) && parseInt(v.status ) <= 1 );
		});

          //console.log(citasTodayAndTomorrow.length);

		var cardStart ='<ul class="list-group list-group-flush">';
				   
		var pacients = $.map(citasTodayAndTomorrow, function(value) {
			var  boton = dateButtons(dateConvert(value.date),value.id,value.reminder);
			return(`<li id="li-${value.id}" class="list-group-item">`+
				   `<p><strong>Nombre: </strong>${value.nombre}</p>`+
				   `<p><strong>Teléfono: </strong>${value.tel}</p>`+
				   `<p><strong>Fecha: </strong>${dateConvert(value.date)}</p>`+
				   boton+
				   '</li>');
		});
            cardEnd = '</ul>';
			cardStart+= pacients+cardEnd;

			if(citasTodayAndTomorrow.length == 0){
			   $('#bell').remove();
			}else{
			   $('#mark').text(citasTodayAndTomorrow.length);
			}
			
		$('#bell').on('click',alert);

		function alert(){
			if(citasTodayAndTomorrow.length >= 1){
				
			alertify.confirm('Recordatorios del día',`${cardStart}`,
			function(){
				alertify.success('Ok');
			},
			function(){
				alertify.error('Cancel');
			})
		   }

		$('#msnj').on('click',updateStatusMsnj);
		$('#call').on('click',updateStatusCall);
		}	

		
		function updateStatusCall() {

			var reminder_id = $('#call').val();
			var id = $('#call').data('id');
			request = $.ajax({
            url: "./?action=updatereminder",
            type: "post",
            data: `&id=${id}&reminder_id=${reminder_id}`
            });

			request.done(function (response, textStatus, jqXHR){
              alert('Actualizado con éxito!!');
			  $('#call').remove();
			  $(`#li-${id}`).append(`<i class="fa fa-check fa-4x btn-call text-success" aria-hidden="true"></i>`);
            });
			request.fail(function (jqXHR, textStatus, errorThrown){
			   console.error(
                 "The following error occurred: "+
                  textStatus, errorThrown
                );
            });
		}
		function updateStatusMsnj() {
			var reminder_id = $('#msnj').val();
			var id = $('#msnj').data('id');
			request = $.ajax({
            url: "./?action=updatereminder",
            type: "post",
            data: `&id=${id}&reminder_id=${reminder_id}`
            });

			request.done(function (response, textStatus, jqXHR){
              alert('Actualizado con éxito!!');
			  $('#msnj').remove();
			  $(`#li-${id}`).append(`<i class="fa fa-check fa-4x btn-call text-success" aria-hidden="true"></i>`);
            });
			request.fail(function (jqXHR, textStatus, errorThrown){
			   console.error(
                 "The following error occurred: "+
                  textStatus, errorThrown
                );
            });
		}
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