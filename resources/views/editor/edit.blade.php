@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Editing: {{ $node->title }}</h1>

    @if (session('status'))
        <div class="p-2 bg-green-100 text-green-700 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('docs.update', $node->id) }}">
        @csrf
        <textarea name="content" rows="20"
                  class="w-full border border-gray-300 rounded p-3 font-mono"
                  required>{{ old('content', $node->document->content) }}</textarea>

        <div class="mt-4 flex justify-between">
            <a href="{{ route('docs', [
                'sectionSlug' => $version->section->slug,
                'version' => $version->version_number,
                'docPath' => $node->path
            ]) }}"
               class="text-gray-600 underline">Cancel</a>

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
