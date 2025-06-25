<ul class="text-sm">
    @foreach ($tree as $node)
        @include('partials.node', ['node' => $node])
    @endforeach
</ul>