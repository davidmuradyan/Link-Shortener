<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    const ROUTE = '/link/';

    protected $fillable = [
        'website_url',
        'domain_id',
        'slug',
        'clicks',
        'creator_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(LinkView::class);
    }

    public function getUrl() {
        return $this->domain->name . Link::ROUTE . $this->slug;
    }
}
