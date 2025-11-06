@props(['disabled' => false])

@php
// Ini adalah style default (background putih, teks hitam)
$classes = ($disabled ?? false)
            ? 'border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm'
            : 'border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>