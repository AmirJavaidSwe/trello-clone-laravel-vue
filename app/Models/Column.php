<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Column extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    protected $appends = ['editableTitle'];

    public function cards() {
        return $this->hasMany(Card::class);
    }

    protected function editableTitle(): Attribute
    {
        return new Attribute(
            get: fn () => false,
        );
    }
}
