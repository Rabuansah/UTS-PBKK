<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasUlids;

    protected $table = 'books';

    protected $fillable = [
        'title', 
        'isbn', 
        'publisher', 
        'year_published', 
        'stock'
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'isbn' => 'string',
            'publisher' => 'string',
            'year_published' => 'string',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'book_id');
    }

    public function bookAuthors(): HasMany
    {
        return $this->hasMany(BookAuthor::class, 'book_id');
    }
}
