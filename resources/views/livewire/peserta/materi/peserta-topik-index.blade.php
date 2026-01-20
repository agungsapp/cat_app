<div>
		<!-- Header + Search -->
		<div class="card mb-4 border-0 shadow-sm">
				<div class="card-body">
						<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
								<div>
										<h3 class="mb-1">Materi Pembelajaran</h3>
										<p class="text-muted mb-0">Pilih materi untuk mulai belajar</p>
								</div>
								<div class="w-100 w-md-50">
										<input type="text" wire:model.live.debounce.500ms="search" class="form-control"
												placeholder="Cari topik atau materi...">
								</div>
						</div>
				</div>
		</div>

		<!-- Daftar Topik -->
		<div class="row g-4">
				@forelse($topiks as $topik)
						<div class="col-12">
								<div class="card border-0 shadow-lg">
										<!-- Header Topik -->
										<div class="card-header bg-gradient-primary border-0 text-white">
												<div class="d-flex justify-content-between align-items-center">
														<div>
																<h5 class="mb-0 text-white">{{ $topik->nama_topik }}</h5>
																<small class="opacity-9">
																		{{ $topik->total_konten }} konten •
																		{{ $topik->completed_konten }} selesai •
																		{{ $topik->progress }}% progress
																</small>
														</div>
														<div class="progress" style="width: 120px; height: 8px;">
																<div class="progress-bar bg-light" style="width: {{ $topik->progress }}%"></div>
														</div>
												</div>
										</div>

										<!-- Grid Materi -->
										<div class="card-body">
												@if ($topik->materis->count() > 0)
														<div class="row g-4">
																@foreach ($topik->materis as $materi)
																		<div class="col-md-6 col-lg-4">
																				<a href="{{ route('peserta.materi.show', $materi->id) }}" class="text-decoration-none">
																						<div class="card h-100 hover-lift bg-light border-0 p-4 text-center shadow-sm">
																								<div class="mb-3">
																										<i class='bx bx-book-open fs-1 text-primary'></i>
																								</div>
																								<h6 class="text-dark mb-2">{{ $materi->judul }}</h6>
																								<small class="text-muted d-block">
																										{{ $materi->submateris->sum(fn($s) => $s->kontens->count()) }} konten
																								</small>
																								@php
																										$completedInMateri = 0;
																										foreach ($materi->submateris as $sub) {
																										    foreach ($sub->kontens as $k) {
																										        if (\App\Models\UserMateriProgress::isCompleted(auth()->id(), $k->id)) {
																										            $completedInMateri++;
																										        }
																										    }
																										}
																								@endphp
																								@if ($completedInMateri > 0)
																										<small class="text-success d-block mt-1">
																												<i class='bx bx-check-circle'></i> {{ $completedInMateri }} selesai
																										</small>
																								@endif
																						</div>
																				</a>
																		</div>
																@endforeach
														</div>
												@else
														<p class="text-muted py-4 text-center">
																<i class='bx bx-info-circle'></i> Belum ada materi di topik ini
														</p>
												@endif
										</div>
								</div>
						</div>
				@empty
						<div class="col-12">
								<div class="py-5 text-center">
										<i class='bx bx-book-open fs-1 text-muted'></i>
										<h5 class="text-muted mt-3">Belum ada topik tersedia</h5>
										<p class="text-muted">Tunggu admin menambahkan materi pembelajaran</p>
								</div>
						</div>
				@endforelse
		</div>

		@push('css')
				<style>
						.hover-lift {
								transition: all 0.3s ease;
								border: 1px solid transparent !important;
						}

						.hover-lift:hover {
								transform: translateY(-12px);
								box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
								border-color: #0d6efd !important;
						}

						.bg-gradient-primary {
								background: linear-gradient(135deg, #0d6efd, #6610f2) !important;
						}
				</style>
		@endpush
</div>
