<?php

namespace App\Http\Controllers;

use App\Support\LicenseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LicenseController extends Controller
{
    public function show(LicenseManager $licenses): Response
    {
        return response()
            ->view('license.activation', [
                'licenseStatus' => $licenses->status(),
                'licensePath' => $licenses->licensePath(),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function store(Request $request, LicenseManager $licenses): RedirectResponse
    {
        $validated = $request->validate([
            'license_file' => ['nullable', 'file', 'max:512'],
            'license_text' => ['nullable', 'string'],
        ]);

        $licenseText = trim((string) ($validated['license_text'] ?? ''));
        if ($request->hasFile('license_file')) {
            $licenseText = (string) file_get_contents($request->file('license_file')->getRealPath());
        }

        if ($licenseText === '') {
            return back()->with('error', 'Vui lòng tải file license hoặc dán nội dung license.');
        }

        $validation = $licenses->validateLicenseText($licenseText);
        if (! $validation['valid']) {
            return back()->with('error', $validation['message']);
        }

        $document = json_decode($licenseText, true);
        if (! is_array($document)) {
            return back()->with('error', 'File bản quyền không đúng định dạng JSON.');
        }

        $licenses->install($document);

        return redirect()->route('license.show')->with('status', 'Đã kích hoạt bản quyền thành công.');
    }
}
