<div class="container-fluid py-4">
		<div class="row">
				<div class="col-12">
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center">
										<div>
												<h5>Soal {{ $nomor }} / {{ $totalSoal }}</h5>
												<small class="text-muted">{{ $soal->jenis->nama }}</small>
										</div>
										<div>
												<span class="badge bg-info">Skor: {{ $soal->skor }}</span>
										</div>
								</div>
								<div class="card-body">
										<!-- Pertanyaan -->
										@if ($soal->media_type === 'image' && $soal->media_path)
												<img src="{{ Storage::url($soal->media_path) }}" class="img-fluid mb-3 rounded" style="max-height: 300px;">
										@elseif($soal->media_type === 'audio' && $soal->media_path)
												<audio controls class="w-100 mb-3">
														<source src="{{ Storage::url($soal->media_path) }}">
												</audio>
										@endif
										<p class="fs-5">{!! nl2br(e($soal->pertanyaan_text)) !!}</p>

										<!-- Opsi Jawaban -->
										<div class="mt-4">
												@foreach ($soal->opsi as $opsi)
														<div class="form-check {{ $selectedOpsi == $opsi->id ? 'bg-light' : '' }} mb-3 rounded border p-3">
																<input class="form-check-input" type="radio" name="opsi" id="opsi_{{ $opsi->id }}"
																		wire:click="pilihJawaban({{ $opsi->id }})" {{ $selectedOpsi == $opsi->id ? 'checked' : '' }}>
																<label class="form-check-label d-flex align-items-center" for="opsi_{{ $opsi->id }}">
																		<span class="badge bg-primary me-2">{{ $opsi->label }}</span>
																		<span>
																				@if ($opsi->media_type === 'image' && $opsi->media_path)
																						<img src="{{ Storage::url($opsi->media_path) }}" class="img-thumbnail me-2"
																								style="max-height: 80px;">
																				@elseif($opsi->media_type === 'audio' && $opsi->media_path)
																						<audio controls class="me-2" style="height: 30px;">
																								<source src="{{ Storage::url($opsi->media_path) }}">
																						</audio>
																				@endif
																				{{ $opsi->teks }}
																		</span>
																</label>
														</div>
												@endforeach
										</div>

										<!-- Navigasi -->
										<div class="d-flex justify-content-between mt-4">
												<button wire:click="prev" class="btn btn-secondary" {{ $nomor <= 1 ? 'disabled' : '' }}>
														Sebelumnya
												</button>
												@if ($nomor < $totalSoal)
														<button wire:click="next" class="btn btn-primary">Berikutnya</button>
												@else
														<button wire:click="selesai" class="btn btn-success">Selesai Ujian</button>
												@endif
										</div>
								</div>
						</div>
				</div>
		</div>
</div>
