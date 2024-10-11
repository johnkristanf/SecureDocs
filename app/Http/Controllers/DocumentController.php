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


    public function RenderProjectFiles()
    {
        $documents = $this->getDocuments();
        $documentSizes = $this->getUploadedFileSizes();
        

        // Log::debug("DOCUMENTS SIZES", [
        //     'sizes' => $documentSizes
        // ]);

        return Inertia::render('ProjectFiles', [
            'documents' => $documents,
            'sizes'     => $documentSizes
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


    function getDocuments()
    {
        try {
            $userFolder = 'documents/' . Auth::id() . '/';  // Path to user-specific folder in S3
            $bucketName = env('AWS_BUCKET');

            // List all files in the user's folder
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $bucketName,
                'Prefix' => $userFolder,
            ]);

            $documents = [];
            if (isset($objects['Contents'])) {

                foreach ($objects['Contents'] as $object) {

                    $key = $object['Key'];
                    $lastModified = $object['LastModified']->format('M d, Y');

                    $cmd = $this->s3Client->getCommand('GetObject', [
                        'Bucket' => $bucketName,
                        'Key'    => $key
                    ]);

                    $request = $this->s3Client->createPresignedRequest($cmd, '+60 minutes');
                    $presignedURL = (string) $request->getUri();

                    $documents[] = [
                        'name' => basename($key),  
                        'url' => $presignedURL,
                        'uploaded_date' => $lastModified
                    ];
                }
            }

           return $documents;

        } catch (\Exception $e) {
            Log::error('Error getting document in S3 bucket: ' . $e->getMessage());
        }
    }


    function getUploadedFileSizes()
    {
        try {
            $bucketName = env('AWS_BUCKET');
            $fileGroups = [
                'Documents' => ['docx'],
                'Spreadsheets' => ['xlsx', 'csv'],
                'Images' => ['jpg', 'png'],
                'PDFs' => ['pdf'],
            ];
    

            $results = $this->s3Client->listObjectsV2([
                'Bucket' => $bucketName
            ]);

            $totalSizes = [
                'Documents' => 0,
                'Spreadsheets' => 0,
                'Images' => 0,
                'PDFs' => 0,
            ];

            // Log::debug('S3 List Objects Response', [
            //     'Bucket' => $bucketName,
            //     'Contents' => isset($results['Contents']) ? $results['Contents'] : 'No Contents',
            // ]);


            if(isset($results['Contents'])){
                foreach ($results['Contents'] as $document) {
                    $documentKey = $document['Key'];
                    $documentSize = $document['Size']; // Size in bytes
                    
                    $documentExtension = pathinfo($documentKey, PATHINFO_EXTENSION);

                    foreach($fileGroups as $group => $extensions){
                        if(in_array($documentExtension, $extensions)){
                            // Log::debug("FILE MATCHED", [
                            //     'Group' => $group,
                            //     'File' => $documentKey,
                            //     'Size' => $documentSize,
                            //     'documentExtension' => $documentExtension,
                            // ]);

                            $totalSizes[$group] += $documentSize;
                        }
                    }
                }
            }


            $totalSizesMB = array_map(function ($size) {
                return round($size / 1048576, 2); // Convert bytes to MB
            }, $totalSizes);

            return $totalSizesMB;


        } catch (\Exception $e) {
            Log::error('Error getting document sizes in S3 bucket: ' . $e->getMessage());
        }
    }


    public function deleteDocuments(string $documentName)
    {
        try {

            $s3Key = 'documents/' . Auth::id() . '/' . $documentName;
            Log::debug('Document s3Key: '. $s3Key);

            $result = $this->s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => $s3Key
            ]);

            if($result){
                Log::info("File deleted successfully from S3", ['s3Key' => $s3Key]);
                return back()->with('success', 'File uploaded successfully.');
            }

        } catch (\Exception $e) {
            Log::error('Error deleting document sizes in S3 bucket: ' . $e->getMessage());
        }
    }
   
}
