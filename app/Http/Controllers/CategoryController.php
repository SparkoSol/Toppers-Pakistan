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
        return redirect('/category');
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
            Category::where('id', $id)
            ->update(['name'  => $name,
                     'image'  => $imageName
                     ]);
        }
        else{
        Category::where('id', $id)
                ->update(['name'  => $name]);
        }
        
        return redirect('/category');
    }

    public function deleteCategory($id)
    {
        Category::where('id', $id)
                ->delete();
        
        return redirect('/category');
    }
}