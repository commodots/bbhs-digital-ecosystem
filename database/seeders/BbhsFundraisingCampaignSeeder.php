<?php

namespace Database\Seeders;

use App\Models\FundraisingCampaign;
use Illuminate\Database\Seeder;

/**
 * Creates review-only fundraising campaign drafts from project needs mentioned
 * in the 2025 brochure's 2024 school development report.
 * These are NOT live appeals: amounts, dates and current project status require
 * confirmation by BBHS before publication.
 */
class BbhsFundraisingCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            [
                'slug' => 'school-classroom-block-development',
                'title' => 'School Classroom Block Development',
                'category' => 'School Development',
                'excerpt' => 'Draft campaign relating to the three-classroom block with staff room and toilets reported as ongoing in the 2024 school development report.',
                'description' => "REVIEW DRAFT — NOT A LIVE FUNDRAISING APPEAL.\n\nThe BBHS 102nd Founders’ Day brochure (2025), in its 2024 school development report, lists construction of a block of three classrooms with staff room and toilets as an ongoing project facilitated by Hon. Ayo Somide of the 1995 Set.\n\nBefore publication, BBHS must confirm whether the project is still ongoing, its current scope, approved fundraising target, authorised payment arrangements, campaign dates and responsible committee.",
            ],
            [
                'slug' => 'school-perimeter-fence-reconstruction',
                'title' => 'School Perimeter Fence Reconstruction',
                'category' => 'School Development',
                'excerpt' => 'Draft campaign relating to the fence reconstruction reported as ongoing in the 2024 school development report.',
                'description' => "REVIEW DRAFT — NOT A LIVE FUNDRAISING APPEAL.\n\nThe BBHS 102nd Founders’ Day brochure (2025), in its 2024 school development report, lists reconstruction of the collapsed fence along the teak plantation as an ongoing project undertaken by NEC with support from branches and class sets.\n\nBefore publication, BBHS must confirm the present condition and completion status, approved scope, fundraising target, authorised payment arrangements, campaign dates and responsible committee.",
            ],
            [
                'slug' => 'pa-jelil-adelekan-alumni-complex',
                'title' => 'Pa Jelil Adelekan Alumni Complex',
                'category' => 'Alumni Facilities',
                'excerpt' => 'Draft campaign relating to the Alumni Complex project listed for Lagos Branch in the 2024 school development report.',
                'description' => "REVIEW DRAFT — NOT A LIVE FUNDRAISING APPEAL.\n\nThe BBHS 102nd Founders’ Day brochure (2025), in its 2024 school development report, lists the Pa Jelil Adelekan Alumni Complex under Lagos Branch and calls on the branch to progress the project.\n\nBefore publication, BBHS must confirm the current project status, approved scope, ownership and oversight, fundraising target, authorised payment arrangements, campaign dates and responsible committee.",
            ],
        ];

        foreach ($campaigns as $campaign) {
            FundraisingCampaign::updateOrCreate(
                ['slug' => $campaign['slug']],
                [
                    'project_id' => null,
                    'title' => $campaign['title'],
                    'category' => $campaign['category'],
                    'scope_type' => 'national',
                    'scope_id' => null,
                    'excerpt' => $campaign['excerpt'],
                    'description' => $campaign['description'],
                    'image' => null,
                    'target_amount_kobo' => 0,
                    'starts_at' => null,
                    'ends_at' => null,
                    'published' => false,
                    'status' => 'draft',
                    'created_by' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                ]
            );
        }

        $this->command?->info('BBHS fundraising campaign drafts seeded (' . count($campaigns) . ' records).');
        $this->command?->warn('All campaigns remain unpublished with zero targets. Confirm project status, targets, dates and payment details before approval and publication.');
    }
}
