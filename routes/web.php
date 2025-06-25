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

Route::get('/{sectionPath}/{version}/{any?}', function ($sectionPath, $version, $any = null) {
    $sectionSlugs = explode('/', $sectionPath);

    // Traverse slugs to get the final section
    $parent = null;
    foreach ($sectionSlugs as $slug) {
        $query = \App\Models\Section::where('slug', $slug);
        if ($parent) {
            $query->where('parent_id', $parent->id);
        } else {
            $query->whereNull('parent_id');
        }
        $parent = $query->firstOrFail();
    }
    $section = $parent;

    $sections = \App\Models\Section::with(['children.versions', 'versions'])->whereNull('parent_id')->get();
    $version = $section->versions()->where('version_number', $version)->firstOrFail();
    $allNodes = $version->nodes()->with('children', 'document', 'version.section')->get();
    $tree = buildTree($allNodes);

    $currentNode = $any
        ? $version->nodes()->with('document')->where('path', $any)->first()
        : null;

    return view('layouts.app', compact(
        'section', 'sections', 'version', 'tree', 'currentNode'
    ));
})->where('sectionPath', '.*')->where('any', '.*')->name('docs');

// Fallback for home or redirect
Route::get('/', function () {
    return redirect('/core/13.7');
});
