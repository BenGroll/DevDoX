<?php

use Illuminate\Support\Facades\Route;
use App\Models\Section;

function buildTree($nodes, $parentId = null, $depth = 0)
{
    return $nodes
    ->where('parent_id', $parentId)
    ->map(function ($node) use ($nodes, $depth) {
        $node->depth = $depth;
        $node->children = buildTree($nodes, $node->id, $depth + 1);
        return $node;
    });
}

// Fallback for home or redirect
Route::get('/', function () {
    return redirect('/core/13.7');
});

Route::post('/document/{node}/update', function (\App\Models\Node $node) {
    request()->validate([
        'content' => 'required|string',
    ]);

    $document = $node->document;
    $document->update([
        'content' => request('content'),
        'last_edited_by' => null, // or auth()->id() if using login
    ]);

    return redirect()->route('docs', [
        'sectionSlug' => $node->version->section->slug,
        'version' => $node->version->version_number,
        'docPath' => $node->path,
    ])->with('status', 'Saved!');
})->name('docs.update');

Route::get('/{sectionSlug}/{version}/{docPath}/edit', function ($sectionSlug, $version, $docPath) {
    $section = \App\Models\Section::where('slug', urldecode($sectionSlug))->firstOrFail();
    $version = $section->versions()->where('version_number', $version)->firstOrFail();

    $docPath = trim($docPath, '/');
    $node = $version->nodes()->where('path', $docPath)->with('document')->firstOrFail();
    abort_unless($node->type === 'document', 404);

    // Needed for layout: sidebar, picker
    $sections = \App\Models\Section::with(['children.versions', 'versions'])->whereNull('parent_id')->get();
    $allNodes = $version->nodes()->with('children', 'document', 'version.section')->get();

    // Build the navigation tree
    $tree = collect($allNodes)->where('parent_id', null)->map(function ($node) use ($allNodes) {
        $node->depth = 0;
        $node->children = buildTree($allNodes, $node->id, 1);
        return $node;
    });

    return view('editor.edit', [
        'section' => $section,
        'version' => $version,
        'node' => $node,
        'sections' => $sections,
        'tree' => $tree,
        'currentNode' => $node, // Optional: highlights the selected one
    ]);
})->where('docPath', '.*')->name('docs.edit');

Route::get('/{sectionSlug}/{version}/{docPath}/download', function ($sectionSlug, $version, $docPath) {
    $section = \App\Models\Section::where('slug', urldecode($sectionSlug))->firstOrFail();
    $version = $section->versions()->where('version_number', $version)->firstOrFail();
    
    $docPath = trim($docPath, '/');
    $node = $version->nodes()->with('document')->where('path', $docPath)->firstOrFail();
    
    abort_unless($node->type === 'document' && $node->document, 404);
    
    $filename = $node->slug . '.md';
    $content = $node->document->content;
    
    return Response::make($content, 200, [
        'Content-Type' => 'text/markdown',
        'Content-Disposition' => "attachment; filename=\"$filename\""
    ]);
})->where('docPath', '.*')->name('docs.download');

Route::post('/create-node', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'version_id' => 'required|exists:versions,id',
        'parent_id' => 'required|exists:nodes,id',
        'type' => 'required|in:folder,document',
        'title' => 'required|string|max:255',
    ]);

    $parent = \App\Models\Node::find($validated['parent_id']);
    $slug = \Illuminate\Support\Str::slug($validated['title']);
    $path = "{$parent->path}/{$slug}";

    $node = \App\Models\Node::create([
        'version_id' => $validated['version_id'],
        'parent_id' => $validated['parent_id'],
        'title' => $validated['title'],
        'slug' => $slug,
        'path' => $path,
        'type' => $validated['type'],
    ]);

    if ($node->type === 'document') {
        \App\Models\Document::create([
            'node_id' => $node->id,
            'content' => "# {$node->title}\n\nWrite your content here.",
        ]);
    }

    return redirect()->route('docs', [
        'sectionSlug' => $node->version->section->slug,
        'version' => $node->version->version_number,
        'docPath' => $node->path,
    ]);
})->name('docs.store');

Route::get('/{sectionSlug}/{version}/{docPath?}', function ($sectionSlug, $version, $docPath = null) {
    $section = \App\Models\Section::where('slug', urldecode($sectionSlug))->firstOrFail();
    $version = $section->versions()->where('version_number', $version)->firstOrFail();

    $docPath = trim($docPath ?? '', '/');
    $currentNode = $docPath
        ? $version->nodes()->where('path', $docPath)->with('document')->first()
        : null;

    $children = collect();
    if ($currentNode && $currentNode->type === 'folder') {
        $children = $currentNode->children()->orderBy('order')->get();
    }

    // Required for navigation and picker
    $sections = \App\Models\Section::with(['children.versions', 'versions'])->whereNull('parent_id')->get();
    $allNodes = $version->nodes()->with('children', 'document', 'version.section')->get();
    $tree = buildTree($allNodes);

    return view('partials.content', compact(
        'section', 'sections', 'version', 'tree', 'currentNode', 'children'
    ));
})->where('docPath', '.*')->name('docs');

