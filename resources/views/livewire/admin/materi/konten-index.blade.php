<div>
		<div class="row">
				<div class="col-12">
						<!-- Breadcrumb + Judul + Tombol Tambah -->
						<div class="d-flex justify-content-between align-items-start mb-4">
								<div>
										<nav aria-label="breadcrumb">
												<ol class="breadcrumb">
														<li class="breadcrumb-item">
																<a href="{{ route('admin.materi.topik.index') }}">Kelola Topik</a>
														</li>
														<li class="breadcrumb-item">
																<a href="{{ route('admin.materi.materi.index', $topik->id) }}">{{ $topik->nama_topik }}</a>
														</li>
														<li class="breadcrumb-item">
																<a
																		href="{{ route('admin.materi.submateri.index', [$topik->id, $materi->id]) }}">{{ $materi->judul }}</a>
														</li>
														<li class="breadcrumb-item active" aria-current="page">{{ $submateri->judul }}</li>
												</ol>
										</nav>
										<h4 class="mb-0">Kelola Konten → {{ $submateri->judul }}</h4>
								</div>

								<button wire:click="openCreateModal" class="btn btn-primary btn-sm">
										<i class="bx bx-plus"></i> Tambah Konten
								</button>
						</div>

						<!-- Daftar Konten -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Konten</h5>
								</div>

								<div class="card-body pb-2 pt-3">
										<div class="row mb-3">
												<div class="col-12">
														<input type="text" wire:model.live="search" class="form-control w-25 ms-auto"
																placeholder="Cari konten...">
												</div>
										</div>

										<div class="table-responsive p-3 px-0">
												<table class="align-items-center mb-0 table">
														<thead>
																<tr>
																		<th style="width: 8%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">#
																		</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Preview</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Tipe</th>
																		<th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Info</th>
																		<th style="width: 12%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">
																				Urutan</th>
																		<th style="width: 18%" class="text-uppercase text-secondary font-weight-bolder opacity-7 text-xs">Aksi
																		</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listKonten as $index => $item)
																		<tr>
																				<td>{{ $listKonten->firstItem() + $index }}</td>
																				<td>
																						@if ($item->tipe === 'video')
																								<a href="{{ $item->file_path }}" target="_blank" class="d-block">
																										<img src="{{ $item->getYouTubeThumbnail() }}" class="youtube-thumbnail rounded shadow-sm"
																												alt="YouTube Preview">
																								</a>
																						@else
																								<a href="{{ Storage::url($item->file_path) }}" target="_blank">
																										<i class="bx bxs-file-pdf fs-1 text-danger"></i>
																								</a>
																						@endif
																				</td>
																				<td>
																						<span class="badge {{ $item->tipe === 'video' ? 'bg-success' : 'bg-danger' }}">
																								{{ strtoupper($item->tipe) }}
																						</span>
																				</td>
																				<td>
																						@if ($item->tipe === 'video')
																								<small>{{ Str::limit($item->file_path, 50) }}</small>
																						@else
																								<small>{{ basename($item->file_path) }}</small>
																						@endif
																						<br>
																						<small class="text-muted">{{ $item->isi ?? '-' }}</small>
																				</td>
																				<td class="text-center">
																						<span class="badge bg-info">{{ $item->urutan }}</span>
																				</td>
																				<td>
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info" title="Edit">
																								<x-icon name="edit" />
																						</button>
																						<button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger"
																								title="Hapus">
																								<x-icon name="delete" />
																						</button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="6" class="text-muted p-4 text-center">
																						<i class='bx bx-archive fs-1 text-secondary'></i>
																						<p class="fs-3 text-secondary">- Belum ada konten -</p>
																				</td>
																		</tr>
																@endforelse
														</tbody>
												</table>
										</div>
								</div>

								<div class="card-footer">
										<div class="mt-3 px-3">
												{{ $listKonten->links() }}
										</div>
								</div>
						</div>
				</div>
		</div>

		<!-- Modal Tambah / Edit Konten -->
		@if ($showModal)
				<div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
						role="dialog" wire:click.outside="closeModal">
						<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
								<div class="modal-content">
										<div class="modal-header">
												<h5 class="modal-title">
														{{ $updateMode ? 'Edit Konten' : 'Tambah Konten Baru' }}
												</h5>
												<button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
										</div>

										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="modal-body">
														<div class="row mb-3">
																<div class="col-md-4">
																		<label class="form-label">Tipe Konten</label>
																		<select wire:model.live="tipe" class="form-select">
																				<option value="video">Video (YouTube)</option>
																				<option value="pdf">PDF</option>
																		</select>
																</div>

																@if ($tipe === 'video')
																		<div class="col-md-8">
																				<label class="form-label">Link YouTube</label>
																				<input type="url" wire:model="youtube_url"
																						class="form-control @error('youtube_url') is-invalid @enderror"
																						placeholder="https://www.youtube.com/watch?v=...">
																				@error('youtube_url')
																						<small class="text-danger">{{ $message }}</small>
																				@enderror
																		</div>
																@else
																		<div class="col-md-8">
																				<label class="form-label">Upload PDF (maks 50MB)</label>
																				<input type="file" wire:model="file" class="form-control @error('file') is-invalid @enderror"
																						accept=".pdf">
																				@error('file')
																						<small class="text-danger">{{ $message }}</small>
																				@enderror
																				<div wire:loading wire:target="file">
																						<small class="text-info d-block mt-1">Sedang mengunggah file...</small>
																				</div>
																		</div>
																@endif
														</div>

														<div class="mb-3">
																<label class="form-label">Deskripsi / Catatan (opsional)</label>
																<textarea wire:model="isi" class="form-control" rows="3"
																  placeholder="Judul tambahan, catatan, atau deskripsi singkat untuk konten ini"></textarea>
														</div>
												</div>

												<div class="modal-footer">
														<button type="button" class="btn btn-secondary" wire:click="closeModal">
																Batal
														</button>
														<button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
																wire:target="store,update">
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

		@push('css')
				<style>
						.youtube-thumbnail {
								width: 120px;
								height: 90px;
								object-fit: cover;
								transition: transform 0.2s;
						}

						.youtube-thumbnail:hover {
								transform: scale(1.05);
						}
				</style>
		@endpush
</div>
