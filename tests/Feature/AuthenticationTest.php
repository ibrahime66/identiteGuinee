<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect();
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_registration_page_is_accessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+224 622 12 34 56',
            'cni_number' => 'CNI-2024-001234',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Conakry',
            'address' => 'Test Address',
            'profession' => 'Test Profession',
        ];

        $response = $this->post('/register', $userData);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'citizen',
        ]);

        $response->assertRedirect();
    }

    public function test_user_cannot_register_with_duplicate_email()
    {
        $existingUser = User::factory()->create();

        $userData = [
            'name' => 'Test User',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+224 622 12 34 56',
            'cni_number' => 'CNI-2024-001234',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Conakry',
            'address' => 'Test Address',
        ];

        $response = $this->post('/register', $userData);

        $this->assertDatabaseMissing('users', [
            'email' => $existingUser->email,
            'name' => 'Test User',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_is_redirected_to_admin_dashboard()
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_citizen_is_redirected_to_citizen_dashboard()
    {
        $citizen = User::factory()->citizen()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $citizen->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($citizen);
        $response->assertRedirect(route('citizen.dashboard'));
    }

    public function test_login_rate_limiting()
    {
        $user = User::factory()->create();

        // Attempt login 6 times (limit is 5)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response->assertSessionHasErrors('email');
    }
}
