<?php

namespace App\Http\Controllers;

use App\ItemImage;
use Illuminate\Http\Request;

class ItemImageController extends Controller
{
    public function store($id) {
        $uploadedFiles = request()->images;
        if($uploadedFiles) {
            foreach ($uploadedFiles as $file) {
                $itemImage = new ItemImage();
                $imageName = time().mt_rand(0, 1000).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('images/items'), $imageName);
                $itemImage->name = $imageName;
                $itemImage->item_id = $id;
                $itemImage->save();
            }
        }
        return response()->json([
            'message' => 'success',
        ],200);
    }

    public function update($id) {
        try {
            $uploadedFiles = request()->images;
            $deletedImages = request()->deletedImages;
            if ($deletedImages) {
                foreach($deletedImages as $deletedImage) {
                    $image = ItemImage::where('id', $deletedImage)->first();
                    if ($image != null) {
                        if (file_exists(public_path('images/items/').$image->name)) {
                            unlink(public_path('images/items/').$image->name);
                        }
                        ItemImage::where('id', $deletedImage)->delete();
                    }
                    error_log($image);
                }
            }
            if ($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    $itemImage = new ItemImage();
                    $imageName = time().mt_rand(0, 1000).'.'.$file->getClientOriginalExtension();
                    $file->move(public_path('images/items'), $imageName);
                    $itemImage->name = $imageName;
                    $itemImage->item_id = $id;
                    $itemImage->save();
                }
            }
            return response()->json([
                'message' => 'success',
            ],200);
        } catch(\Throwable $e) {
            error_log($e);
            return response()->json([
                'error' => $e,
            ],200);
        }
    }

    public function getByItemId($id) {
        return ItemImage::where('item_id', $id)->get();
    }
}
