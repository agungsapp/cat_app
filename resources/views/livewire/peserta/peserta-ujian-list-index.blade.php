<div class="container-fluid py-4">
		<div class="row">
				<div class="col-12">
						<div class="card">
								<div class="card-header pb-0">
										<h5 class="mb-0">
												{{ $tipeUjian->nama }}
												<span class="badge bg-primary ms-2">{{ $sesi->count() }} Sesi Tersedia</span>
										</h5>
								</div>
								<div class="card-body">
										@forelse($sesi as $item)
												<div class="card mb-3 shadow-sm">
														<div class="card-body">
																<div class="d-flex justify-content-between align-items-start">
																		<div class="flex-grow-1">
																				<h6 class="mb-1">{{ $item->judul }}</h6>
																				@if ($item->deskripsi)
																						<p class="text-muted small mb-2">{{ Str::limit($item->deskripsi, 100) }}</p>
																				@endif
																				<div class="d-flex small text-muted gap-3">
																						<span><i class="fas fa-clock"></i> {{ $item->durasi_menit }} menit</span>
																						@if ($item->waktu_mulai)
																								<span><i class="fas fa-calendar"></i> {{ $item->waktu_mulai->format('d M Y H:i') }}</span>
																						@endif
																				</div>
																		</div>
																		<div>
																				<button class="btn btn-success btn-sm" disabled>
																						<i class="fas fa-play"></i> Mulai
																				</button>
																		</div>
																</div>
														</div>
												</div>
										@empty
												<div class="py-5 text-center">
														<i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
														<p class="text-muted">Belum ada sesi ujian untuk {{ $tipeUjian->nama }} saat ini.</p>
												</div>
										@endforelse
								</div>
						</div>
				</div>
		</div>
</div>
