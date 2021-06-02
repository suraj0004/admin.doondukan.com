<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\BrandImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\TempProduct;
use Validator;
use Image;

class AdminController extends Controller
{
    public function brand()
    {
    	$data = Brand::latest()->paginate(50);

    	return view('brand',compact('data'));
    }

    public function category()
    {
    	$data = Category::latest()->paginate(50);

    	return view('category',compact('data'));
    }

    public function product()
    {
    	$data = Product::with(['brand','category'])->latest()->paginate(50);

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
        $brand->country = $request->country;

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
        $category->slug = Str::slug($request->name);

        if($request->hasFile('image')){
            $category->image = saveFile(config("constants.disks.CATEGORY"), $category->slug, $request->file('image'), true);
        }

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
        $product->slug = Str::slug($request->name);
        $product->brand_id = $request->brand;
        $product->category_id = $request->category;
        $product->price = $request->price;
        $product->weight = $request->weight;
        $product->weight_type = $request->weight_type;

        if($request->hasFile('image')){
            $product->image = saveFile(config("constants.disks.PRODUCT"), $product->slug, $request->file('image'), true);
        }
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
        $data = Product::where('id',$id)->with(['brand','category'])->firstOrFail();
        $brands = Brand::all();
        $categories = Category::all();
        return view('editProduct',compact('data','brands','categories'));
    }

    public function updateBrand($id,Request $request)
    {
        $data = Brand::where('id',$id)->first();
        $data->brand_name = $request->name;
        $data->country = $request->country;

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
        $data->slug = Str::slug($request->name);
        if($request->hasFile('image')){
            $image = $data->image;
            $data->image = saveFile(config("constants.disks.CATEGORY"), $data->slug, $request->file('image'), true,$image);
        }
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
        $product->slug = Str::slug($request->name);
        $product->brand_id = $request->brand;
        $product->category_id = $request->category;
        $product->price = $request->price;
        $product->weight = $request->weight;
        $product->weight_type = $request->weight_type;
        
        
        if($request->hasFile('image')){
            $image = $request->image;
            $product->image = saveFile(config("constants.disks.PRODUCT"), $product->slug, $request->file('image'), false,$image);
        }

        
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
        $data = User::with('store')->latest()->paginate(50);

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
            if($request->hasFile('image') )
            {
                $user = User::where('id',$user->id)->first();
                //Save full size image
                $image = $request->file('image');
                $profile_img = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/profileimages/'.$user->id."/");
                $image->move($destinationPath, $profile_img);

                //Thumbnail
                $image_resize = Image::make(public_path().'/profileimages/'.$user->id."/".$profile_img);
                $image_resize->fit(300, 300);
                $image_resize->save(public_path('profileimages/'.$user->id.'/thumb_'.$profile_img));
                $user->image = $profile_img;
                $user->save();
            }
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
            'email' => 'required|email|unique:users,email,'.$id,
            'store_email'=>'nullable|unique:stores,email,'.$id.',user_id'
        ]);

        $user =  User::where('id',$id)->first();
        if(!$user)
        {
            return back()->with(['status'=>'danger','message'=>'User not found.']);
        }

        if($request->hasFile('image') )
        {
            //Save full size image
            $image = $request->file('image');
            $profile_img = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/profileimages/'.$user->id."/");
            $image->move($destinationPath, $profile_img);

            //Thumbnail
            $image_resize = Image::make(public_path().'/profileimages/'.$user->id."/".$profile_img);
            $image_resize->fit(300, 300);
            $image_resize->save(public_path('profileimages/'.$user->id.'/thumb_'.$profile_img));
        }
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email= $request->email;
        $user->image = $profile_img ?? $user->image;
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

    public function TempProduct()
    {
        $data = TempProduct::with('user')->latest()->paginate(50);

        return view('tempProduct',compact('data'));
    }

    public function deleteTempProduct($id)
    {
        $TempProduct = TempProduct::where('id',$id)->firstOrFail();

        if( $TempProduct->delete() )
        {
            return back()->with(['status'=>'success','message'=>'Temp Product deleted Succefully.']);
        }
        else
        {
            return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
        }
    }

    public function addTempProductToProduct($id) {
        $data = TempProduct::with(['brand','category'])->where('id',$id)->first();
        $brands = Brand::all();
        $categories = Category::all();
        return view('addTempProductToProduct',compact('data','brands','categories'));
    }

    public function searchProduct(Request $request)
    {
        $data = Product::select('id','name')->where("name","LIKE",$request->name."%")->get();
        if(count($data) >= 1 ) {
            return response()->json(['statusCode'=>200,'success'=>true,'data'=>$data], 200);
        } else {
            return response()->json(['statusCode'=>200,'success'=>false,], 200);
        }
    }

    public function storeTempProductToMainProduct($id,Request $request)
    {
        $checkTemp = TempProduct::where('id',$id)->first();
        if($checkTemp) {
            if(!empty($request->tempMainProductId)) {
                $product = Product::where('id',$request->tempMainProductId)->first();
                if($product) {
                    $setProductId = Purchase::where('product_id',$id)->update(
                        ['product_id'=>$product->id,'product_source'=>'main']
                    );
                    $setProductId = Sale::where('product_id',$id)->update(
                        ['product_id'=>$product->id,'product_source'=>'main']
                    );
                    $setProductId = Stock::where('product_id',$id)->update(
                        ['product_id'=>$product->id,'product_source'=>'main']
                    );
                    $checkTemp->delete();
                    return redirect()->route('TempProduct')->with(['status'=>'success','message'=>'Product Added Succefully.']);
                } else {
                    return back()->with(['status'=>'danger','message'=>'Product Not Found.']);
                }
            } else {
                $product = new Product();
                $product->name = $request->name;
                $product->brand_id = $request->brand;
                $product->category_id = $request->category;
                $product->weight = $request->weight;
                $product->weight_type = $request->weight_type;

                if( $product->save() ) {
                    $setProductId = Purchase::where('product_id',$id)->update(
                        ['product_id'=>$product->id,'product_source'=>'main']
                    );
                    $setProductId = Sale::where('product_id',$id)->update(
                        ['product_id'=>$product->id,'product_source'=>'main']
                    );
                    $setProductId = Stock::where('product_id',$id)->update(
                        ['product_id'=>$product->id,'product_source'=>'main']
                    );
                    $checkTemp->delete();
                    return redirect()->route('TempProduct')->with(['status'=>'success','message'=>'Product Added Succefully.']);
                } else {
                    return back()->with(['status'=>'danger','message'=>'Oops! Something went wrong.']);
                }
            }
        } else {
            return back()->with(['status'=>'danger','message'=>'Temp Product Not Found.']);
        }
    }
}






