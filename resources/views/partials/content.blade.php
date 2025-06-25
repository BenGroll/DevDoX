@if ($currentNode?->type === 'document')
    <article class="prose max-w-none">
        {!! \Illuminate\Support\Str::markdown($currentNode->document->content ?? '') !!}
    </article>

@elseif ($currentNode?->type === 'folder')
    <h2 class="text-xl font-semibold mb-4">{{ $currentNode->title }}</h2>

    @if ($children->isEmpty())
        <p class="text-gray-500">This folder has no entries yet.</p>
    @else
        <ul class="space-y-2">
            @foreach ($children as $child)
                <li>
                    <a href="{{ route('docs', [
                        'sectionSlug' => $child->version->section->path,
                        'version' => $child->version->version_number,
                        'docPath' => $child->path
                        ]) }}"
                    class="text-blue-600 hover:underline">
                        @if ($child->type === 'folder') 📁 @else 📄 @endif
                        {{ $child->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

@else
    <p class="text-gray-500">No content selected.</p>
@endif
