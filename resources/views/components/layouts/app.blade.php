@props([
    'title' => null,
    'mainClass' => null,
])

<x-layouts.app.sidebar :title="$title">
    <flux:main :class="$mainClass">
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
