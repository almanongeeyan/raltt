<?php
class ImageHasher
{
    public function calculate($filePath): string {
        $imageData = file_get_contents($filePath);
        if ($imageData === false) throw new Exception("Could not read image file: $filePath");
        return $this->calculateFromBlob($imageData);
    }

    public function calculateFromBlob(string $imageData): string {
        $img = @imagecreatefromstring($imageData);
        if (!$img) throw new Exception("Invalid image data provided. Could be a corrupt image.");
        $resized = imagecreatetruecolor(8, 8);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, 8, 8, imagesx($img), imagesy($img));
        $pixels = [];
        $totalValue = 0;
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $gray = (imagecolorat($resized, $x, $y) >> 16) & 0xFF;
                $pixels[] = $gray;
                $totalValue += $gray;
            }
        }
        imagedestroy($img);
        imagedestroy($resized);
        $averageValue = $totalValue / 64;
        $hash = '';
        foreach ($pixels as $pixel) {
            $hash .= ($pixel >= $averageValue) ? '1' : '0';
        }
        $hexHash = '';
        for ($i = 0; $i < 64; $i += 4) {
            $hexHash .= dechex(bindec(substr($hash, $i, 4)));
        }
        return $hexHash;
    }
    
    public function distance(string $hash1, string $hash2): int {
        $distance = 0;
        for ($i = 0; $i < strlen($hash1); $i++) {
            $h1 = base_convert($hash1[$i], 16, 2);
            $h2 = base_convert($hash2[$i], 16, 2);
            $distance += substr_count(str_pad($h1, 4, '0', STR_PAD_LEFT) ^ str_pad($h2, 4, '0', STR_PAD_LEFT), '1');
        }
        return $distance;
    }
}