<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function storeEcomData(Request $request)
    {
        exit();
        ini_set ('max_execution_time', 72000000000);
       $data = $request->data;
       foreach ($data as $key => $value) {
            $catSlug = Str::slug($value['name']);
            $Category = Category::where('slug',$catSlug)->first();
            if(!$Category) {
                $Category = new Category();
                $Category->category_name = $value['name'];
                $Category->slug = $catSlug;
                $Category->save();
            }
            
          foreach ($value['products'] as $Valuekey => $valueData) {
            if($valueData['id'] <= 1892) {
                continue;
            }
              sleep(2);
              $product = new Product();
              $name_weight_type = explode(" ",$valueData['name']);
              $weight = array_pop($name_weight_type);
              $product->name = implode(" ",$name_weight_type);
              $product->slug = Str::slug($product->name);
              $filename = 'temp-image.jpg';
              $tempImage = tempnam(sys_get_temp_dir(), $filename);
              copy($valueData['image'], $tempImage);
               try {
                    $product->image = saveFile(config("constants.disks.PRODUCT"), $product->slug, $tempImage, true);
                } catch (Exception $e) {
                    $product->image = NULL;
                }
              $product->brand_id = NULL;
              $product->category_id = $Category->id;
              $product->price = $valueData['original_price'];
              $product->weight = $weight;
              $product->weight_type = strtolower($valueData['unit']);
              $product->save();
              //die;
          }
       }
    }
}
