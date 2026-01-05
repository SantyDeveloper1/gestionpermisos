<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Evita que Chrome guarde la página en caché -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<title>Admin escolar| Dashboard</title>
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css')}}">
	<!-- Tempusdominus Bootstrap 4 -->
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
	<!-- iCheck -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
	<!-- JQVMap -->
	<!--<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/jqvmap/jqvmap.min.css') }}">-->
	<!-- overlayScrollbars -->
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
	<!-- summernote -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/summernote/summernote-bs4.min.css') }}">
	<!-- Bootstrap Color Picker -->
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
	<!-- Select2 -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/select2/css/select2.min.css') }}">
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
	<!-- Bootstrap4 Duallistbox -->
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
	<!-- BS Stepper -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/bs-stepper/css/bs-stepper.min.css') }}">
	<!-- dropzonejs -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/dropzone/min/dropzone.min.css') }}">
	<!-- DataTables -->
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
	<link rel="stylesheet"
		href="{{ asset('plugins/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/dist/css/adminlte.min.css') }}">

	<!-- fullCalendar -->
	<link rel="stylesheet" href="{{ asset('plugins/adminlte/plugins/fullcalendar/main.css') }}">

	<link rel="stylesheet" href="{{ asset('plugins/pnotify/pnotify.custom.min.css') }}">
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
	<div class="wrapper">

		<!-- Preloader -->
		<!--<div class="preloader flex-column justify-content-center align-items-center">
		<img class="animation__shake" src="{{asset('plugins/adminlte/dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
	</div>-->
		<!-- Loader Animado Profesional -->
		<div id="loader" class="loader">
			<div class="spinner-wrapper">
				<div class="spinner"></div>
			</div>
		</div>

		<style>
			/* Fondo suave con degradado */
			.loader {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: linear-gradient(135deg, #f9fafc 0%, #e6ecf5 100%);
				display: flex;
				justify-content: center;
				align-items: center;
				z-index: 9999;
				transition: opacity 0.8s ease, visibility 0.8s ease;
			}

			.loader.hidden {
				opacity: 0;
				visibility: hidden;
			}

			/* Contenedor centrado */
			.spinner-wrapper {
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 18px;
			}

			/* Spinner profesional (línea fina y sombra sutil) */
			.spinner {
				width: 70px;
				height: 70px;
				border: 3px solid transparent;
				border-top: 3px solid #007bff;
				border-radius: 50%;
				animation: spin 1s linear infinite, glow 2s ease-in-out infinite alternate;
				position: relative;
				box-shadow: 0 0 15px rgba(0, 123, 255, 0.25);
			}

			/* Aro interno suave */
			.spinner::before {
				content: "";
				position: absolute;
				top: 6px;
				left: 6px;
				right: 6px;
				bottom: 6px;
				border-radius: 50%;
				border: 2px solid rgba(0, 123, 255, 0.15);
			}

			/* Animación de giro */
			@keyframes spin {
				to {
					transform: rotate(360deg);
				}
			}

			/* Efecto de resplandor suave */
			@keyframes glow {
				0% {
					box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
				}

				100% {
					box-shadow: 0 0 20px rgba(0, 123, 255, 0.6);
				}
			}

			.disabled-link {
				pointer-events: none;
				/* Evita clic */
				opacity: 0.8;
				/* Se ve desactivado */
				cursor: not-allowed;
				/* Cursor bloqueado */
			}
		</style>

		<script>
			window.addEventListener("load", () => {
				const loader = document.getElementById("loader");
				if (loader) {
					setTimeout(() => {
						loader.classList.add("hidden");
					}, 500); // mantiene visible medio segundo
				}
			});
		</script>
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="index3.html" class="nav-link">Home</a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="#" class="nav-link">Contact</a>
				</li>
			</ul>

			<!-- Right navbar links -->
			<ul class="navbar-nav ml-auto">
				<!-- Navbar Search -->
				<li class="nav-item">
					<a class="nav-link" data-widget="navbar-search" href="#" role="button">
						<i class="fas fa-search"></i>
					</a>
					<div class="navbar-search-block">
						<form class="form-inline">
							<div class="input-group input-group-sm">
								<input class="form-control form-control-navbar" type="search" placeholder="Search"
									aria-label="Search">
								<div class="input-group-append">
									<button class="btn btn-navbar" type="submit">
										<i class="fas fa-search"></i>
									</button>
									<button class="btn btn-navbar" type="button" data-widget="navbar-search">
										<i class="fas fa-times"></i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</li>

				<!-- Messages Dropdown Menu -->
				<li class="nav-item dropdown">

				<li class="nav-item">
					<a class="nav-link" data-widget="fullscreen" href="#" role="button">
						<i class="fas fa-expand-arrows-alt"></i>
					</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
						<div class="image mr-2">
							<img src="{{asset('plugins/adminlte/dist/img/user2-160x160.jpg')}}"
								class="img-circle elevation-2" alt="User Image" style="width:30px; height:30px;">
						</div>
					</a>

					<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<span class="dropdown-item dropdown-header">Cuenta de Usuario</span>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-user mr-2"></i> Perfil
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-cog mr-2"></i> Configuración
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item" id="logout-btn">
							<i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
						</a>

						<script>
							// Esperar a que el DOM y jQuery estén listos
							document.addEventListener('DOMContentLoaded', function () {
								// CSRF Token setup
								$.ajaxSetup({
									headers: {
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
									}
								});

								// Logout con AJAX
								document.getElementById('logout-btn').addEventListener('click', function (e) {
									e.preventDefault();

									swal({
										title: 'Cerrar sesión',
										text: '¿Está seguro que desea cerrar sesión?',
										icon: 'warning',
										buttons: ['Cancelar', 'Sí, cerrar sesión'],
										dangerMode: true
									}).then((confirmed) => {
										if (!confirmed) return;

										// Hacer logout con AJAX
										$.ajax({
											url: '{{ route("logout") }}',
											type: 'POST',
											dataType: 'json',
											success: function (response) {
												if (response.success) {
													// Limpiar sesión del navegador
													sessionStorage.clear();
													localStorage.clear();

													// Mostrar notificación
													new PNotify({
														title: 'Sesión cerrada',
														text: 'Ha cerrado sesión correctamente.',
														type: 'success',
														delay: 1000
													});

													// Redirigir usando replace para evitar retroceso
													setTimeout(function () {
														window.location.replace(response.redirect || '/login');
													}, 1000);
												}
											},
											error: function () {
												// Si falla AJAX, hacer logout tradicional
												window.location.replace('/login');
											}
										});
									});
								});
							});
						</script>
					</div>
				</li>
		</nav>
		<!-- /.navbar -->

		<!-- Main Sidebar Container -->
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<!-- Brand Logo -->
			<a href="index3.html" class="brand-link">
				<img src="{{ asset('plugins/adminlte/dist/img/dais.png') }}" alt="Logo EPIIS"
					style="width: 100%; height: 28px; object-fit: contain;">
			</a>

			<!-- Sidebar -->
			<div class="sidebar">
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="{{asset('plugins/adminlte/dist/img/logoEPIIS.png')}}" class="img-circle elevation-2"
							alt="User Image">
					</div>
					<div class="info">
						<a href="#" class="d-block">
							{{ Auth::check() ? Auth::user()->name : 'ADMINISTRADOR' }}
						</a>
					</div>
				</div>

				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
						data-accordion="false">
						<!-- Add icons to the links using the .nav-icon class
							 with font-awesome or any other icon font library -->
						<li class="nav-item {{ request()->is('/') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
								<i class="nav-icon fas fa-tachometer-alt"></i>
								<p>
									Dashboard
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
										<i class="far fa-circle nav-icon"></i>
										<p>Página principal</p>
									</a>
								</li>
							</ul>
						</li>
						{{-- ASIGNATURA --}}
						<li class="nav-item {{ request()->is('admin/usuarios*') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('admin/usuarios*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-book"></i>
								<p>
									Asignar Roles
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>

							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('admin/usuarios') }}"
										class="nav-link {{ request()->is('admin/usuarios') ? 'active' : '' }}">
										<i class="nav-icon fas fa-list"></i>
										<p>Lista de usuarios</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="{{ url('admin/usuarios/asignar_roles') }}"
										class="nav-link {{ request()->is('admin/usuarios/asignar_roles') ? 'active' : '' }}">
										<i class="nav-icon fas fa-plus-circle"></i>
										<p>Asignar Roles</p>
									</a>
								</li>
							</ul>
						</li>
						
						<li class="nav-item {{ request()->is('admin/docente*') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('admin/docente*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-user-tie"></i>
								<p>
									Gestión de Docentes
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>

							<ul class="nav nav-treeview">

								<!-- Lista de Docentes -->
								<li class="nav-item">
									<a href="{{ url('admin/docente') }}"
										class="nav-link {{ request()->is('admin/docente') ? 'active' : '' }}">
										<i class="fas fa-id-badge nav-icon"></i>
										<p>Lista de Docentes</p>
									</a>
								</li>
								<!-- Grados Académicos -->
								<li class="nav-item">
									<a href="{{ url('admin/docente/grados-academicos/insert') }}"
										class="nav-link {{ request()->is('admin/docente/grados-academicos*') ? 'active' : '' }}">
										<i class="fas fa-graduation-cap nav-icon"></i>
										<p>Grados Académicos</p>
									</a>
								</li>

								<!-- Categorías Docente -->
								<li class="nav-item">
									<a href="{{ url('admin/docente/categoria-docente/insert') }}"
										class="nav-link {{ request()->is('admin/docente/categoria-docente/insert*') ? 'active' : '' }}">
										<i class="fas fa-layer-group nav-icon"></i>
										<p>Categorías Docente</p>
									</a>
								</li>

								<!-- Tipos de Contrato Docente -->
								<li class="nav-item">
									<a href="{{ url('admin/docente/tipo_contrato/insert') }}"
										class="nav-link {{ request()->is('admin/docente/tipo_contrato/insert*') ? 'active' : '' }}">
										<i class="fas fa-file-contract nav-icon"></i>
										<p>Tipos de Contrato</p>
									</a>
								</li>
							</ul>
						</li>
						{{-- TIPO PERMISO --}}
						<li class="nav-item {{ request()->is('admin/tipo_permiso*') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('admin/tipo_permiso*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-tags"></i>
								<p>
									Tipo Permiso
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>

							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('admin/tipo_permiso') }}"
										class="nav-link {{ request()->is('admin/tipo_permiso') ? 'active' : '' }}">
										<i class="nav-icon fas fa-list"></i>
										<p>Tipo Permiso</p>
									</a>
								</li>
							</ul>
						</li>
						{{-- PERMISO --}}
						<li class="nav-item {{ request()->is('admin/permiso*') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('admin/permiso*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-file-signature"></i>
								<p>
									Permiso
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>

							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('admin/permiso') }}"
										class="nav-link {{ request()->is('admin/permiso') ? 'active' : '' }}">
										<i class="nav-icon fas fa-list"></i>
										<p>Permiso</p>
									</a>
								</li>
							</ul>
						</li>
						{{-- PLAN DE RECUPERACIÓN --}}
						<li class="nav-item {{ request()->is('admin/plan_recuperacion*') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('admin/plan_recuperacion*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-clipboard-list"></i>
								<p>
									Plan de Recuperación
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>

							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('admin/plan_recuperacion') }}"
										class="nav-link {{ request()->is('admin/plan_recuperacion') ? 'active' : '' }}">
										<i class="nav-icon fas fa-list"></i>
										<p>Plan de Recuperación</p>
									</a>
								</li>
							</ul>
						</li>
						{{-- SESIÓN DE RECUPERACIÓN --}}
						<li class="nav-item {{ request()->is('admin/sesion_recuperacion*') ? 'menu-open' : '' }}">
							<a href="#" class="nav-link {{ request()->is('admin/sesion_recuperacion*') ? 'active' : '' }}">
								<i class="nav-icon fas fa-calendar-check"></i>
								<p>
									Sesión de Recuperación
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>

							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="{{ url('admin/sesion_recuperacion') }}"
										class="nav-link {{ request()->is('admin/sesion_recuperacion') ? 'active' : '' }}">
										<i class="nav-icon fas fa-list"></i>
										<p>Sesión de Recuperación</p>
									</a>
								</li>
							</ul>
						</li>

					</ul>
				</nav>
				<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0">@yield('titleGeneral')</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active">Dashboard v1</li>
							</ol>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">
					<div class="row" style="background-color: #ffffff;">
						<div class="col-md-12 pt-3 pb-3">
							@yield('sectionGeneral')
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		<footer class="main-footer">
			<strong>Copyright &copy; 2025-{{date('Y')}}</strong>
			All rights reserved.
			<div class="float-right d-none d-sm-inline-block">
				<b>Version</b> 5.3.0
			</div>
		</footer>

		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Control sidebar content goes here -->
		</aside>
		<!-- /.control-sidebar -->
	</div>
	<!-- ./wrapper -->

	<!-- jQuery -->
	<script src="{{ asset('plugins/adminlte/plugins/jquery/jquery.min.js') }}"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="{{ asset('plugins/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
		$.widget.bridge('uibutton', $.ui.button)
	</script>
	<!-- Bootstrap 4 -->
	<script src="{{ asset('plugins/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
	<!-- ChartJS -->
	<script src="{{ asset('plugins/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
	<!-- Sparkline -->
	<script src="{{ asset('plugins/adminlte/plugins/sparklines/sparkline.js') }}"></script>
	<!-- JQVMap -->
	<script src="{{ asset('plugins/adminlte/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
	<!-- jQuery Knob Chart -->
	<script src="{{ asset('plugins/adminlte/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
	<!-- daterangepicker -->
	<script src="{{ asset('plugins/adminlte/plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
	<!-- Tempusdominus Bootstrap 4 -->
	<script src="{{ asset('plugins/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
	</script>
	<!-- Summernote -->
	<script src="{{ asset('plugins/adminlte/plugins/summernote/summernote-bs4.min.js') }}"></script>
	<!-- overlayScrollbars -->
	<script src="{{ asset('plugins/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

	<!-- Select2 -->
	<script src="{{ asset('plugins/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
	<!-- Bootstrap4 Duallistbox -->
	<script src="{{ asset('plugins/adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}">
	</script>
	<script src="{{ asset('plugins/adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
	<!-- bootstrap color picker -->
	<script
		src="{{ asset('plugins/adminlte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
	<!-- Bootstrap Switch -->
	<script src="{{ asset('plugins/adminlte/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
	<!-- BS-Stepper -->
	<script src="{{ asset('plugins/adminlte/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
	<!-- dropzonejs -->
	<script src="{{ asset('plugins/adminlte/plugins/dropzone/min/dropzone.min.js') }}"></script>

	<!-- DataTables  & Plugins -->
	<script src="{{ asset('plugins/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
	<script
		src="{{ asset('plugins/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
	<script
		src="{{ asset('plugins/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/jszip/jszip.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
	<script src="{{ asset('plugins/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

	<!-- fullCalendar 2.2.5 -->
	<script src="{{ asset('plugins/adminlte/plugins/fullcalendar/main.js') }}"></script>
	<!-- ESTE es el correcto para tu versión -->
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>

	<!-- AdminLTE App -->
	<script src="{{ asset('plugins/adminlte/dist/js/adminlte.min.js') }}"></script>

	<script src="{{ asset('plugins/formvalidation/formValidation.min.js') }}"></script>
	<script src="{{ asset('plugins/formvalidation/bootstrap.validation.min.js') }}"></script>

	<script src="{{ asset('plugins/pnotify/pnotify.custom.min.js') }}"></script>
	<script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>

	<script>
		var _urlBase = '{{url('/')}}';

		@if(Session::has('listMessage'))
			@foreach(Session::get('listMessage') as $value)
				new PNotify(
					{
						title: '{{Session::get('typeMessage') == 'error' ? 'No se pudo proceder!' : 'Correcto!'}}',
						text: '{{$value}}',
						type: '{{Session::get('typeMessage')}}'
					});
			@endforeach
		@endif
	</script>


	<!-- SCRIPT PARA DATA TABLES para grados docente-->
	<script>
		$(document).ready(function () {
			$('#tablaExample2').DataTable({
				responsive: true,
				autoWidth: false,
				pageLength: 10,
				language: {
					decimal: "",
					emptyTable: "No hay datos disponibles en la tabla",
					info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
					infoEmpty: "Mostrando 0 a 0 de 0 entradas",
					infoFiltered: "(filtrado de _MAX_ entradas totales)",
					lengthMenu: "Mostrar _MENU_ entradas",
					loadingRecords: "Cargando...",
					processing: "Procesando...",
					search: "Buscar:",
					zeroRecords: "No se encontraron registros coincidentes",
					paginate: {
						first: "Primero",
						last: "Último",
						next: "Siguiente",
						previous: "Anterior"
					}
				}
			}).buttons().container().appendTo('#tablaExample2_wrapper .col-md-6:eq(0)');
		});

	</script>

	<!-- ✅ Toast Notification - Solo se muestra después del login -->
	@if(session('show_login_toast'))
		<div id="toast" class="toast">
			<div class="toast-content">
				<div class="icon">
					<i class="fas fa-check-circle"></i>
				</div>
				<div class="message">
					<span class="title">BIENVENIDO</span>
					<span class="text">Has iniciado sesión correctamente.</span>
				</div>
				<span class="close">&times;</span>
			</div>
			<div class="progress"></div>
		</div>
	@endif

	<style>
		/* ===== TOAST ESTILO ===== */
		.toast {
			position: fixed;
			top: 20px;
			right: 20px;
			background: #fff;
			border-left: 5px solid #2ecc71;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
			border-radius: 6px;
			overflow: hidden;
			width: 320px;
			opacity: 0;
			transform: translateY(-20px);
			pointer-events: none;
			transition: opacity 0.5s ease, transform 0.5s ease;
			font-family: "Poppins", sans-serif;
			z-index: 9999;
		}

		.toast.show {
			opacity: 1;
			transform: translateY(0);
			pointer-events: all;
		}

		/* Contenido principal */
		.toast-content {
			display: flex;
			align-items: center;
			padding: 12px 16px;
		}

		.toast .icon {
			color: #2ecc71;
			font-size: 24px;
			margin-right: 12px;
		}

		.toast .message {
			flex: 1;
			display: flex;
			flex-direction: column;
		}

		.toast .title {
			font-weight: 600;
			color: #333;
			margin-bottom: 3px;
			font-size: 15px;
		}

		.toast .text {
			color: #666;
			font-size: 13px;
		}

		/* Botón de cerrar */
		.toast .close {
			font-size: 18px;
			color: #777;
			cursor: pointer;
			margin-left: 10px;
			transition: color 0.3s;
		}

		.toast .close:hover {
			color: #333;
		}

		/* Barra de progreso animada */
		.toast .progress {
			height: 3px;
			background: #2ecc71;
			width: 100%;
			animation: progress 3s linear forwards;
		}

		@keyframes progress {
			from {
				width: 100%;
			}

			to {
				width: 0%;
			}
		}
	</style>

	@if(session('show_login_toast'))
		<script>
			// ===== TOAST SCRIPT =====
			document.addEventListener("DOMContentLoaded", () => {
				const toast = document.getElementById("toast");
				const close = document.querySelector(".toast .close");
				const progress = document.querySelector(".toast .progress");

				if (toast) {
					// Mostrar el toast al cargar la página
					setTimeout(() => {
						toast.classList.add("show");
					}, 400);

					// Ocultar automáticamente después de 3.2 segundos
					setTimeout(() => {
						if (toast.classList.contains("show")) {
							toast.classList.remove("show");

							// Remover el toast del DOM después de la animación
							setTimeout(() => {
								toast.remove();
							}, 500);
						}
					}, 4400);

					// Cerrar manualmente
					if (close) {
						close.addEventListener("click", () => {
							toast.classList.remove("show");
							setTimeout(() => {
								toast.remove();
							}, 500);
						});
					}
				}
			});
		</script>
	@endif
	@yield('js')

	<script>
		$('html').on('keydown', () => {
			if (event.keyCode == 13) {
				return false;
			}
		});
	</script>

	@stack('scripts_before')

	<style>
		.dataTables_empty {
			background-color: #f8d7da !important;
			/* Rojo claro tipo alerta */
			color: #721c24 !important;
			/* Texto en rojo oscuro */
			font-weight: bold;
			text-align: center;
			padding: 10px;
			border-radius: 4px;
		}
	</style>
	<!-- Page specific script -->
	<script>
		$(function () {
			// Solo inicializar si no se ha establecido la bandera skipDefaultDataTable
			if (typeof skipDefaultDataTable === 'undefined' || !skipDefaultDataTable) {
				$("#example1").DataTable({
					"responsive": true,
					"lengthChange": false,
					"autoWidth": false,
					"buttons": [{
						extend: 'copy',
						text: 'Copiar',
						className: 'btn btn-sm btn-primary'
					},
					{
						extend: 'csv',
						text: 'CSV',
						className: 'btn btn-sm btn-success'
					},
					{
						extend: 'excel',
						text: 'Excel',
						className: 'btn btn-sm btn-info'
					},
					{
						extend: 'pdf',
						text: 'PDF',
						className: 'btn btn-sm btn-danger'
					},
					{
						extend: 'print',
						text: 'Imprimir',
						className: 'btn btn-sm btn-warning'
					}
					],
					"language": {
						"decimal": "",
						"emptyTable": "No hay datos disponibles en la tabla",
						"info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
						"infoEmpty": "Mostrando 0 a 0 de 0 entradas",
						"infoFiltered": "(filtrado de _MAX_ entradas totales)",
						"infoPostFix": "",
						"thousands": ",",
						"lengthMenu": "Mostrar _MENU_ entradas",
						"loadingRecords": "Cargando...",
						"processing": "Procesando...",
						"search": "Buscar:",
						"zeroRecords": "No se encontraron registros coincidentes",
						"paginate": {
							"first": "Primero",
							"last": "Último",
							"next": "Siguiente",
							"previous": "Anterior"
						},
						"aria": {
							"sortAscending": ": activar para ordenar la columna de manera ascendente",
							"sortDescending": ": activar para ordenar la columna de manera descendente"
						},
						"buttons": {
							"copy": "Copiar",
							"copyTitle": "Copiado al portapapeles",
							"copySuccess": {
								"_": "Se copiaron %d filas al portapapeles",
								"1": "Se copió una fila al portapapeles"
							}
						}
					}
				}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
			}
		});
	</script>

	<script>
		$(function () {
			//Initialize Select2 Elements
			$('.select2').select2()

			//Initialize Select2 Elements
			$('.select2bs4').select2({
				theme: 'bootstrap4'
			})

			//Datemask dd/mm/yyyy
			$('#datemask').inputmask('dd/mm/yyyy', {
				'placeholder': 'dd/mm/yyyy'
			})
			//Datemask2 mm/dd/yyyy
			$('#datemask2').inputmask('mm/dd/yyyy', {
				'placeholder': 'mm/dd/yyyy'
			})
			//Money Euro
			$('[data-mask]').inputmask()

			//Date picker
			$('#reservationdate').datetimepicker({
				format: 'L'
			});

			//Date and time picker
			$('#reservationdatetime').datetimepicker({
				icons: {
					time: 'far fa-clock'
				}
			});

			//Date range picker
			$('#reservation').daterangepicker()
			//Date range picker with time picker
			$('#reservationtime').daterangepicker({
				timePicker: true,
				timePickerIncrement: 30,
				locale: {
					format: 'MM/DD/YYYY hh:mm A'
				}
			})
			//Date range as a button
			$('#daterange-btn').daterangepicker({
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
						'month').endOf('month')]
				},
				startDate: moment().subtract(29, 'days'),
				endDate: moment()
			},
				function (start, end) {
					$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
						'MMMM D, YYYY'))
				}
			)

			//Timepicker
			$('#timepicker').datetimepicker({
				format: 'LT'
			})

			//Bootstrap Duallistbox
			$('.duallistbox').bootstrapDualListbox()

			//Colorpicker
			$('.my-colorpicker1').colorpicker()
			//color picker with addon
			$('.my-colorpicker2').colorpicker()

			$('.my-colorpicker2').on('colorpickerChange', function (event) {
				$('.my-colorpicker2 .fa-square').css('color', event.color.toString());
			})

			$("input[data-bootstrap-switch]").each(function () {
				$(this).bootstrapSwitch('state', $(this).prop('checked'));
			})

		})
	</script>
</body>

</html>