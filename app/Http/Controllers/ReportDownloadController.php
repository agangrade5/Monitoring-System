<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportDownloadController extends Controller
{
    /**
     * Download generated report file securely using signed URL.
     *
     * @param Request $request
     * @param string $file
     * @return BinaryFileResponse
     */
    public function download(Request $request, string $file): BinaryFileResponse
    {
        // Sanitize filename to prevent directory traversal
        $file = basename($file);
        $filePath = storage_path("app/reports/{$file}");

        if (!File::exists($filePath)) {
            abort(404, 'Report file not found or has expired.');
        }

        return response()->download($filePath, $file, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$file}\"",
        ]);
    }
}
