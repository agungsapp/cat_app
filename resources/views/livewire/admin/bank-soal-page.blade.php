<div>
		<div class="container-fluid py-4">
				<!-- Header -->
				<div class="row mb-4">
						<div class="col-12">
								<div class="card">
										<div class="card-body">
												<div class="row align-items-center">
														<div class="col-md-4">
																<h4 class="mb-0">📚 Bank Soal</h4>
														</div>
														<div class="col-md-8 text-end">
																<button wire:click="openModal" class="btn btn-primary">
																		<i class="fas fa-plus"></i> Tambah Soal
																</button>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>

				<!-- Filter & Search -->
				<div class="row mb-4">
						<div class="col-md-6">
								<input type="text" wire:model.live="search" class="form-control" placeholder="🔍 Cari soal...">
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
																		<span class="badge bg-success ms-2">Skor: {{ $soal->skor }}</span>
																</div>
																<div>
																		<button wire:click="edit({{ $soal->id }})" class="btn btn-sm btn-warning">
																				<i class="fas fa-edit"></i>
																		</button>
																		<button wire:click="confirmDelete({{ $soal->id }})" class="btn btn-sm btn-danger">
																				<i class="fas fa-trash"></i>
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
																@foreach ($soal->opsi as $opsi)
																		<div
																				class="d-flex align-items-start {{ $opsi->is_correct ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }} mb-2 rounded p-2">
																				<div class="me-3">
																						<span class="badge {{ $opsi->is_correct ? 'bg-success' : 'bg-secondary' }}">
																								{{ $opsi->label }}
																						</span>
																				</div>
																				<div class="flex-grow-1">
																						@if ($opsi->media_type === 'image' && $opsi->media_path)
																								<img src="{{ Storage::url($opsi->media_path) }}" alt="Opsi {{ $opsi->label }}"
																										class="img-fluid rounded" style="max-height: 150px;">
																						@elseif($opsi->media_type === 'audio' && $opsi->media_path)
																								<audio controls style="height: 30px; width: 200px;">
																										<source src="{{ Storage::url($opsi->media_path) }}" type="audio/mpeg">
																								</audio>
																						@else
																								<span>{{ $opsi->teks }}</span>
																						@endif
																						@if ($opsi->is_correct)
																								<i class="fas fa-check-circle text-success ms-2"></i>
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
												<p class="mb-0">Belum ada soal. Klik "Tambah Soal" untuk mulai membuat bank soal.</p>
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

		<!-- Modal Create/Edit -->
		@if ($showModal)
				<div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
						<div class="modal-dialog modal-xl modal-dialog-scrollable">
								<div class="modal-content">
										<div class="modal-header">
												<h5 class="modal-title">{{ $isEdit ? 'Edit Soal' : 'Tambah Soal Baru' }}</h5>
												<button type="button" class="btn-close" wire:click="closeModal"></button>
										</div>
										<div class="modal-body">
												<form wire:submit.prevent="save">
														<!-- Jenis Ujian -->
														<div class="mb-3">
																<label class="form-label">Jenis Ujian <span class="text-danger">*</span></label>
																<select wire:model="jenis_id" class="form-select" required>
																		<option value="">-- Pilih Jenis --</option>
																		@foreach ($jenisUjian as $jenis)
																				<option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
																		@endforeach
																</select>
																@error('jenis_id')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<!-- Pertanyaan -->
														<div class="mb-3">
																<label class="form-label">Tipe Media Pertanyaan</label>
																<select wire:model.live="media_type" class="form-select">
																		<option value="none">Teks Saja</option>
																		<option value="image">Gambar</option>
																		<option value="audio">Audio</option>
																</select>
														</div>

														@if ($media_type !== 'none')
																<div class="mb-3">
																		<label class="form-label">Upload Media Pertanyaan</label>
																		<input type="file" wire:model="media_file" class="form-control"
																				accept="{{ $media_type === 'image' ? 'image/*' : 'audio/*' }}">
																		@error('media_file')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror

																		@if ($media_file)
																				<div class="mt-2">
																						<small class="text-success">✓ File dipilih: {{ $media_file->getClientOriginalName() }}</small>
																				</div>
																		@endif
																</div>
														@endif

														<div class="mb-3">
																<label class="form-label">Teks Pertanyaan</label>
																<textarea wire:model="pertanyaan_text" class="form-control" rows="3"
																  placeholder="Tulis pertanyaan di sini..."></textarea>
																@error('pertanyaan_text')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<!-- Skor -->
														<div class="mb-3">
																<label class="form-label">Skor <span class="text-danger">*</span></label>
																<input type="number" wire:model="skor" class="form-control" min="1" required>
																@error('skor')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<hr>

														<!-- Opsi Jawaban -->
														<h6 class="mb-3">Pilihan Jawaban</h6>
														@foreach ($opsi as $index => $item)
																@php
																		$isCorrect = is_array($item) && isset($item['is_correct']) ? $item['is_correct'] : false;
																		$label = is_array($item) && isset($item['label']) ? $item['label'] : '';
																		$mediaType = is_array($item) && isset($item['media_type']) ? $item['media_type'] : 'none';
																		$teks = is_array($item) && isset($item['teks']) ? $item['teks'] : '';
																@endphp
																<div class="card {{ $isCorrect ? 'border-success' : '' }} mb-3">
																		<div class="card-body">
																				<div class="row align-items-center mb-2">
																						<div class="col-auto">
																								<span class="badge bg-primary">{{ $label }}</span>
																						</div>
																						<div class="col">
																								<div class="form-check" style="cursor: pointer;">
																										<input class="form-check-input" type="radio" name="correct_answer"
																												id="correct_{{ $index }}" value="{{ $index }}"
																												wire:model.live="correctAnswerIndex" style="cursor: pointer;">
																										<label class="form-check-label" for="correct_{{ $index }}"
																												style="cursor: pointer;">
																												Jawaban Benar
																										</label>
																								</div>
																						</div>
																						<div class="col-auto">
																								@if (is_array($opsi) && count($opsi) > 2)
																										<button type="button" wire:click="removeOpsi({{ $index }})"
																												class="btn btn-sm btn-danger">
																												<i class="fas fa-trash"></i>
																										</button>
																								@endif
																						</div>
																				</div>

																				<div class="mb-2">
																						<label class="form-label">Tipe Media</label>
																						<select wire:model.live="opsi.{{ $index }}.media_type"
																								class="form-select-sm form-select">
																								<option value="none">Teks</option>
																								<option value="image">Gambar</option>
																								<option value="audio">Audio</option>
																						</select>
																				</div>

																				@if ($mediaType === 'none')
																						<input type="text" wire:model="opsi.{{ $index }}.teks" class="form-control"
																								placeholder="Tulis jawaban...">
																				@else
																						<input type="file" wire:model="opsi.{{ $index }}.media_file" class="form-control"
																								accept="{{ $mediaType === 'image' ? 'image/*' : 'audio/*' }}">
																						@if (isset($item['media_file']) && $item['media_file'])
																								<small class="text-success">✓ {{ $item['media_file']->getClientOriginalName() }}</small>
																						@endif
																				@endif
																		</div>
																</div>
														@endforeach

														@if (count($opsi) < 8)
																<button type="button" wire:click="addOpsi" class="btn btn-sm btn-outline-primary mb-3">
																		<i class="fas fa-plus"></i> Tambah Opsi
																</button>
														@endif

														@error('opsi')
																<div class="alert alert-danger">{{ $message }}</div>
														@enderror
												</form>
										</div>
										<div class="modal-footer">
												<button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
												<button type="button" wire:click="save" class="btn btn-primary">
														<i class="fas fa-save"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
												</button>
										</div>
								</div>
						</div>
				</div>
		@endif
</div>
