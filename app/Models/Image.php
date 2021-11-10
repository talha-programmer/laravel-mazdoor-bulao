<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Image extends Model
{
    use HasFactory;

    // Get full URL of the image and thumbnail
    public function getImageUrlAttribute($imageUrl)
    {
        return URL::to('/'). "/$imageUrl";
    }

    public function getImageThumbnailUrlAttribute($thumbnailUrl)
    {
        return URL::to('/'). "/$thumbnailUrl";
    }

    public function imageable()
    {
        return $this->morphTo(__FUNCTION__, 'imageable_type', 'imageable_id');
    }
}
