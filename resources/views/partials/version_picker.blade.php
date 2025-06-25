<div id="version-selector" class="flex gap-2 items-center">
    <form action="" method="GET" class="flex gap-2">
        <select name="section" onchange="this.form.submit()" class="text-sm border rounded px-2 py-1">
            @foreach($sections as $sec)
                <option value="{{ $sec->slug }}" @selected($sec->id === $section->id)>{{ $sec->name }}</option>
            @endforeach
        </select>

        <select name="version" onchange="this.form.submit()" class="text-sm border rounded px-2 py-1">
            @foreach($section->versions as $ver)
                <option value="{{ $ver->version_number }}" @selected($ver->id === $version->id)>{{ $ver->version_number }}</option>
            @endforeach
        </select>
    </form>
</div>
