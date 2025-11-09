<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        Auth::user()->is_admin ? $this->redirectIntended(route('dashboard'), navigate: false) : $this->redirectIntended(route('admin.dashboard'), navigate: false);
        // $this->redirectIntended(route('admin.dashboard'), navigate: false);
    }
};
?>

<main class="main-content mt-0">
		<div class="page-header align-items-start min-vh-50 border-radius-lg m-3 pb-11 pt-5"
				style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg'); background-position: top;">
				<span class="mask bg-gradient-dark opacity-6"></span>
				<div class="container">
						<div class="row justify-content-center">
								<div class="col-lg-5 mx-auto text-center">
										<h1 class="mb-2 mt-5 text-white">Selamat Datang !</h1>
										<p class="text-lead text-white">
												Silahkan mendaftar untuk membuat akun dan login.
										</p>
								</div>
						</div>
				</div>
		</div>

		<div class="container">
				<div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
						<div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
								<div class="card z-index-0">
										<div class="card-header pt-4 text-center">
												<h5>Register</h5>
										</div>

										<div class="card-body">
												<form wire:submit.prevent="register" role="form">

														<!-- Name -->
														<div class="mb-3">
																<input wire:model="name" type="text" id="name"
																		class="form-control @error('name') is-invalid @enderror" placeholder="Name" required autofocus
																		autocomplete="name">
																@error('name')
																		<span class="invalid-feedback d-block mt-1">{{ $message }}</span>
																@enderror
														</div>

														<!-- Email -->
														<div class="mb-3">
																<input wire:model="email" type="email" id="email"
																		class="form-control @error('email') is-invalid @enderror" placeholder="Email" required
																		autocomplete="username">
																@error('email')
																		<span class="invalid-feedback d-block mt-1">{{ $message }}</span>
																@enderror
														</div>

														<!-- Password -->
														<div class="mb-3">
																<input wire:model="password" type="password" id="password"
																		class="form-control @error('password') is-invalid @enderror" placeholder="Password" required
																		autocomplete="new-password">
																@error('password')
																		<span class="invalid-feedback d-block mt-1">{{ $message }}</span>
																@enderror
														</div>

														<!-- Confirm Password -->
														<div class="mb-3">
																<input wire:model="password_confirmation" type="password" id="password_confirmation"
																		class="form-control @error('password_confirmation') is-invalid @enderror"
																		placeholder="Confirm Password" required autocomplete="new-password">
																@error('password_confirmation')
																		<span class="invalid-feedback d-block mt-1">{{ $message }}</span>
																@enderror
														</div>

														<!-- Terms & Conditions -->
														<div class="form-check form-check-info text-start">
																<input class="form-check-input" type="checkbox" id="flexCheckDefault" checked>
																<label class="form-check-label" for="flexCheckDefault">
																		I agree to the
																		<a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
																</label>
														</div>

														<!-- Submit Button -->
														<div class="text-center">
																<button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">
																		Sign up
																</button>
														</div>

														<!-- Already have account -->
														<p class="mb-0 mt-3 text-center text-sm">
																Sudah punya akun ?
																<a href="{{ route('login') }}" class="text-dark font-weight-bolder" wire:navigate>
																		Sign in
																</a>
														</p>
												</form>
										</div>
								</div>
						</div>
				</div>
		</div>
</main>
