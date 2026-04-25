<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrateur Principal',
            'email' => 'admin@identiguinee.gn',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '+224 622 12 34 56',
            'nationality' => 'Guinéenne',
        ]);

        // Create demo citizen
        $citizen = User::create([
            'name' => 'Mamadou Diallo',
            'email' => 'citoyen@identiguinee.gn',
            'password' => Hash::make('password'),
            'role' => 'citizen',
            'phone' => '+224 622 12 34 57',
            'cni_number' => 'CNI-2020-000001',
            'birth_date' => '1990-05-15',
            'birth_place' => 'Conakry',
            'address' => 'Rue du Commerce, Dixinn, Conakry',
            'profession' => 'Comptable',
            'nationality' => 'Guinéenne',
        ]);

        // Create sample document requests
        $requests = [
            [
                'reference' => 'CNI-2024-001234',
                'document_type' => 'cni',
                'first_name' => 'Mamadou',
                'last_name' => 'Diallo',
                'birth_date' => '1990-05-15',
                'birth_place' => 'Conakry',
                'address' => 'Rue du Commerce, Dixinn, Conakry',
                'phone' => '+224 622 12 34 57',
                'status' => 'validée',
                'priority' => 'normal',
                'notes' => 'Demande complète avec tous les documents requis.',
                'validated_at' => now(),
                'validated_by' => $admin->id,
            ],
            [
                'reference' => 'PAS-2024-000567',
                'document_type' => 'passeport',
                'first_name' => 'Aïssatou',
                'last_name' => 'Bah',
                'birth_date' => '1985-08-22',
                'birth_place' => 'Kankan',
                'address' => 'Avenue de la République, Kankan',
                'phone' => '+224 622 12 34 58',
                'status' => 'en cours',
                'priority' => 'urgent',
                'notes' => 'Demande urgente pour voyage professionnel.',
            ],
            [
                'reference' => 'PER-2024-000890',
                'document_type' => 'permis',
                'first_name' => 'Ousmane',
                'last_name' => 'Condé',
                'birth_date' => '1992-12-03',
                'birth_place' => 'Labé',
                'address' => 'Route de Mamou, Labé',
                'phone' => '+224 622 12 34 59',
                'status' => 'rejetée',
                'priority' => 'normal',
                'rejection_reason' => 'Documents incomplets - manque le certificat médical.',
                'rejected_at' => now(),
                'rejected_by' => $admin->id,
            ],
        ];

        foreach ($requests as $requestData) {
            $request = DocumentRequest::create(array_merge($requestData, [
                'user_id' => $citizen->id,
            ]));

            // Create document for validated request
            if ($requestData['status'] === 'validée') {
                Document::create([
                    'reference' => $requestData['reference'],
                    'user_id' => $citizen->id,
                    'request_id' => $request->id,
                    'document_type' => $requestData['document_type'],
                    'holder_name' => $requestData['first_name'] . ' ' . $requestData['last_name'],
                    'birth_date' => $requestData['birth_date'],
                    'birth_place' => $requestData['birth_place'],
                    'issue_date' => now()->subMonths(2),
                    'expiry_date' => now()->addYears(10),
                    'qr_code' => $requestData['reference'],
                    'is_valid' => true,
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@identiguinee.gn / admin123');
        $this->command->info('Citizen: citoyen@identiguinee.gn / password');
    }
}
