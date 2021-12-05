<?php

namespace App\Http\Controllers\Home;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Products;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categorys = Category::with(['childrenRecursive'])->where('parent_id', 0)->get();
        $idCategory = $request->input('id') != '' ? $request->input('id') : $categorys[0]['id'];

        $products = Products::select(['products.*', 'categorys.name as categorys_name'])
            ->join('categorys', 'categorys.id', '=', 'products.category_id')
            ->where(function ($query) use ($idCategory) {
                $query->where('categorys.id', $idCategory)
                    ->orWhere('categorys.parent_id', $idCategory);
            })->with('images');
        $products = $products->orderBy('products.id','desc')->simplePaginate(16);
        if ($request->ajax()) {
            return view('home.includes.product-content', compact('categorys', 'products'));
        }
        return view('index', ['categorys' => $categorys, 'products' => $products]);
    }
}
