<div class="container-fluid py-4">
		<div class="row">
				<!-- DAFTAR RIWAYAT -->
				<div class="col-lg-5">
						<div class="card">
								<div class="card-header bg-dark">
										<h5 class="mb-0 text-white">Riwayat Ujian</h5>
								</div>
								<div class="card-body p-0">
										@forelse ($riwayat as $h)
												<div class="border-bottom {{ $selectedHasil?->id == $h->id ? 'bg-light' : '' }} p-3">
														<div class="d-flex justify-content-between align-items-center">
																<div>
																		<h6 class="mb-1">{{ $h->sesiUjian->judul }}</h6>
																		<small class="text-muted">
																				{{ $h->sesiUjian->tipeUjian->nama }}
																				| {{ $h->mulai_at->format('d/m/Y H:i') }}
																		</small>
																</div>
																<div class="text-end">
																		<div class="fs-5 fw-bold text-success">{{ $h->skor ?? '-' }}</div>
																		<button wire:click="lihatDetail({{ $h->id }})" class="btn btn-sm btn-outline-primary">
																				Lihat
																		</button>
																</div>
														</div>
												</div>
										@empty
												<div class="text-muted p-4 text-center">
														Belum ada riwayat ujian
												</div>
										@endforelse
								</div>
						</div>
				</div>

				<!-- DETAIL JAWABAN -->
				<div class="col-lg-7">
						@if ($selectedHasil)
								<div class="card">
										<div class="card-header bg-primary d-flex justify-content-between text-white">
												<h5 class="mb-0">Detail Jawaban</h5>
												<small>Skor: {{ $selectedHasil->skor }}</small>
										</div>
										<div class="card-body" style="max-height: 70vh; overflow-y: auto;">
												@foreach ($soalList as $index => $soal)
														@php
																$n = $index + 1;
																$j = $jawaban[$soal->id] ?? null;
																$status = $j ? ($j->benar ? 'benar' : 'salah') : 'tidak-dijawab';
														@endphp
														<div
																class="{{ $status == 'benar' ? 'border-success' : ($status == 'salah' ? 'border-danger' : 'border-secondary') }} mb-3 rounded border p-3">
																<div class="d-flex justify-content-between mb-2">
																		<strong>Soal {{ $n }}</strong>
																		<span
																				class="badge {{ $status == 'benar' ? 'bg-success' : ($status == 'salah' ? 'bg-danger' : 'bg-secondary') }}">
																				{{ $status == 'benar' ? 'Benar' : ($status == 'salah' ? 'Salah' : 'Tidak Dijawab') }}
																		</span>
																</div>

																<p class="mb-2">{!! nl2br(e($soal->pertanyaan_text)) !!}</p>

																<!-- Jawaban Peserta -->
																@if ($j)
																		<div class="alert alert-info small mb-2 p-2">
																				<strong>Jawaban Anda:</strong>
																				{{ $j->opsi?->label }}. {{ $j->opsi?->teks }}
																		</div>
																@endif

																<!-- Kunci Jawaban -->
																<div class="alert alert-success small mb-2 p-2">
																		<strong>Kunci:</strong>
																		@php
																				$kunci = $soal->opsi->where('is_correct', true)->first();
																		@endphp
																		{{ $kunci->label }}. {{ $kunci->teks }}
																</div>

																<!-- Pembahasan (jika ada) -->
																@if ($soal->pembahasan)
																		<details class="mt-2">
																				<summary class="text-primary small cursor-pointer">Lihat Pembahasan</summary>
																				<div class="bg-light mt-2 rounded p-2">
																						{!! nl2br(e($soal->pembahasan)) !!}
																				</div>
																		</details>
																@endif
														</div>
												@endforeach
										</div>
								</div>
						@else
								<div class="card h-100 d-flex align-items-center justify-content-center text-muted">
										<div class="text-center">
												<i class="fas fa-eye fa-3x mb-3"></i>
												<p>Pilih riwayat untuk melihat detail</p>
										</div>
								</div>
						@endif
				</div>
		</div>
</div>
