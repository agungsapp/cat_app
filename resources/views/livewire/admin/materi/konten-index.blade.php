<div>
		<div class="row">
				<div class="col-12">
						<!-- Breadcrumb -->
						<div class="mb-4">
								<nav aria-label="breadcrumb">
										<ol class="breadcrumb">
												<li class="breadcrumb-item"><a href="{{ route('admin.materi.topik.index') }}">Kelola Topik</a></li>
												<li class="breadcrumb-item"><a
																href="{{ route('admin.materi.materi.index', $topik->id) }}">{{ $topik->nama_topik }}</a></li>
												<li class="breadcrumb-item"><a
																href="{{ route('admin.materi.submateri.index', [$topik->id, $materi->id]) }}">{{ $materi->judul }}</a>
												</li>
												<li class="breadcrumb-item active">{{ $submateri->judul }}</li>
										</ol>
								</nav>
								<h4 class="mb-0">Kelola Konten → {{ $submateri->judul }}</h4>
						</div>

						<!-- Form Tambah / Edit -->
						<div class="card mb-4">
								<div class="card-body">
										<form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
												<div class="row mb-3">
														<div class="col-md-3">
																<label class="form-label">Tipe Konten</label>
																<select wire:model.live="tipe" class="form-select">
																		<option value="video">Video (YouTube)</option>
																		<option value="pdf">PDF</option>
																</select>
														</div>

														@if ($tipe === 'video')
																<div class="col-md-9">
																		<label class="form-label">Link YouTube</label>
																		<input type="url" wire:model="youtube_url"
																				class="form-control @error('youtube_url') is-invalid @enderror"
																				placeholder="https://www.youtube.com/watch?v=...">
																		@error('youtube_url')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																</div>
														@else
																<div class="col-md-9">
																		<label class="form-label">Upload PDF</label>
																		<input type="file" wire:model="file" class="form-control @error('file') is-invalid @enderror"
																				accept=".pdf">
																		@error('file')
																				<small class="text-danger">{{ $message }}</small>
																		@enderror
																		<div wire:loading wire:target="file">
																				<small class="text-info">Sedang mengunggah...</small>
																		</div>
																</div>
														@endif
												</div>

												<div class="row mb-3">
														<div class="col-12">
																<label class="form-label">Deskripsi (Opsional)</label>
																<textarea wire:model="isi" class="form-control" rows="3" placeholder="Judul tambahan, catatan, dll"></textarea>
														</div>
												</div>

												<div class="row">
														<div class="col-12">
																<button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
																		wire:target="{{ $updateMode ? 'update' : 'store' }}">
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

						<!-- Daftar Konten -->
						<div class="card">
								<div class="card-header d-flex justify-content-between align-items-center pb-0">
										<h5 class="mb-0">Daftar Konten</h5>
								</div>

								<div class="card-body pb-2 pt-0">
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
																		<th style="width: 8%" class="opacity-7 text-xs">#</th>
																		<th class="opacity-7 text-xs">Preview</th>
																		<th class="opacity-7 text-xs">Tipe</th>
																		<th class="opacity-7 text-xs">Info</th>
																		<th style="width: 12%" class="opacity-7 text-xs">Urutan</th>
																		<th style="width: 18%" class="opacity-7 text-xs">Aksi</th>
																</tr>
														</thead>
														<tbody>
																@forelse ($listKonten as $item)
																		<tr>
																				<td>{{ $loop->iteration + $listKonten->perPage() * ($listKonten->currentPage() - 1) }}</td>
																				<td>
																						@if ($item->tipe === 'video')
																								<a href="{{ $item->file_path }}" target="_blank" class="d-block">
																										<img src="{{ $item->getYouTubeThumbnail() }}" class="youtube-thumbnail rounded shadow-sm"
																												alt="YouTube">
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
																						<button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info"><x-icon
																										name="edit" /></button>
																						<button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger"><x-icon
																										name="delete" /></button>
																				</td>
																		</tr>
																@empty
																		<tr>
																				<td colspan="6" class="text-muted p-4 text-center">
																						<i class='bxr fs-1 text-secondary bx-archive'></i>
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
