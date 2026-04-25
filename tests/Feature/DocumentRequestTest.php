<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DocumentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_create_document_request()
    {
        $user = User::factory()->citizen()->create();
        $this->actingAs($user);

        $requestData = [
            'document_type' => 'cni',
            'first_name' => 'Mamadou',
            'last_name' => 'Diallo',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Conakry',
            'address' => 'Test Address',
            'phone' => '+224 622 12 34 56',
        ];

        $response = $this->post('/citoyen/demande', $requestData);

        $this->assertDatabaseHas('document_requests', [
            'user_id' => $user->id,
            'document_type' => 'cni',
            'first_name' => 'Mamadou',
            'last_name' => 'Diallo',
        ]);

        $response->assertRedirect();
    }

    public function test_guest_cannot_create_document_request()
    {
        $requestData = [
            'document_type' => 'cni',
            'first_name' => 'Mamadou',
            'last_name' => 'Diallo',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Conakry',
            'address' => 'Test Address',
            'phone' => '+224 622 12 34 56',
        ];

        $response = $this->post('/citoyen/demande', $requestData);

        $response->assertRedirect('/login');
    }

    public function test_document_request_validation()
    {
        $user = User::factory()->citizen()->create();
        $this->actingAs($user);

        // Test missing required fields
        $response = $this->post('/citoyen/demande', []);

        $response->assertSessionHasErrors([
            'document_type',
            'first_name',
            'last_name',
            'birth_date',
            'birth_place',
            'address',
            'phone',
        ]);
    }

    public function test_citizen_can_view_their_requests()
    {
        $user = User::factory()->citizen()->create();
        $this->actingAs($user);

        $request = DocumentRequest::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/citoyen/dashboard');

        $response->assertStatus(200);
        $response->assertSee($request->reference);
    }

    public function test_admin_can_view_all_requests()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $request = DocumentRequest::factory()->create();

        $response = $this->get('/administration/demandes');

        $response->assertStatus(200);
        $response->assertSee($request->reference);
    }

    public function test_citizen_cannot_view_admin_requests()
    {
        $user = User::factory()->citizen()->create();
        $this->actingAs($user);

        $response = $this->get('/administration/demandes');

        $response->assertRedirect('/administration/connexion');
    }

    public function test_admin_can_validate_request()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $request = DocumentRequest::factory()->create(['status' => 'en cours']);

        $response = $this->post("/administration/demande/{$request->id}/valider");

        $this->assertDatabaseHas('document_requests', [
            'id' => $request->id,
            'status' => 'validée',
            'validated_by' => $admin->id,
        ]);

        $response->assertRedirect();
    }

    public function test_admin_can_reject_request()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $request = DocumentRequest::factory()->create(['status' => 'en cours']);

        $response = $this->post("/administration/demande/{$request->id}/rejeter", [
            'reason' => 'Documents incomplets',
        ]);

        $this->assertDatabaseHas('document_requests', [
            'id' => $request->id,
            'status' => 'rejetée',
            'rejected_by' => $admin->id,
            'rejection_reason' => 'Documents incomplets',
        ]);

        $response->assertRedirect();
    }

    public function test_cannot_validate_already_processed_request()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $request = DocumentRequest::factory()->create(['status' => 'validée']);

        $response = $this->post("/administration/demande/{$request->id}/valider");

        $response->assertRedirect();
    }

    public function test_document_request_generates_unique_reference()
    {
        $user = User::factory()->citizen()->create();
        $this->actingAs($user);

        $requestData = [
            'document_type' => 'cni',
            'first_name' => 'Mamadou',
            'last_name' => 'Diallo',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Conakry',
            'address' => 'Test Address',
            'phone' => '+224 622 12 34 56',
        ];

        $this->post('/citoyen/demande', $requestData);
        $this->post('/citoyen/demande', $requestData);

        $requests = DocumentRequest::where('user_id', $user->id)->get();
        $this->assertEquals(2, $requests->count());
        $this->assertNotEquals($requests[0]->reference, $requests[1]->reference);
    }
}
