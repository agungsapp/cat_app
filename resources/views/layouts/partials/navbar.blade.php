<nav class="navbar navbar-main navbar-expand-lg border-radius-xl mx-4 px-0 shadow-none" id="navbarBlur"
		data-scroll="false">
		<div class="container-fluid px-3 py-1">
				<nav aria-label="breadcrumb">
						<ol class="breadcrumb me-sm-6 mb-0 me-5 bg-transparent px-0 pb-0 pt-1">
								<li class="breadcrumb-item text-sm"><a class="text-white opacity-5" href="javascript:;">Pages</a></li>
								<li class="breadcrumb-item active text-sm text-white" aria-current="page">Dashboard</li>
						</ol>
						<h6 class="font-weight-bolder mb-0 text-white">Dashboard</h6>
				</nav>
				<div class="navbar-collapse mt-sm-0 me-md-0 me-sm-4 collapse mt-2" id="navbar">
						<div class="ms-md-auto pe-md-3 d-flex align-items-center">
								{{-- <div class="input-group">
										<span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
										<input type="text" class="form-control" placeholder="Type here...">
								</div> --}}
						</div>
						<ul class="navbar-nav justify-content-end">
								<li class="nav-item d-flex align-items-center">
										<form method="POST" action="{{ route('logout') }}" class="d-flex align-items-center m-0 p-0">
												@csrf
												<button type="submit" class="nav-link font-weight-bold bg-primary rounded-3 border-0 px-4 text-white">
														<i class="fa fa-sign-out me-sm-1"></i>
														<span class="d-sm-inline d-none">Logout</span>
												</button>
										</form>
								</li>

								<li class="nav-item d-xl-none d-flex align-items-center ps-3">
										<a href="javascript:;" class="nav-link p-0 text-white" id="iconNavbarSidenav">
												<div class="sidenav-toggler-inner">
														<i class="sidenav-toggler-line bg-white"></i>
														<i class="sidenav-toggler-line bg-white"></i>
														<i class="sidenav-toggler-line bg-white"></i>
												</div>
										</a>
								</li>
								<li class="nav-item d-flex align-items-center px-3">
										<a href="javascript:;" class="nav-link p-0 text-white">
												<i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
										</a>
								</li>

						</ul>
				</div>
		</div>
</nav>
