<div class="container-fluid py-4">
		<div class="row justify-content-center">
				<div class="col-md-8">
						<div class="card border-0 shadow-lg">
								<div class="card-header bg-success py-4 text-center text-white">
										<h3 class="mb-0">
												<i class="fas fa-check-circle"></i> Ujian Selesai!
										</h3>
								</div>
								<div class="card-body py-5 text-center">
										<div class="display-1 fw-bold text-success mb-3">
												{{ $skor }}
										</div>
										<p class="lead text-muted">
												dari {{ $totalSoal }} soal ({{ $jawabanBenar }} benar)
										</p>

										<div class="row mt-4">
												<div class="col-md-6 mb-3">
														<div class="card border-primary">
																<div class="card-body">
																		<h5 class="text-primary">
																				<i class="fas fa-clock"></i> Durasi
																		</h5>
																		<p class="fs-4 mb-0">{{ $durasiDigunakan }} menit</p>
																		<small class="text-muted">dari {{ $hasil->sesiUjian->durasi_menit }} menit</small>
																</div>
														</div>
												</div>
												<div class="col-md-6 mb-3">
														<div class="card border-info">
																<div class="card-body">
																		<h5 class="text-info">
																				<i class="fas fa-trophy"></i> Sesi
																		</h5>
																		<p class="fs-5 mb-0">{{ $hasil->sesiUjian->judul }}</p>
																		<small class="text-muted">{{ $hasil->sesiUjian->tipeUjian->nama }}</small>
																</div>
														</div>
												</div>
										</div>

										<div class="mt-4">
												<a href="{{ route('peserta.dashboard.index') }}" class="btn btn-lg btn-outline-primary">
														Kembali ke Dashboard
												</a>
										</div>
								</div>
						</div>
				</div>
		</div>
</div>
