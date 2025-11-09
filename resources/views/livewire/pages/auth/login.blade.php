<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        Auth::user()->is_admin ? $this->redirectIntended(route('dashboard'), navigate: false) : $this->redirectIntended(route('admin.dashboard'), navigate: false);
    }
};
?>

<main class="main-content mt-0">
		<section>
				<div class="page-header min-vh-100">
						<div class="container">
								<div class="row">
										<div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
												<div class="card card-plain">
														<div class="card-header pb-0 text-start">
																<h4 class="font-weight-bolder">Login</h4>
																<p class="mb-0">Masukan email dan password untuk login.</p>
														</div>

														<div class="card-body">
																<!-- Session Status -->
																<x-auth-session-status class="mb-3" :status="session('status')" />

																<form wire:submit.prevent="login" role="form">
																		<!-- Email Address -->
																		<div class="mb-3">
																				<input type="email" id="email" wire:model="form.email"
																						class="form-control form-control-lg @error('form.email') is-invalid @enderror" placeholder="Email"
																						required autofocus autocomplete="username">
																				@error('form.email')
																						<span class="invalid-feedback d-block mt-1">{{ $message }}</span>
																				@enderror
																		</div>

																		<!-- Password -->
																		<div class="mb-3">
																				<input type="password" id="password" wire:model="form.password"
																						class="form-control form-control-lg @error('form.password') is-invalid @enderror"
																						placeholder="Password" required autocomplete="current-password">
																				@error('form.password')
																						<span class="invalid-feedback d-block mt-1">{{ $message }}</span>
																				@enderror
																		</div>

																		<!-- Remember Me -->
																		<div class="form-check form-switch mb-3">
																				<input wire:model="form.remember" class="form-check-input" type="checkbox" id="rememberMe">
																				<label class="form-check-label" for="rememberMe">Ingat saya</label>
																		</div>

																		<!-- Forgot Password -->
																		{{-- @if (Route::has('password.request'))
																				<div class="mb-3 text-end">
																						<a class="text-primary text-gradient font-weight-bold text-sm"
																								href="{{ route('password.request') }}" wire:navigate>
																								Forgot your password?
																						</a>
																				</div>
																		@endif --}}

																		<!-- Submit Button -->
																		<div class="text-center">
																				<button type="submit" class="btn btn-lg btn-primary w-100 mb-0">
																						Log In
																				</button>
																		</div>
																</form>
														</div>

														<div class="card-footer px-lg-2 px-1 pt-0 text-center">
																<p class="mx-auto mb-4 text-sm">
																		Belum punya akun ?
																		<a href="{{ route('register') }}" class="text-primary text-gradient font-weight-bold">Sign up</a>
																</p>
														</div>
												</div>
										</div>

										<!-- Right side image section -->
										<div
												class="col-6 d-lg-flex d-none h-100 position-absolute justify-content-center flex-column end-0 top-0 my-auto pe-0 text-center">
												<div
														class="position-relative bg-gradient-primary h-100 border-radius-lg d-flex flex-column justify-content-center m-3 overflow-hidden px-7"
														style="background-image: url('{{ asset('images/bg-logo.png') }}');
														background-position: center;
                                   background-size: cover;">
														<span class="mask bg-gradient-primary opacity-6"></span>
														<h4 class="font-weight-bolder position-relative mt-5 text-white">
																"Tumbuh bersama kami!"
														</h4>
														<p class="position-relative text-white">
																Raih cita cita menjadi ASN.
														</p>
												</div>
										</div>

								</div>
						</div>
				</div>
		</section>
</main>
