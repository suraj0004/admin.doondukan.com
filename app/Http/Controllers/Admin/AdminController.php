<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Imports\BrandImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use Validator;
use Image;

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

    public function user()
    {
        $data = User::with('store')->paginate(50);

        return view('user',compact('data'));
    }

    public function deleteUser($id)
    {
        $deleteUser = User::where('id',$id)->firstOrFail();

        if( $deleteUser->delete() ) 
        {
            return back()->with(['status'=>'success','message'=>'User deleted Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
        }
    }

    public function storeUser(Request $request)
    {
        $this->validate($request, [ 
            'email' => 'required|email|unique:users',
            'store_email'=>'nullable|unique:stores,email'
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email= $request->email;
        $user->password = bcrypt($request->password);
        if( $user->save() ) 
        {
            if( !empty($request->store_name) || !empty($request->store_email) || !empty($request->store_number) || !empty($request->registration_date) || !empty($request->store_about) || !empty($request->store_address) ) 
            {
                if($request->hasFile('logo') ) 
                {
                    //Save full size image 
                    $image = $request->file('logo');
                    $name = time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/shopimages/'.$user->id."/");
                    $image->move($destinationPath, $name);
                    
                    //Thumbnail
                    $image_resize = Image::make(public_path().'/shopimages/'.$user->id."/".$name);
                    $image_resize->fit(300, 300);
                    $image_resize->save(public_path('shopimages/' .$user->id.'/thumb_'.$name));
                }
                $store = new Store();
                $store->user_id = $user->id;
                $store->name = $request->store_name;
                $store->mobile = $request->store_number;
                $store->email = $request->store_email;
                $store->logo = $name ?? null;
                $store->address = $request->store_address;
                $store->about = $request->store_about;
                $store->registration_date = $request->registration_date;
                $store->valid_upto = $request->valid_upto_date;
                $store->save();
            }
            return back()->with(['status'=>'success','message'=>'User Created Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
        }
    }

    public function updateUser($id,Request $request)
    {
        $this->validate($request, [ 
            'email' => 'required|email|unique:users,email,'.$id
        ]);

        $user =  User::where('id',$id)->first();
        if(!$user) 
        {
            return back()->with(['status'=>'danger','message'=>'User not found.']);
        }
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email= $request->email;
        if(!empty($request->password) && $request->password != "**********" ) 
        {
            $user->password = bcrypt($request->password);
        }
     
        if( $user->save() ) 
        {
            if( !empty($request->store_name) || !empty($request->store_email) || !empty($request->store_number) || !empty($request->registration_date) || !empty($request->store_about) || !empty($request->store_address) ) 
            {   

                if($request->hasFile('logo') ) 
                {
                    //Save full size image 
                    $image = $request->file('logo');
                    $name = time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/shopimages/'.$user->id."/");
                    $image->move($destinationPath, $name);
                    
                    //Thumbnail
                    $image_resize = Image::make(public_path().'/shopimages/'.$user->id."/".$name);
                    $image_resize->fit(300, 300);
                    $image_resize->save(public_path('shopimages/' .$user->id.'/thumb_'.$name));
                }
                
                $store = Store::where('user_id',$user->id)->first();
                if(!$store) 
                {
                    $store = new Store();
                }
                $store->user_id = $user->id;
                $store->name = $request->store_name;
                $store->mobile = $request->store_number;
                $store->email = $request->store_email;
                $store->logo = $name ?? null;
                $store->address = $request->store_address;
                $store->about = $request->store_about;
                $store->registration_date = $request->registration_date;
                $store->valid_upto = $request->valid_upto_date;
                $store->save();
            }
            return back()->with(['status'=>'success','message'=>'User updated Succefully.']);
        }
        else 
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
        }
    }

    public function editUser($id)
    {
        $data = User::with('store')->where('id',$id)->firstOrFail();
        return view('editUser',compact('data'));
    }

    public function importBrands(Request $request)
    {
        Excel::import(new BrandImport,request()->file('excelfile'));
        return back()->with(['status'=>'success','message'=>'Brands Imported Succefully.']);
    }
}






