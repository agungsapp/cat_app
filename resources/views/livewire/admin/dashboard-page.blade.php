<div>
		<!-- Header dengan tombol refresh -->
		<div class="d-flex justify-content-between align-items-center mb-4">
				<div>
						<h4 class="mb-0">Dashboard Admin</h4>
						<p class="text-muted mb-0 text-sm">Ringkasan sistem ujian online</p>
				</div>
				<button wire:click="refresh" class="btn btn-sm btn-outline-primary">
						<i class="fas fa-sync-alt me-1"></i> Refresh
				</button>
		</div>

		<!-- 4 Kartu Statistik -->
		<div class="row mb-4">
				<!-- Total Peserta -->
				<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Total Peserta</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ number_format($totalPeserta) }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-secondary">Terdaftar</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-primary shadow-primary rounded-circle text-center">
																<i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Ujian Aktif -->
				<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Ujian Aktif</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ $ujianAktif }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-success">Sedang berlangsung</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-success shadow-success rounded-circle text-center">
																<i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Ujian Selesai Hari Ini -->
				<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Selesai Hari Ini</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ $ujianSelesaiHariIni }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-info">Ujian diselesaikan</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-info shadow-info rounded-circle text-center">
																<i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Rata-rata Nilai -->
				<div class="col-xl-3 col-sm-6">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Rata-rata Nilai</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ number_format($rataRataNilai, 1) }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-warning">Dari semua ujian</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-warning shadow-warning rounded-circle text-center">
																<i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>
		</div>

		<div class="row">
				<!-- Ujian Sedang Berlangsung -->
				<div class="col-lg-7 mb-4">
						<div class="card h-100">
								<div class="card-header p-3 pb-0">
										<div class="d-flex justify-content-between align-items-center">
												<h6 class="mb-0">
														<i class="fas fa-clock text-warning me-2"></i>
														Ujian Sedang Berlangsung
												</h6>
												<span class="badge bg-warning text-dark">{{ $ujianBerlangsung->count() }} Aktif</span>
										</div>
								</div>
								<div class="card-body p-3">
										@if ($ujianBerlangsung->count() > 0)
												<div class="table-responsive">
														<table class="table-sm align-items-center mb-0 table">
																<thead>
																		<tr>
																				<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Peserta</th>
																				<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ujian</th>
																				<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mulai</th>
																				<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status
																				</th>
																		</tr>
																</thead>
																<tbody>
																		@foreach ($ujianBerlangsung as $hasil)
																				<tr>
																						<td>
																								<div class="d-flex px-2 py-1">
																										<div class="d-flex flex-column justify-content-center">
																												<h6 class="mb-0 text-sm">{{ $hasil->user->name }}</h6>
																												<p class="text-secondary mb-0 text-xs">{{ $hasil->user->email }}</p>
																										</div>
																								</div>
																						</td>
																						<td>
																								<p class="font-weight-bold mb-0 text-sm">{{ Str::limit($hasil->sesiUjian->nama, 30) }}</p>
																						</td>
																						<td>
																								<p class="text-secondary mb-0 text-xs">{{ $hasil->mulai_at->diffForHumans() }}</p>
																						</td>
																						<td class="text-center align-middle">
																								<span class="badge badge-sm bg-gradient-warning">
																										<i class="fas fa-circle text-white" style="font-size: 0.5rem;"></i>
																										Berlangsung
																								</span>
																						</td>
																				</tr>
																		@endforeach
																</tbody>
														</table>
												</div>
										@else
												<div class="py-4 text-center">
														<i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
														<p class="text-muted mb-0 mt-2">Tidak ada ujian yang sedang berlangsung</p>
												</div>
										@endif
								</div>
						</div>
				</div>

				<!-- Riwayat Ujian Terbaru -->
				<div class="col-lg-5 mb-4">
						<div class="card h-100">
								<div class="card-header p-3 pb-0">
										<h6 class="mb-0">
												<i class="fas fa-history text-success me-2"></i>
												Riwayat Terbaru
										</h6>
								</div>
								<div class="card-body p-3">
										@if ($riwayatTerbaru->count() > 0)
												<div class="timeline timeline-one-side">
														@foreach ($riwayatTerbaru->take(5) as $hasil)
																<div class="timeline-block mb-3">
																		<span
																				class="timeline-step bg-gradient-{{ $hasil->skor >= 75 ? 'success' : ($hasil->skor >= 60 ? 'warning' : 'danger') }}">
																				<i class="ni ni-{{ $hasil->skor >= 75 ? 'check-bold' : 'bell-55' }} text-white"></i>
																		</span>
																		<div class="timeline-content">
																				<h6 class="text-dark font-weight-bold mb-0 text-sm">
																						{{ $hasil->user->name }}
																				</h6>
																				<p class="text-secondary font-weight-normal mb-0 mt-1 text-xs">
																						{{ Str::limit($hasil->sesiUjian->nama, 35) }}
																				</p>
																				<p class="mb-0 mt-1 text-xs">
																						<span
																								class="badge badge-sm bg-gradient-{{ $hasil->skor >= 75 ? 'success' : ($hasil->skor >= 60 ? 'warning' : 'danger') }}">
																								Nilai: {{ number_format($hasil->skor, 1) }}
																						</span>
																						<span class="text-muted ms-2">{{ $hasil->selesai_at->diffForHumans() }}</span>
																				</p>
																		</div>
																</div>
														@endforeach
												</div>
										@else
												<div class="py-4 text-center">
														<i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
														<p class="text-muted mb-0 mt-2">Belum ada riwayat ujian</p>
												</div>
										@endif
								</div>
						</div>
				</div>
		</div>

		<!-- Loading Indicator -->
		{{-- <div wire:loading.flex
				class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center start-0 top-0"
				style="background: rgba(0,0,0,0.3); z-index: 9999;">
				<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
						<span class="visually-hidden">Loading...</span>
				</div>
		</div> --}}
</div>

@push('js')
		<script>
				// Auto-refresh setiap 30 detik
				setInterval(() => {
						@this.call('refresh');
				}, 30000);

				// Notifikasi setelah refresh
				window.addEventListener('dashboard-refreshed', () => {
						console.log('Dashboard refreshed');
				});
		</script>
@endpush
