<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Agenda</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('./css/fullcalendar.css') }}">
</head>
<style>
	.input_error_active {
		display: block;
		padding: 0;
		margin: 0;
		color: #ec0505;
	}
</style>

<body>
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
@if (Route::has('login'))
    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
        @auth
            <a href="{{ url('/home') }}" class="text-sm text-gray-700 underline">Home</a>
        @else
            <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
            @endif
        @endif
    </div>
@endif
</div>
	<!-- modal para citas nuevas con javascript -->
	<div class="modal fade" id="nuevaCita" tabindex="-1" aria-labelledby="nuevaCita" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="titleModal"></h5>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="need-validation" id="apptModal" action="" method="POST">
						@csrf
						<input type="hidden" name="idEvent" id="idEvent">
						<!-- Busco paciente -->
						<!-- Motivo de la cita -->
						<div class="form-group">
							<label for="title">Motivo</label>
							<input type="text" name="title" id="aptTitle" class="form-control" placeholder="Motivo de la consulta">
							<h5 class="input_error_active" id="errorTitle" style="color: red"></h5>

						</div>
						<!-- Datos de la cita-->
						<div class="form-row">
							<!-- Fecha de la cita -->
							<div class="col">
								<label for="aptDate">Fecha</label>
								<input type="date" name="aptDate" id="aptDate" class="form-control" disabled>
								<h5 class="" id="msgVacio_aptDate" style="color: red"></h5>
							</div>
							<div class="col">
								<label for="aptHour">Hora</label>
								<input type="time" name="aptHour" id="aptHour" class="form-control" disabled>
								<h5 class="" id="msgVacio_aptHour" style="color: red"></h5>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" id='btnCancel' data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-danger d-none" id="btnEliminar">Eliminar</button>
					<button type="button" class="btn btn-warning d-none" id="btnModificar">Modificar</button>
					<button type="button" class="btn btn-success" id="btnAgregar">Reservar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- contenedor del calendario -->
	<div id="calendar"></div>
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	<script src="{{ asset('./js/fullcalendar.js') }}"></script>
	<script>
		window.addEventListener('DOMContentLoaded', initCalendar)

		//botones modal
		let btnAgregar = document.querySelector('#btnAgregar')
		let btnModificar = document.querySelector('#btnModificar')
		let btnEliminar = document.querySelector('#btnEliminar')
		let btnCancel = document.querySelector('#btnCancel')

		//
		const form = document.querySelector('#apptModal')
		const token = document.getElementsByName('_token')

		// inputs  modal
		let motivoAppt = document.querySelector('#aptTitle')
		let dateAppt = document.querySelector('#aptDate')
		let hourAppt = document.querySelector('#aptHour')
		let idAppt = document.querySelector('#idEvent')

		// borrar datos si se hace click en el boton cancelar
		btnCancel.addEventListener('click', () => {
			//	limpiarFormulario()
		})
		// elemento Calendario
		let calendarEl = document.querySelector('#calendar')

		let objCalendar = initCalendar()

		function initCalendar() {
			let calendar = new FullCalendar.Calendar(calendarEl, {
				locale: 'es',
				initialView: 'timeGridWeek',
				headerToolbar: {
					left: 'prev next today',
					center: 'title',
					right: 'dayGridMonth timeGridWeek timeGridDay'
				},
				eventSources: [{
					url: 'appointment'
				}],
				dateClick: function(info) {
					cambiarTituloModal("Reservar cita")
					const clickCalendar = info.dateStr.split('T')
					dateAppt.value = clickCalendar[0]
					hourAppt.value = clickCalendar[1].substr(0, 8)
					$('#nuevaCita').modal()
				},
				eventClick: function(info) {
					btnModificar.classList.remove('d-none')
					btnEliminar.classList.remove('d-none')
					let idAppt = document.querySelector('#idEvent')
					idAppt.value = info.event.id

					motivoAppt.value = info.event.title
					let fechaHora = info.event.startStr.split("T")

					dateAppt.value = fechaHora[0]
					hourAppt.value = fechaHora[1].substr(0, 5)

					$('#nuevaCita').modal('toggle')
				}
			})
			calendar.setOption('locale', 'Es')
			calendar.render()

			return calendar
		}

		function cambiarTituloModal(title) {
			let modal = document.querySelector('#titleModal')
			modal.innerText = title
		}

		async function guardarDatos(path = '', metod = 'POST') {
			const objAppointment = {
				title: motivoAppt.value,
				start: dateAppt.value + ' ' + hourAppt.value,
				end: dateAppt.value + ' ' + hourAppt.value,
			}
			let url = 'appointment' + path
			try {
				let peticion = await fetch(url, {
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': token[0].defaultValue
					},
					method: metod,
					body: JSON.stringify(objAppointment)
				})
				let response = await peticion.json()
				if (response.errors !== undefined) {
					document.querySelector("#errorTitle").innerText = response.errors.title
					hideErrors('Title')
					return;
				} else {
					limpiarFormulario()
					objCalendar.refetchEvents()
					$('#nuevaCita').modal('toggle')
					return
				}
			} catch (error) {
				console.error(error);
			}
		}

		btnAgregar.addEventListener('click', function() {
			guardarDatos()
			return false;
		})

		btnModificar.addEventListener('click', async (e) => {
			guardarDatos(inf)
			console.info(e)
		})

		btnEliminar.addEventListener('click', async () => {

			let sendData = await fetch(`appointment/${idAppt.value}`, {
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': token[0].defaultValue
				},
				method: 'DELETE',
				body: {
					id: idAppt.value
				}
			})
			if (sendData.status === 204 || sendData.status === 205) {
				limpiarFormulario()
				objCalendar.refetchEvents()
				$('#nuevaCita').modal('toggle')
				return;
			}
			return sendData.json();
		})

		/* function guardaAjax() {
			let count = 0;

			const objAppointment = {
				title: motivoAppt.value,
				start: dateAppt.value + ' ' + hourAppt.value,
				end: dateAppt.value + ' ' + hourAppt.value,
			}
			$.ajax({
				url: '/appointment',
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': token[0].defaultValue
				},
				data: objAppointment,
				success: function(msg) {
					console.log('veces enviado al servidor: ' + count++);
					objCalendar.refetchEvents()
					limpiarFormulario()
					$('#nuevaCita').modal('toggle')
					return msg
				},
				error: function(error) {
					console.error('pasaesto: ', error);
				}
			})
		} Por ahora no la uso, si se mantiene comentada se puede borrar*/

		function limpiarFormulario() {
			$('#aptTitle').val('');
			$('#aptDate').val('');
			$('#aptHour').val('');
		}

		function hideErrors(field) {
			const input = document.querySelector(`#apt${field}`)
			input.addEventListener('input', () => {
				if (input.value !== '') {
					return document.querySelector(`#error${field}`).innerText = ''
				}
				document.querySelector(`#error${field}`).innerText = 'Debes colocar el motivo de tu consulta'
			})
		}
	</script>
</body>

</html>

<?php
$sesionCompleta = 4500;
$mediaSesion = 2700;
$vendajeNM = 900;
?>