<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AttendanceProfilePhotoRule implements Rule
{
    protected $messageText = 'Invalid profile photo.';

    public function passes($attribute, $value)
    {
        if (!$value || !method_exists($value, 'getRealPath')) {
            $this->messageText = 'Please upload a valid photo file.';
            return false;
        }

        $path = $value->getRealPath();
        if (!$path || !is_file($path)) {
            $this->messageText = 'Uploaded photo could not be processed.';
            return false;
        }

        $size = @getimagesize($path);
        if (!$size || empty($size[0]) || empty($size[1])) {
            $this->messageText = 'Uploaded file is not a valid image.';
            return false;
        }

        $width = (int) $size[0];
        $height = (int) $size[1];

        if ($width < 240 || $height < 320) {
            $this->messageText = 'Photo resolution is too low. Minimum is 240x320.';
            return false;
        }

        if ($height <= $width) {
            $this->messageText = 'Photo must be straight portrait (vertical).';
            return false;
        }

        $ratio = $width / max($height, 1);
        if ($ratio < 0.55 || $ratio > 0.90) {
            $this->messageText = 'Photo frame is not suitable. Keep a standard portrait framing.';
            return false;
        }

        $image = $this->loadImageResource($path);
        if (!$image) {
            $this->messageText = 'Could not read image pixels for validation.';
            return false;
        }

        $bgCheck = $this->hasLightBackground($image, $width, $height);
        if (!$bgCheck) {
            imagedestroy($image);
            $this->messageText = 'Background must be white or very light.';
            return false;
        }

        $sideMarginCheck = $this->hasSideMargins($image, $width, $height);
        if (!$sideMarginCheck) {
            imagedestroy($image);
            $this->messageText = 'Both ears/side margins are not clearly visible. Re-capture with full face in frame.';
            return false;
        }

        $tiltOk = $this->isReasonablyStraight($image, $width, $height);
        imagedestroy($image);

        if (!$tiltOk) {
            $this->messageText = 'Photo is tilted. Keep head and camera straight.';
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->messageText;
    }

    protected function loadImageResource($path)
    {
        $bytes = @file_get_contents($path);
        if ($bytes === false) {
            return null;
        }

        $img = @imagecreatefromstring($bytes);
        return $img ?: null;
    }

    protected function hasLightBackground($img, $w, $h)
    {
        $marginX = max(6, (int) round($w * 0.08));
        $marginY = max(6, (int) round($h * 0.08));
        $step = max(2, (int) floor(min($w, $h) / 120));

        $light = 0;
        $total = 0;

        // Top and bottom borders
        for ($x = 0; $x < $w; $x += $step) {
            for ($y = 0; $y < $marginY; $y += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $light++;
                }
                $total++;
            }
            for ($y = max(0, $h - $marginY); $y < $h; $y += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $light++;
                }
                $total++;
            }
        }

        // Left and right borders
        for ($y = $marginY; $y < max($marginY, $h - $marginY); $y += $step) {
            for ($x = 0; $x < $marginX; $x += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $light++;
                }
                $total++;
            }
            for ($x = max(0, $w - $marginX); $x < $w; $x += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $light++;
                }
                $total++;
            }
        }

        if ($total === 0) {
            return false;
        }

        return ($light / $total) >= 0.58;
    }

    protected function hasSideMargins($img, $w, $h)
    {
        $strip = max(8, (int) round($w * 0.10));
        $top = (int) round($h * 0.18);
        $bottom = (int) round($h * 0.88);
        $step = max(2, (int) floor(min($w, $h) / 120));

        $leftLight = 0;
        $leftTotal = 0;
        for ($x = 0; $x < $strip; $x += $step) {
            for ($y = $top; $y < $bottom; $y += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $leftLight++;
                }
                $leftTotal++;
            }
        }

        $rightLight = 0;
        $rightTotal = 0;
        for ($x = max(0, $w - $strip); $x < $w; $x += $step) {
            for ($y = $top; $y < $bottom; $y += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $rightLight++;
                }
                $rightTotal++;
            }
        }

        if ($leftTotal === 0 || $rightTotal === 0) {
            return false;
        }

        $leftRatio = $leftLight / $leftTotal;
        $rightRatio = $rightLight / $rightTotal;

        // Require some clear side background on both sides to avoid over-zoomed/cropped face.
        return $leftRatio >= 0.22 && $rightRatio >= 0.22;
    }

    protected function isReasonablyStraight($img, $w, $h)
    {
        $step = max(3, (int) floor(min($w, $h) / 100));
        $points = [];

        for ($y = 0; $y < $h; $y += $step) {
            for ($x = 0; $x < $w; $x += $step) {
                if (!$this->isLightPixel($img, $x, $y)) {
                    $points[] = [$x, $y];
                }
            }
        }

        $count = count($points);
        if ($count < 60) {
            return true;
        }

        $sumX = 0.0;
        $sumY = 0.0;
        foreach ($points as $p) {
            $sumX += $p[0];
            $sumY += $p[1];
        }

        $meanX = $sumX / $count;
        $meanY = $sumY / $count;

        $covXX = 0.0;
        $covYY = 0.0;
        $covXY = 0.0;

        foreach ($points as $p) {
            $dx = $p[0] - $meanX;
            $dy = $p[1] - $meanY;
            $covXX += $dx * $dx;
            $covYY += $dy * $dy;
            $covXY += $dx * $dy;
        }

        if (($covXX + $covYY) == 0.0) {
            return true;
        }

        $angleRad = 0.5 * atan2(2.0 * $covXY, ($covXX - $covYY));
        $angleDeg = abs(rad2deg($angleRad));

        // Accept if dominant axis is close to vertical (about 90 degree from x-axis).
        $tiltFromVertical = abs(90.0 - $angleDeg);
        return $tiltFromVertical <= 28.0;
    }

    protected function isLightPixel($img, $x, $y)
    {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $brightness = ($r + $g + $b) / 3.0;
        $delta = $max - $min;

        return $brightness >= 190 && $delta <= 38;
    }
}
