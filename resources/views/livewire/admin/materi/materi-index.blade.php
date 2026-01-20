<div>
		<div class="row">
				<div class="col-12">
						<!-- Breadcrumb + Judul + Tombol Tambah -->
						<div class="d-flex justify-content-between align-items-start mb-4">
								<div>
										<nav aria-label="breadcrumb">
												<ol class="breadcrumb">
														<li class="breadcrumb-item"><a href="{{ route('admin.materi.topik.index') }}">Kelola Topik</a></li>
														<li class="breadcrumb-item active" aria-current="page">{{ $topik->nama_topik }}</li>
												</ol>
										</nav>
										<h4 class="mb-0">Kelola Materi → {{ $topik->nama_topik }}</h4>
								</div>
								<button wire:click="openCreateModal" class="btn btn-primary btn-sm">
										<i class="bx bx-plus"></i> Tambah Materi
								</button>
						</div>

						<!-- Daftar Materi -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Materi</h5>
								</div>

								<div class="card-body pb-2 pt-3">
										<div class="row mb-3">
												<div class="col-12">
														<input type="text" wire:model.live="search" class="form-control w-25 ms-auto"
																placeholder="Cari materi...">
												</div>
										</div>

										<div class="table-responsive p-3 px-0">
												<table class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th style="width: 8%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">#
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Judul Materi</th>
																		<th style="width: 15%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Urutan</th>
																		<th style="width: 28%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Aksi
																		</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listMateri as $index => $item)
																		<tr>
																				<td>{{ $listMateri->firstItem() + $index }}</td>
																				<td>{{ $item->judul }}</td>
																				<td class="text-center">
																						<span class="badge bg-info">{{ $item->urutan }}</span>
																				</td>
																				<td>
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info" title="Edit">
																								<x-icon name="edit" />
																						</button>

																						<a href="{{ route('admin.materi.submateri.index', [$topik->id, $item->id]) }}"
																								class="btn btn-sm btn-success">Kelola Submateri</a>

																						<button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger"
																								title="Hapus">
																								<x-icon name="delete" />
																						</button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="4" class="text-muted p-4 text-center">
																						<i class='bx bx-archive fs-1 text-secondary'></i>
																						<p class="fs-3 text-secondary">- Belum ada materi -</p>
																				</td>
																		</tr>
																@endforelse
														</tbody>
												</table>
										</div>
								</div>

								<div class="card-footer">
										<div class="mt-3 px-3">
												{{ $listMateri->links() }}
										</div>
								</div>
						</div>
				</div>
		</div>

		<!-- Modal Create / Edit Materi -->
		@if ($showModal)
				<div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
						role="dialog" wire:click.outside="closeModal">
						<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
										<div class="modal-header">
												<h5 class="modal-title">
														{{ $updateMode ? 'Edit Materi' : 'Tambah Materi Baru' }}
												</h5>
												<button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
										</div>

										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="modal-body">
														<div class="mb-3">
																<label for="judul_modal" class="form-label">Judul Materi</label>
																<input type="text" wire:model="judul" class="form-control @error('judul') is-invalid @enderror"
																		id="judul_modal" placeholder="Masukkan judul materi">
																@error('judul')
																		<small class="text-danger">{{ $message }}</small>
																@enderror
														</div>
												</div>

												<div class="modal-footer">
														<button type="button" class="btn btn-secondary" wire:click="closeModal">
																Batal
														</button>
														<button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store,update">
																<span wire:loading.remove wire:target="store,update">
																		{{ $updateMode ? 'Update' : 'Simpan' }}
																</span>
																<span wire:loading wire:target="store,update">
																		<i class="bx bx-loader bx-spin"></i> Memproses...
																</span>
														</button>
												</div>
										</form>
								</div>
						</div>
				</div>
		@endif
</div>
