<div>
		<!-- Breadcrumb -->
		<nav aria-label="breadcrumb" class="mb-4">
				<ol class="breadcrumb">
						<li class="breadcrumb-item">
								<a href="{{ route('peserta.materi.index') }}">Materi</a>
						</li>
						<li class="breadcrumb-item">
								<a href="{{ route('peserta.materi.index') }}#topik-{{ $materi->topik->id }}">
										{{ $materi->topik->nama_topik }}
								</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">
								{{ $materi->judul }}
						</li>
				</ol>
		</nav>

		<!-- Header Materi + Progress -->
		<div class="card mb-5 border-0 shadow-sm">
				<div class="card-body text-md-start text-center">
						<h1 class="display-6 fw-bold mb-3">{{ $materi->judul }}</h1>
						<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
								<div>
										<p class="text-muted mb-0">
												<strong>{{ $total_konten }}</strong> konten •
												<strong>{{ $completed_konten }}</strong> selesai •
												<strong class="text-primary">{{ $progress }}%</strong> progress
										</p>
								</div>
								<div class="progress w-100 w-md-50" style="height: 14px;">
										<div class="progress-bar {{ $progress == 100 ? 'bg-success' : 'bg-primary' }}"
												style="width: {{ $progress }}%">
										</div>
								</div>
						</div>
				</div>
		</div>

		<!-- Accordion Submateri -->
		<div class="accordion shadow-lg" id="submateriAccordion">
				@forelse($materi->submateris->sortBy('urutan') as $index => $submateri)
						<div class="accordion-item mb-3 rounded border-0 shadow-sm">
								<h2 class="accordion-header" id="heading-{{ $submateri->id }}">
										<button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} fw-bold fs-5" type="button"
												data-bs-toggle="collapse" data-bs-target="#collapse-{{ $submateri->id }}"
												aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $submateri->id }}">
												{{ $submateri->judul }}
												<span class="badge bg-light text-dark ms-auto">
														{{ $submateri->kontens->count() }} konten
												</span>
										</button>
								</h2>
								<div id="collapse-{{ $submateri->id }}" class="accordion-collapse {{ $index === 0 ? 'show' : '' }} collapse"
										aria-labelledby="heading-{{ $submateri->id }}" data-bs-parent="#submateriAccordion">
										<div class="accordion-body bg-light">
												@if ($submateri->kontens->count() > 0)
														<div class="row g-3">
																@foreach ($submateri->kontens->sortBy('urutan') as $konten)
																		<div class="col-md-6 col-lg-4">
																				<a href="{{ route('peserta.materi.konten', ['materi' => $materi->id, 'konten' => $konten->id]) }}"
																						class="text-decoration-none">
																						<div
																								class="card h-100 hover-lift {{ $konten->is_completed ? 'border-success border-3' : '' }} border-0 p-4 shadow-sm">
																								<div class="d-flex align-items-center">
																										<div class="me-3">
																												@if ($konten->tipe === 'video')
																														<i class='bx bxl-youtube fs-1 text-danger'></i>
																												@else
																														<i class='bx bxs-file-pdf fs-1 text-danger'></i>
																												@endif
																										</div>
																										<div class="flex-grow-1">
																												<h6 class="text-dark mb-1">
																														{{ $konten->isi ?? ($konten->tipe === 'video' ? 'Video YouTube' : 'File PDF') }}
																												</h6>
																												<small class="text-muted">
																														{{ $konten->tipe === 'video' ? 'Video' : 'PDF' }}
																														@if ($konten->is_completed)
																																<span class="text-success ms-2">✓ Selesai</span>
																														@endif
																												</small>
																										</div>
																										@if ($konten->is_completed)
																												<i class='bx bx-check-circle text-success fs-3'></i>
																										@endif
																								</div>
																						</div>
																				</a>
																		</div>
																@endforeach
														</div>
												@else
														<p class="text-muted py-4 text-center">
																<i class='bx bx-info-circle fs-3'></i><br>
																Belum ada konten di submateri ini
														</p>
												@endif
										</div>
								</div>
						</div>
				@empty
						<div class="py-5 text-center">
								<i class='bx bx-book-open fs-1 text-muted'></i>
								<h5 class="text-muted mt-3">Belum ada submateri</h5>
								<p class="text-muted">Admin belum menambahkan submateri untuk materi ini</p>
						</div>
				@endforelse
		</div>

		@push('css')
				<style>
						.hover-lift {
								transition: all 0.3s ease;
						}

						.hover-lift:hover {
								transform: translateY(-10px);
								box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
						}

						.accordion-button:not(.collapsed) {
								background: linear-gradient(135deg, #0d6efd, #6610f2) !important;
								color: white !important;
						}

						.accordion-button:not(.collapsed)::after {
								filter: brightness(0) invert(1);
						}

						.border-3 {
								border-width: 3px !important;
						}
				</style>
		@endpush
</div>
