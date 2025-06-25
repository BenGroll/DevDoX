@extends('layouts.app')

@section('content')
    <h1 class="text-xl font-bold mb-4">Search Results for: "{{ $query }}"</h1>

    @if ($results->isEmpty())
        <p class="text-gray-500 italic">No matches found.</p>
    @else
        <ul class="space-y-2">
            @foreach ($results as $node)
                <li>
                    <a href="{{ route('docs', [
                        'sectionSlug' => $section->slug,
                        'version' => $version->version_number,
                        'docPath' => $node->path
                    ]) }}"
                       class="text-blue-600 hover:underline">
                        {{ $node->title }} — <span class="text-gray-500 text-sm">{{ $node->path }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
