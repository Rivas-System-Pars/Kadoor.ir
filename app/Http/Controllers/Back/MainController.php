<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $users_count    = User::where('level', '!=', 'creator')->count();
        $products_count = Product::count();
        $orders_count   = Order::count();
        $total_sell     = Order::where('status', 'paid')->sum('price');

        return view('back.index', compact(
            'users_count',
            'products_count',
            'orders_count',
            'total_sell'
        ));
    }

    public function get_tags(Request $request)
    {
        $tags = Tag::where('name', 'like', '%' . $request->term . '%')
            ->latest()
            ->take(5)
            ->pluck('name')
            ->toArray();

        return response()->json($tags);
    }

    public function getLabels(Request $request)
    {
        $labels = Label::where('title', 'like', '%' . $request->term . '%')
            ->latest()
            ->take(5)
            ->pluck('title')
            ->toArray();

        return response()->json($labels);
    }

    public function login()
    {
        return view('back.auth.login');
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(15);

        auth()->user()->unreadNotifications->markAsRead();

        return view('back.notifications', compact('notifications'));
    }

    public function fileManager()
    {
        $this->authorize('file-manager');

        return view('back.file-manager');
    }

    public function fileManagerIframe()
    {
        $this->authorize('file-manager');

        return view('back.file-manager-iframe');
    }
}
