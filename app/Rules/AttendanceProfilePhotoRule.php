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
            $this->messageText = 'Background must be plain white or very light. '
                . 'Please take the photo against a white wall with even lighting (no shadow, no dark or patterned background).';
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

    /**
     * Background check.
     *
     * Only the region that is really background in a passport photo is sampled: the
     * top band and the left/right strips of the upper part of the frame. The bottom
     * edge is deliberately skipped - in a correct passport photo the shoulders and
     * clothing touch the bottom, so counting it made genuine white-background photos
     * fail the check.
     */
    protected function hasLightBackground($img, $w, $h)
    {
        $marginX = max(6, (int) round($w * 0.08));
        $topBand = max(6, (int) round($h * 0.12));
        /* Side strips are sampled beside the head only, above the shoulder line. */
        $sideBottom = (int) round($h * 0.55);
        $step = max(2, (int) floor(min($w, $h) / 120));

        $light = 0;
        $total = 0;

        // Top band (always background in a portrait)
        for ($x = 0; $x < $w; $x += $step) {
            for ($y = 0; $y < $topBand; $y += $step) {
                if ($this->isLightPixel($img, $x, $y)) {
                    $light++;
                }
                $total++;
            }
        }

        // Left and right strips beside the head
        for ($y = $topBand; $y < max($topBand, $sideBottom); $y += $step) {
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

        return ($light / $total) >= 0.55;
    }

    protected function hasSideMargins($img, $w, $h)
    {
        $strip = max(8, (int) round($w * 0.10));
        /* Sampled beside the head only. Going further down reaches the shoulders,
           which are never background and made valid photos fail. */
        $top = (int) round($h * 0.15);
        $bottom = (int) round($h * 0.55);
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

        // Some clear background beside the head on both sides, so an over-zoomed or
        // cropped face is still rejected without failing normal passport framing.
        return $leftRatio >= 0.15 && $rightRatio >= 0.15;
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

        /* Accept if the dominant axis is roughly vertical. Kept generous so normal
           head-and-shoulders framing is never mistaken for a tilted photo. */
        $tiltFromVertical = abs(90.0 - $angleDeg);
        return $tiltFromVertical <= 40.0;
    }

    /**
     * A "background" pixel: bright and not strongly coloured.
     *
     * Thresholds are deliberately tolerant - studio white walls photograph as light
     * grey, cream or faintly blue depending on lighting and camera white balance, and
     * the earlier strict values rejected those genuine white backgrounds.
     */
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

        return $brightness >= 165 && $delta <= 60;
    }
}
