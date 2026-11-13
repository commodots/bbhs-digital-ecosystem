<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchCity;
use App\Models\ExecutivePosition;
use App\Models\GraduationSet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BbhsOrganisationSeeder extends Seeder
{
    public function run(): void
    {
        for ($year = 1970; $year <= 2026; $year++) {
            GraduationSet::updateOrCreate(
                ['graduation_year' => $year],
                ['name' => substr((string) $year, -2) . ' Set', 'is_active' => true]
            );
        }

        // Eight BBHS branches recorded from the Association's 2025 brochure.
        // Only confirmed city mappings are added here; regional/international
        // branches remain unmapped until their exact coverage cities are verified.
        $branchDefinitions = [
            'Lagos Branch' => [
                'description' => 'Lagos, Nigeria',
                'cities' => [['Lagos', 'Nigeria']],
            ],
            'Abeokuta Branch' => [
                'description' => 'Abeokuta, Ogun State, Nigeria',
                'cities' => [['Abeokuta', 'Nigeria']],
            ],
            'Abuja Branch' => [
                'description' => 'Abuja, Nigeria',
                'cities' => [['Abuja', 'Nigeria']],
            ],
            'Ibadan Branch' => [
                'description' => 'Ibadan, Oyo State, Nigeria',
                'cities' => [['Ibadan', 'Nigeria']],
            ],
            'Ijebu/Remo Branch' => [
                'description' => 'Ijebu/Remo area, Ogun State, Nigeria',
                'cities' => [],
            ],
            'Ota Branch' => [
                'description' => 'Ota, Ogun State, Nigeria',
                'cities' => [['Ota', 'Nigeria']],
            ],
            'UK/Ireland Branch' => [
                'description' => 'United Kingdom and Ireland',
                'cities' => [],
            ],
            'US/Canada Branch' => [
                'description' => 'United States and Canada',
                'cities' => [],
            ],
        ];

        foreach ($branchDefinitions as $branchName => $definition) {
            $branch = Branch::updateOrCreate(
                ['slug' => Str::slug($branchName)],
                [
                    'name' => $branchName,
                    'description' => $definition['description'],
                    'is_active' => true,
                ]
            );

            foreach ($definition['cities'] as [$city, $country]) {
                BranchCity::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'city_normalized' => Str::lower(trim($city)),
                        'country_normalized' => Str::lower(trim($country)),
                    ],
                    [
                        'city' => $city,
                        'country' => $country,
                    ]
                );
            }
        }

        $positions = [
            'global' => [
                ['President', 'president', 10],
                ['1st Vice President', 'first-vice-president', 20],
                ['2nd Vice President', 'second-vice-president', 30],
                ['General Secretary', 'general-secretary', 40],
                ['Financial Secretary', 'financial-secretary', 50],
                ['Treasurer', 'treasurer', 60],
                ['Public Relations Officer', 'public-relations-officer', 70],
                ['Social Secretary', 'social-secretary', 80],
                ['Welfare Officer', 'welfare-officer', 90],
                ['Auditor', 'auditor', 100],
            ],
            'set' => [
                ['Set President', 'set-president', 10],
                ['Set Secretary', 'set-secretary', 20],
            ],
            'branch' => [
                ['Branch President', 'branch-president', 10],
                ['Branch Secretary', 'branch-secretary', 20],
            ],
        ];

        foreach ($positions as $scope => $items) {
            foreach ($items as [$name, $code, $sort]) {
                ExecutivePosition::updateOrCreate(
                    ['scope_type' => $scope, 'code' => $code],
                    ['name' => $name, 'sort_order' => $sort, 'is_active' => true]
                );
            }
        }

        // Safely map legacy class_set values where the graduation year is unambiguous.
        // Unrecognised values are deliberately left for administrator review.
        $this->mapLegacyClassSets();
    }

    private function mapLegacyClassSets(): void
    {
        \DB::table('alumni_profiles')
            ->whereNull('graduation_set_id')
            ->whereNotNull('class_set')
            ->orderBy('id')
            ->chunkById(200, function ($profiles) {
                foreach ($profiles as $profile) {
                    $value = trim((string) $profile->class_set);
                    if (! preg_match('/^(?:19|20)?(\d{2})(?:\s*set)?$/i', $value, $matches)) {
                        continue;
                    }

                    $shortYear = (int) $matches[1];
                    $year = str_starts_with($value, '19') || str_starts_with($value, '20')
                        ? (int) substr($value, 0, 4)
                        : ($shortYear >= 70 ? 1900 + $shortYear : 2000 + $shortYear);

                    if ($year < 1970 || $year > 2026) {
                        continue;
                    }

                    $setId = GraduationSet::where('graduation_year', $year)->value('id');
                    if ($setId) {
                        \DB::table('alumni_profiles')->where('id', $profile->id)
                            ->update(['graduation_set_id' => $setId]);
                    }
                }
            });
    }
}
