<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhoneResource;
use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PhoneApiController extends Controller
{
    public function index()
    {
        $phones = Phone::with('category')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => PhoneResource::collection($phones),
            'message' => 'Phones retrieved successfully',
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $phones = Phone::where('name', 'LIKE', "%$search%")
            ->where('qty', '>', 0)
            ->limit(10)
            ->get();

        return response()->json($phones);
    }

    public function show($id)
    {
        $phone = Phone::with('category')->find($id);

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new PhoneResource($phone),
            'message' => 'Phone retrieved successfully',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('phones', 'public');
        }

        $phone = Phone::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'qty' => $validated['qty'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => new PhoneResource($phone),
            'message' => 'Phone created successfully',
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $phone = Phone::find($id);

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($phone->image) {
                \Storage::disk('public')->delete($phone->image);
            }

            $phone->image = $request->file('image')->store('phones', 'public');
        }

        $phone->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'qty' => $validated['qty'],
            'description' => $validated['description'] ?? $phone->description,
        ]);

        return response()->json([
            'success' => true,
            'data' => new PhoneResource($phone),
            'message' => 'Phone updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $phone = Phone::find($id);

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($phone->image) {
            \Storage::disk('public')->delete($phone->image);
        }

        $phone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Phone deleted successfully',
        ]);
    }

    public function categories()
    {
        $categories = \App\Models\Category::withCount('phones')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully',
        ]);
    }
}
