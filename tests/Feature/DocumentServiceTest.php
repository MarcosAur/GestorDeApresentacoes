<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        Storage::fake('s3');
    }

    public function test_can_store_document_via_api(): void
    {
        $role = Role::where('slug', 'competidor')->first();
        $user = User::factory()->create(['role_id' => $role->id]);
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $this->actingAs($user);

        $response = $this->postJson('/api/documents', [
            'document_file' => $file,
            'document_type' => 'Termo de Responsabilidade'
        ]);

        $response->assertStatus(201);
        $document = UserDocument::first();
        
        $this->assertEquals($user->id, $document->user_id);
        $this->assertEquals('Termo de Responsabilidade', $document->document_type);
        
        Storage::disk('s3')->assertExists($document->file_path);
    }
}
