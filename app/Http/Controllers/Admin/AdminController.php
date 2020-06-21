<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class AdminController extends Controller
{
    public function brand()
    {
    	$data = Brand::paginate(50);

    	return view('brand',compact('data'));
    }

    public function category()
    {
    	$data = Category::paginate(50);

    	return view('category',compact('data'));
    }

    public function product()
    {
    	$data = Product::with(['brand','category'])->paginate(50);

    	return view('product',compact('data'));
    }
    
    public function createProduct()
    {
        $brands = Brand::all();
        $categories = Category::all();
        return view('createProduct',compact('brands','categories'));
    }

    public function storeBrand(Request $request)
    {
    	$brand = new Brand();
    	$brand->brand_name = $request->name;

    	if( $brand->save() ) 
    	{
    		return back()->with(['status'=>'success','message'=>'Brand Added Succefully.']);
    	}
    	else 
    	{
    		return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);	
    	}
    }

    public function storeCategory(Request $request)
    {
    	$category = new Category();
    	$category->category_name = $request->name;

    	if( $category->save() ) 
    	{
    		return back()->with(['status'=>'success','message'=>'Category Added Succefully.']);
    	}
    	else 
    	{
    		return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);	
    	}
    }

    public function storeProduct(Request $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->brand_id = $request->brand;
        $product->category_id = $request->category;
        $product->weight = $request->weight;
        $product->weight_type = $request->weight_type;
        
        if( $product->save() ) 
        {
            return back()->with(['status'=>'success','message'=>'Product Added Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']); 
        }
    }

    public function deleteBrand($id)
    {
    	$deleteBrand = Brand::where('id',$id)->firstOrFail();

    	if( $deleteBrand->delete() ) 
    	{
    		return back()->with(['status'=>'success','message'=>'Brand deleted Succefully.']);
    	}
    	else 
    	{
    		return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
    	}
    }

    public function deleteCategory($id)
    {
    	$deleteCategory = Category::where('id',$id)->firstOrFail();

    	if( $deleteCategory->delete() ) 
    	{
    		return back()->with(['status'=>'success','message'=>'Category deleted Succefully.']);
    	}
    	else 
    	{
    		return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
    	}
    }

    public function deleteProduct($id)
    {
    	$deleteProduct = Product::where('id',$id)->firstOrFail();

    	if( $deleteProduct->delete() ) 
    	{
    		return back()->with(['status'=>'success','message'=>'Product deleted Succefully.']);
    	}
    	else 
    	{
    		return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
    	}
    }

    public function editBrand($id)
    {
        $data = Brand::where('id',$id)->firstOrFail();

        return view('editBrand',compact('data'));
    }

    public function editCategory($id)
    {
        $data = Category::where('id',$id)->firstOrFail();

        return view('editCategory',compact('data'));
    }

    public function editProduct($id)
    {
        $data = Product::where('id',$id)->with(['brand','category'])->first();
        $brands = Brand::all();
        $categories = Category::all();
        return view('editProduct',compact('data','brands','categories'));
    }

    public function updateBrand($id,Request $request)
    {
        $data = Brand::where('id',$id)->first();

        $data->brand_name = $request->name;

        if( $data->save() ) 
        {
            return back()->with(['status'=>'success','message'=>'Brand Updated Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']); 
        }
    }

    public function updateCategory($id,Request $request)
    {
        $data = Category::where('id',$id)->first();

        $data->category_name = $request->name;

        if( $data->save() ) 
        {
            return back()->with(['status'=>'success','message'=>'Category Updated Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']); 
        }
    }

    public function updateProduct($id,Request $request)
    {
        $product = Product::where('id',$id)->first();
        $product->name = $request->name;
        $product->brand_id = $request->brand;
        $product->category_id = $request->category;
        $product->weight = $request->weight;
        $product->weight_type = $request->weight_type;
        
        if( $product->save() ) 
        {
            return back()->with(['status'=>'success','message'=>'Product Updated Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']); 
        }
    }
}






