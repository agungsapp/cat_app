<div>
		<div class="row">
				<div class="col-12">
						<div class="mb-4">
								<h4 class="mb-0">Kelola Topik</h4>
						</div>

						<!-- Form Tambah / Edit -->
						<div class="card mb-4">
								<div class="card-body">
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="row mb-4">
														<div class="col-md-6">
																<label for="nama_topik" class="form-label">Nama Topik</label>
																<input type="text" wire:model="nama_topik"
																		class="form-control @error('nama_topik') is-invalid @enderror" id="nama_topik"
																		placeholder="Masukkan nama topik">
																@error('nama_topik')
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
																		<button type="button" class="btn btn-secondary" wire:click="resetForm">Batal</button>
																@endif
														</div>
												</div>
										</form>
								</div>
						</div>

						<!-- Daftar Topik -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Topik</h5>
								</div>

								<div class="card-body pb-2 pt-0">
										<div class="row mb-3">
												<div class="col-12">
														<input type="text" wire:model.live="search" class="form-control w-25 ms-auto"
																placeholder="Cari topik...">
												</div>
										</div>

										<div class="table-responsive p-3 px-0">
												<table class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th style="width: 8%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">#
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Nama Topik</th>
																		<th style="width: 15%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Urutan</th>
																		<th style="width: 28%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Aksi
																		</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listTopik as $index => $item)
																		<tr>
																				<td>{{ $loop->iteration + $listTopik->perPage() * ($listTopik->currentPage() - 1) }}</td>
																				<td>{{ $item->nama_topik }}</td>
																				<td class="text-center">
																						<span class="badge bg-info">{{ $item->urutan }}</span>
																				</td>
																				<td>
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info"><x-icon
																										name="edit" /></button>

																						<a href="{{ route('admin.materi.materi.index', $item->id) }}"
																								class="btn btn-sm btn-success">Kelola Materi</a>

																						<button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger"><x-icon
																										name="delete" /></button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="4" class="text-muted p-4 text-center">
																						<i class='bxr fs-1 text-secondary bx-archive'></i>
																						<p class="fs-3 text-secondary">- Belum ada topik -</p>
																				</td>
																		</tr>
																@endforelse
														</tbody>
												</table>
										</div>
								</div>

								<div class="card-footer">
										<div class="mt-3 px-3">
												{{ $listTopik->links() }}
										</div>
								</div>
						</div>
				</div>
		</div>
</div>
