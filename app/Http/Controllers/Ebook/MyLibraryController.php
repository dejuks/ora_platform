<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * "My Digital Library" — every eBook this reader has purchased, with
 * a re-download link. Not gated by module.access:library (buying a
 * book doesn't require joining any module) — just being signed in.
 */
class MyLibraryController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->ebookOrders()
            ->completed()
            ->with('book')
            ->latest('paid_at')
            ->paginate(12);

        return view('modules.ebook.my-library.index', compact('orders'));
    }
}
