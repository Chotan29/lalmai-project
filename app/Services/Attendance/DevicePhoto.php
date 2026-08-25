<?php

namespace App\Services\Attendance;

use Illuminate\Support\Facades\Log;

/**
 * A copy of somebody's photo cut down to what the attendance device will actually take.
 *
 * The device downloads the picture itself over the college wifi, and it is fussy in two ways that
 * are easy to miss: it refuses anything taller than 1080 pixels, and it abandons a download that
 * takes too long - LAN_EXP-4044, "File download timeout". Of 1,208 students with a photo, 405 are
 * too tall and 310 are over half a megabyte. Both failures look the same from the screen: the
 * student is enrolled, has no face, and is a stranger at the gate.
 *
 * The original is never touched. It is what prints on the ID card, and shrinking it to suit a
 * door reader would be paying for the device with the thing the photo was collected for.
 *
 * The smaller copy is written once as a plain file under public/ and handed to the device as an
 * ordinary url. It is not generated on request: the device asks for over a thousand of these, and
 * a static file costs the server nothing where a thousand PHP requests would cost it a great deal.
 *
 * Vendor neutral - nothing here knows which brand is on the wall. The limits come from config.
 */
class DevicePhoto
{
    /**
     * The address to give the device for this person's face, or null if there is no usable photo.
     *
     * @param  string $type   student|staff
     * @param  int    $id     the person's id, which is also what the device reports back
     * @param  string $file   the file name held on the person's record
     */
    public function urlFor($type, $id, $file)
    {
        $file = trim((string) $file);
        if ($file === '') { return null; }

        $source = public_path($this->sourceDir($type) . $file);
        if (!is_file($source)) {
            Log::warning('Device photo missing on disk', ['type' => $type, 'id' => $id, 'file' => $file]);
            return null;
        }

        $name  = $type . '-' . $id . '.jpg';
        $dir   = trim((string) config('devices.photo.cache_dir', 'images/devicePhoto'), '/');
        $cache = public_path($dir . '/' . $name);

        /* Remake it if it was never made, or if somebody has since uploaded a new photo. */
        if (!is_file($cache) || filemtime($cache) < filemtime($source)) {
            if (!$this->shrink($source, $cache)) {
                /*
                 * Better the original than nothing: a photo the device might refuse still has a
                 * chance, and a person with no face at all has none.
                 */
                return $this->publicUrl($this->sourceDir($type) . $file);
            }
        }

        return $this->publicUrl($dir . '/' . $name);
    }

    /* ---------------- pieces ---------------- */

    protected function sourceDir($type)
    {
        return $type === 'staff' ? 'images/staffProfile/' : 'images/studentProfile/';
    }

    /**
     * An absolute address that works from where the device is standing - it knows nothing of our
     * storage layout and cannot resolve a development tunnel, so the base can be overridden.
     */
    protected function publicUrl($relative)
    {
        $base = trim((string) config('devices.photo_base_url', ''));

        return $base !== ''
            ? rtrim($base, '/') . '/' . ltrim($relative, '/')
            : asset($relative);
    }

    /**
     * Write a smaller jpeg beside the original. Returns false if the server cannot do it, which
     * is a real possibility on shared hosting where GD is sometimes left out.
     */
    protected function shrink($source, $target)
    {
        if (!function_exists('imagecreatetruecolor')) {
            Log::warning('Cannot resize photos for the device - GD is not installed on this server');
            return false;
        }

        $size = @getimagesize($source);
        if (!$size) { return false; }

        [$width, $height] = $size;
        if ($width < 1 || $height < 1) { return false; }

        $max = (int) config('devices.photo.max_height', 640);
        if ($max < 120) { $max = 120; }

        /* Only ever smaller. Blowing a small photo up adds no detail and only makes the file
           heavier, which is the problem we are here to solve. */
        $scale     = $height > $max ? $max / $height : 1;
        $newWidth  = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $src = $this->read($source, $size[2]);
        if (!$src) { return false; }

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        /* A png with transparency becomes black on a jpeg unless something is put behind it, and
           a black rectangle where a face should be is not a photo the device can use. */
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $dir = dirname($target);
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            imagedestroy($src); imagedestroy($dst);
            Log::warning('Cannot create the device photo folder', ['dir' => $dir]);
            return false;
        }

        $ok = @imagejpeg($dst, $target, (int) config('devices.photo.quality', 85));

        imagedestroy($src);
        imagedestroy($dst);

        if (!$ok) {
            Log::warning('Could not write the smaller photo', ['target' => $target]);
        }

        return (bool) $ok;
    }

    protected function read($path, $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG: return @imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:  return @imagecreatefrompng($path);
            case IMAGETYPE_GIF:  return @imagecreatefromgif($path);
            case IMAGETYPE_WEBP: return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false;
        }

        return false;
    }
}
