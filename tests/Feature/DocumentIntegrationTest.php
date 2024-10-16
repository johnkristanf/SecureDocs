<?php

namespace Tests\Feature;

use App\Http\Controllers\DocumentController;
use App\Models\Documents;
use App\Models\User;
use Aws\S3\S3Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class DocumentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_documents(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $mocks3Client = Mockery::mock(S3Client::class);
        $mocks3Client->shouldReceive('putObject')
        ->once()
        ->andReturn([
            'ObjectURL' => 'https://example-s3-url.com/document.pdf'
        ]);

        $this->app->instance(S3Client::class, $mocks3Client);
        
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/upload', [
            'file' => $file
        ]);


        $response->assertRedirect();
        $response->assertSessionHas('success', 'File uploaded successfully.');

        $this->assertDatabaseHas('documents', [
            'name' => 'document.pdf',
            'key' =>  'documents/1/document.pdf',
            'user_id' => $user->id,
        ]);



    }



    // NEED TO BUILD FACTORY FOR THE DOCUMENTS

    public function test_get_documents(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $mocks3Client = Mockery::mock(S3Client::class);
        $mocks3Client->shouldReceive('getCommand')
        ->with('GetObject', Mockery::on(function ($param){
            return isset($document['Bucket']) && isset($document['Key']);
        }))
        ->andReturnSelf();

        
        $mocks3Client->shouldReceive('createPresignedRequest')
            ->andReturn(Mockery::mock([
                'getUri' => 'https://example-presigned-url.com/document.pdf'
        ]));


        $this->app->instance(S3Client::class, $mocks3Client);


        Documents::factory()->create([
            'name' => 'document1.pdf',
            'key' => 'documents/' . $user->id . '/document1.pdf',
            'user_id' => $user->id,
            'isRecycle' => false,
        ]);

        Documents::factory()->create([
            'name' => 'document2.pdf',
            'key' => 'documents/' . $user->id . '/document2.pdf',
            'user_id' => $user->id,
            'isRecycle' => false,
        ]);


        $this->actingAs($user);

        $documentController = app(DocumentController::class);
        $documents = $documentController->getDocuments(false);

        $this->assertCount(2, $documents);


        $this->assertEquals('document1.pdf', $documents[0]['name']);
        $this->assertEquals('https://example-presigned-url.com/document.pdf', $documents[0]['url']);
        $this->assertEquals($user->id, $documents[0]['user_id']);

    }


    


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
