<div>
		<div class="row">
				<div class="col-12">
						<!-- Form Tambah / Edit -->
						<div class="card mb-4">
								<div class="card-body">
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="row mb-4">
														<div class="col-md-4">
																<label for="nama" class="form-label">Tipe Ujian</label>
																<input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror"
																		id="nama" placeholder="Masukkan tipe ujian">
																@error('nama')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<div class="col-md-4">
																<label for="max_attempt" class="form-label">
																		Maksimal Percobaan
																		<small class="text-muted">(kosongkan = Unlimited)</small>
																</label>
																<input type="number" wire:model="max_attempt"
																		class="form-control @error('max_attempt') is-invalid @enderror" id="max_attempt"
																		placeholder="Contoh: 3" min="1">
																@error('max_attempt')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>
												</div>

												<div class="row">
														<div class="col-12">
																<button type="submit" class="btn btn-primary">
																		{{ $updateMode ? 'Update' : 'Simpan' }}
																</button>

																@if ($updateMode)
																		<button type="button" class="btn btn-secondary ms-2" wire:click="resetForm">
																				Batal
																		</button>
																@endif
														</div>
												</div>
										</form>
								</div>
						</div>

						<!-- Daftar Tipe Ujian -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Tipe Ujian</h5>
								</div>

								<div class="card-body pb-2 pt-0">
										<div class="row mb-3">
												<div class="col-12">
														<input type="text" wire:model.live="search" class="form-control w-25 ms-auto" placeholder="Cari...">
												</div>
										</div>

										<div class="table-responsive p-3 px-0">
												<table class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th style="width: 10%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">#
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Nama Tipe Ujian</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Maksimal Percobaan</th>
																		<th style="width: 20%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Aksi
																		</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listTipeUjian as $index => $item)
																		<tr>
																				<td>{{ $loop->iteration }}</td>
																				<td>{{ $item->nama }}</td>
																				<td>
																						@if ($item->max_attempt)
																								<span class="badge bg-success">{{ $item->max_attempt }} kali</span>
																						@else
																								<span class="badge bg-secondary">Unlimited</span>
																						@endif
																				</td>
																				<td>
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info">
																								<x-icon name="edit" />
																						</button>
																						<button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger">
																								<x-icon name="delete" />
																						</button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="4" class="text-muted p-5 text-center">
																						<i class="bx bx-archive fs-1 text-secondary"></i>
																						<p class="fs-4 text-secondary mt-3">- Belum ada data -</p>
																				</td>
																		</tr>
																@endforelse
														</tbody>
												</table>
										</div>
								</div>

								<div class="card-footer">
										<div class="mt-3 px-3">
												{{ $listTipeUjian->links() }}
										</div>
								</div>
						</div>
				</div>
		</div>
</div>
