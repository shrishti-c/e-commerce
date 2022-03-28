<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;



class HomeController extends Controller
{
    public function redirect()
    {
        $usertype = Auth::user()->usertype;

        if ($usertype == '1') {
            return view('admin.home');
        } else {

            $data = product::all();
            $user = auth()->user();
            $count = cart::where('address', $user->address)->count();
            return view('User.home', compact('data', 'count'));
        }
    }

    public function index()
    {

        if (Auth::id()) {
            return redirect('redirect');
        } else {
            $data = product::all();
            return view('User.home', compact('data'));
        }
    }

    public function search(Request $request)
    {

        $search = $request->search;

        $data = product::where('title', 'Like', '%' . $search . '%')->get();
        return view('user.home', compact('data'));
    }

    public function addcart(Request $request, $id)
    {
        if (Auth::id()) {

            $user = auth()->user();
            $product = product::find($id);
            $cart = new cart;
            $cart->name = $user->name;
            $cart->address = $user->address;

            $cart->product_title = $product->title;
            $cart->price = $product->price;
            $cart->quantity = $request->quantity;
            $cart->save();
            return redirect()->back()->with('message', 'Product added to cart');
        } else {
            return redirect('login');
        }
    }

    public function showcart()
    {
        $user = auth()->user();
        $cart = cart::where('address', $user->address)->get();
        $count = cart::where('address', $user->address)->count();
        return view('user.showcart',  compact('count', 'cart'));
    }

    public function deletecart($id)
    {
        $data = cart::find($id);
        $data->delete();
        return redirect()->back()->with('message', 'Product removed successfully');
    }

    public function confirmorder(Request $request)
    {
        $user = auth()->user();
        $name = $user->name;
        $phone = $user->phone;
        $address = $user->address;

        foreach ($request->productname as $key => $productname) {
            $order = new Order;

            $order->product_name = $request->productname[$key];
            $order->price = $request->productname[$key];
            $order->quantity = $request->productname[$key];
            $order->name = $name;
            $order->phone = $phone;
            $order->address = $address;
            $order->status = "Not delievered";
            $order->save();
        }
        DB::table('carts')->where('address', $address)->delete();
        return redirect()->back();
    }
}
