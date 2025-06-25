<div id="version-selector" class="flex gap-2 items-center">
    <select id="section-select" class="text-sm border rounded px-2 py-1">
        @foreach($sections as $sec)
            <option value="{{ $sec->slug }}"
                data-versions='@json($sec->versions->pluck("version_number"))'
                @selected($sec->id === $section->id)>
                {{ $sec->name }}
            </option>
        @endforeach
    </select>

    <select id="version-select" class="text-sm border rounded px-2 py-1">
        @foreach($section->versions as $ver)
            <option value="{{ $ver->version_number }}" @selected($ver->id === $version->id)>
                {{ $ver->version_number }}
            </option>
        @endforeach
    </select>

    <button onclick="goToSelected()" class="text-sm px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
        Go
    </button>
</div>

<script>
    const sectionSelect = document.getElementById('section-select');
    const versionSelect = document.getElementById('version-select');

    sectionSelect.addEventListener('change', () => {
        const selected = sectionSelect.options[sectionSelect.selectedIndex];
        const versions = JSON.parse(selected.dataset.versions);

        versionSelect.innerHTML = '';
        versions.forEach(v => {
            const option = document.createElement('option');
            option.value = v;
            option.textContent = v;
            versionSelect.appendChild(option);
        });
    });

    function goToSelected() {
        const section = sectionSelect.value;
        const version = versionSelect.value;

        if (section && version) {
            const newUrl = `/${section}/${version}`;
            window.location.href = newUrl;
        }
    }
</script>
