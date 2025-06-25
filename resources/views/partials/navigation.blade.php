<ul class="text-sm">
    @foreach ($tree as $node)
        @include('partials.node', ['node' => $node])
    @endforeach
</ul>
<script>
    function showQuickAdd(nodeId, type) {
        const form = document.getElementById(`quick-form-${nodeId}`);
        const typeInput = document.getElementById(`type-${nodeId}`);
        typeInput.value = type;
        form.classList.remove('hidden');
        form.querySelector('input[name="title"]').focus();
    }

    function hideQuickAdd(nodeId) {
        const form = document.getElementById(`quick-form-${nodeId}`);
        form.classList.add('hidden');
        form.reset();
    }
</script>
