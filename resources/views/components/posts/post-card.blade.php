@props(['post'])

<div class="{{ $attributes }}">
    <a wire:navigate href="{{ route('posts.show', $post->slug) }}">
        <div>
            <img class="w-full rounded-xl"
                src="{{ $post->getThumbnailUrl() }}">
        </div>
    </a>
    <div class="mt-3">
        <div class="flex items-center gap-2 mb-2">

        @if ($category = $post->categories()->first())
        <x-posts.category-badge :category="$category" />

        @endif

            <p class="text-sm text-gray-500">{{ $post->published_at }}</p>
        </div>
        <a wire:navigate href="{{ route('posts.show', $post->slug) }}" class="text-xl font-bold text-gray-900">{{ $post->title }}</a>
    </div>

</div>
