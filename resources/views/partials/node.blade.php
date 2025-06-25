<li class="mb-1 ml-{{ $node->depth * 4 }}">
    <a href="{{ route('docs', [
        'sectionPath' => $node->version->section->path,
        'version' => $node->version->version_number,
        'any' => $node->path
        ]) }}"
       class="block px-2 py-1 rounded hover:bg-gray-200 transition
              @if (isset($currentNode) && $currentNode->id === $node->id) bg-blue-100 font-semibold @endif">
        @if ($node->type === 'folder') 📁 @else 📄 @endif
        {{ $node->title }}
    </a>

    @if ($node->children->isNotEmpty())
        <ul class="ml-4 border-l pl-2">
            @foreach ($node->children as $child)
                @include('partials.node', ['node' => $child, 'currentNode' => $currentNode ?? null])
            @endforeach
        </ul>
    @endif
</li>
