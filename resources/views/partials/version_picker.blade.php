<div id="version-selector" class="flex gap-2 items-center">
    <!-- Top-Level Section Dropdown -->
    <select id="section-select" class="text-sm border rounded px-2 py-1">
        @foreach ($sections as $sec)
            <option value="{{ $sec->slug }}"
                data-is-group="{{ $sec->children->isNotEmpty() ? '1' : '0' }}"
                data-children='@json($sec->children->mapWithKeys(fn($c) => [$c->slug => $c->versions->pluck("version_number")]))'
                data-versions='@json($sec->versions->pluck("version_number"))'
                @selected($sec->id === $section->id || optional($section->parent)->id === $sec->id)>
                {{ $sec->name }}
            </option>
        @endforeach
    </select>

    <!-- Child Section Dropdown (for plugins) -->
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

    // Initial load
    updateVersionSelector();

    sectionSelect.addEventListener('change', updateVersionSelector);
    childSelect.addEventListener('change', updateVersionList);

    function updateVersionSelector() {
        const selected = sectionSelect.options[sectionSelect.selectedIndex];
        const isGroup = selected.dataset.isGroup === '1';

        versionSelect.innerHTML = '';
        childSelect.innerHTML = '';
        childSelect.classList.toggle('hidden', !isGroup);

        if (isGroup) {
            const children = JSON.parse(selected.dataset.children || '{}');
            Object.keys(children).forEach(slug => {
                const option = document.createElement('option');
                option.value = slug;
                option.textContent = slug;
                childSelect.appendChild(option);
            });

            // Trigger version list update for first child
            updateVersionList();
        } else {
            const versions = JSON.parse(selected.dataset.versions || '[]');
            versions.forEach(v => {
                const option = document.createElement('option');
                option.value = v;
                option.textContent = v;
                versionSelect.appendChild(option);
            });
        }
    }

    function updateVersionList() {
        const parent = sectionSelect.options[sectionSelect.selectedIndex];
        const children = JSON.parse(parent.dataset.children || '{}');
        const selectedChild = childSelect.value;

        const versions = children[selectedChild] || [];
        versionSelect.innerHTML = '';
        versions.forEach(v => {
            const option = document.createElement('option');
            option.value = v;
            option.textContent = v;
            versionSelect.appendChild(option);
        });
    }

    function goToSelected() {
        const parent = sectionSelect.value;
        const selected = sectionSelect.options[sectionSelect.selectedIndex];
        const isGroup = selected.dataset.isGroup === '1';

        let sectionSlug = parent;
        if (isGroup) {
            sectionSlug = childSelect.value;
        }

        const version = versionSelect.value;
        if (sectionSlug && version) {
            const newUrl = `/${sectionSlug}/${version}`;
            window.location.href = newUrl;
        }
    }
</script>
