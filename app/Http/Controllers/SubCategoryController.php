<?php

namespace App\Http\Controllers;


use App\SubCategory;
use App\Category;
use App\Item;
use App\Variant;
use App\ItemImage;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }


    public function index(){
        return view('subCategory')->with('subCategories', SubCategory::all());
    }

    public function addSubCategory()
    {
        $categories = Category::all();
        return view('subCategory.add-sub-category',compact('categories'));
    }

    public function storeSubCategory(Request $request) {

        $subCategory = new SubCategory();
        $subCategory->name = request('name');
        $subCategory->category_id = request('category_id');

        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/subCategory'), $imageName);
        $subCategory->image = $imageName;
        error_log($subCategory);
        $subCategory->save();
        return $subCategory;
    }

    public function editSubCategory($id)
    {
        $subCategory = SubCategory::where('id', $id)->first();
        $categories = Category::all();
        return view('subCategory.edit-sub-category',compact('subCategory','categories'));
    }

    public function updateSubCategory($id)
    {
        $name = request('name');
        $category = request('category_id');
        $subCategory = SubCategory::where('id',$id)->first();
        error_log($category);
        if(request('image') != null){
            if ($subCategory->image != null) {
                if (file_exists(public_path('images/subCategory/').$subCategory->image)) {
                    unlink(public_path('images/subCategory/').$subCategory->image);
                }
            }
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/subCategory'), $imageName);
            return SubCategory::where('id', $id)
            ->update(['name'  => $name,
                        'category_id' => $category,
                     'image'  => $imageName
                     ]);
        }
        else{
            return SubCategory::where('id', $id)
                ->update(['name'  => $name,'category_id' => $category]
            );
        }

//        return redirect('/subCategory');
    }

    public function deleteSubCategory($id)
    {
        try {
            error_log('here');
            $subCategory = SubCategory::where('id',$id)->first();
            SubCategory::where('id', $id)
                ->delete();
            if ($subCategory->image != null) {
                if (file_exists(public_path('images/subCategory/').$subCategory->image)) {
                    unlink(public_path('images/subCategory/').$subCategory->image);
                }
            }
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Please delete all products relevant to this sub category inorder to remove this sub category.',
            ],200);
        }
    }

    public function apiIndex()
    {
        return SubCategory::all();
    }
    public function availableItems($id,$branch)
    {
        error_log($branch);
        $sendItems = [];
        if ($branch !== 'null') {
            $items = Item::where('subCategory_id',$id)->where('branch_id',$branch)->with('unit')->get();
        } else {
            $items = Item::where('subCategory_id',$id)->with('unit')->get();
        }
        foreach ($items as $item) {
            $obj = new \stdClass();
            $variants = Variant::where('item_id', $item->id)->where('stock','>',0)->get();
            $images = ItemImage::where('item_id',$item->id)->get();
            $obj->item = $item;
            $obj->variants = $variants;
            $obj->images = $images;
            array_push($sendItems,$obj);
        }
        return $sendItems;
    }

    public function apiIndexById($id)
    {
        return SubCategory::where('id', $id)->first();
    }

    public function apiProducts($id)
    {
        $products = Item::where('subCategory_id',$id)->get();
        return $products;
    }
}
