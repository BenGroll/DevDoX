<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = ['section_id', 'version_number', 'release_date'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }
}
