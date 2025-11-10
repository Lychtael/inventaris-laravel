@props(['attributes' => []])

<img {{ $attributes->merge(['src' => asset('img/Logo-Diskominfo.png'), 'alt' => 'Logo Diskominfo']) }}>