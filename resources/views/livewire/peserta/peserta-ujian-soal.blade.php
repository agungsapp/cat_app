<div>
		<div class="container-fluid py-4">
				<div class="row">
						<!-- PANEL KIRI: SOAL -->
						<div class="col-lg-8">
								<div class="card shadow">
										<div class="card-header bg-primary d-flex justify-content-between align-items-center text-white">
												<div>
														<h5 class="mb-0">Soal {{ $nomor }} / {{ $totalSoal }}</h5>
														<small>{{ $soalList->get($nomor - 1)?->jenis?->nama ?? 'Soal' }}</small>
												</div>
												<div>
														<span class="badge bg-danger fs-5 px-3 py-2" id="timer-display">
																{{ str_pad(floor($waktuSisa / 60), 2, '0', STR_PAD_LEFT) }}:{{ str_pad($waktuSisa % 60, 2, '0', STR_PAD_LEFT) }}
														</span>
												</div>
										</div>

										<div class="card-body">
												@php
														$soal = $soalList->get($nomor - 1);
												@endphp

												@if ($soal)
														<!-- Media Soal -->
														@if ($soal->media_type === 'image' && $soal->media_path)
																<div class="mb-3 text-center">
																		<img src="{{ Storage::url($soal->media_path) }}" class="img-fluid rounded shadow-sm"
																				style="max-height: 300px;" alt="Media Soal">
																</div>
														@endif

														<!-- Pertanyaan -->
														<div class="mb-4">
																<p class="fs-5 fw-bold">{!! nl2br(e($soal->pertanyaan_text)) !!}</p>
														</div>

														<!-- Opsi Jawaban -->
														<div class="mt-4">
																@foreach ($soal->opsi as $opsi)
																		<div
																				class="form-check opsi-item {{ $selectedOpsi[$nomor] == $opsi->id ? 'bg-light border-primary border-2' : 'border-secondary' }} mb-3 rounded border p-3 transition-all"
																				wire:key="opsi-{{ $opsi->id }}">
																				<input class="form-check-input" type="radio" name="opsi_{{ $nomor }}"
																						id="opsi_{{ $opsi->id }}"
																						wire:click="pilihJawaban({{ $nomor }}, {{ $opsi->id }})"
																						{{ $selectedOpsi[$nomor] == $opsi->id ? 'checked' : '' }}>

																				<label class="form-check-label d-flex align-items-center w-100 cursor-pointer"
																						for="opsi_{{ $opsi->id }}">
																						<span class="badge bg-primary fs-6 me-3">{{ $opsi->label }}</span>
																						<span class="flex-grow-1">
																								@if ($opsi->media_type === 'image' && $opsi->media_path)
																										<img src="{{ Storage::url($opsi->media_path) }}" class="img-thumbnail me-2"
																												style="max-height: 80px;" alt="Opsi {{ $opsi->label }}">
																								@endif
																								{{ $opsi->teks }}
																						</span>
																				</label>
																		</div>
																@endforeach
														</div>

														<!-- Navigasi Bawah -->
														<div class="d-flex justify-content-between border-top mt-4 pt-3">
																<button wire:click="pindahSoal({{ $nomor - 1 }})" class="btn btn-secondary"
																		{{ $nomor <= 1 ? 'disabled' : '' }}>
																		<i class="fas fa-chevron-left me-1"></i> Sebelumnya
																</button>

																@if ($nomor < $totalSoal)
																		<button wire:click="pindahSoal({{ $nomor + 1 }})" class="btn btn-primary">
																				Berikutnya <i class="fas fa-chevron-right ms-1"></i>
																		</button>
																@else
																		<button wire:click="selesai" class="btn btn-success"
																				onclick="return confirm('Anda yakin ingin menyelesaikan ujian?')">
																				<i class="fas fa-check me-1"></i> Selesai Ujian
																		</button>
																@endif
														</div>
												@else
														<div class="alert alert-warning">
																<i class="fas fa-exclamation-triangle me-2"></i>
																Soal tidak ditemukan.
														</div>
												@endif
										</div>
								</div>
						</div>

						<!-- PANEL KANAN: NAVIGASI NOMOR -->
						<div class="col-lg-4">
								<div class="card sticky-top shadow" style="top: 20px;">
										<div class="card-header bg-dark text-center">
												<h6 class="mb-0 text-white"><i class="fas fa-list-ol me-2"></i>Navigasi Soal</h6>
										</div>

										<div class="card-body p-3">
												<!-- Grid Nomor Soal -->
												<div class="row g-2 mb-3">
														@foreach ($soalList as $index => $s)
																@php
																		$n = $index + 1;
																		$btnClass = 'btn-outline-secondary';

																		if ($n == $nomor) {
																		    $btnClass = 'btn-primary';
																		} elseif (isset($jawabanStatus[$n]) && $jawabanStatus[$n] == 'terjawab') {
																		    $btnClass = 'btn-success';
																		}
																@endphp

																<div class="col-auto">
																		<button wire:click="pindahSoal({{ $n }})"
																				class="btn btn-sm {{ $btnClass }} nomor-soal"
																				style="width: 60px; height: 45px; font-weight: 600;" wire:key="nav-{{ $n }}">
																				{{ $n }}
																		</button>
																</div>
														@endforeach
												</div>

												<hr>

												<!-- Legenda -->
												<div class="small">
														<div class="d-flex align-items-center mb-2">
																<span class="badge bg-success me-2" style="width: 20px; height: 20px;"></span>
																<span>Terjawab ({{ collect($jawabanStatus)->filter(fn($s) => $s == 'terjawab')->count() }})</span>
														</div>
														<div class="d-flex align-items-center mb-2">
																<span class="badge bg-outline-secondary me-2 border" style="width: 20px; height: 20px;"></span>
																<span>Belum Dijawab ({{ collect($jawabanStatus)->filter(fn($s) => $s == 'belum')->count() }})</span>
														</div>
														<div class="d-flex align-items-center">
																<span class="badge bg-primary me-2" style="width: 20px; height: 20px;"></span>
																<span>Sedang Dikerjakan</span>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>
		</div>
		<!-- Loading Indicator -->
		{{-- <div wire:loading class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center start-0 top-0"
				style="background: rgba(0,0,0,0.3); z-index: 9999;">
				<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
						<span class="visually-hidden">Loading...</span>
				</div>
		</div> --}}

</div>

@push('css')
		<style>
				.opsi-item {
						cursor: pointer;
						transition: all 0.2s ease;
				}

				.opsi-item:hover {
						background-color: #f8f9fa !important;
						border-color: #0d6efd !important;
						transform: translateX(5px);
				}

				.opsi-item .form-check-input {
						cursor: pointer;
						width: 1.25rem;
						height: 1.25rem;
				}

				.cursor-pointer {
						cursor: pointer;
				}

				.nomor-soal {
						transition: all 0.2s ease;
				}

				.nomor-soal:hover {
						transform: scale(1.1);
				}

				.transition-all {
						transition: all 0.2s ease;
				}

				.sticky-top {
						position: sticky;
				}

				#timer-display {
						font-family: 'Courier New', monospace;
						letter-spacing: 2px;
				}
		</style>
@endpush

@push('js')
		<script>
				// Timer polling setiap detik
				setInterval(() => {
						@this.call('pollTimer');
				}, 1000);

				// Prevent accidental page leave
				// Sekarang HANYA WARNING, TIDAK AUTO-SELESAI
				window.addEventListener('beforeunload', function(e) {
						if (!isLeavingIntentionally) {
								e.preventDefault();
								e.returnValue = 'Jawaban Anda sudah tersimpan.';
						}
				});

				// Keyboard navigation
				document.addEventListener('keydown', function(e) {
						// Arrow Left = Previous
						if (e.key === 'ArrowLeft' && {{ $nomor }} > 1) {
								@this.call('pindahSoal', {{ $nomor }} - 1);
						}

						// Arrow Right = Next
						if (e.key === 'ArrowRight' && {{ $nomor }} < {{ $totalSoal }}) {
								@this.call('pindahSoal', {{ $nomor }} + 1);
						}

						// Number keys 1-5 for options (if applicable)
						if (e.key >= '1' && e.key <= '5') {
								const opsiElements = document.querySelectorAll('input[name="opsi_{{ $nomor }}"]');
								const index = parseInt(e.key) - 1;
								if (opsiElements[index]) {
										opsiElements[index].click();
								}
						}
				});

				// Auto-save notification
				window.addEventListener('jawaban-tersimpan', () => {
						// Optional: Show brief success indicator
						const savedIndicator = document.createElement('div');
						savedIndicator.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
						savedIndicator.innerHTML = '<i class="fas fa-check-circle me-2"></i>Jawaban tersimpan';
						savedIndicator.style.zIndex = '10000';
						document.body.appendChild(savedIndicator);

						setTimeout(() => {
								savedIndicator.remove();
						}, 2000);
				});
		</script>
@endpush
