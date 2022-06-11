<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Questionnaire extends Model
{
    use AsSource;
    use Filterable;

    protected $fillable = ['full_name', 'email', 'mobile_phone', 'campus', 'apartment', 'parking', 'images',
        'status', 'telegram_id',];

    protected $casts = [
        'images' => "array",
        'created_at' => "datetime:Y-m-d H:i:s",
        'updated_at' => "datetime:Y-m-d H:i:s"
    ];


}
