<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'recipe_id',
        'content',
    ];

    public function recipe()
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
