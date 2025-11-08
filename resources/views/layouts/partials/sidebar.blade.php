<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-xl fixed-start my-3 ms-4 border-0 bg-white"
		id="sidenav-main">
		<div class="sidenav-header">
				<i class="fas fa-times text-secondary position-absolute d-none d-xl-none end-0 top-0 cursor-pointer p-3 opacity-5"
						aria-hidden="true" id="iconSidenav"></i>
				<a class="navbar-brand d-flex flex-column m-0"
						href=" https://demos.creative-tim.com/argon-dashboard/pages/dashboard.html " target="_blank">
						<img src="{{ asset('images') }}/logo.png" class="navbar-brand-img w-75 mx-auto" alt="main_logo">
						<span class="font-weight-bold text-capitalize ms-1 text-center">{{ Auth::user()->role }}</span>
						<span>Is_Login : {{ Auth::check() }}</span>
				</a>
		</div>
		<hr class="horizontal dark mt-0">
		<div class="navbar-collapse collapse w-auto" id="sidenav-collapse-main">
				<ul class="navbar-nav">

						@can('admin')
								<li class="nav-item">
										<a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
												<div
														class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
														<i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
												</div>
												<span class="nav-link-text ms-1">Dashboard</span>
										</a>
								</li>
								{{-- master start --}}
								@can('access-master')
										<li class="nav-item mt-3">
												<h6 class="text-uppercase font-weight-bolder opacity-6 ms-2 ps-4 text-xs">Data Master</h6>
										</li>
										<li class="nav-item">
												<a class="nav-link {{ Route::is('admin.master.jenis-ujian') ? 'active' : '' }}"
														href="{{ route('admin.master.jenis-ujian') }}">
														<div
																class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
																<i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
														</div>
														<span class="nav-link-text ms-1">Jenis Ujian</span>
												</a>
										</li>
										<li class="nav-item">
												<a class="nav-link {{ Route::is('admin.master.tipe-ujian') ? 'active' : '' }}"
														href="{{ route('admin.master.tipe-ujian') }}">
														<div
																class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
																<i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
														</div>
														<span class="nav-link-text ms-1">Tipe Ujian</span>
												</a>
										</li>
								@endcan
								{{-- master end --}}
								<li class="nav-item mt-3">
										<h6 class="text-uppercase font-weight-bolder opacity-6 ms-2 ps-4 text-xs">Data ujian</h6>
								</li>
								<li class="nav-item">
										<a class="nav-link {{ Route::is('admin.bank-soal.*') ? 'active' : '' }}"
												href="{{ route('admin.bank-soal.index') }}">
												<div
														class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
														<i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
												</div>
												<span class="nav-link-text ms-1">Bank Soal</span>
										</a>
								</li>
								<li class="nav-item">
										<a class="nav-link {{ Route::is('admin.sesi-ujian.*') ? 'active' : '' }}"
												href="{{ route('admin.sesi-ujian.index') }}">
												<div
														class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
														<i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
												</div>
												<span class="nav-link-text ms-1">Sesi Ujian</span>
										</a>
								</li>
						@endcan
						@can('peserta')
								<li class="nav-item">
										<a class="nav-link {{ Route::is('peserta.dashboard.*') ? 'active' : '' }}"
												href="{{ route('peserta.dashboard.index') }}">
												<div
														class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
														<i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
												</div>
												<span class="nav-link-text ms-1">Dashboard</span>
										</a>
								</li>

								<li class="nav-item mt-3">
										<h6 class="text-uppercase font-weight-bolder opacity-6 ms-2 ps-4 text-xs">Mulai Ujian</h6>
								</li>
								@foreach (\App\Models\TipeUjian::all() as $tipe)
										<li class="nav-item">
												<a class="nav-link {{ Route::is('peserta.ujian.index') && request()->route('slug') == $tipe->slug ? 'active' : '' }}"
														href="{{ route('peserta.ujian.index', $tipe->slug) }}">
														<div class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2">
																<i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
														</div>
														<span class="nav-link-text ms-1">{{ $tipe->nama }}</span>
												</a>
										</li>
								@endforeach

						@endcan


						<li class="nav-item">
								<a class="nav-link" href="../pages/profile.html">
										<div
												class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
												<i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
										</div>
										<span class="nav-link-text ms-1">Profile</span>
								</a>
						</li>
						<li class="nav-item">
								<a class="nav-link" href="../pages/sign-in.html">
										<div
												class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
												<i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
										</div>
										<span class="nav-link-text ms-1">Sign In</span>
								</a>
						</li>
						<li class="nav-item">
								<a class="nav-link" href="../pages/sign-up.html">
										<div
												class="icon icon-shape icon-sm border-radius-md d-flex align-items-center justify-content-center me-2 text-center">
												<i class="ni ni-collection text-dark text-sm opacity-10"></i>
										</div>
										<span class="nav-link-text ms-1">Sign Up</span>
								</a>
						</li>
				</ul>
		</div>
		<div class="sidenav-footer mx-3">
				<div class="card card-plain shadow-none" id="sidenavCard">
						<img class="w-50 mx-auto" src="{{ asset('argon') }}/img/illustrations/icon-documentation.svg"
								alt="sidebar_illustration">
						<div class="card-body w-100 p-3 pt-0 text-center">
								<div class="docs-info">
										<h6 class="mb-0">Need help?</h6>
										<p class="font-weight-bold mb-0 text-xs">Please check our docs</p>
								</div>
						</div>
				</div>
				<a href="https://www.creative-tim.com/learning-lab/bootstrap/license/argon-dashboard" target="_blank"
						class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
				<a class="btn btn-primary btn-sm w-100 mb-0"
						href="https://www.creative-tim.com/product/argon-dashboard-pro?ref=sidebarfree" type="button">Upgrade to
						pro</a>
		</div>
</aside>
