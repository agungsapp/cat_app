{{-- resources/views/livewire/peserta/peserta-ujian-kerjakan.blade.php --}}
<div class="container-fluid">

		{{-- Deskripsi Sesi --}}
		<div class="card mb-3 shadow-sm">
				<div class="card-body">
						<h2 class="card-title h4">{{ $sesi->judul }}</h2>
						<p class="card-text text-muted">{{ $sesi->deskripsi }}</p>

						{{-- Info Tambahan --}}
						<div class="d-flex mt-3 flex-wrap gap-3">
								<div class="text-muted small">
										<strong>Tipe Ujian:</strong> {{ $sesi->tipeUjian->nama }}
								</div>
								@if ($sesi->tipeUjian->max_attempt)
										<div class="text-muted small">
												<strong>Max Percobaan:</strong> {{ $sesi->tipeUjian->max_attempt }} kali
										</div>
								@endif
								@if ($sesi->waktu_mulai)
										<div class="text-muted small">
												<strong>Jadwal:</strong>
												{{ \Carbon\Carbon::parse($sesi->waktu_mulai)->format('d M Y H:i') }}
												-
												{{ $sesi->waktu_selesai ? \Carbon\Carbon::parse($sesi->waktu_selesai)->format('d M Y H:i') : 'Tidak dibatasi' }}
										</div>
								@endif
						</div>
				</div>
		</div>

		{{-- Status + Tombol --}}
		<div class="card bg-light mb-3">
				<div class="card-body">
						@if (session('error'))
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
										{{ session('error') }}
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
						@endif

						@if ($status)
								<div class="mb-3">{!! $status !!}</div>
						@endif

						<button wire:click="start" @if (!$canStart) disabled @endif
								class="btn {{ $canStart ? 'btn-primary' : 'btn-secondary' }}">
								{{ $buttonText }}
						</button>
				</div>
		</div>

		{{-- Tabel Riwayat Attempt --}}
		<div class="card shadow-sm">
				<div class="card-body">
						<h3 class="card-title h5 mb-3">Riwayat Pengerjaan</h3>

						<div class="table-responsive">
								<table class="table-bordered table-hover table">
										<thead class="table-secondary">
												<tr>
														<th class="text-center">Percobaan</th>
														<th>Mulai</th>
														<th>Selesai</th>
														<th>Durasi</th>
														<th class="text-center">Skor</th>
														<th class="text-center">Status</th>
														<th class="text-center">Aksi</th>
												</tr>
										</thead>
										<tbody>
												@forelse ($attempts as $i => $attempt)
														<tr class="{{ $loop->first && !$attempt->selesai_at ? 'table-primary' : '' }}">
																<td class="text-center">{{ $i + 1 }}</td>
																<td>{{ \Carbon\Carbon::parse($attempt->mulai_at)->format('d/m/Y H:i') }}</td>
																<td>{{ $attempt->selesai_at ? \Carbon\Carbon::parse($attempt->selesai_at)->format('d/m/Y H:i') : '-' }}
																</td>
																<td>
																		@if ($attempt->selesai_at)
																				{{ \Carbon\Carbon::parse($attempt->mulai_at)->diffForHumans(\Carbon\Carbon::parse($attempt->selesai_at), true) }}
																		@else
																				<span class="text-primary fw-bold">Berlangsung</span>
																		@endif
																</td>
																<td class="fw-bold text-center">{{ $attempt->skor ?? '-' }}</td>
																<td class="text-center">
																		@if (!$attempt->selesai_at)
																				<span class="badge bg-primary">Sedang Dikerjakan</span>
																		@else
																				<span class="badge bg-success">Selesai</span>
																		@endif
																</td>
																<td class="text-center">
																		@if (!$attempt->selesai_at)
																				<a href="{{ route('peserta.ujian.soal', ['hasil_id' => $attempt->id, 'nomor' => 1]) }}"
																						class="btn btn-sm btn-primary">
																						Lanjutkan
																				</a>
																		@else
																				{{-- Uncomment jika sudah ada route review --}}
																				{{-- <a href="{{ route('peserta.ujian.review', $attempt->id) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            Lihat Pembahasan
                                        </a> --}}
																				<span class="text-muted">-</span>
																		@endif
																</td>
														</tr>
												@empty
														<tr>
																<td colspan="7" class="text-muted py-4 text-center">
																		Belum ada riwayat ujian.
																</td>
														</tr>
												@endforelse
										</tbody>
								</table>
						</div>
				</div>
		</div>

</div>
@push('css')
		<style>
				.gap-3>* {
						margin-right: 1rem;
				}
		</style>
@endpush
