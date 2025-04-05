<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasStoreFile
{
    public function storeFile(Request $request, string $resource, string $path = null, $disk = "public"): string {
        $file = $request->file($resource);
        $name = time().$file->hashName();

        // store the file
        $file->storeAs($path !== null ? $path : $resource, $name, $disk);
        return $name;
    }
}
