<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'difficulty',
        'prep_time',
        'cook_time',
        'image',
        'video',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Ingredient()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients');
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];
}
