<div class="container-fluid py-4">
		<div class="row mb-4">
				<div class="col-12 d-flex justify-content-between align-items-center">
						<h4>Sesi Ujian</h4>
						<a href="{{ route('admin.sesi-ujian.create') }}" class="btn btn-primary">
								<i class="fas fa-plus"></i> Buat Sesi
						</a>
				</div>
		</div>

		<div class="row mb-4">
				<div class="col-md-6">
						<input type="text" wire:model.live="search" class="form-control" placeholder="Cari judul...">
				</div>
				<div class="col-md-6">
						<select wire:model.live="filterTipe" class="form-select">
								<option value="">Semua Tipe</option>
								@foreach ($tipeUjian as $t)
										<option value="{{ $t->id }}">{{ $t->nama }}</option>
								@endforeach
						</select>
				</div>
		</div>

		<div class="row">
				@forelse($sesi as $s)
						<div class="col-md-6 mb-3">
								<div class="card h-100">
										<div class="card-body">
												<h5>{{ $s->judul }}</h5>
												<p class="text-muted small">{{ $s->tipeUjian->nama }} • {{ $s->durasi_menit }} menit</p>
												@if ($s->waktu_mulai)
														<p class="small">Mulai: {{ $s->waktu_mulai->format('d/m/Y H:i') }}</p>
												@endif
												<div class="mt-3">
														<a href="{{ route('admin.sesi-ujian.edit', $s->id) }}" class="btn btn-sm btn-warning">Edit</a>
														<a href="{{ route('admin.sesi-ujian.assign', $s->id) }}" class="btn btn-sm btn-info">Pilih Soal</a>
														<button wire:click="delete({{ $s->id }})" class="btn btn-sm btn-danger">Hapus</button>
												</div>
										</div>
								</div>
						</div>
				@empty
						<div class="col-12 mt-5 text-center">
								<div class="card">
										<div class="card-body">
												<i class='bxr fs-1 text-secondary bx-archive'></i>
												<p class="fs-3 text-secondary">- Belum ada data -</p>
										</div>
								</div>
						</div>
				@endforelse
		</div>

		{{ $sesi->links() }}
</div>
