@if ($currentNode?->type === 'document')
    <article class="prose max-w-none">
        {!! \Illuminate\Support\Str::markdown($currentNode->document->content ?? '') !!}
    </article>
@elseif ($currentNode)
    <h2 class="text-lg font-semibold">{{ $currentNode->title }}</h2>
    <p class="text-gray-500">This is a folder. Select a document from the left.</p>
@else
    <p class="text-gray-500">No content selected.</p>
@endif
