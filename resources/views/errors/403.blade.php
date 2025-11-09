{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>403 - Akses Ditolak</title>
		@vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Sesuaikan kalau pakai Vite --}}
		<style>
				body {
						background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
						font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
						height: 100vh;
						margin: 0;
						display: flex;
						align-items: center;
						justify-content: center;
				}

				.error-container {
						text-align: center;
						color: white;
						padding: 40px;
						border-radius: 20px;
						background: rgba(255, 255, 255, 0.1);
						backdrop-filter: blur(10px);
						box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
						max-width: 500px;
				}

				.error-code {
						font-size: 120px;
						font-weight: 900;
						margin: 0;
						text-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
				}

				.error-message {
						font-size: 28px;
						margin: 20px 0;
						font-weight: 600;
				}

				.error-detail {
						font-size: 16px;
						opacity: 0.9;
						margin-bottom: 30px;
				}

				.btn-home {
						background: rgba(255, 255, 255, 0.25);
						color: white;
						border: 2px solid white;
						padding: 12px 30px;
						border-radius: 50px;
						text-decoration: none;
						font-weight: 600;
						transition: all 0.3s ease;
				}

				.btn-home:hover {
						background: white;
						color: #667eea;
				}
		</style>
</head>

<body>
		<div class="error-container">
				<h1 class="error-code">403</h1>
				<h2 class="error-message">Akses Ditolak</h2>
				<p class="error-detail">
						Maaf, kamu tidak memiliki permission untuk mengakses halaman ini.<br>
						{{ $exception->getMessage() ?: 'Kamu tidak punya akses.' }}
				</p>
				<a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn-home">
						Kembali
				</a>
				<a href="{{ route('login') }}" class="btn-home ms-3">
						Dashboard
				</a>
		</div>
</body>

</html>
