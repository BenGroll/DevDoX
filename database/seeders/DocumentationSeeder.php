<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Version;
use App\Models\Node;
use App\Models\Document;
use Illuminate\Support\Str;

class DocumentationSeeder extends Seeder
{
    public function run()
    {
        $structure = [
            'Core' => ['13.7', '13.6', '13.0'],
            'Core-Plugin[Statistics]' => ['2', '1.1', '1'],
            'Custom-Plugin[Budgets]' => ['2', '1.1', '1'],
            'Portv2' => ['2', '1.1', '1'],
        ];

        foreach ($structure as $sectionName => $versions) {
            $section = Section::create([
                'name' => $sectionName,
                'slug' => Str::slug($sectionName),
            ]);

            foreach ($versions as $versionNumber) {
                $version = Version::create([
                    'section_id' => $section->id,
                    'version_number' => $versionNumber,
                ]);

                $this->createNodeTree($version);
            }
        }
    }

    private function createNodeTree(Version $version)
    {
        // Root "General"
        $general = Node::create([
            'version_id' => $version->id,
            'title' => 'General',
            'slug' => 'general',
            'path' => 'general',
            'type' => 'folder',
            'is_root' => true,
        ]);

        // Changelogs
        $changelogs = Node::create([
            'version_id' => $version->id,
            'parent_id' => $general->id,
            'title' => 'Changelogs',
            'slug' => 'changelogs',
            'path' => 'general/changelogs',
            'type' => 'folder',
        ]);

        // Determine document type by version_number
        $docType = str_starts_with($version->version_number, '13.0') || $version->version_number === '2' || $version->version_number === '1'
            ? 'Major'
            : 'Minor';

        $docSlug = Str::slug($docType);

        $docNode = Node::create([
            'version_id' => $version->id,
            'parent_id' => $changelogs->id,
            'title' => $docType,
            'slug' => $docSlug,
            'path' => "general/changelogs/{$docSlug}",
            'type' => 'document',
        ]);

        Document::create([
            'node_id' => $docNode->id,
            'content' => "# {$docType} Changelog\n\nDetails for version {$version->version_number}.",
        ]);

        // Installation
        Node::create([
            'version_id' => $version->id,
            'parent_id' => $general->id,
            'title' => 'Installation',
            'slug' => 'installation',
            'path' => 'general/installation',
            'type' => 'folder',
        ]);
    }
}
