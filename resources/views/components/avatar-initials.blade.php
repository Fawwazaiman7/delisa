@props([
    'name' => '',
    'size' => 'h-10 w-10',
    'background' => 'bg-pink-50',
    'text' => 'text-pink-500',
    'border' => 'border-2 border-pink-100',
])

@php
    $parts = preg_split('/\s+/u', trim($name ?? '')) ?: [];
    $initials = collect($parts)
        ->filter(fn ($part) => $part !== '')
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    if ($initials === '') {
        $initials = 'U';
    }
@endphp

<div {{ $attributes->class(["inline-flex items-center justify-center rounded-full font-semibold uppercase", $size, $background, $text, $border]) }}>
    {{ $initials }}
</div>

