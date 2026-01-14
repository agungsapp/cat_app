<div>
		<div class="row">
				<div class="col-12">
						<!-- Card dengan tombol Tambah -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Jenis Ujian</h5>
										<button wire:click="openCreateModal" class="btn btn-primary btn-sm">
												<i class="bx bx-plus"></i> Tambah Jenis Ujian
										</button>
								</div>

								<div class="card-body pb-2 pt-3">
										<div class="row mb-3">
												<div class="col-12">
														<input type="text" wire:model.live="search" class="form-control w-25 ms-auto" placeholder="Cari...">
												</div>
										</div>
										<div class="table-responsive">
												<table id="dataTable" class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th style="width: 10%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				#
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Nama Jenis Ujian
																		</th>
																		<th style="width: 20%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Aksi
																		</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listJenisUjian as $index => $item)
																		<tr>
																				<td>{{ $listJenisUjian->firstItem() + $index }}</td>
																				<td>{{ $item->nama }}</td>
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
																				<td colspan="3" class="text-muted p-4 text-center">
																						<i class='bxr fs-1 text-secondary bx-archive'></i>
																						<p class="fs-3 text-secondary">- Belum ada data -</p>
																				</td>
																		</tr>
																@endforelse
														</tbody>
												</table>
										</div>
								</div>
								<div class="card-footer">
										<div class="mt-3 px-3">
												{{ $listJenisUjian->links() }}
										</div>
								</div>
						</div>
				</div>
		</div>

		<!-- Modal Create/Edit -->
		@if ($showModal)
				<div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
						role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
										<div class="modal-header">
												<h5 class="modal-title">
														{{ $updateMode ? 'Edit Jenis Ujian' : 'Tambah Jenis Ujian' }}
												</h5>
												<button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
										</div>
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="modal-body">
														<div class="mb-3">
																<label for="namaModal" class="form-label">Jenis Ujian</label>
																<input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror"
																		id="namaModal" placeholder="Masukkan jenis ujian">
																@error('nama')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>
												</div>
												<div class="modal-footer">
														<button type="button" class="btn btn-secondary" wire:click="closeModal">
																Batal
														</button>
														<button type="submit" class="btn btn-primary">
																{{ $updateMode ? 'Update' : 'Simpan' }}
														</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		@endif
</div>
