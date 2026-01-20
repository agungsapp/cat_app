<div class="container-fluid py-4">
		@php
				$isBenarSalah = $tipe_penilaian === 'benar_salah';
				$isBobotOpsi = $tipe_penilaian === 'bobot_opsi';
		@endphp

		<div class="row justify-content-center">
				<div class="col-md-10">
						<div class="card shadow">
								<div class="card-header bg-primary text-white">
										<h4 class="mb-0 text-white">Tambah Soal Baru</h4>
								</div>
								<div class="card-body">
										<form wire:submit.prevent="save">

												<!-- Jenis Ujian -->
												<div class="mb-3">
														<label class="form-label">Jenis Ujian <span class="text-danger">*</span></label>
														<select wire:model.live="jenis_id" class="@error('jenis_id') is-invalid @enderror form-select" required>
																<option value="">-- Pilih Jenis --</option>
																@foreach ($jenisUjian as $jenis)
																		<option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
																@endforeach
														</select>
														@error('jenis_id')
																<small class="text-danger">{{ $message }}</small>
														@enderror
												</div>

												<!-- Media Pertanyaan -->
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
																<label class="form-label">Upload Media</label>
																<input type="file" wire:model="media_file"
																		class="form-control @error('media_file') is-invalid @enderror"
																		accept="{{ $media_type === 'image' ? 'image/*' : 'audio/*' }}">
																@error('media_file')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
																@if ($media_file)
																		<small class="text-success d-block mt-1">File: {{ $media_file->getClientOriginalName() }}</small>
																@endif
														</div>
												@endif

												<div class="mb-3">
														<label class="form-label">Teks Pertanyaan</label>
														<textarea wire:model="pertanyaan_text" class="form-control" rows="3" placeholder="Tulis pertanyaan..."></textarea>
														@error('pertanyaan_text')
																<small class="text-danger">{{ $message }}</small>
														@enderror
												</div>

												<hr>

												<h5 class="mb-3">Pilihan Jawaban</h5>

												@foreach ($opsi as $index => $item)
														@php
																$mediaType = $item['media_type'] ?? 'none';
														@endphp

														<div class="card mb-3">
																<div class="card-body">

																		<!-- HEADER OPSI -->
																		<div class="row align-items-center mb-2">
																				<div class="d-flex align-items-center col-auto">
																						<span class="badge bg-primary fs-6">{{ $item['label'] }}</span>
																				</div>

																				{{-- RADIO JAWABAN BENAR (HANYA BENAR_SALAH) --}}
																				@if ($isBenarSalah)
																						<div class="col d-flex align-items-center">
																								<div class="form-check mb-0">
																										<input class="form-check-input" type="radio" wire:model="correctAnswerIndex"
																												value="{{ $index }}" id="radio-{{ $index }}">
																										<label class="form-check-label" for="radio-{{ $index }}">
																												Jawaban Benar
																										</label>
																								</div>
																						</div>
																				@else
																						<div class="col"></div>
																				@endif

																				<div class="d-flex align-items-center col-auto">
																						@if (count($opsi) > 2)
																								<button type="button" wire:click="removeOpsi({{ $index }})"
																										class="btn btn-sm btn-danger d-inline-flex align-items-center">
																										<i class="fas fa-trash-alt me-1"></i> Hapus
																								</button>
																						@endif
																				</div>
																		</div>

																		<!-- TIPE MEDIA OPSI -->
																		<div class="mb-2">
																				<label class="form-label">Tipe Media Opsi</label>
																				<select wire:model.live="opsi.{{ $index }}.media_type" class="form-select-sm form-select">
																						<option value="none">Teks</option>
																						<option value="image">Gambar</option>
																						<option value="audio">Audio</option>
																				</select>
																		</div>

																		<!-- KONTEN OPSI -->
																		@if ($mediaType === 'none')
																				<input type="text" wire:model="opsi.{{ $index }}.teks" class="form-control mb-2"
																						placeholder="Tulis jawaban...">
																		@else
																				<input type="file" wire:model="opsi.{{ $index }}.media_file" class="form-control mb-2"
																						accept="{{ $mediaType === 'image' ? 'image/*' : 'audio/*' }}">
																		@endif

																		{{-- INPUT SKOR (HANYA TKP) --}}
																		@if ($isBobotOpsi)
																				<div class="mt-2">
																						<label class="form-label">Skor Opsi (1–5)</label>
																						<input type="number" wire:model="opsi.{{ $index }}.skor" min="1" max="5"
																								class="form-control" placeholder="Masukkan skor">
																				</div>
																		@endif

																</div>
														</div>
												@endforeach


												@if (count($opsi) < 8)
														<button type="button" wire:click="addOpsi" class="btn btn-sm btn-outline-primary mb-3">
																Tambah Opsi
														</button>
												@endif

												@if ($isBenarSalah)
														@error('correctAnswerIndex')
																<div class="alert alert-danger">{{ $message }}</div>
														@enderror
												@endif


												<!-- SEMUA ERROR DI BAWAH -->
												@if ($errors->any())
														<div class="alert alert-danger mt-3">
																<strong>Isi form dengan benar:</strong>
																<ul class="mb-0 mt-2">
																		@foreach ($errors->all() as $error)
																				<li>{{ $error }}</li>
																		@endforeach
																</ul>
														</div>
												@endif

												<div class="d-flex justify-content-end mt-4 gap-2">
														<a href="{{ route('admin.bank-soal.index') }}" class="btn btn-secondary">Batal</a>
														<button type="submit" class="btn btn-primary">Simpan Soal</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		</div>
</div>
