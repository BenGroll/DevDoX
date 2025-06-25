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

Route::get('/{sectionSlug}/{version}/{docPath?}', function ($sectionSlug, $version, $docPath = null) {
    // Decode the slug (just in case) and replace tilde with bracketed style if needed
    $sectionSlug = urldecode($sectionSlug);

    // Match real section by slug (e.g. 'plugins~statistics' becomes slug 'plugins~statistics')
    $section = \App\Models\Section::where('slug', $sectionSlug)->firstOrFail();

    $version = $section->versions()->where('version_number', $version)->firstOrFail();

    $sections = \App\Models\Section::with(['children.versions', 'versions'])->whereNull('parent_id')->get();
    $allNodes = $version->nodes()->with('children', 'document', 'version.section')->get();
    $tree = buildTree($allNodes);

    $docPath = trim($docPath ?? '', '/');
    $currentNode = $docPath
        ? $version->nodes()->with('document')->where('path', $docPath)->first()
        : null;

    $children = collect();
    if ($currentNode && $currentNode->type === 'folder') {
        $children = $currentNode->children()->orderBy('order')->get();
    }

    return view('layouts.app', compact(
        'section', 'sections', 'version', 'tree', 'currentNode', 'children'
    ));
})->where('docPath', '.*')->name('docs');

// Fallback for home or redirect
Route::get('/', function () {
    return redirect('/core/13.7');
});
