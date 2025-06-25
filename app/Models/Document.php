<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['node_id', 'content', 'last_edited_by'];

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }
}
