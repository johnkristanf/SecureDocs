<?php

namespace App\Services;

use App\Models\Documents;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class DeleteOldDocuments 
{
    public function __invoke()
    {

        $s3Client = new S3Client([
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

        Log::debug("THE SCHEDULE HITS");


        // Define the threshold date to delete documents inside bin for the last 30 days
        $thresholdDate = Carbon::now()->subDays(30);

        // Fetch Documents After the Threshold Date
        $documents = Documents::where('isRecycle', true)
        ->where('created_at', '<', $thresholdDate)
        ->get();

        Log::debug("Documents from scheduler", [
            'documents' => $documents
        ]);


        foreach ($documents as $document) {

            $s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key'    => $document['key'],
            ]);

            Log::info("Deleted file from S3: ". $document['url']);

            $document->delete();
            Log::info("Deleted Document" . $document['name']);
        }
        
    }

    protected function getS3KeyFromUrl($url)
    {
        $parsedUrl = parse_url($url);
        return ltrim($parsedUrl['path'], '/'); 
    }
}