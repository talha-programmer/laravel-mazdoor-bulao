<?php

namespace App\Util;

use App\Models\Image;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ImageLibrary;

class ImageUtil
{
    /**
     * Create a thumbnail of image and save the image and thumbnail
     * Also store image object in database
     */
    public static function saveImage($image, string $path, $imageable): bool
    {
        $imageObject = new Image();
        $imageName = time() . '-' . Str::kebab($image->getClientOriginalName());

        $thumbnail = ImageLibrary::make($image)->resize('250', null, function ($constraint){
            $constraint->aspectRatio();
        });
        $thumbnailName = "thumbnail-" . $imageName;

        $image->move(public_path($path), $imageName);
        $thumbnail->save(public_path($path. "/$thumbnailName"));

        $imageObject->image_path = $path . "/$imageName";
        $imageObject->image_thumbnail_path = $path . "/$thumbnailName";

        $imageObject->imageable()->associate($imageable);

        return $imageObject->save();
    }

}
