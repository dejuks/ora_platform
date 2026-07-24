<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'module_id',
        'parent_id',
        'title',
        'icon',
        'route',
        'permission',
        'sort_order',
        'is_active',
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

