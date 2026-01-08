<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
 

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::all();

        $response = $banners->map(function ($banner) {
            return [
                'img' => asset('storage/' . $banner->file_path),
                'link' => $banner->link
            ];
        });

        return response()->json([
            'error' => null,
            'banners' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
