<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\User;
use App\Models\DocumentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_can_be_created()
    {
        $user = User::factory()->create();
        $request = DocumentRequest::factory()->create(['user_id' => $user->id]);
        $document = Document::factory()->create([
            'user_id' => $user->id,
            'request_id' => $request->id,
        ]);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertEquals($user->id, $document->user_id);
        $this->assertEquals($request->id, $document->request_id);
    }

    public function test_document_has_default_valid_status()
    {
        $document = Document::factory()->create();
        $this->assertTrue($document->is_valid);
    }

    public function test_document_type_label_accessor()
    {
        $cniDocument = Document::factory()->create(['document_type' => 'cni']);
        $passportDocument = Document::factory()->create(['document_type' => 'passeport']);
        $permitDocument = Document::factory()->create(['document_type' => 'permis']);

        $this->assertEquals('Carte Nationale d\'Identité', $cniDocument->document_type_label);
        $this->assertEquals('Passeport', $passportDocument->document_type_label);
        $this->assertEquals('Permis de conduire', $permitDocument->document_type_label);
    }

    public function test_status_label_accessor()
    {
        $validDocument = Document::factory()->create(['is_valid' => true]);
        $revokedDocument = Document::factory()->create([
            'is_valid' => false,
            'revoked_at' => now(),
        ]);

        $this->assertEquals('Valide', $validDocument->status_label);
        $this->assertEquals('Révoqué', $revokedDocument->status_label);
    }

    public function test_is_valid_method()
    {
        $validDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->addYear(),
        ]);
        $invalidDocument = Document::factory()->create([
            'is_valid' => false,
            'expiry_date' => now()->addYear(),
        ]);
        $expiredDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->subDay(),
        ]);
        $revokedDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->addYear(),
            'revoked_at' => now(),
        ]);

        $this->assertTrue($validDocument->isValid());
        $this->assertFalse($invalidDocument->isValid());
        $this->assertFalse($expiredDocument->isValid());
        $this->assertFalse($revokedDocument->isValid());
    }

    public function test_is_expired_method()
    {
        $validDocument = Document::factory()->create(['expiry_date' => now()->addYear()]);
        $expiredDocument = Document::factory()->create(['expiry_date' => now()->subDay()]);

        $this->assertFalse($validDocument->isExpired());
        $this->assertTrue($expiredDocument->isExpired());
    }

    public function test_is_revoked_method()
    {
        $activeDocument = Document::factory()->create(['revoked_at' => null]);
        $revokedDocument = Document::factory()->create(['revoked_at' => now()]);

        $this->assertFalse($activeDocument->isRevoked());
        $this->assertTrue($revokedDocument->isRevoked());
    }

    public function test_revoke_method()
    {
        $document = Document::factory()->create([
            'is_valid' => true,
            'revoked_at' => null,
            'revocation_reason' => null,
        ]);

        $document->revoke('Test revocation reason');

        $this->assertFalse($document->is_valid);
        $this->assertNotNull($document->revoked_at);
        $this->assertEquals('Test revocation reason', $document->revocation_reason);
    }

    public function test_generate_qr_code_method()
    {
        $document = Document::factory()->create([
            'document_type' => 'cni',
            'id' => 123,
        ]);

        $qrCode = $document->generateQrCode();
        $this->assertEquals('DOC-CNI-00000123', $qrCode);
    }

    public function test_generate_reference_method()
    {
        $document = Document::factory()->create([
            'document_type' => 'passeport',
            'issue_date' => now()->setYear(2024),
            'id' => 456,
        ]);

        $reference = $document->generateReference();
        $this->assertStringStartsWith('PAS-2024-', $reference);
    }

    public function test_scope_valid_filters_valid_documents()
    {
        $validDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->addYear(),
            'revoked_at' => null,
        ]);
        $invalidDocument = Document::factory()->create([
            'is_valid' => false,
            'expiry_date' => now()->addYear(),
            'revoked_at' => null,
        ]);
        $expiredDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->subDay(),
            'revoked_at' => null,
        ]);

        $validDocuments = Document::valid()->get();
        $this->assertEquals(1, $validDocuments->count());
        $this->assertEquals($validDocument->id, $validDocuments->first()->id);
    }

    public function test_scope_invalid_filters_invalid_documents()
    {
        $validDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->addYear(),
            'revoked_at' => null,
        ]);
        $invalidDocument = Document::factory()->create([
            'is_valid' => false,
            'expiry_date' => now()->addYear(),
            'revoked_at' => null,
        ]);
        $expiredDocument = Document::factory()->create([
            'is_valid' => true,
            'expiry_date' => now()->subDay(),
            'revoked_at' => null,
        ]);

        $invalidDocuments = Document::invalid()->get();
        $this->assertEquals(2, $invalidDocuments->count());
    }

    public function test_scope_of_type_filters_by_document_type()
    {
        Document::factory()->create(['document_type' => 'cni']);
        Document::factory()->create(['document_type' => 'passeport']);
        Document::factory()->create(['document_type' => 'permis']);

        $cniDocuments = Document::ofType('cni')->get();
        $this->assertEquals(1, $cniDocuments->count());
        $this->assertEquals('cni', $cniDocuments->first()->document_type);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $document->user);
        $this->assertEquals($user->id, $document->user->id);
    }

    public function test_request_relationship()
    {
        $request = DocumentRequest::factory()->create();
        $document = Document::factory()->create(['request_id' => $request->id]);

        $this->assertInstanceOf(DocumentRequest::class, $document->request);
        $this->assertEquals($request->id, $document->request->id);
    }

    public function test_casts_attributes()
    {
        $document = Document::factory()->create([
            'birth_date' => '1990-01-01',
            'issue_date' => '2024-01-01',
            'expiry_date' => '2034-01-01',
            'revoked_at' => '2024-06-01 10:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $document->birth_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->issue_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->expiry_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->revoked_at);
        $this->assertIsBool($document->is_valid);
    }
}
