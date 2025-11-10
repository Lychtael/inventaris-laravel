@props(['href' => '#', 'method' => 'GET'])

@php
    $classes = 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-600 hover:text-white focus:outline-none focus:bg-gray-600 focus:text-white transition duration-150 ease-in-out';
    
    $isForm = strcasecmp($method, 'POST') === 0 || strcasecmp($method, 'PUT') === 0 || strcasecmp($method, 'DELETE') === 0;
@endphp

@if ($isForm)
    <form method="POST" action="{{ $href }}">
        @csrf
        @method($method)
        <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
            {{ $slot }}
        </button>
    </form>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endif