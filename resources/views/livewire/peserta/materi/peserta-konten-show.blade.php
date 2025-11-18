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
								<a href="{{ route('peserta.materi.show', $materi->id) }}">{{ $materi->judul }}</a>
						</li>
						<li class="breadcrumb-item active">{{ $konten->isi ?? 'Konten' }}</li>
				</ol>
		</nav>

		<!-- Header -->
		<div class="card mb-4 border-0 shadow-sm">
				<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
								<div>
										<h2 class="fw-bold mb-1">
												@if ($konten->tipe === 'video')
														Video
												@else
														PDF
												@endif
												{{ $konten->isi ?? 'Tanpa Judul' }}
										</h2>
										<p class="text-muted mb-0">Submateri: <strong>{{ $konten->submateri->judul }}</strong></p>
								</div>
								<div>
										@if ($isCompleted)
												<span class="badge bg-success">SELESAI</span>
										@else
												<div class="spinner-border text-primary spinner-border-sm"></div>
										@endif
								</div>
						</div>
				</div>
		</div>

		<!-- KONTEN -->
		@if ($konten->tipe === 'video')
				<div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-lg">
						<iframe src="{{ $konten->getYouTubeEmbedUrl() }}" allowfullscreen class="border-0"></iframe>
				</div>
		@elseif ($konten->tipe === 'pdf' && $pdfUrl)
				<!-- PDF VIEWER DENGAN IFRAME -->
				<div class="card rounded-3 overflow-hidden border-0 shadow-lg">
						<div class="card-header d-flex justify-content-between align-items-center bg-white py-2">
								<h6 class="mb-0">Preview PDF</h6>
								<div>
										<a href="{{ $pdfUrl }}?v={{ time() }}" target="_blank"
												class="btn btn-sm btn-outline-primary">Buka Tab Baru</a>
										<a href="{{ $pdfUrl }}?v={{ time() }}" download class="btn btn-sm btn-outline-danger">Unduh</a>
								</div>
						</div>
						<div class="card-body position-relative p-0" style="height: 80vh;">
								<!-- LOADING SPINNER -->
								<div id="pdf-loading" class="d-flex align-items-center justify-content-center h-100 bg-light">
										<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
												<span class="visually-hidden">Memuat PDF...</span>
										</div>
								</div>

								<!-- IFRAME PDF -->
								<iframe id="pdf-iframe"
										src="{{ $pdfUrl }}?v={{ time() }}#toolbar=0&navpanes=0&scrollbar=1&view=FitH"
										type="application/pdf" width="100%" height="100%" class="d-none border-0"
										onload="this.classList.remove('d-none'); document.getElementById('pdf-loading').remove();"
										onerror="document.getElementById('pdf-error').classList.remove('d-none');">
								</iframe>

								<!-- ERROR NOTICE -->
								<div id="pdf-error"
										class="d-none text-danger bg-light h-100 d-flex align-items-center justify-content-center p-4 text-center">
										Gagal memuat PDF.
										<a href="{{ $pdfUrl }}?v={{ time() }}" target="_blank" class="link ms-2">Coba buka di tab
												baru</a>
								</div>
						</div>
				</div>
				<div class="alert alert-warning">File PDF tidak ditemukan.</div>
		@endif

		<!-- Action Buttons -->
		<div class="mt-4 text-center">
				<a href="{{ route('peserta.materi.show', $materi->id) }}" class="btn btn-outline-secondary btn-lg">
						Kembali
				</a>
		</div>

		<!-- CSS -->
		@push('css')
				<style>
						embed {
								display: block;
						}

						.card-header .btn-sm {
								font-size: 0.8rem;
								padding: 0.25rem 0.5rem;
						}
				</style>
		@endpush
</div>
