<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Phone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CategoryPhoneSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'iPhone', 'color' => '#000000', 'icon' => '🍎'],
            ['name' => 'Samsung', 'color' => '#1428A0', 'icon' => '📱'],
            ['name' => 'OnePlus', 'color' => '#F50514', 'icon' => '⚡'],
            ['name' => 'Google Pixel', 'color' => '#4285F4', 'icon' => '🔍'],
        ];

        foreach ($categories as $catData) {
            $image = $this->generateImage($catData['name'], $catData['color']);
            $filename = 'categories/'.strtolower(str_replace(' ', '_', $catData['name'])).'.png';
            Storage::disk('public')->put($filename, $image);

            $category = Category::create([
                'name' => $catData['name'],
                'image' => $filename,
            ]);

            $this->createPhones($category);
        }
    }

    private function createPhones($category)
    {
        $phones = [
            ['iPhone', 15, 999],
            ['iPhone', 14, 799],
            ['iPhone', 13, 699],
            ['Samsung', 'Galaxy S24', 999],
            ['Samsung', 'Galaxy S23', 799],
            ['Samsung', 'Galaxy A54', 499],
            ['OnePlus', '12', 799],
            ['OnePlus', '11', 699],
            ['Google Pixel', '8 Pro', 999],
            ['Google Pixel', '8', 799],
        ];

        foreach ($phones as $phone) {
            if ($category->name === $phone[0]) {
                $image = $this->generatePhoneImage($phone[1], $category->name);
                $filename = 'phones/'.strtolower(str_replace(' ', '_', $category->name)).'_'.strtolower(str_replace(' ', '_', $phone[1])).'.png';
                Storage::disk('public')->put($filename, $image);

                Phone::create([
                    'name' => "{$phone[0]} {$phone[1]}",
                    'category_id' => $category->id,
                    'price' => $phone[2],
                    'qty' => rand(5, 20),
                    'image' => $filename,
                ]);
            }
        }
    }

    private function generateImage($text, $color)
    {
        $width = 400;
        $height = 300;
        $image = imagecreate($width, $height);

        $bgColor = imagecolorallocate($image, 245, 245, 245);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $colorValue = imagecolorallocate($image, hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));

        imagefill($image, 0, 0, $bgColor);

        $rectY = 50;
        imagefilledrectangle($image, 0, $rectY, $width, $rectY + 150, $colorValue);

        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $x = ($width - $textWidth) / 2;
        $y = ($rectY + 75) - 15;

        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return $imageData;
    }

    private function generatePhoneImage($model, $brand)
    {
        $width = 400;
        $height = 500;
        $image = imagecreate($width, $height);

        $bgColor = imagecolorallocate($image, 200, 200, 200);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $phoneColor = imagecolorallocate($image, 50, 50, 50);

        imagefill($image, 0, 0, $bgColor);

        $phoneX = 50;
        $phoneY = 80;
        $phoneW = 300;
        $phoneH = 350;

        imagefilledrectangle($image, $phoneX, $phoneY, $phoneX + $phoneW, $phoneY + $phoneH, $phoneColor);
        imagefilledrectangle($image, $phoneX + 10, $phoneY + 40, $phoneX + $phoneW - 10, $phoneY + $phoneH - 40, imagecolorallocate($image, 100, 200, 255));

        $text = "$brand $model";
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $x = ($width - $textWidth) / 2;
        $y = 450;

        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return $imageData;
    }
}
