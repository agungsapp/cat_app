<div class="container-fluid py-4">
		<div class="row justify-content-center">
				<div class="col-md-10">
						<div class="card shadow">
								<div class="card-header bg-primary text-white">
										<h4 class="mb-0">Tambah Soal Baru</h4>
								</div>
								<div class="card-body">
										<form wire:submit.prevent="save">

												<!-- Jenis Ujian -->
												<div class="mb-3">
														<label class="form-label">Jenis Ujian <span class="text-danger">*</span></label>
														<select wire:model="jenis_id" class="@error('jenis_id') is-invalid @enderror form-select" required>
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

												<div class="mb-3">
														<label class="form-label">Skor <span class="text-danger">*</span></label>
														<input type="number" wire:model="skor" class="form-control @error('skor') is-invalid @enderror"
																min="1" required>
														@error('skor')
																<small class="text-danger">{{ $message }}</small>
														@enderror
												</div>

												<hr>

												<h5 class="mb-3">Pilihan Jawaban</h5>

												@foreach ($opsi as $index => $item)
														@php
																$isCorrect = $item['is_correct'] ?? false;
																$mediaType = $item['media_type'] ?? 'none';
														@endphp

														<div class="card {{ $isCorrect ? 'border-success border-2' : '' }} mb-3">
																<div class="card-body">
																		<div class="row align-items-center mb-2">
																				<div class="col-auto">
																						<span class="badge bg-primary fs-6">{{ $item['label'] }}</span>
																				</div>
																				<div class="col">
																						<div class="form-check">
																								<input class="form-check-input" type="radio" name="correct_answer"
																										id="correct_{{ $index }}" value="{{ $index }}"
																										wire:model.live="correctAnswerIndex">
																								<label class="form-check-label" for="correct_{{ $index }}">
																										Jawaban Benar
																								</label>
																						</div>
																				</div>
																				<div class="col-auto">
																						@if (count($opsi) > 2)
																								<button type="button" wire:click="removeOpsi({{ $index }})"
																										class="btn btn-sm btn-danger">Hapus</button>
																						@endif
																				</div>
																		</div>

																		<div class="mb-2">
																				<label class="form-label">Tipe Media Opsi</label>
																				<select wire:model.live="opsi.{{ $index }}.media_type" class="form-select-sm form-select">
																						<option value="none">Teks</option>
																						<option value="image">Gambar</option>
																						<option value="audio">Audio</option>
																				</select>
																		</div>

																		@if ($mediaType === 'none')
																				<input type="text" wire:model="opsi.{{ $index }}.teks"
																						class="form-control @error("opsi.{$index}.teks") is-invalid @enderror"
																						placeholder="Tulis jawaban...">
																				@error("opsi.{$index}.teks")
																						<small class="text-danger">{{ $message }}</small>
																				@enderror
																		@else
																				<input type="file" wire:model="opsi.{{ $index }}.media_file"
																						class="form-control @error("opsi.{$index}.media_file") is-invalid @enderror"
																						accept="{{ $mediaType === 'image' ? 'image/*' : 'audio/*' }}">
																				@error("opsi.{$index}.media_file")
																						<small class="text-danger">{{ $message }}</small>
																				@enderror
																				@if (isset($item['media_file']) && $item['media_file'])
																						<small class="text-success d-block mt-1">
																								File: {{ $item['media_file']->getClientOriginalName() }}
																						</small>
																				@endif
																		@endif
																</div>
														</div>
												@endforeach

												@if (count($opsi) < 8)
														<button type="button" wire:click="addOpsi" class="btn btn-sm btn-outline-primary mb-3">
																Tambah Opsi
														</button>
												@endif

												@error('correctAnswerIndex')
														<div class="alert alert-danger">{{ $message }}</div>
												@enderror

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
