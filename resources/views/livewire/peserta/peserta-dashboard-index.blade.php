<div>
		<!-- Header -->
		<div class="d-flex justify-content-between align-items-center mb-4">
				<div>
						<h4 class="mb-0">Dashboard Peserta</h4>
						<p class="text-muted mb-0 text-sm">Selamat datang, <strong>{{ Auth::user()->name }}</strong></p>
				</div>
				<button wire:click="refresh" class="btn btn-sm btn-outline-primary">
						<i class="fas fa-sync-alt me-1"></i> Refresh
				</button>
		</div>

		<!-- 4 Kartu Statistik Peserta -->
		<div class="row mb-4">
				<!-- Ujian Tersedia -->
				<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Ujian Tersedia</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ $ujianTersedia }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-primary">Dapat dikerjakan</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-primary shadow-primary rounded-circle text-center">
																<i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Ujian Selesai -->
				<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Ujian Selesai</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ $ujianSelesai }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-success">Telah dikerjakan</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-success shadow-success rounded-circle text-center">
																<i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Rata-rata Nilai -->
				<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
																		<span class="text-info">Dari semua ujian</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-info shadow-info rounded-circle text-center">
																<i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Nilai Tertinggi -->
				<div class="col-xl-3 col-sm-6">
						<div class="card">
								<div class="card-body p-3">
										<div class="row">
												<div class="col-8">
														<div class="numbers">
																<p class="text-uppercase font-weight-bold text-muted mb-0 text-sm">Nilai Tertinggi</p>
																<h5 class="font-weight-bolder mb-0">
																		{{ number_format($nilaiTertinggi, 1) }}
																</h5>
																<p class="mb-0 text-sm">
																		<span class="text-warning">Pencapaian terbaik</span>
																</p>
														</div>
												</div>
												<div class="col-4 text-end">
														<div class="icon icon-shape bg-gradient-warning shadow-warning rounded-circle text-center">
																<i class="ni ni-trophy text-lg opacity-10" aria-hidden="true"></i>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>
		</div>

		<div class="row">
				<!-- Ujian Aktif / Tersedia -->
				<div class="col-lg-7 mb-4">
						<div class="card h-100">
								<div class="card-header p-3 pb-0">
										<div class="d-flex justify-content-between align-items-center">
												<h6 class="mb-0">
														<i class="fas fa-clipboard-list text-primary me-2"></i>
														Ujian Tersedia
												</h6>
												<span class="badge bg-primary">{{ $ujianAktif->count() }} Ujian</span>
										</div>
								</div>
								<div class="card-body p-3">
										@if ($ujianAktif->count() > 0)
												<div class="list-group">
														@foreach ($ujianAktif as $sesi)
																<div class="list-group-item mb-3 rounded border p-3">
																		<div class="d-flex justify-content-between align-items-start">
																				<div class="flex-grow-1">
																						<h6 class="text-dark mb-1">{{ $sesi->nama }}</h6>
																						<p class="text-muted mb-2 text-sm">{{ Str::limit($sesi->deskripsi, 80) }}</p>

																						<div class="d-flex gap-3 text-xs">
																								<span>
																										<i class="fas fa-question-circle text-primary me-1"></i>
																										{{ $sesi->soal_count }} Soal
																								</span>
																								<span>
																										<i class="fas fa-clock text-warning me-1"></i>
																										{{ $sesi->durasi_menit }} Menit
																								</span>
																								<span>
																										<i class="fas fa-calendar text-info me-1"></i>
																										Sampai {{ $sesi->waktu_selesai->format('d M Y H:i') }}
																								</span>
																						</div>
																				</div>

																				<div class="ms-3 text-end">
																						<a href="{{ route('peserta.ujian.mulai', $sesi->id) }}" class="btn btn-sm btn-primary">
																								<i class="fas fa-play me-1"></i>
																								Mulai Ujian
																						</a>
																				</div>
																		</div>
																</div>
														@endforeach
												</div>

												@if ($ujianTersedia > 5)
														<div class="mt-3 text-center">
																<a href="{{ route('peserta.ujian.index', 'semua') }}" class="btn btn-sm btn-outline-primary">
																		Lihat Semua Ujian <i class="fas fa-arrow-right ms-1"></i>
																</a>
														</div>
												@endif
										@else
												<div class="py-5 text-center">
														<i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
														<p class="text-muted mb-0 mt-3">Belum ada ujian yang tersedia saat ini</p>
														<p class="text-muted text-sm">Silakan cek kembali nanti</p>
												</div>
										@endif
								</div>
						</div>
				</div>

				<!-- Riwayat Ujian Terbaru -->
				<div class="col-lg-5 mb-4">
						<div class="card h-100">
								<div class="card-header p-3 pb-0">
										<div class="d-flex justify-content-between align-items-center">
												<h6 class="mb-0">
														<i class="fas fa-history text-success me-2"></i>
														Riwayat Terbaru
												</h6>
												@if ($riwayatTerbaru->count() > 0)
														<a href="{{ route('peserta.riwayat-ujian.index') }}" class="text-primary text-xs">
																Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
														</a>
												@endif
										</div>
								</div>
								<div class="card-body p-3">
										@if ($riwayatTerbaru->count() > 0)
												<div class="timeline timeline-one-side">
														@foreach ($riwayatTerbaru as $hasil)
																@php
																		// ✅ Kategori berdasarkan skor (tanpa "lulus/tidak lulus")
																		$kategori = match (true) {
																		    $hasil->skor >= 400 => ['class' => 'success', 'icon' => 'trophy', 'label' => 'Sangat Baik'],
																		    $hasil->skor >= 300 => ['class' => 'info', 'icon' => 'check-bold', 'label' => 'Baik'],
																		    $hasil->skor >= 200 => ['class' => 'warning', 'icon' => 'satisfied', 'label' => 'Cukup'],
																		    default => ['class' => 'secondary', 'icon' => 'bullet-list-67', 'label' => 'Perlu Ditingkatkan'],
																		};
																@endphp

																<div class="timeline-block mb-3">
																		<span class="timeline-step bg-gradient-{{ $kategori['class'] }}">
																				<i class="ni ni-{{ $kategori['icon'] }} text-white"></i>
																		</span>
																		<div class="timeline-content">
																				<h6 class="text-dark font-weight-bold mb-0 text-sm">
																						{{ Str::limit($hasil->sesiUjian->judul, 30) }}
																				</h6>
																				<p class="text-muted mb-1 mt-1 text-xs">
																						{{ $hasil->sesiUjian->tipeUjian->nama }}
																				</p>
																				<p class="mb-2 text-xs">
																						<span class="badge badge-sm bg-gradient-{{ $kategori['class'] }}">
																								Skor: {{ number_format($hasil->skor, 0) }} - {{ $kategori['label'] }}
																						</span>
																				</p>
																				<p class="text-secondary font-weight-normal mb-0 text-xs">
																						<i class="fas fa-clock me-1"></i>
																						{{ $hasil->selesai_at->diffForHumans() }}
																				</p>
																				<a href="{{ route('peserta.ujian.selesai', $hasil->id) }}" class="text-primary text-xs">
																						Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
																				</a>
																		</div>
																</div>
														@endforeach
												</div>
										@else
												<div class="py-5 text-center">
														<i class="fas fa-clipboard-check text-muted" style="font-size: 3rem;"></i>
														<p class="text-muted mb-0 mt-3">Belum ada riwayat ujian</p>
														<p class="text-muted text-sm">Mulai kerjakan ujian pertama Anda!</p>
												</div>
										@endif
								</div>
						</div>
				</div>
		</div>

		<!-- Quick Info / Tips -->
		@if ($ujianTersedia > 0)
				<div class="row">
						<div class="col-12">
								<div class="alert alert-info alert-dismissible fade show" role="alert">
										<span class="alert-icon text-white"><i class="ni ni-bell-55"></i></span>
										<span class="alert-text text-white">
												<strong>Info:</strong> Ada <strong>{{ $ujianTersedia }}</strong> ujian yang dapat Anda kerjakan saat ini.
												@if ($ujianAktif->where('tipeUjian.slug', 'simulasi')->count() > 0)
														Termasuk <strong>{{ $ujianAktif->where('tipeUjian.slug', 'simulasi')->count() }}</strong> simulasi CPNS!
												@endif
												Pastikan Anda siap sebelum memulai.
										</span>
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
												<span aria-hidden="true">&times;</span>
										</button>
								</div>
						</div>
				</div>
		@elseif ($ujianSelesai > 0 && $ujianTersedia == 0)
				<div class="row">
						<div class="col-12">
								<div class="alert alert-success alert-dismissible fade show" role="alert">
										<span class="alert-icon text-white"><i class="ni ni-like-2"></i></span>
										<span class="alert-text text-white">
												Bagus! Anda telah menyelesaikan <strong>{{ $ujianSelesai }}</strong> ujian.
												Rata-rata skor Anda: <strong>{{ number_format($rataRataNilai, 0) }}</strong>.
												Terus tingkatkan!
										</span>
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
												<span aria-hidden="true">&times;</span>
										</button>
								</div>
						</div>
				</div>
		@endif

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
				// Auto-refresh setiap 60 detik (untuk update ujian baru)
				setInterval(() => {
						@this.call('refresh');
				}, 60000);

				// Event listener untuk refresh
				window.addEventListener('dashboard-refreshed', () => {
						console.log('Dashboard peserta refreshed');
				});
		</script>
@endpush
