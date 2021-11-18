<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Image extends Model
{
    use HasFactory;
    protected $appends = [
      'image_url',
      'image_thumbnail_url'
    ];

    // Get full URL of the image and thumbnail
    public function getImageUrlAttribute()
    {
        return URL::to('/'). "/$this->image_path";
    }

    public function getImageThumbnailUrlAttribute()
    {
        return URL::to('/'). "/$this->image_thumbnail_path";
    }

    public function imageable()
    {
        return $this->morphTo(__FUNCTION__, 'imageable_type', 'imageable_id');
    }
}
