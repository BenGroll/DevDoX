<li class="mb-1 ml-{{ $node->depth * 4 }}">
    <div class="group flex items-center justify-between pr-2">
        <a href="{{ route('docs', [
            'sectionSlug' => $node->version->section->slug,
            'version' => $node->version->version_number,
            'docPath' => $node->path,
        ]) }}"
           class="block px-2 py-1 rounded hover:bg-gray-200 transition text-base
               @if (isset($currentNode) && $currentNode->id === $node->id) bg-blue-100 font-semibold @endif">
            @if ($node->type === 'folder') > @else # @endif
            {{ $node->title }}
        </a>

        @if ($node->type === 'folder')
            <div class="flex gap-1 items-center opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                <button onclick="showQuickAdd('{{ $node->id }}', 'document')" class="text-base text-blue-500 hover:text-blue-700">>+</button>
                <button onclick="showQuickAdd('{{ $node->id }}', 'folder')" class="text-base text-green-600 hover:text-green-800">#+</button>
            </div>
        @endif
    </div>

    @if ($node->type === 'folder')
        <!-- Inline create form -->
        <form method="POST" action="{{ route('docs.store') }}"
              id="quick-form-{{ $node->id }}" class="mt-1 ml-4 hidden flex gap-2 items-center">
            @csrf
            <input type="hidden" name="version_id" value="{{ $node->version->id }}">
            <input type="hidden" name="parent_id" value="{{ $node->id }}">
            <input type="hidden" name="type" id="type-{{ $node->id }}" value="document">
            <input type="text" name="title" placeholder="New name..." required class="text-base px-2 py-1 border rounded w-44">
            <button type="submit" class="text-sm bg-blue-500 text-white px-2 py-1 rounded">Add</button>
            <button type="button" onclick="hideQuickAdd('{{ $node->id }}')" class="text-sm text-gray-500">✖</button>
        </form>
    @endif

    @if ($node->children->isNotEmpty())
        <ul class="ml-4 border-l pl-2">
            @foreach ($node->children as $child)
                @include('partials.node', ['node' => $child, 'currentNode' => $currentNode ?? null])
            @endforeach
        </ul>
    @endif
</li>