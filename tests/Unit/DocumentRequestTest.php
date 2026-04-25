<?php

namespace Tests\Unit;

use App\Models\DocumentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_request_can_be_created()
    {
        $user = User::factory()->create();
        $request = DocumentRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(DocumentRequest::class, $request);
        $this->assertEquals($user->id, $request->user_id);
    }

    public function test_document_request_has_default_status()
    {
        $request = DocumentRequest::factory()->create();
        $this->assertEquals('en cours', $request->status);
    }

    public function test_document_request_has_default_priority()
    {
        $request = DocumentRequest::factory()->create();
        $this->assertEquals('normal', $request->priority);
    }

    public function test_document_request_generates_unique_reference()
    {
        $request1 = DocumentRequest::factory()->create();
        $request2 = DocumentRequest::factory()->create();

        $this->assertNotEquals($request1->reference, $request2->reference);
    }

    public function test_document_type_label_accessor()
    {
        $cniRequest = DocumentRequest::factory()->create(['document_type' => 'cni']);
        $passportRequest = DocumentRequest::factory()->create(['document_type' => 'passeport']);
        $permitRequest = DocumentRequest::factory()->create(['document_type' => 'permis']);

        $this->assertEquals('Carte Nationale d\'Identité', $cniRequest->document_type_label);
        $this->assertEquals('Passeport', $passportRequest->document_type_label);
        $this->assertEquals('Permis de conduire', $permitRequest->document_type_label);
    }

    public function test_status_label_accessor()
    {
        $pendingRequest = DocumentRequest::factory()->create(['status' => 'en cours']);
        $validatedRequest = DocumentRequest::factory()->create(['status' => 'validée']);
        $rejectedRequest = DocumentRequest::factory()->create(['status' => 'rejetée']);

        $this->assertEquals('En cours', $pendingRequest->status_label);
        $this->assertEquals('Validée', $validatedRequest->status_label);
        $this->assertEquals('Rejetée', $rejectedRequest->status_label);
    }

    public function test_priority_label_accessor()
    {
        $normalRequest = DocumentRequest::factory()->create(['priority' => 'normal']);
        $urgentRequest = DocumentRequest::factory()->create(['priority' => 'urgent']);

        $this->assertEquals('Normal', $normalRequest->priority_label);
        $this->assertEquals('Urgent', $urgentRequest->priority_label);
    }

    public function test_scope_pending_filters_by_status()
    {
        DocumentRequest::factory()->create(['status' => 'en cours']);
        DocumentRequest::factory()->create(['status' => 'validée']);
        DocumentRequest::factory()->create(['status' => 'rejetée']);

        $pendingRequests = DocumentRequest::pending()->get();
        $this->assertEquals(1, $pendingRequests->count());
        $this->assertEquals('en cours', $pendingRequests->first()->status);
    }

    public function test_scope_validated_filters_by_status()
    {
        DocumentRequest::factory()->create(['status' => 'en cours']);
        DocumentRequest::factory()->create(['status' => 'validée']);
        DocumentRequest::factory()->create(['status' => 'rejetée']);

        $validatedRequests = DocumentRequest::validated()->get();
        $this->assertEquals(1, $validatedRequests->count());
        $this->assertEquals('validée', $validatedRequests->first()->status);
    }

    public function test_scope_rejected_filters_by_status()
    {
        DocumentRequest::factory()->create(['status' => 'en cours']);
        DocumentRequest::factory()->create(['status' => 'validée']);
        DocumentRequest::factory()->create(['status' => 'rejetée']);

        $rejectedRequests = DocumentRequest::rejected()->get();
        $this->assertEquals(1, $rejectedRequests->count());
        $this->assertEquals('rejetée', $rejectedRequests->first()->status);
    }

    public function test_scope_urgent_filters_by_priority()
    {
        DocumentRequest::factory()->create(['priority' => 'normal']);
        DocumentRequest::factory()->create(['priority' => 'urgent']);

        $urgentRequests = DocumentRequest::urgent()->get();
        $this->assertEquals(1, $urgentRequests->count());
        $this->assertEquals('urgent', $urgentRequests->first()->priority);
    }

    public function test_can_be_validated_method()
    {
        $pendingRequest = DocumentRequest::factory()->create(['status' => 'en cours']);
        $validatedRequest = DocumentRequest::factory()->create(['status' => 'validée']);
        $rejectedRequest = DocumentRequest::factory()->create(['status' => 'rejetée']);

        $this->assertTrue($pendingRequest->canBeValidated());
        $this->assertFalse($validatedRequest->canBeValidated());
        $this->assertFalse($rejectedRequest->canBeValidated());
    }

    public function test_can_be_rejected_method()
    {
        $pendingRequest = DocumentRequest::factory()->create(['status' => 'en cours']);
        $validatedRequest = DocumentRequest::factory()->create(['status' => 'validée']);
        $rejectedRequest = DocumentRequest::factory()->create(['status' => 'rejetée']);

        $this->assertTrue($pendingRequest->canBeRejected());
        $this->assertFalse($validatedRequest->canBeRejected());
        $this->assertFalse($rejectedRequest->canBeRejected());
    }

    public function test_is_processed_method()
    {
        $pendingRequest = DocumentRequest::factory()->create(['status' => 'en cours']);
        $validatedRequest = DocumentRequest::factory()->create(['status' => 'validée']);
        $rejectedRequest = DocumentRequest::factory()->create(['status' => 'rejetée']);

        $this->assertFalse($pendingRequest->isProcessed());
        $this->assertTrue($validatedRequest->isProcessed());
        $this->assertTrue($rejectedRequest->isProcessed());
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $request = DocumentRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $request->user);
        $this->assertEquals($user->id, $request->user->id);
    }

    public function test_casts_attributes()
    {
        $request = DocumentRequest::factory()->create([
            'birth_date' => '1990-01-01',
            'validated_at' => '2024-01-01 10:00:00',
            'rejected_at' => null,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $request->birth_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $request->validated_at);
        $this->assertNull($request->rejected_at);
    }
}
