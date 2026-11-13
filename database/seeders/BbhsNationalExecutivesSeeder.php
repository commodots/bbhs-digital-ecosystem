<?php

namespace Database\Seeders;

use App\Models\AlumniProfile;
use App\Models\ExecutivePosition;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BbhsNationalExecutivesSeeder extends Seeder
{
    public function run(): void
    {
        // Current committee as confirmed by the Association, serving through January 2027.
        // Sowunmi Abdulrasaq (1986) is intentionally excluded following his departure.
        $people = [
            ['Hon. Justice Olugbeyega Ogunfowora', 'Olugbeyega', 'Ogunfowora', 1982, 'National President', 'president', 'president@bbhsoba.org', 'justice-olugbeyega-ogunfowora.jpg'],
            ['Barr. Dare Oloyede', 'Dare', 'Oloyede', 1984, '1st National Vice President', 'first-vice-president', 'firstvicepresident@bbhsoba.org', 'dare-oloyede.jpg'],
            ['Prince Oluwatoyin Ayoade', 'Oluwatoyin', 'Ayoade', 1987, '2nd National Vice President', 'second-vice-president', 'secondvicepresident@bbhsoba.org', 'oluwatoyin-ayoade.jpg'],
            ['Otunba Oluwole Okunowo', 'Oluwole', 'Okunowo', 1993, 'National Secretary', 'national-secretary', 'secretary@bbhsoba.org', 'oluwole-okunowo.jpg'],
            ['Dr. Mumin Sanni', 'Mumin', 'Sanni', 1999, 'Assistant National Secretary', 'assistant-national-secretary', 'assistantsecretary@bbhsoba.org', 'mumin-sanni.jpg'],
            ['Alh. Saheed Adeola', 'Saheed', 'Adeola', 1995, 'National Treasurer', 'treasurer', 'treasurer@bbhsoba.org', 'saheed-adeola.jpg'],
            ['Mr. Olayinka S. Ogunseye', 'Olayinka S.', 'Ogunseye', 1990, 'National Social Secretary', 'social-secretary', 'socialsecretary@bbhsoba.org', 'olayinka-ogunseye.jpg'],
            ['Mr. Oyekunle Seun Oyewole', 'Oyekunle Seun', 'Oyewole', 2007, 'National Publicity Secretary', 'national-publicity-secretary', 'publicitysecretary@bbhsoba.org', 'oyekunle-oyewole.jpg'],
            ['Mr. Seun Gabriel Ogunmefun', 'Seun Gabriel', 'Ogunmefun', 2002, 'National Ledger Adviser', 'national-ledger-adviser', 'ledgeradviser@bbhsoba.org', 'seun-gabriel-ogunmefun.jpg'],
            ['Mr. Azeez Akanbi', 'Azeez', 'Akanbi', 1985, 'National Auditor', 'auditor', 'auditor@bbhsoba.org', 'azeez-akanbi.jpg'],
            ['‘Bosun Sowunmi, Esq', '‘Bosun', 'Sowunmi', 1982, 'Ex-Officio I', 'ex-officio-i', 'exofficio1@bbhsoba.org', 'bosun-sowunmi.jpg'],
            ['Alh. Oluwole Babatunde', 'Oluwole', 'Babatunde', 1994, 'Ex-Officio II', 'ex-officio-ii', 'exofficio2@bbhsoba.org', 'oluwole-babatunde.jpg'],
            ['Dr. Abayomi Oluseye Olajide', 'Abayomi Oluseye', 'Olajide', 1989, 'Ex-Officio III', 'ex-officio-iii', 'exofficio3@bbhsoba.org', 'abayomi-olajide.jpg'],
        ];

        foreach ($people as [$name, $first, $last, $year, $positionName, $code, $email, $photo]) {
            $set = GraduationSet::query()->where('graduation_year', $year)->first();
            if (! $set) continue;

            $user = User::query()->where('email', $email)->first();
            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(64)),
                    'role' => 'member',
                    'member_status' => 'approved',
                    'is_admin' => false,
                ]);
            } else {
                $user->forceFill(['name' => $name, 'role' => 'member', 'member_status' => 'approved', 'is_admin' => false])->save();
            }

            AlumniProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $first,
                    'last_name' => $last,
                    'class_set' => $year . ' Set',
                    'graduation_set_id' => $set->id,
                    'photo' => '/images/bbhs/executives/' . $photo,
                    'approved' => true,
                    'directory_visible' => true,
                ]
            );

            $position = ExecutivePosition::updateOrCreate(
                ['scope_type' => 'global', 'code' => $code],
                ['name' => $positionName, 'sort_order' => (array_search($code, array_column($people, 5), true) + 1) * 10, 'is_active' => true]
            );

            LeadershipAssignment::updateOrCreate(
                ['user_id' => $user->id, 'executive_position_id' => $position->id, 'scope_type' => 'global', 'scope_id' => null],
                ['starts_at' => '2025-01-01', 'ends_at' => '2027-01-31', 'is_active' => true, 'notes' => 'Current National Executive Committee; term confirmed through January 2027. Seeded from the BBHS 102nd Founders’ Day brochure and subsequent Association corrections.']
            );
        }

        // Retain the former committee member as an ordinary alumni member, without an active executive appointment.
        $former = User::query()->firstOrCreate(
            ['email' => 'sowunmi.abdulrasaq@bbhsoba.org'],
            ['name' => 'Alh. Sowunmi Abdulrasaq', 'password' => Hash::make(Str::random(64)), 'role' => 'member', 'member_status' => 'approved', 'is_admin' => false]
        );
        $formerSet = GraduationSet::query()->where('graduation_year', 1986)->first();
        if ($formerSet) {
            AlumniProfile::updateOrCreate(
                ['user_id' => $former->id],
                ['first_name' => 'Sowunmi', 'last_name' => 'Abdulrasaq', 'class_set' => '1986 Set', 'graduation_set_id' => $formerSet->id, 'photo' => '/images/bbhs/executives/sowunmi-abdulrasaq.jpg', 'approved' => true, 'directory_visible' => true]
            );
        }
    }
}
