<div>
		<div class="row">
				<div class="col-12">
						<div class="card mb-4">
								<div class="card-body">
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="row">
														<div class="col-md-4">
																<label for="nama" class="form-label">Jenis Ujian</label>
																<input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror"
																		id="nama" placeholder="Masukkan jenis ujian">
																@error('nama')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<div class="col-md-3 align-self-end">
																<button type="submit" class="btn btn-primary">
																		{{ $updateMode ? 'Update' : 'Simpan' }}
																</button>

																@if ($updateMode)
																		<button type="button" class="btn btn-secondary" wire:click="resetForm">Batal</button>
																@endif
														</div>
												</div>
										</form>
								</div>
						</div>

						<div class="card">
								<div class="card-header pb-0">
										<h5 class="mb-0">Daftar Jenis Ujian</h5>
								</div>
								<div class="card-body px-0 pb-2 pt-0">
										<div class="table-responsive p-3">
												<table class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">#</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Nama Jenis Ujian</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Aksi</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listJenisUjian as $index => $item)
																		<tr>
																				<td>{{ $index + 1 }}</td>
																				<td>{{ $item->nama }}</td>
																				<td>
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info">Edit</button>
																						<button wire:click="confirmDelete({{ $item->id }})"
																								class="btn btn-sm btn-danger">Hapus</button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="3" class="text-muted text-center">Belum ada data.</td>
																		</tr>
																@endforelse
														</tbody>
												</table>
										</div>
								</div>
						</div>
				</div>
		</div>
</div>
