<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_page_is_accessible()
    {
        $response = $this->get('/verification');
        $response->assertStatus(200);
    }

    public function test_can_verify_valid_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'qr_code' => 'CNI-2024-001234',
            'is_valid' => true,
        ]);

        $response = $this->post('/verification/verifier', [
            'document_code' => 'CNI-2024-001234',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Document Valide');
        $response->assertSee($document->holder_name);
    }

    public function test_cannot_verify_invalid_document()
    {
        $response = $this->post('/verification/verifier', [
            'document_code' => 'INVALID-CODE',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Document Invalide');
    }

    public function test_cannot_verify_expired_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'qr_code' => 'CNI-2024-001235',
            'is_valid' => true,
            'expiry_date' => now()->subDays(1),
        ]);

        $response = $this->post('/verification/verifier', [
            'document_code' => 'CNI-2024-001235',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Document Invalide');
    }

    public function test_cannot_verify_revoked_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'qr_code' => 'CNI-2024-001236',
            'is_valid' => false,
            'revoked_at' => now(),
        ]);

        $response = $this->post('/verification/verifier', [
            'document_code' => 'CNI-2024-001236',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Document Invalide');
    }

    public function test_verification_requires_document_code()
    {
        $response = $this->post('/verification/verifier', []);

        $response->assertSessionHasErrors('document_code');
    }

    public function test_verification_code_must_be_string()
    {
        $response = $this->post('/verification/verifier', [
            'document_code' => 123456,
        ]);

        $response->assertSessionHasErrors('document_code');
    }

    public function test_verification_code_has_maximum_length()
    {
        $response = $this->post('/verification/verifier', [
            'document_code' => str_repeat('A', 51),
        ]);

        $response->assertSessionHasErrors('document_code');
    }

    public function test_can_verify_different_document_types()
    {
        $user = User::factory()->create();
        
        $cni = Document::factory()->create([
            'document_type' => 'cni',
            'qr_code' => 'CNI-2024-001234',
            'is_valid' => true,
        ]);

        $passport = Document::factory()->create([
            'document_type' => 'passeport',
            'qr_code' => 'PAS-2024-000567',
            'is_valid' => true,
        ]);

        $permit = Document::factory()->create([
            'document_type' => 'permis',
            'qr_code' => 'PER-2024-000890',
            'is_valid' => true,
        ]);

        // Test CNI verification
        $response = $this->post('/verification/verifier', [
            'document_code' => 'CNI-2024-001234',
        ]);
        $response->assertSee('Carte Nationale d\'Identité');

        // Test Passport verification
        $response = $this->post('/verification/verifier', [
            'document_code' => 'PAS-2024-000567',
        ]);
        $response->assertSee('Passeport');

        // Test Permit verification
        $response = $this->post('/verification/verifier', [
            'document_code' => 'PER-2024-000890',
        ]);
        $response->assertSee('Permis de conduire');
    }
}
