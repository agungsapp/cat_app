<div>
		<div class="row">
				<div class="col-12">
						<!-- Breadcrumb + Judul -->
						<div class="mb-4">
								<nav aria-label="breadcrumb">
										<ol class="breadcrumb">
												<li class="breadcrumb-item"><a href="{{ route('admin.materi.topik.index') }}">Kelola Topik</a></li>
												<li class="breadcrumb-item active">{{ $topik->nama_topik }}</li>
										</ol>
								</nav>
								<h4 class="mb-0">Kelola Materi → {{ $topik->nama_topik }}</h4>
						</div>

						<!-- Form Tambah / Edit -->
						<div class="card mb-4">
								<div class="card-body">
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="row mb-4">
														<div class="col-md-6">
																<label for="judul" class="form-label">Judul Materi</label>
																<input type="text" wire:model="judul" class="form-control @error('judul') is-invalid @enderror"
																		id="judul" placeholder="Masukkan judul materi">
																@error('judul')
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

						<!-- Daftar Materi -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Materi</h5>
								</div>

								<div class="card-body pb-2 pt-0">
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
																				<td>{{ $loop->iteration + $listMateri->perPage() * ($listMateri->currentPage() - 1) }}</td>
																				<td>{{ $item->judul }}</td>
																				<td class="text-center">
																						<span class="badge bg-info">{{ $item->urutan }}</span>
																				</td>
																				<td>
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info"><x-icon
																										name="edit" /></button>

																						<a href="{{ route('admin.materi.submateri.index', [$topik->id, $item->id]) }}"
																								class="btn btn-sm btn-success">Kelola Submateri</a>

																						<button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger"><x-icon
																										name="delete" /></button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="4" class="text-muted p-4 text-center">
																						<i class='bxr fs-1 text-secondary bx-archive'></i>
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
</div>
