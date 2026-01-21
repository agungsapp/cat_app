<div>
		<div class="container-fluid py-4">
				<!-- Warning Badge (Pelanggaran) -->
				@if ($pelanggaranCount > 0)
						<div class="alert alert-warning alert-dismissible fade show position-fixed start-50 translate-middle-x top-0 mt-3"
								style="z-index: 9999; min-width: 400px;" id="warning-badge">
								<i class="fas fa-exclamation-triangle me-2"></i>
								<strong>Peringatan!</strong> Anda telah meninggalkan halaman {{ $pelanggaranCount }} kali.
								Sisa kesempatan: <strong>{{ $maxPelanggaran - $pelanggaranCount }}</strong>
						</div>
				@endif

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
														{{-- DEBUG MODE ONLY --}}
														{{-- <span class="badge bg-warning text-dark">{{ $soal->jenis->nama }}</span> --}}

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

																								{{-- <span
																										class="badge bg-{{ $opsi->is_correct ? 'success' : 'danger' }} text-dark">{{ $opsi->is_correct ? 'pilih_ini' : 'ini_salah' }}</span> --}}
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
																		<button wire:click="konfirmasiSelesai" class="btn btn-success">
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

				/* Anti-Cheating Warning Animation */
				@keyframes shake {

						0%,
						100% {
								transform: translateX(0);
						}

						10%,
						30%,
						50%,
						70%,
						90% {
								transform: translateX(-5px);
						}

						20%,
						40%,
						60%,
						80% {
								transform: translateX(5px);
						}
				}

				.shake-animation {
						animation: shake 0.5s;
				}
		</style>
@endpush

@push('js')
		<script>
				let isLeavingIntentionally = false;
				let warningTimeout = null;

				// ===== ANTI-CHEATING: Tab/Window Leave Detection =====
				document.addEventListener('visibilitychange', function() {
						// Hanya deteksi jika tab disembunyikan (user pindah tab)
						if (document.hidden) {
								// Panggil method Livewire untuk catat pelanggaran
								@this.call('catatPelanggaran');
						}
				});

				// Alternatif: Deteksi blur (kehilangan focus)
				// Uncomment jika ingin lebih ketat (termasuk minimize window)
				/*
				window.addEventListener('blur', function() {
				    @this.call('catatPelanggaran');
				});
				*/

				// Listen untuk event warning dari Livewire
				window.addEventListener('show-warning-pelanggaran', function(event) {
						const data = event.detail[0];

						// Tampilkan notifikasi menggunakan SweetAlert2 (jika ada)
						if (typeof Swal !== 'undefined') {
								Swal.fire({
										icon: 'warning',
										title: 'Peringatan!',
										html: `
                        <p>Anda telah meninggalkan halaman ujian <strong>${data.count} kali</strong>.</p>
                        <p class="text-danger mb-0">Sisa kesempatan: <strong>${data.sisa} kali</strong></p>
                        <p class="small text-muted mt-2">Jika meninggalkan halaman lagi, ujian akan otomatis diselesaikan.</p>
                    `,
										confirmButtonText: 'Saya Mengerti',
										confirmButtonColor: '#dc3545',
										allowOutsideClick: false
								});
						} else {
								// Fallback: alert biasa
								alert(
										`PERINGATAN!\n\nAnda telah meninggalkan halaman ${data.count} kali.\nSisa kesempatan: ${data.sisa} kali.\n\nJika meninggalkan halaman lagi, ujian akan otomatis diselesaikan.`
								);
						}

						// Shake animation untuk warning badge
						const warningBadge = document.getElementById('warning-badge');
						if (warningBadge) {
								warningBadge.classList.add('shake-animation');
								setTimeout(() => {
										warningBadge.classList.remove('shake-animation');
								}, 500);
						}
				});
				// ===== TIMER POLLING =====
				setInterval(() => {
						@this.call('pollTimer');
				}, 1000);

				// ===== PREVENT ACCIDENTAL PAGE LEAVE =====
				window.addEventListener('beforeunload', function(e) {
						if (!isLeavingIntentionally) {
								e.preventDefault();
								e.returnValue = 'Ujian sedang berlangsung. Yakin ingin meninggalkan halaman?';
						}
				});

				// Set flag ketika user klik tombol selesai
				window.addEventListener('livewire:navigating', function() {
						isLeavingIntentionally = true;
				});

				// ===== KEYBOARD NAVIGATION =====
				document.addEventListener('keydown', function(e) {
						// Arrow Left = Previous
						if (e.key === 'ArrowLeft' && {{ $nomor }} > 1) {
								@this.call('pindahSoal', {{ $nomor }} - 1);
						}

						// Arrow Right = Next
						if (e.key === 'ArrowRight' && {{ $nomor }} < {{ $totalSoal }}) {
								@this.call('pindahSoal', {{ $nomor }} + 1);
						}

						// Number keys 1-5 for options
						if (e.key >= '1' && e.key <= '5') {
								const opsiElements = document.querySelectorAll('input[name="opsi_{{ $nomor }}"]');
								const index = parseInt(e.key) - 1;
								if (opsiElements[index]) {
										opsiElements[index].click();
								}
						}
				});

				// ===== AUTO-SAVE NOTIFICATION =====
				window.addEventListener('jawaban-tersimpan', () => {
						const savedIndicator = document.createElement('div');
						savedIndicator.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
						savedIndicator.innerHTML = '<i class="fas fa-check-circle me-2"></i>Jawaban tersimpan';
						savedIndicator.style.zIndex = '10000';
						document.body.appendChild(savedIndicator);

						setTimeout(() => {
								savedIndicator.remove();
						}, 2000);
				});

				// ===== DISABLE RIGHT-CLICK (Optional) =====
				// Uncomment jika ingin disable klik kanan
				/*
				document.addEventListener('contextmenu', function(e) {
				    e.preventDefault();
				    return false;
				});
				*/

				// ===== DISABLE COPY-PASTE (Optional) =====
				// Uncomment jika ingin disable copy-paste
				/*
				document.addEventListener('copy', function(e) {
				    e.preventDefault();
				    return false;
				});
				
				document.addEventListener('cut', function(e) {
				    e.preventDefault();
				    return false;
				});
				*/
		</script>
@endpush
