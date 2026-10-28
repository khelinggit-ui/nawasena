<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Constants\RoleConstants;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            ['name' => 'NAHYUDIN', 'email' => 'nahyudin@nawasena.web.id', 'password' => 'nahyudin12345', 'cabang_id' => 3, 'jabatan_id' => 2],
            ['name' => 'BUSTOMI', 'email' => 'bustomi@nawasena.web.id', 'password' => 'bustomi12345', 'cabang_id' => 6, 'jabatan_id' => 1],
            ['name' => 'SAEFUDIN', 'email' => 'saefudin@nawasena.web.id', 'password' => 'saefudin12345', 'cabang_id' => 1, 'jabatan_id' => 1],
            ['name' => 'ICHSHAN', 'email' => 'ichshan@nawasena.web.id', 'password' => 'ichshan12345', 'cabang_id' => 1, 'jabatan_id' => 2],
            ['name' => 'WAWAN', 'email' => 'wawan@nawasena.web.id', 'password' => 'wawan12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'WUSTORO', 'email' => 'wustoro@nawasena.web.id', 'password' => 'wustoro12345', 'cabang_id' => 1, 'jabatan_id' => 2],
            ['name' => 'RIKI', 'email' => 'riki@nawasena.web.id', 'password' => 'riki12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'TOYIBIN', 'email' => 'toyibin@nawasena.web.id', 'password' => 'toyibin12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'DADI', 'email' => 'dadi@nawasena.web.id', 'password' => 'dadi12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'DEDE', 'email' => 'dede@nawasena.web.id', 'password' => 'dede12345', 'cabang_id' => 1, 'jabatan_id' => 2],
            ['name' => 'BUYUNG', 'email' => 'buyung@nawasena.web.id', 'password' => 'buyung12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'ANDRI', 'email' => 'andri@nawasena.web.id', 'password' => 'andri12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'IKA SARTIKA', 'email' => 'ikasartika@nawasena.web.id', 'password' => 'ikasartika12345', 'cabang_id' => 2, 'jabatan_id' => 2],
            ['name' => 'AWAN', 'email' => 'awan@nawasena.web.id', 'password' => 'awan12345', 'cabang_id' => 3, 'jabatan_id' => 2],
            ['name' => 'NURHADI', 'email' => 'nurhadi@nawasena.web.id', 'password' => 'nurhadi12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'MUH NUR JAYA', 'email' => 'muhnurjaya@nawasena.web.id', 'password' => 'muhnurjaya12345', 'cabang_id' => 3, 'jabatan_id' => 2],
            ['name' => 'SOLEH', 'email' => 'soleh@nawasena.web.id', 'password' => 'soleh12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'HABIB MUSTHOFA', 'email' => 'habibmusthofa@nawasena.web.id', 'password' => 'habibmusthofa12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'ASIH WAHYUNI', 'email' => 'asihwahyuni@nawasena.web.id', 'password' => 'asihwahyuni12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'IRMA', 'email' => 'irma@nawasena.web.id', 'password' => 'irma12345', 'cabang_id' => 2, 'jabatan_id' => 2],
            ['name' => 'HERYADI', 'email' => 'heryadi@nawasena.web.id', 'password' => 'heryadi12345', 'cabang_id' => 3, 'jabatan_id' => 2],
            ['name' => 'ALDI', 'email' => 'aldi@nawasena.web.id', 'password' => 'aldi12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'ADI W', 'email' => 'adiw@nawasena.web.id', 'password' => 'adiw12345', 'cabang_id' => 8, 'jabatan_id' => 2],
            ['name' => 'HERYADI', 'email' => 'heryadi2@nawasena.web.id', 'password' => 'heryadi12345', 'cabang_id' => 2, 'jabatan_id' => 2],
            ['name' => 'DEDI', 'email' => 'dedi@nawasena.web.id', 'password' => 'dedi12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'RENDI', 'email' => 'rendi@nawasena.web.id', 'password' => 'rendi12345', 'cabang_id' => 8, 'jabatan_id' => 2],
            ['name' => 'EFRAN', 'email' => 'efran@nawasena.web.id', 'password' => 'efran12345', 'cabang_id' => 8, 'jabatan_id' => 2],
            ['name' => 'ADI DWI WIBOWO', 'email' => 'adidwiwibowo@nawasena.web.id', 'password' => 'adidwiwibowo12345', 'cabang_id' => 8, 'jabatan_id' => 2],
            ['name' => 'LUSI', 'email' => 'lusi@nawasena.web.id', 'password' => 'lusi12345', 'cabang_id' => 6, 'jabatan_id' => 2],
            ['name' => 'HERYADI', 'email' => 'heryadi3@nawasena.web.id', 'password' => 'heryadi12345', 'cabang_id' => 1, 'jabatan_id' => 2],
            ['name' => 'IRAWAN', 'email' => 'irawan@nawasena.web.id', 'password' => 'irawan12345', 'cabang_id' => 4, 'jabatan_id' => 2],
            ['name' => 'ABU BAKAR', 'email' => 'abubakar@nawasena.web.id', 'password' => 'abubakar12345', 'cabang_id' => 2, 'jabatan_id' => 1],
            ['name' => 'HERRY', 'email' => 'herry@nawasena.web.id', 'password' => 'herry12345', 'cabang_id' => 3, 'jabatan_id' => 1],
            ['name' => 'DANI', 'email' => 'dani@nawasena.web.id', 'password' => 'dani12345', 'cabang_id' => 4, 'jabatan_id' => 1],
            ['name' => 'BUYUNG', 'email' => 'buyung2@nawasena.web.id', 'password' => 'buyung12345', 'cabang_id' => 6, 'jabatan_id' => 1],
            ['name' => 'EPI', 'email' => 'epi@nawasena.web.id', 'password' => 'epi12345', 'cabang_id' => 6, 'jabatan_id' => 1],
            ['name' => 'M. ARIEF', 'email' => 'm.arief@nawasena.web.id', 'password' => 'm.arief12345', 'cabang_id' => 8, 'jabatan_id' => 1],
        ];

        foreach ($usersData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'cabang_id' => $userData['cabang_id'],
                    'jabatan_id' => $userData['jabatan_id'],
                    'status' => 'Active',
                ]
            );

            // Assign marketing role
            if (!$user->hasRole(RoleConstants::MARKETING)) {
                $user->assignRole(RoleConstants::MARKETING);
            }
        }
    }
}
