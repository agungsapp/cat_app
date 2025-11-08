<div>
		<span class="fw-bold">{{ $menit }}:{{ $detik }}</span>
		<!-- Di peserta-ujian-timer.blade.php -->
		@push('js')
				<script>
						let sisa = @js($waktuSisa);
						const timer = setInterval(() => {
								if (sisa <= 0) {
										clearInterval(timer);
										@this.call('selesai');
										return;
								}
								sisa--;
								const menit = String(Math.floor(sisa / 60)).padStart(2, '0');
								const detik = String(sisa % 60).padStart(2, '0');
								document.getElementById('timer')?.innerText = `${menit}:${detik}`;
						}, 1000);
				</script>
		@endpush
</div>
