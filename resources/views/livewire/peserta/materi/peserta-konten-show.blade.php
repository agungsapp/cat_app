<div>
		<!-- Breadcrumb -->
		<nav aria-label="breadcrumb" class="mb-4">
				<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('peserta.materi.index') }}">Materi</a></li>
						<li class="breadcrumb-item">
								<a href="{{ route('peserta.materi.index') }}#topik-{{ $konten->submateri->materi->topik->id }}">
										{{ $konten->submateri->materi->topik->nama_topik }}
								</a>
						</li>
						<li class="breadcrumb-item">
								<a href="{{ route('peserta.materi.show', $materi->id) }}">
										{{ $materi->judul }}
								</a>
						</li>
						<li class="breadcrumb-item active">{{ $konten->isi ?? 'Konten' }}</li>
				</ol>
		</nav>

		<!-- Header Konten -->
		<div class="card mb-4 border-0 shadow-sm">
				<div class="card-body">
						<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">
								<div>
										<h2 class="fw-bold mb-2">
												@if ($konten->tipe === 'video')
														Video
												@else
														PDF
												@endif
												{{ $konten->isi ?? 'Tanpa Judul' }}
										</h2>
										<p class="text-muted mb-0">
												Submateri: <strong>{{ $konten->submateri->judul }}</strong>
										</p>
								</div>
								<div class="text-md-end text-center">
										@if ($isCompleted)
												<div class="text-success fs-1 mb-2">
														<i class='bx bxs-check-circle'></i>
												</div>
												<span class="badge bg-success fs-6">SELESAI</span>
										@else
												<div class="spinner-border text-primary" role="status">
														<span class="visually-hidden">Loading...</span>
												</div>
												<small class="text-primary d-block mt-2">Menandai selesai...</small>
										@endif
								</div>
						</div>
				</div>
		</div>

		<!-- Konten Viewer -->
		<div class="card rounded-3 overflow-hidden border-0 shadow-lg">
				<div class="card-body p-0">
						@if ($konten->tipe === 'video')
								<!-- YouTube Embed -->
								<div class="ratio ratio-16x9">
										<iframe src="{{ $konten->getYouTubeEmbedUrl() }}" title="YouTube video" frameborder="0"
												allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
												allowfullscreen class="rounded">
										</iframe>
								</div>
						@else
								<!-- PDF Viewer via Controller (BEST PRACTICE!) -->
								<div style="height: 80vh; background: #f8f9fa;">
										<iframe src="{{ route('pdf.view', $konten->id) }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH&zoom=100"
												width="100%" height="100%" class="border-0" style="background: white;" onload="this.style.opacity = 1"
												style="opacity: 0; transition: opacity 0.5s ease;">
												<p>
														Browser tidak mendukung iframe.
														<a href="{{ route('pdf.view', $konten->id) }}" target="_blank" class="btn btn-sm btn-primary">
																Buka PDF di Tab Baru
														</a>
												</p>
										</iframe>
								</div>
						@endif
				</div>
		</div>

		<!-- Action Buttons -->
		<div class="mt-4 text-center">
				<a href="{{ route('peserta.materi.show', $materi->id) }}" class="btn btn-outline-secondary btn-lg me-3">
						Kembali ke Materi
				</a>

				@if ($konten->tipe === 'pdf')
						<a href="{{ route('pdf.download', $konten->id) }}" class="btn btn-outline-danger btn-lg me-3">
								Download PDF
						</a>
						<a href="{{ route('pdf.view', $konten->id) }}" target="_blank" class="btn btn-outline-primary btn-lg">
								Buka di Tab Baru
						</a>
				@endif
		</div>

		@push('css')
				<style>
						.ratio iframe {
								border-radius: 1rem;
						}

						iframe {
								transition: opacity 0.5s ease;
						}
				</style>
		@endpush
</div>
