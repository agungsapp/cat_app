<div>
		<div class="container-fluid py-4">
				<!-- Header -->
				<div class="row mb-4">
						<div class="col-12">
								<div class="card">
										<div class="card-body">
												<div class="row align-items-center">
														<div class="col-md-4">
																<h4 class="mb-0">Bank Soal</h4>
														</div>
														<div class="col-md-8 text-end">
																<a href="{{ route('admin.bank-soal.create') }}" class="btn btn-primary">
																		<i class="fas fa-plus"></i> Tambah Soal
																</a>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Filter & Search -->
				<div class="row mb-4">
						<div class="col-md-6">
								<input type="text" wire:model.live="search" class="form-control" placeholder="Cari soal...">
						</div>
						<div class="col-md-6">
								<select wire:model.live="filterJenis" class="form-select">
										<option value="">Semua Jenis Ujian</option>
										@foreach ($jenisUjian as $jenis)
												<option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
										@endforeach
								</select>
						</div>
				</div>

				<!-- Cards Container -->
				<div class="row">
						@forelse($soals as $index => $soal)
								<div class="col-12 mb-4">
										<div class="card h-100 shadow-sm">
												<div class="card-body">
														<!-- Header Card -->
														<div class="d-flex justify-content-between align-items-start mb-3">
																<div>
																		<span class="badge bg-info">{{ $soal->jenis->nama ?? '-' }}</span>
																		@if ($soal->jenis->tipe_penilaian === 'benar_salah')
																				<span class="badge bg-success ms-2">
																						Skor Benar: {{ $soal->jenis->bobot_per_soal }}
																				</span>
																		@elseif ($soal->jenis->tipe_penilaian === 'bobot_opsi')
																				<span class="badge bg-warning ms-2">
																						Tipe: TKP (Bobot Opsi)
																				</span>
																		@endif

																</div>
																<div>
																		<a href="{{ route('admin.bank-soal.edit', $soal->id) }}" class="btn btn-sm btn-warning">
																				<x-icon name="edit" />
																		</a>
																		<button wire:click="confirmDelete({{ $soal->id }})" class="btn btn-sm btn-danger">
																				<x-icon name="delete" />
																		</button>
																</div>
														</div>

														<!-- Pertanyaan -->
														<div class="mb-3">
																<h5 class="fw-bold">Soal #{{ $soals->firstItem() + $index }}</h5>

																@if ($soal->media_type === 'image' && $soal->media_path)
																		<img src="{{ Storage::url($soal->media_path) }}" alt="Soal Image" class="img-fluid mb-2 rounded"
																				style="max-height: 300px;">
																@elseif($soal->media_type === 'audio' && $soal->media_path)
																		<audio controls class="w-100 mb-2">
																				<source src="{{ Storage::url($soal->media_path) }}" type="audio/mpeg">
																		</audio>
																@endif

																@if ($soal->pertanyaan_text)
																		<p class="mb-0">{{ $soal->pertanyaan_text }}</p>
																@endif
														</div>

														<!-- Opsi Jawaban -->
														<div class="mt-3">
																<h6 class="text-muted mb-2">Pilihan Jawaban:</h6>

																@php
																		$isBenarSalah = $soal->jenis->tipe_penilaian === 'benar_salah';
																@endphp

																@foreach ($soal->opsi as $opsi)
																		<div
																				class="d-flex align-items-start {{ $isBenarSalah && $opsi->is_correct ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }} mb-2 rounded p-2">

																				<div class="me-3">
																						<span class="badge {{ $isBenarSalah && $opsi->is_correct ? 'bg-success' : 'bg-secondary' }}">
																								{{ $opsi->label }}
																						</span>
																				</div>

																				<div class="flex-grow-1 d-flex justify-content-between align-items-center">
																						{{-- Konten opsi --}}
																						@if ($opsi->media_type === 'image' && $opsi->media_path)
																								<img src="{{ Storage::url($opsi->media_path) }}" alt="Opsi {{ $opsi->label }}"
																										class="img-fluid rounded" style="max-height: 150px;">
																						@elseif ($opsi->media_type === 'audio' && $opsi->media_path)
																								<audio controls style="height: 30px; width: 200px;">
																										<source src="{{ Storage::url($opsi->media_path) }}" type="audio/mpeg">
																								</audio>
																						@else
																								<span class="{{ $opsi->is_correct ? 'text-white' : '' }}">{{ $opsi->teks }}</span>
																						@endif

																						{{-- ICON BENAR (HANYA TWK / TIU) --}}
																						@if ($isBenarSalah && $opsi->is_correct)
																								<i class="fas fa-check-circle text-success ms-2"></i>
																						@endif

																						{{-- SKOR OPSI (KHUSUS TKP) --}}
																						@if (!$isBenarSalah)
																								<span class="badge bg-info ms-2">
																										Skor: {{ $opsi->skor }}
																								</span>
																						@endif
																				</div>
																		</div>
																@endforeach
														</div>

												</div>
										</div>
								</div>
						@empty
								<div class="col-12">
										<div class="alert alert-info text-center">
												<i class="fas fa-info-circle fa-2x mb-2"></i>
												<p class="mb-0">Belum ada soal. <a href="{{ route('admin.bank-soal.create') }}">Tambah soal baru</a>.</p>
										</div>
								</div>
						@endforelse
				</div>

				<!-- Pagination -->
				<div class="row">
						<div class="col-12">
								{{ $soals->links() }}
						</div>
				</div>
		</div>
</div>
