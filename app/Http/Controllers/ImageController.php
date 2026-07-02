<?php
// app/Http/Controllers/ImageController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    public function show($filename)
    {
        // You can add additional conditions to restrict access here
        if (!auth()->check()) {
            abort(403, 'Unauthorized access');
        }

        $path = storage_path("app/images/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header("Content-Type", $type);
    }
}
