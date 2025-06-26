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

        // Document type based on version number
        $docType = str_starts_with($version->version_number, '13.0') ||
                   $version->version_number === '2' ||
                   $version->version_number === '1'
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

        // Installation folder
        Node::create([
            'version_id' => $version->id,
            'parent_id' => $general->id,
            'title' => 'Installation',
            'slug' => 'installation',
            'path' => 'general/installation',
            'type' => 'folder',
        ]);
    }
    
    public function run() {
        $structure = [
            'Custom-Plugins' => [
                'Budgets' => ['1', '2'],
                'Invoices' => ['1'],
            ],
            'Core-Plugins' => [
                'Statistics' => ['1', '2'],
            ],
            'Core' => ['13.7', '13.6'],
            'Port' => ['1', '2'],
        ];

        foreach ($structure as $sectionName => $content) {
            $section = Section::create([
                'name' => $sectionName,
                'slug' => Str::slug($sectionName),
            ]);

            if (is_array($content) && is_string(array_key_first($content))) {
                // Plugin container: content is plugin => [versions]
                foreach ($content as $pluginName => $pluginVersions) {
                    $plugin = Section::create([
                        'name' => $pluginName,
                        'slug' => Str::slug($pluginName),
                        'parent_id' => $section->id,
                    ]);

                    foreach ($pluginVersions as $versionNumber) {
                        $version = Version::create([
                            'section_id' => $plugin->id,
                            'version_number' => $versionNumber,
                        ]);
                        $this->createNodeTree($version);
                    }
                }
            } else {
                // Flat section like Core/Port
                foreach ($content as $versionNumber) {
                    $version = Version::create([
                        'section_id' => $section->id,
                        'version_number' => $versionNumber,
                    ]);
                    $this->createNodeTree($version);
                }
            }
        }
    }

}
