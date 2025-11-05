@props([
    'name', // contoh: edit, delete, info, plus, etc
    'class' => '', // bisa override class tambahan
])

@php
		$icons = [
		    'edit' => 'bx fs-5 bx-edit',
		    'delete' => 'bx fs-5 bx-trash',
		    'info' => 'bx fs-5 bx-info-circle',
		    'plus' => 'bx fs-5 bx-plus',
		    'view' => 'bx fs-5 bx-show',
		    'search' => 'bx fs-5 bx-search',
		];

		$icon = $icons[$name] ?? $name; // fallback kalau langsung kirim class icon
@endphp

<i class="{{ $icon }} {{ $class }}"></i>
