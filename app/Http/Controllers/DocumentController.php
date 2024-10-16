<?php

namespace App\Http\Controllers;

use App\Models\Documents;
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
        $isRecycle = false;
        $documents = $this->getDocuments($isRecycle);
        $documentSizes = $this->getUploadedFileSizes();
        $picture = $this->getProfilePicture();
    
        return Inertia::render('ProjectFiles', [
            'documents' => $documents,
            'sizes'     => $documentSizes,
            'picture'   => $picture
        ]);
    }

    public function RenderRecycleBin()
    {
        $isRecycle = true;
        $picture = $this->getProfilePicture();
        $documents = $this->getDocuments($isRecycle);

        return Inertia::render('RecycleBin', [
            'picture'   => $picture,
            'documents' => $documents
        ]);
    }

    public function uploadDocuments(Request $request)
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
                $s3Key = 'documents/' . Auth::id() . '/' . $fileName;

                Log::debug('Attempting to upload file to S3');
                
                $result = $this->s3Client->putObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $s3Key,
                    'SourceFile' => $file->getRealPath(),
                    'ACL'    => 'private', 
                ]);

                $path = $result['ObjectURL'];
                $this->insertDocumentsDataInDB($s3Key);

                Log::debug('UPLOAD RESULT: ', [
                    'path' => $path,
                    'date' => date('M d, Y h:i A', time())
                ]);

                return back()->with('success', 'File uploaded successfully.');
            }

        } catch (\Exception $e) {
            Log::error(
                'Error uploading document in S3 bucket: ' . $e->getMessage(), 
            );

            return back()->withErrors([
                'file' => 'There was a problem uploading your document, please try again'
            ])->onlyInput('file');
        }
    }



    function insertDocumentsDataInDB(string $s3Key)
    {
        try {

            Documents::create([
                'name' => basename($s3Key),
                'key'  => $s3Key,
                'user_id' => Auth::id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error inserting document in database: ' . $e->getMessage());
        }
    }


    function getDocuments(bool $isRecycle)
    {
        try {

            $fetchedDocuments = Documents::where('user_id', Auth::id())
            ->where('isRecycle', $isRecycle) 
            ->get();

            $documents = [];

            foreach($fetchedDocuments as $document){
                $cmd = $this->s3Client->getCommand('GetObject', [
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $document['key']
                ]);

                $request = $this->s3Client->createPresignedRequest($cmd, '+60 minutes');
                $presignedURL = (string) $request->getUri();

                $documents[] = [
                    'id'            => $document['id'],
                    'name'          => $document['name'],
                    'user_id'       => $document['user_id'],
                    'url'           => $presignedURL,
                    'created_at'    => $document['created_at'],
                    'isRecycle'     => $document['isRecycle'],
                    'updated_at'    => $document['updated_at']
                ];
            }

            Log::debug("Fetch Documents From DB:", [
                'documents' => $fetchedDocuments
            ]);

            return $documents;

        } catch (\Exception $e) {
            Log::error('Error getting document in DB: ' . $e->getMessage());
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


    public function putToRecycleBin(string $document_id)
    {
        try {
            $document = Documents::find($document_id);

            Log::debug("Document Find: ", [
                'document' => $document
            ]);

            if(isset($document)){
                $document->update([
                    'isRecycle' => true
                ]);
            }

            return back()->with('success', 'Document is moved to recycle bin successfully');

        } catch (\Exception $e) {
            Log::error('Error in updating document to recyle bin: ' . $e->getMessage());
        }
    }

    public function restoreDocument(string $document_id)
    {
        try {

            $document = Documents::find($document_id);

            Log::debug("Document Find: ", [
                'document' => $document
            ]);

            if(isset($document)){
                $document->update([
                    'isRecycle' => false
                ]);
            }

            return back()->with('success', 'Document is restored from recycle bin successfully');

        } catch (\Exception $e) {
            Log::error('Error in restoring document from recyle bin: ' . $e->getMessage());
        }
    }


    public function deleteForeverDocuments(string $documentID, string $documentName)
    {
        try {

            $deleted = Documents::destroy($documentID);

            $s3Key = 'documents/' . Auth::id() . '/' . $documentName;
            Log::debug('Document s3Key: '. $s3Key);

            $result = $this->s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => $s3Key
            ]);

            if($result && $deleted){
                Log::info("File deleted successfully from S3", ['s3Key' => $s3Key]);
                Log::info("File deleted successfully from DB", ['deleted' => $deleted]);

                return back()->with('success', 'Document Forever Deleted!');
            }

        } catch (\Exception $e) {
            Log::error('Error deleting document sizes in S3 bucket: ' . $e->getMessage());
        }
    }

    
    function getProfilePicture()
    {
        try {
            $userPrefix = 'picture/' . Auth::id() . '/';
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => env('AWS_BUCKET'),
                'Prefix' => $userPrefix
            ]);

            $picture = [];
            if(isset($objects['Contents'])){

                foreach($objects['Contents'] as $object){
                    $key = $object['Key'];
                    
                    $cmd = $this->s3Client->getCommand('GetObject', [
                        'Bucket' => env('AWS_BUCKET'),
                        'Key'    => $key
                    ]);

                    $request = $this->s3Client->createPresignedRequest($cmd, '+60 minutes');
                    $presignedURL = (string) $request->getUri();

                    $picture[] = [
                        'url' => $presignedURL
                    ];
                }
            }

            Log::debug("Profile Picture: ", [
                'picture' => $picture
            ]);

            return $picture;

        } catch (\Exception $e) {
            Log::error(
                'Error getting picture in S3 bucket: ' . $e->getMessage(), 
            );
        }
    }
   
}
