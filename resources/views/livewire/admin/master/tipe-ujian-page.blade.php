<div>
		<div class="row">
				<div class="col-12">
						<!-- Daftar Tipe Ujian -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Tipe Ujian</h5>
										<button wire:click="openCreateModal" class="btn btn-primary btn-sm">
												<i class="bx bx-plus"></i> Tambah Tipe Ujian
										</button>
								</div>

								<div class="card-body pb-2 pt-3">
										<div class="row mb-3">
												<div class="col-12">
														<input type="text" wire:model.live="search" class="form-control w-25 ms-auto" placeholder="Cari...">
												</div>
										</div>

										<div class="table-responsive">
												<table class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th style="width: 10%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				#
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Nama Tipe Ujian
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Maksimal Percobaan
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Mode
																		</th>
																		<th style="width: 20%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Aksi
																		</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listTipeUjian as $index => $item)
																		<tr>
																				<td>{{ $listTipeUjian->firstItem() + $index }}</td>
																				<td>{{ $item->nama }}</td>
																				<td>
																						@if ($item->max_attempt)
																								<span class="badge bg-success">{{ $item->max_attempt }} kali</span>
																						@else
																								<span class="badge bg-secondary">Unlimited</span>
																						@endif
																				</td>
																				<td>{{ $item->mode }}</td>
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

		<!-- Modal Create/Edit -->
		@if ($showModal)
				<div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
						role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
										<div class="modal-header">
												<h5 class="modal-title">
														{{ $updateMode ? 'Edit Tipe Ujian' : 'Tambah Tipe Ujian' }}
												</h5>
												<button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
										</div>
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="modal-body">
														<div class="mb-3">
																<label for="namaModal" class="form-label">Tipe Ujian</label>
																<input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror"
																		id="namaModal" placeholder="Masukkan tipe ujian">
																@error('nama')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<div class="mb-3">
																<label for="maxAttemptModal" class="form-label">
																		Maksimal Percobaan
																		<small class="text-muted">(kosongkan = Unlimited)</small>
																</label>
																<input type="number" wire:model="max_attempt"
																		class="form-control @error('max_attempt') is-invalid @enderror" id="maxAttemptModal"
																		placeholder="Contoh: 3" min="1">
																@error('max_attempt')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>

														<div class="mb-3">
																<label for="modeModal" class="form-label">Mode</label>
																<select wire:model="mode" class="@error('mode') is-invalid @enderror form-select" id="modeModal">
																		<option value="">-- Pilih Mode --</option>
																		<option value="random_all">Random All</option>
																		<option value="random_by_jenis">Random By Jenis</option>
																		<option value="fixed_rule">Fixed Rule</option>
																</select>
																@error('mode')
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
