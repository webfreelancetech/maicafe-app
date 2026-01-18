<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HandlesImageUploads
{
    /**
     * Generate SEO-friendly filename for uploaded image
     *
     * @param UploadedFile $file
     * @param string $name
     * @param string $folder
     * @return string
     */
    protected function generateSeoFileName(UploadedFile $file, string $name, string $folder): string
    {
        // Create SEO-friendly slug from name
        $slug = Str::slug($name);
        
        // Limit slug length to prevent very long filenames
        $slug = Str::limit($slug, 50, '');
        
        // Get file extension (lowercase)
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Add short unique identifier to prevent overwrites
        $uniqueId = substr(uniqid(), -6);
        
        // Generate filename: item-name-abc123.jpg
        $fileName = "{$slug}-{$uniqueId}.{$extension}";
        
        return $fileName;
    }

    /**
     * Store uploaded image with SEO-friendly filename
     *
     * @param UploadedFile $file
     * @param string $name
     * @param string $folder
     * @return string
     */
    protected function storeImageWithSeoName(UploadedFile $file, string $name, string $folder): string
    {
        $fileName = $this->generateSeoFileName($file, $name, $folder);
        
        // Store the file with custom name
        $file->storeAs($folder, $fileName, 'public');
        
        return "{$folder}/{$fileName}";
    }
}
