<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        try {

            $request->validate([
                    'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,jpg,png|max:2048',
                ],
                [
                    'file.mimes' => 'The uploaded file must be a PDF, Word document, Excel sheet, or an image.',
                    'file.max' => 'The uploaded file must not be larger than 2MB.',
                ]
            );

            if ($request->hasFile('file')) {
                $file = $request->file('file');
    
               
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'documents/' . $fileName;

                $uploadResult = Storage::disk('s3')->put($filePath, file_get_contents($file));

                Log::debug('FILE DETAILS: ', [
                    'file ni dol' => $file,
                    'fileName' => $fileName
                ]);
    
                Log::debug('UPLOAD RESULT: ', ['uploadResult' => $uploadResult]);
    
                Log::debug('AWS CREDENTIALS: ', [
                    'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID'),
                    'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY'),
                    'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
                    'AWS_BUCKET' => env('AWS_BUCKET'),
                    'AWS_USE_PATH_STYLE_ENDPOINT' => env('AWS_USE_PATH_STYLE_ENDPOINT'),
                ]);
    
                return back()->with('success', 'File uploaded successfully.');
            }

        } catch (\Exception $e) {
            Log::error(
                'Error uploading document in s3 bucket: '. $e->getMessage(), 
                ['trace' => $e->getTraceAsString()]
            );

            return back()->withErrors([
                'file' => 'There was a problem uploading your document, please try again'
            ])->onlyInput('file');
        }
        
    }
}
