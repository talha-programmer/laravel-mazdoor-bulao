<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
      'name',
      'category'
    ];
    use HasFactory;

    public function workers()
    {
        return $this->belongsToMany(User::class, 'worker_skills');
    }
}
