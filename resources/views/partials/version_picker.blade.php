<div id="version-selector" class="flex gap-2 items-center">
    <!-- Top-Level Section Dropdown -->
    <select id="section-select" class="text-sm border rounded px-2 py-1">
        @foreach ($sections as $sec)
            <option value="{{ $sec->slug }}"
                data-is-group="{{ $sec->children->isNotEmpty() ? '1' : '0' }}"
                data-children='@json($sec->children->map(fn($c) => [
                    'slug' => $c->slug,
                    'name' => $c->name,
                    'versions' => $c->versions->pluck("version_number")
                ]))'
                data-versions='@json($sec->versions->pluck("version_number"))'
                @selected($sec->id === $section->id || optional($section->parent)->id === $sec->id)>
                {{ $sec->name }}
            </option>
        @endforeach
    </select>

    <!-- Plugin Name Dropdown -->
    <select id="child-select" class="text-sm border rounded px-2 py-1 hidden"></select>

    <!-- Version Dropdown -->
    <select id="version-select" class="text-sm border rounded px-2 py-1"></select>

    <!-- Go Button -->
    <button onclick="goToSelected()" class="text-sm px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
        Go
    </button>
</div>
<script>
const sectionSelect = document.getElementById('section-select');
const childSelect = document.getElementById('child-select');
const versionSelect = document.getElementById('version-select');

updatePicker(true);

sectionSelect.addEventListener('change', () => updatePicker(false));
childSelect.addEventListener('change', updateVersionList);

function updatePicker(initial = false) {
    const selected = sectionSelect.options[sectionSelect.selectedIndex];
    const isGroup = selected.dataset.isGroup === '1';

    childSelect.classList.toggle('hidden', !isGroup);
    versionSelect.innerHTML = '';
    childSelect.innerHTML = '';

    if (isGroup) {
        const children = JSON.parse(selected.dataset.children || '[]');
        const currentSlug = "{{ $section->slug }}";
        const currentVersion = "{{ $version->version_number }}";

        children.forEach(child => {
            const option = document.createElement('option');
            option.value = child.slug;
            option.textContent = child.name;
            if (initial && child.slug === currentSlug) option.selected = true;
            option.dataset.versions = JSON.stringify(child.versions);
            childSelect.appendChild(option);
        });

        updateVersionList(currentVersion);
    } else {
        const versions = JSON.parse(selected.dataset.versions || '[]');
        const currentVersion = "{{ $version->version_number }}";

        versions.forEach(v => {
            const option = document.createElement('option');
            option.value = v;
            option.textContent = v;
            if (initial && v === currentVersion) option.selected = true;
            versionSelect.appendChild(option);
        });
    }
}

function updateVersionList(selectedVersion = null) {
    const selectedChild = childSelect.options[childSelect.selectedIndex];
    const versions = JSON.parse(selectedChild.dataset.versions || '[]');

    versionSelect.innerHTML = '';
    versions.forEach(v => {
        const option = document.createElement('option');
        option.value = v;
        option.textContent = v;
        if (v === selectedVersion) option.selected = true;
        versionSelect.appendChild(option);
    });
}

function goToSelected() {
    const parent = sectionSelect.options[sectionSelect.selectedIndex];
    const isGroup = parent.dataset.isGroup === '1';
    const version = versionSelect.value;

    let sectionSlug = parent.value;

    if (isGroup) {
        const pluginSlug = childSelect.value;
        sectionSlug = `${sectionSlug}~${pluginSlug}`; // Combine e.g. plugins[statistics]
    }

    if (sectionSlug && version) {
        window.location.href = `/${sectionSlug}/${version}`;
    }
}
</script>
