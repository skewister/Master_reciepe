<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'content',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function recipe(){
        return $this->belongsTo(Recipe::class);
    }

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'recipe_id' => 'integer',
    ];
}
