<?php

namespace App\Http\Controllers;


use App\SubCategory;
use App\Category;
use App\Product;
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
        return view('SubCategory.add-sub-category',compact('categories'));
    }

    public function storeSubCategory(Request $request) {
        $subCategory = new SubCategory();
        $subCategory->name = request('name');
        $subCategory->category_id = request('category');


        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/subCategory'), $imageName);

        $subCategory->image = $imageName;
        $subCategory->save();
        return redirect('/subCategory');
    }

    public function editSubCategory($id)
    {
        $subCategory = SubCategory::where('id', $id)->first();
        $categories = Category::all();
        return view('SubCategory.edit-sub-category',compact('subCategory','categories'));
    }

    public function updateSubCategory($id)
    {
        $name = request('name');
        $category = request('category');
        if(request('image') != null){
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/subCategory'), $imageName);
            SubCategory::where('id', $id)
            ->update(['name'  => $name,
                        'category_id' => $category,
                     'image'  => $imageName
                     ]);
        }
        else{
            SubCategory::where('id', $id)
                ->update(['name'  => $name,'category_id' => $category]
            );
        }
        
        return redirect('/subCategory');
    }

    public function deleteSubCategory($id)
    {
        SubCategory::where('id', $id)
                ->delete();
        
        return redirect('/subCategory');
    }

    public function apiIndex()
    {
        return SubCategory::all();
    }

    public function apiIndexById($id)
    {
        return SubCategory::where('id', $id)->first();
    }

    public function apiProducts($id)
    {
        $products = Product::where('subCategory_id',$id)->get();
        return $products;
    }
}
