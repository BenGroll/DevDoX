<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'slug'];

    public function versions() {
        return $this->hasMany(Version::class);
    }

    public function parent() {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Section::class, 'parent_id');
    }

    public function isGroup() {
        return $this->children()->exists();
    }

    public function getPathAttribute() {
        $slugs = [];
        $current = $this;
        while ($current) {
            array_unshift($slugs, $current->slug);
            $current = $current->parent;
        }
        return implode('/', $slugs);
    }

}
