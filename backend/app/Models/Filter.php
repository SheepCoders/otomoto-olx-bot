<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_email', 'site', 'category', 'search_text', 'price_from', 'price_to', 'year_from', 'year_to', 'last_sent_at'
    ];
}
