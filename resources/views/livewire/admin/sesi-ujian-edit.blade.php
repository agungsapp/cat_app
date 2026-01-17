<div class="container py-4">
		<div class="row justify-content-center">
				<div class="col-md-8">
						<div class="card">
								<div class="card-header bg-warning text-white">
										<h4 class="text-white">Edit Sesi Ujian</h4>
								</div>
								<div class="card-body">
										<form wire:submit.prevent="save">
												{{-- BASIC INFO --}}
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

												{{-- TIPE UJIAN --}}
												<div class="row">
														<div class="col-4 mb-3">
																<label>Tipe Ujian <span class="text-danger">*</span></label>
																<select wire:model.live="tipe_ujian_id"
																		class="@error('tipe_ujian_id') is-invalid @enderror form-select">
																		<option value="">Pilih Tipe</option>
																		@foreach ($tipeUjian as $t)
																				<option value="{{ $t->id }}">{{ $t->nama }} | {{ $t->mode }}</option>
																		@endforeach
																</select>
																@error('tipe_ujian_id')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>
												</div>

												{{-- MODE-SPECIFIC FORMS --}}
												@if ($mode == 'fixed_rule')
														<div class="card mb-4">
																<div class="card-body">
																		@foreach ($komposisi as $index => $row)
																				<div class="row align-items-end mb-2">
																						<div class="col-5">
																								<label>Jenis Soal *</label>
																								<select wire:model="komposisi.{{ $index }}.jenis_ujian_id"
																										class="@error("komposisi.$index.jenis_ujian_id") is-invalid @enderror form-select">
																										<option value="">Pilih Jenis</option>
																										@foreach ($jenisUjian as $ju)
																												<option value="{{ $ju->id }}">{{ $ju->nama }}</option>
																										@endforeach
																								</select>
																								@error("komposisi.$index.jenis_ujian_id")
																										<small class="text-danger">{{ $message }}</small>
																								@enderror
																						</div>

																						<div class="col-3">
																								<label>Jumlah Soal *</label>
																								<input type="number" wire:model="komposisi.{{ $index }}.jumlah_soal"
																										class="form-control @error("komposisi.$index.jumlah_soal") is-invalid @enderror"
																										min="1">
																								@error("komposisi.$index.jumlah_soal")
																										<small class="text-danger">{{ $message }}</small>
																								@enderror
																						</div>

																						<div class="col-2">
																								<button type="button" wire:click="removeKomposisi({{ $index }})"
																										class="btn btn-danger w-100">
																										<i class="bx bx-trash"></i>
																								</button>
																						</div>
																				</div>
																		@endforeach

																		<button type="button" wire:click="addKomposisi" class="btn btn-primary mt-2">
																				+ Tambah Komposisi
																		</button>
																</div>
														</div>
												@elseif($mode == 'random_by_jenis')
														<div class="row">
																<div class="col-5 mb-3">
																		<label>Jenis Soal <span class="text-danger">*</span></label>
																		<select wire:model="jenis_ujian_id" class="@error('jenis_ujian_id') is-invalid @enderror form-select">
																				<option value="">Pilih Jenis</option>
																				@foreach ($jenisUjian as $ju)
																						<option value="{{ $ju->id }}">{{ $ju->nama }}</option>
																				@endforeach
																		</select>
																		@error('jenis_ujian_id')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																</div>

																<div class="col-3 mb-3">
																		<label>Jumlah Soal <span class="text-danger">*</span></label>
																		<input type="number" wire:model="jumlah_soal"
																				class="form-control @error('jumlah_soal') is-invalid @enderror" min="1">
																		@error('jumlah_soal')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																</div>
														</div>
												@elseif($mode == 'random_all')
														<div class="row">
																<div class="col-3 mb-3">
																		<label>Jumlah Soal <span class="text-danger">*</span></label>
																		<input type="number" wire:model="jumlah_soal"
																				class="form-control @error('jumlah_soal') is-invalid @enderror" min="1">
																		@error('jumlah_soal')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																</div>
														</div>
												@endif

												{{-- ERRORS SUMMARY --}}
												@if ($errors->any())
														<div class="alert alert-danger">
																<ul class="mb-0">
																		@foreach ($errors->all() as $error)
																				<li>{{ $error }}</li>
																		@endforeach
																</ul>
														</div>
												@endif

												{{-- ACTIONS --}}
												<div class="d-flex gap-2">
														<a href="{{ route('admin.sesi-ujian.index') }}" class="btn btn-secondary">Batal</a>
														<button type="submit" class="btn btn-warning text-white">
																<i class="bx bx-save"></i> Update & Regenerate Soal
														</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		</div>
</div>
@push('js')
		<script>
				document.addEventListener('livewire:init', () => {
						Livewire.on('redirect-after-delay', () => {
								setTimeout(() => {
										window.location.href = "{{ route('admin.sesi-ujian.index') }}";
								}, 5000);
								// alert("sudah di picu");
						});
				});
		</script>
@endpush
