@extends('layouts.app')

@section('content')
    @if ($currentNode && $currentNode->type === 'document' && $currentNode->document)
        <div class="text-right mb-2">
            <a href="{{ route('docs.edit', [
                'sectionSlug' => $section->slug,
                'version' => $version->version_number,
                'docPath' => $currentNode->path
            ]) }}"
            class="text-sm text-blue-600 hover:underline">✏️ Edit</a>
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


