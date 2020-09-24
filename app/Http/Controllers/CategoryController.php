<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
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

    public function index()
    {
        $categories = Category::all();
        return view('category',compact('categories'));
    }

    public function addCategory()
    {
        return view('category.add-category');
    }

    public function storeCategory(Request $request) {
        $category = new Category();
        $category->name = request('name');

        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/category'), $imageName);

        $category->image = $imageName;
        $category->save();
        return $category;
        //        return redirect('/category');
    }

    public function editCategory($id)
    {
        $category = Category::where('id', $id)->first();
        return view('category.edit-category',compact('category'));
    }

    public function updateCategory($id)
    {
        $name = request('name');
        if(request('image') != null){
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/category'), $imageName);
            return Category::where('id', $id)
            ->update(['name'  => $name,
                     'image'  => $imageName
                     ]);
        }
        else{
        return Category::where('id', $id)
                ->update(['name'  => $name]);
        }
//        return redirect('/category');
    }

    public function deleteCategory($id)
    {
        try {
            error_log('here');
            Category::where('id', $id)
                ->delete();
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Please delete all Sub Categories relevant to this category inorder to remove this category.',
            ],200);
            error_log('we here');
            error_log($e);
        }

    }

    public function apiIndex()
    {
        return Category::all();
    }

    public function apiIndexById($id)
    {
        return Category::where('id', $id)->first();
    }

    public function apiProducts($id)
    {
        $category = Category::where('id',$id)->first();
        $products = $category->products;
        return $products;
    }

    public function apiSubCategories($id)
    {
        $category = Category::where('id',$id)->first();
        $subCategories = $category->subCategories;
        return $subCategories;
    }
}
