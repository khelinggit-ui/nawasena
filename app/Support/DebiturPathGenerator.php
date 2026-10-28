<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DebiturPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $model = $media->model;
        if ($model && isset($model->nama_peminjam)) {
            // Sanitize name for folder (replace spaces with _, remove weird chars)
            $safeName = preg_replace('/[^A-Za-z0-9_-]/', '', str_replace(' ', '_', $model->nama_peminjam));
            // Append model ID to ensure uniqueness if there are duplicate names
            return $safeName . '_' . $model->id;
        }

        // Fallback to default behavior if something goes wrong
        return $media->getKey();
    }
}
