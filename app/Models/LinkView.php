<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkView extends Model
{
    // ToDo: more data can be kept in this model

    use HasFactory;

    protected $fillable = [
        'ip',
        'link_id',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }
}
