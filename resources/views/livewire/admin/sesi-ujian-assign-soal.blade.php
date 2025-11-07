<div class="container py-4">
		<div class="row">
				<div class="col-12">
						<div class="card">
								<div class="card-header d-flex justify-content-between">
										<h4>Pilih Soal untuk: {{ $sesi->judul }}</h4>
										<a href="{{ route('admin.sesi-ujian.index') }}" class="btn btn-secondary">Kembali</a>
								</div>
								<div class="card-body">
										<div class="row mb-3">
												<div class="col-md-6">
														<input type="text" wire:model.live="search" class="form-control" placeholder="Cari soal...">
												</div>
												<div class="col-md-6">
														<select wire:model.live="filterJenis" class="form-select">
																<option value="">Semua Jenis</option>
																@foreach ($jenisUjian as $j)
																		<option value="{{ $j->id }}">{{ $j->nama }}</option>
																@endforeach
														</select>
												</div>
										</div>

										<form wire:submit.prevent="save">
												<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
														<table class="table-bordered table">
																<thead class="table-light">
																		<tr>
																				<th width="5%"><input type="checkbox" wire:model="selectedSoal" value="all"></th>
																				<th>Jenis</th>
																				<th>Pertanyaan</th>
																		</tr>
																</thead>
																<tbody>
																		@foreach ($soal as $s)
																				<tr>
																						<td>
																								<input type="checkbox" wire:model="selectedSoal" value="{{ $s->id }}">
																						</td>
																						<td><span class="badge bg-info">{{ $s->jenis->nama }}</span></td>
																						<td>{{ Str::limit($s->pertanyaan_text, 80) }}</td>
																				</tr>
																		@endforeach
																</tbody>
														</table>
												</div>

												<div class="mt-3">
														<button type="submit" class="btn btn-success">Simpan Pilihan Soal</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		</div>
</div>
