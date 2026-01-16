<div class="container py-4">
		<div class="row justify-content-center">
				<div class="col-md-8">
						<div class="card">
								<div class="card-header bg-primary text-white">
										<h4 class="text-white">Buat Sesi Ujian</h4>
								</div>
								<div class="card-body">
										<form wire:submit.prevent="save">
												<div class="mb-3">
														<label>Judul <span class="text-danger">*</span></label>
														<input type="text" wire:model="judul" class="form-control @error('judul') is-invalid @enderror">
														@error('judul')
																<small class="text-danger">{{ $message }}</small>
														@enderror
												</div>

												<div class="mb-3">
														<label>Deskripsi</label>
														<textarea wire:model="deskripsi" class="form-control" rows="3" placeholder="Opsional: keterangan tambahan"></textarea>
														@error('deskripsi')
																<small class="text-danger">{{ $message }}</small>
														@enderror
												</div>


												<div class="row">

														<div class="col-4 mb-3">
																<label>Durasi (menit) <span class="text-danger">*</span></label>
																<input type="number" wire:model="durasi_menit"
																		class="form-control @error('durasi_menit') is-invalid @enderror" min="1">
																@error('durasi_menit')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<div class="col-4 mb-3">
																<label>Waktu Mulai</label>
																<input type="datetime-local" wire:model="waktu_mulai" class="form-control">
														</div>

														<div class="col-4 mb-3">
																<label>Waktu Selesai</label>
																<input type="datetime-local" wire:model="waktu_selesai"
																		class="form-control @error('waktu_selesai') is-invalid @enderror">
																@error('waktu_selesai')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

												</div>
												<hr class="border">
												<div class="row">
														<div class="col-4 mb-3">
																<label>Tipe Ujian <span class="text-danger">*</span></label>
																<select wire:model.live="tipe_ujian_id"
																		class="@error('tipe_ujian_id') is-invalid @enderror form-select">
																		<option value="">Pilih Tipe</option>
																		@foreach ($tipeUjian as $t)
																				<option value="{{ $t->id }}">{{ $t->nama . ' | ' . $t->mode }}</option>
																		@endforeach
																</select>
																@error('tipe_ujian_id')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>
												</div>


												@if ($mode == 'random_by_jenis')
														<div class="card mb-4">
																<div class="card-body">
																		{{-- row komposisi soal --}}
																		<div class="row">
																				<div class="col-5 mb-3">
																						<label>Jenis Soal <span class="text-danger">*</span></label>
																						<select wire:model="jenis_ujian_id"
																								class="@error('jenis_ujian_id') is-invalid @enderror form-select">
																								<option value="">Pilih Tipe</option>
																								@foreach ($jenisUjian as $ju)
																										<option value="{{ $ju->id }}">{{ $ju->nama }}</option>
																								@endforeach
																						</select>
																						@error('jenis_ujian_id')
																								<small class="text-danger">{{ $message }}</small>
																						@enderror
																				</div>

																				<div class="col-3 mb-3">
																						<label>Jumlah Soal<span class="text-danger">*</span></label>
																						<input type="number" wire:model="jumlah_soal"
																								class="form-control @error('jumlah_soal') is-invalid @enderror" min="1">
																						@error('jumlah_soal')
																								<small class="text-danger">{{ $message }}</small>
																						@enderror
																				</div>

																				<div class="col-2 mb-3">
																						<label>Durasi (menit) <span class="text-danger">*</span></label>
																						<input type="number" wire:model="durasi_menit"
																								class="form-control @error('durasi_menit') is-invalid @enderror" min="1">
																						@error('durasi_menit')
																								<small class="text-danger">{{ $message }}</small>
																						@enderror
																				</div>

																				<div class="col-2 align-items-end d-flex">
																						<button class="btn btn-danger"><i class="bx fs-5 bx-trash"></i></button>
																				</div>

																		</div>

																		{{-- row button add komposisi --}}
																		<div class="row mt-2">
																				<button class="btn btn-primary col-3 mx-auto">+ Tambah Komposisi</button>
																		</div>

																</div>
														</div>
												@elseif($mode == 'random_all')
														<div class="row">
																<div class="col-3 mb-3">
																		<label>Jumlah Soal<span class="text-danger">*</span></label>
																		<input type="number" wire:model="jumlah_soal"
																				class="form-control @error('jumlah_soal') is-invalid @enderror" min="1">
																		@error('jumlah_soal')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																</div>

																<div class="col-2 mb-3">
																		<label>Durasi (menit) <span class="text-danger">*</span></label>
																		<input type="number" wire:model="durasi_menit"
																				class="form-control @error('durasi_menit') is-invalid @enderror" min="1">
																		@error('durasi_menit')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																</div>
														</div>

												@endif


												<div class="form-check d-none mb-3">
														<input type="checkbox" wire:model="is_active" class="form-check-input" id="active">
														<label class="form-check-label" for="active">Aktif</label>
												</div>

												@if ($errors->any())
														<div class="alert alert-danger">
																<ul class="mb-0">
																		@foreach ($errors->all() as $error)
																				<li>{{ $error }}</li>
																		@endforeach
																</ul>
														</div>
												@endif

												<div class="d-flex gap-2">
														<a href="{{ route('admin.sesi-ujian.index') }}" class="btn btn-secondary">Batal</a>
														<button type="submit" class="btn btn-primary">Simpan</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		</div>
</div>
