<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function test_user_has_correct_role_by_default()
    {
        $user = User::factory()->create();
        $this->assertEquals('citizen', $user->role);
    }

    public function test_user_can_be_admin()
    {
        $admin = User::factory()->admin()->create();
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isCitizen());
    }

    public function test_user_can_be_citizen()
    {
        $citizen = User::factory()->citizen()->create();
        $this->assertEquals('citizen', $citizen->role);
        $this->assertTrue($citizen->isCitizen());
        $this->assertFalse($citizen->isAdmin());
    }

    public function test_user_password_is_hashed()
    {
        $password = 'password123';
        $user = User::factory()->create(['password' => $password]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(\Hash::check($password, $user->password));
    }

    public function test_user_has_document_requests_relationship()
    {
        $user = User::factory()->create();
        $request = DocumentRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(DocumentRequest::class, $user->documentRequests->first());
        $this->assertEquals(1, $user->documentRequests->count());
    }

    public function test_user_has_documents_relationship()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Document::class, $user->documents->first());
        $this->assertEquals(1, $user->documents->count());
    }

    public function test_scope_citizens_filters_by_role()
    {
        User::factory()->citizen()->count(3)->create();
        User::factory()->admin()->count(2)->create();

        $citizens = User::citizens()->get();
        $this->assertEquals(3, $citizens->count());
        $citizens->each(function ($user) {
            $this->assertEquals('citizen', $user->role);
        });
    }

    public function test_scope_admins_filters_by_role()
    {
        User::factory()->citizen()->count(3)->create();
        User::factory()->admin()->count(2)->create();

        $admins = User::admins()->get();
        $this->assertEquals(2, $admins->count());
        $admins->each(function ($user) {
            $this->assertEquals('admin', $user->role);
        });
    }

    public function test_email_is_unique()
    {
        $user = User::factory()->create();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => $user->email]);
    }

    public function test_cni_number_is_unique()
    {
        $user = User::factory()->create();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['cni_number' => $user->cni_number]);
    }

    public function test_fillable_attributes()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '+224 622 12 34 56',
            'cni_number' => 'CNI-2024-001234',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Conakry',
            'address' => 'Test Address',
            'profession' => 'Test Profession',
            'nationality' => 'Guinéenne',
        ];

        $user = User::create($userData);

        foreach ($userData as $key => $value) {
            if ($key !== 'password') {
                $this->assertEquals($value, $user->$key);
            }
        }
    }

    public function test_hidden_attributes()
    {
        $user = User::factory()->create();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    public function test_casts_attributes()
    {
        $user = User::factory()->create([
            'birth_date' => '1990-01-01',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->birth_date);
    }
}
