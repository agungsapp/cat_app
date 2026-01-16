<!--
=========================================================
* Argon Dashboard 3 - v2.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('argon') }}/img/apple-icon.png">
		<link rel="icon" type="image/png" href="{{ asset('argon') }}/img/favicon.png">
		<title>{{ $title ?? 'Berkarir ASN' }}</title>
		<!--     Fonts and icons     -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
		<!-- Nucleo Icons -->
		<link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
		<link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
		<!-- Font Awesome Icons -->
		<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
		<!-- CSS Files -->
		<link id="pagestyle" href="{{ asset('argon') }}/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
		<link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>

		@vite('resources/js/app.js')

		@livewireStyles
		@stack('css')
</head>

<body class="g-sidenav-show bg-gray-100">
		<div class="min-height-300 bg-dark position-absolute w-100"></div>
		@if (!request()->routeIs('peserta.ujian.*') || request()->routeIs('peserta.ujian.index'))
				@include('layouts.partials.sidebar')
		@endif
		<main class="main-content position-relative border-radius-lg">
				<!-- Navbar -->
				@if (!request()->routeIs('peserta.ujian.*') || request()->routeIs('peserta.ujian.index'))
						@include('layouts.partials.navbar')
				@endif
				<!-- End Navbar -->
				<div style="min-height: 80vh;" class="container py-4">

						{{ $slot }}

				</div>
				<div class="container">
						<footer class="footer pt-3">
								<div class="container-fluid">
										<div class="row align-items-center justify-content-lg-between">
												<div class="col-lg-6 mb-lg-0 mb-4">
														<div class="copyright text-muted text-lg-start text-center text-sm">
																©
																<script>
																		document.write(new Date().getFullYear())
																</script>,
																made with SancaDev
														</div>
												</div>

										</div>
								</div>
						</footer>
				</div>
		</main>

		<!--   Core JS Files   -->
		<script src="{{ asset('argon') }}/js/core/popper.min.js"></script>
		<script src="{{ asset('argon') }}/js/core/bootstrap.min.js"></script>
		<script src="{{ asset('argon') }}/js/plugins/perfect-scrollbar.min.js"></script>
		<script src="{{ asset('argon') }}/js/plugins/smooth-scrollbar.min.js"></script>
		<script src="{{ asset('argon') }}/js/plugins/chartjs.min.js"></script>

		<script>
				var win = navigator.platform.indexOf('Win') > -1;
				if (win && document.querySelector('#sidenav-scrollbar')) {
						var options = {
								damping: '0.5'
						}
						Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
				}
		</script>
		<!-- Github buttons -->
		<script async defer src="https://buttons.github.io/buttons.js"></script>
		<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
		<script src="{{ asset('argon') }}/js/argon-dashboard.min.js?v=2.1.0"></script>

		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		@livewireScripts
		{{-- @livewireAlerts --}}

		@stack('js')

</body>

</html>
