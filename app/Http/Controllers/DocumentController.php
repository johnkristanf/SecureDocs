<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DocumentController extends Controller
{
    private $s3Client;

    public function __construct()
    {

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'http' => [
                'verify' => false,  
            ],
        ]);
        
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                    'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,jpg,png|max:2048',
                ],
                [
                    'file.mimes' => 'The uploaded file must be a PDF, Word document, Excel sheet, or an image.',
                    'file.max' => 'The uploaded file must not be larger than 2MB.',
                ]
            );

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $filePath = 'documents/' . Auth::id() . '/' . $fileName;

                $result = $this->s3Client->putObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $filePath,
                    'SourceFile' => $file->getRealPath(),
                    'ACL'    => 'private', 
                ]);

                // Get the uploaded file URL
                $path = $result['ObjectURL'];

                Log::debug('UPLOAD RESULT: ', [
                    'path' => $path,
                    'date' => date('M d, Y h:i A', time())
                ]);

                return back()->with('success', 'File uploaded successfully.');
            }

        } catch (\Exception $e) {
            Log::error(
                'Error uploading document in S3 bucket: ' . $e->getMessage(), 
                ['trace' => $e->getTraceAsString()]
            );

            return back()->withErrors([
                'file' => 'There was a problem uploading your document, please try again'
            ])->onlyInput('file');
        }
    }


    public function getDocuments()
    {
        try {
            $userFolder = 'documents/' . Auth::id() . '/';  // Path to user-specific folder in S3
            $bucket = env('AWS_BUCKET');

            // List all files in the user's folder
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => $userFolder,
            ]);

            $files = [];
            if (isset($objects['Contents'])) {
                foreach ($objects['Contents'] as $object) {
                    $key = $object['Key'];

                    // Generate a pre-signed URL for each file
                    $cmd = $this->s3Client->getCommand('GetObject', [
                        'Bucket' => $bucket,
                        'Key'    => $key
                    ]);

                    $request = $this->s3Client->createPresignedRequest($cmd, '+60 minutes');
                    $presignedURL = (string) $request->getUri();

                    $files[] = [
                        'name' => basename($key),  
                        'url' => $presignedURL,
                    ];
                }
            }

            Log::debug("FILES", [
                'files' => $files
            ]);

            return Inertia::render('ProjectFiles', [
                'files' => $files,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting document in S3 bucket: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Unable to retrieve documents'], 500);
        }
    }
}
