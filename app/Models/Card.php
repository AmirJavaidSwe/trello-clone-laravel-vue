<?php

namespace App\Models;

use Hamcrest\Description;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['column_id', 'title', 'description', 'sort_order'];

    protected $appends = ['editableTitle'];

    protected function editableTitle(): Attribute
    {
        return new Attribute(
            get: fn () => false,
        );
    }
}
