<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'slug'];

    public function versions()
    {
        return $this->hasMany(Version::class);
    }
}
