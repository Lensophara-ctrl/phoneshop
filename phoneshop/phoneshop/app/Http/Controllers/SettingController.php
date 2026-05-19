<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'about_cv_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'store_icon' => 'nullable|string|max:100',
        ]);

        $skipKeys = ['_token', 'store_logo', 'about_cv_photo', 'remove_logo', 'footer_quick_links', 'footer_support_links', 'footer_section_titles_quick_links', 'footer_section_titles_support', 'footer_section_titles_follow_us'];

        foreach ($request->except($skipKeys) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        // Handle Quick Links (array -> JSON)
        if ($request->has('footer_quick_links')) {
            $links = array_values($request->input('footer_quick_links'));
            Setting::updateOrCreate(['key' => 'footer_quick_links'], ['value' => json_encode($links)]);
        }

        // Handle Support Links (array -> JSON)
        if ($request->has('footer_support_links')) {
            $links = array_values($request->input('footer_support_links'));
            Setting::updateOrCreate(['key' => 'footer_support_links'], ['value' => json_encode($links)]);
        }

        // Handle Section Titles
        $sectionTitles = [];
        if ($request->filled('footer_section_titles_quick_links')) {
            $sectionTitles['quick_links'] = $request->input('footer_section_titles_quick_links');
        }
        if ($request->filled('footer_section_titles_support')) {
            $sectionTitles['support'] = $request->input('footer_section_titles_support');
        }
        if ($request->filled('footer_section_titles_follow_us')) {
            $sectionTitles['follow_us'] = $request->input('footer_section_titles_follow_us');
        }
        if (!empty($sectionTitles)) {
            Setting::updateOrCreate(['key' => 'footer_section_titles'], ['value' => json_encode($sectionTitles)]);
        }

        // Handle logo removal
        if ($request->input('remove_logo') == '1') {
            $existingLogo = Setting::where('key', 'store_logo')->first();
            if ($existingLogo && $existingLogo->value) {
                // Delete old logo file
                if (\Storage::disk('public')->exists($existingLogo->value)) {
                    \Storage::disk('public')->delete($existingLogo->value);
                }
                // Remove from database
                $existingLogo->delete();
            }
        }

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            $existingLogo = Setting::where('key', 'store_logo')->first();
            if ($existingLogo && $existingLogo->value) {
                if (\Storage::disk('public')->exists($existingLogo->value)) {
                    \Storage::disk('public')->delete($existingLogo->value);
                }
            }
            
            // Store new logo
            $logoPath = $request->file('store_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $logoPath]);
        }

        // Handle CV photo upload
        if ($request->hasFile('about_cv_photo')) {
            $cvPhotoPath = $request->file('about_cv_photo')->store('cv', 'public');
            Setting::updateOrCreate(['key' => 'about_cv_photo'], ['value' => $cvPhotoPath]);
        }

        return back()->with('success', 'Settings updated successfully');
    }
}
