<div id="search-bar" class="flex-grow max-w-md ml-auto">
    <form method="GET" action="{{ route('docs.search') }}" class="flex items-center space-x-2">
    <input type="hidden" name="section" value="{{ $section->slug }}">
    <input type="hidden" name="version" value="{{ $version->version_number }}">
    <input type="text" name="q" placeholder="Search..." class="border px-2 py-1 rounded text-sm w-64">
    <button type="submit" class="px-3 py-1 text-sm bg-blue-500 text-white rounded">Search</button>
</form>

</div>
