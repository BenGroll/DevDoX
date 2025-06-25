@extends('layouts.app')

@section('content')
    @if ($currentNode && $currentNode->type === 'document' && $currentNode->document)
        <div class="text-right mb-2" style="align-items: center">
            <a href="{{ route('docs.edit', [
                'sectionSlug' => $section->slug,
                'version' => $version->version_number,
                'docPath' => $currentNode->path
            ]) }}"
            class="text-xl text-blue-600 hover:underline">Edit</a>
            <a href="{{ route('docs.download', [
                'sectionSlug' => $section->slug,
                'version' => $version->version_number,
                'docPath' => $currentNode->path
            ]) }}"
            class="text-xl text-grey-600 hover:underline">⤓ Download </a>
        <!-- Delete Button -->
        <button onclick="openDeleteModal()" class="text-xl text-red-600 hover:underline">X Delete</button>
        </div>
        <article class="prose max-w-full">
            {!! \Illuminate\Support\Str::markdown($currentNode->document->content) !!}
        </article>
    @elseif ($currentNode && $currentNode->type === 'folder')
        <div class="space-y-2">
            <h2 class="text-xl font-semibold mb-4">Contents of "{{ $currentNode->title }}"</h2>
            <ul>
                @foreach ($children as $child)
                    <li>
                        <a class="text-blue-600 hover:underline" href="{{ route('docs', [
                            'sectionSlug' => urlencode($version->section->slug),
                            'version' => $version->version_number,
                            'docPath' => $child->path,
                        ]) }}">
                            @if ($child->type === 'folder') > @else # @endif
                            {{ $child->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="text-gray-500 italic">Select a document to begin.</p>
    @endif

@endsection

@if ($currentNode && $currentNode->type === 'document')
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg space-y-4">
            <h2 class="text-lg font-bold text-red-600">⚠️ Confirm Deletion</h2>
            <p>This will permanently delete the entry: 
                <code class="text-sm font-mono bg-gray-100 px-1">{{ $currentNode->path }}</code>
            </p>
            <p class="text-sm">Type <strong class="text-red-600">{{ $currentNode->path }}</strong> to confirm deletion.</p>

            <form method="POST" action="{{ route('docs.destroy', $currentNode->id) }}" class="space-y-4">
                @csrf
                @method('DELETE')
                <input type="text" id="confirmText" class="w-full border px-3 py-2 rounded text-sm" placeholder="Type exact path to confirm">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="text-sm px-3 py-1 text-gray-600 hover:underline">Cancel</button>
                    <button type="submit" id="confirmDeleteButton" class="text-sm px-3 py-1 bg-red-600 text-white rounded opacity-50 cursor-not-allowed" disabled>Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('confirmText').value = '';
        document.getElementById('confirmDeleteButton').disabled = true;
        document.getElementById('confirmDeleteButton').classList.add('opacity-50', 'cursor-not-allowed');
    }

    document.getElementById('confirmText').addEventListener('input', function () {
        const required = "{{ $currentNode->path }}";
        const typed = this.value.trim();
        const button = document.getElementById('confirmDeleteButton');

        if (typed === required) {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });
    </script>
@endif