<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = [
        'version_id', 'parent_id', 'type', 'title', 'slug', 'path', 'order', 'is_root'
    ];

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function parent()
    {
        return $this->belongsTo(Node::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Node::class, 'parent_id');
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }
}
