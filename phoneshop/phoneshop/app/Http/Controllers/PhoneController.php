<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Phone;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index()
    {
        // Check permission
        if (!auth()->user()->hasPermission('view_phones')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view phones');
        }

        $phones = Phone::with('category')->latest()->get();

        return view('phones.index', compact('phones'));
    }

    public function create()
    {
        // Check permission
        if (!auth()->user()->hasPermission('create_phones')) {
            return redirect()->route('phones.index')->with('error', 'You do not have permission to create phones');
        }

        $categories = Category::all();

        return view('phones.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('create_phones')) {
            return redirect()->route('phones.index')->with('error', 'You do not have permission to create phones');
        }

        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'qty' => 'required|integer',
            'image' => 'nullable|image',
            'detail_images.*' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('phones', 'public');
            
            \Log::info('Image uploaded', [
                'path' => $imagePath,
                'full_path' => storage_path('app/public/' . $imagePath),
                'exists' => file_exists(storage_path('app/public/' . $imagePath))
            ]);
        }

        // Handle detail images
        $detailImages = [];
        if ($request->hasFile('detail_images')) {
            foreach ($request->file('detail_images') as $image) {
                $detailImages[] = $image->store('phones/details', 'public');
            }
        }

        $phone = Phone::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'qty' => $request->qty,
            'image' => $imagePath,
            'detail_images' => $detailImages,
            'description' => $request->description,
        ]);

        \Log::info('Phone created', [
            'id' => $phone->id,
            'name' => $phone->name,
            'image' => $phone->image
        ]);

        return redirect()->route('phones.index')
            ->with('success', 'Phone created successfully');
    }

    public function edit(Phone $phone)
    {
        // Check permission
        if (!auth()->user()->hasPermission('edit_phones')) {
            return redirect()->route('phones.index')->with('error', 'You do not have permission to edit phones');
        }

        $categories = Category::all();

        return view('phones.edit', compact('phone', 'categories'));
    }

    public function update(Request $request, Phone $phone)
    {
        // Check permission
        if (!auth()->user()->hasPermission('edit_phones')) {
            return redirect()->route('phones.index')->with('error', 'You do not have permission to edit phones');
        }

        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'qty' => 'required|integer',
            'image' => 'nullable|image',
            'detail_images.*' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'qty' => $request->qty,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($phone->image) {
                \Storage::disk('public')->delete($phone->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('phones', 'public');
            $data['image'] = $imagePath;
            
            \Log::info('Image updated', [
                'phone_id' => $phone->id,
                'old_image' => $phone->image,
                'new_image' => $imagePath,
                'full_path' => storage_path('app/public/' . $imagePath),
                'exists' => file_exists(storage_path('app/public/' . $imagePath))
            ]);
        }

        // Handle detail images
        $detailImages = $phone->detail_images ?? [];
        
        // Remove deleted images
        if ($request->has('remove_detail_images')) {
            $removeImages = $request->input('remove_detail_images');
            foreach ($removeImages as $imageToRemove) {
                \Storage::disk('public')->delete($imageToRemove);
                $detailImages = array_values(array_filter($detailImages, fn($img) => $img !== $imageToRemove));
            }
        }

        // Add new detail images
        if ($request->hasFile('detail_images')) {
            foreach ($request->file('detail_images') as $image) {
                $detailImages[] = $image->store('phones/details', 'public');
            }
        }

        $data['detail_images'] = $detailImages;

        $phone->update($data);

        \Log::info('Phone updated', [
            'id' => $phone->id,
            'name' => $phone->name,
            'image' => $phone->image
        ]);

        return redirect()->route('phones.index')
            ->with('success', 'Phone updated successfully');
    }

    public function destroy(Phone $phone)
    {
        // Check permission
        if (!auth()->user()->hasPermission('delete_phones')) {
            return back()->with('error', 'You do not have permission to delete phones');
        }

        if ($phone->image) {
            \Storage::disk('public')->delete($phone->image);
        }

        // Delete detail images
        if ($phone->detail_images) {
            foreach ($phone->detail_images as $detailImage) {
                \Storage::disk('public')->delete($detailImage);
            }
        }

        $phone->delete();

        return back()->with('success', 'Phone deleted');
    }

    // Debug upload test
    public function debugUpload()
    {
        return view('phones.debug-upload');
    }

    public function debugUploadTest(Request $request)
    {
        $debug = [
            'has_file' => $request->hasFile('test_image'),
            'file_info' => null,
            'validation' => 'pending',
            'storage_result' => null,
        ];

        if ($request->hasFile('test_image')) {
            $file = $request->file('test_image');
            $debug['file_info'] = [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
            ];

            try {
                $request->validate([
                    'test_image' => 'required|image|max:10240',
                ]);
                $debug['validation'] = 'passed';

                $path = $file->store('phones/test', 'public');
                $debug['storage_result'] = [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'exists' => file_exists(storage_path('app/public/' . $path)),
                    'public_url' => asset('storage/' . $path),
                ];

                return redirect()->route('phones.debug-upload')
                    ->with('success', 'Upload successful! Image saved to: ' . $path)
                    ->with('debug', $debug);

            } catch (\Exception $e) {
                $debug['validation'] = 'failed';
                $debug['error'] = $e->getMessage();
                return redirect()->route('phones.debug-upload')
                    ->with('debug', $debug)
                    ->withErrors(['test_image' => $e->getMessage()]);
            }
        }

        return redirect()->route('phones.debug-upload')
            ->with('debug', $debug)
            ->withErrors(['test_image' => 'No file uploaded']);
    }
}
