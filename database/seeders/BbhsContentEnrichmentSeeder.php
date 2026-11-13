<?php

namespace Database\Seeders;

use App\Models\HistoricalArchive;
use Illuminate\Database\Seeder;

/**
 * Phase 4C: brochure-derived content enrichment.
 * All records are deliberately created as drafts pending BBHS OBA verification.
 */
class BbhsContentEnrichmentSeeder extends Seeder
{
    public function run(): void
    {
        $source = 'BBHS 102nd Founders’ Day brochure (2025)';
        $records = [
            [
                'title' => 'Distinguished Old Boy, Merit Awardees and Wall of Fame – 2023',
                'slug' => 'distinguished-old-boy-merit-awards-wall-of-fame-2023',
                'category' => 'Honours & Awards', 'record_year' => 2023, 'source_page' => '25',
                'summary' => 'Recognition list reported in the Association’s annual report section of the 2025 brochure.',
                'body' => "DISTINGUISHED OLD BOY OF 2023\nProf. Olukayode Oyekanmi Oyesiku (1975)\n\nMERIT AWARDEES\n1. Mr Akinola Oyebola Olusegun (1986)\n2. Alhaji Shakiru Oluwatoyin Azeez (1976)\n3. Mr Kolawole Ayodele Adeneye (1977)\n4. Alhaji Akeem Oloyede (1981)\n5. Gafar Olalekan Aliu (1984)\n\nWALL OF FAME\n1. Prof. Olukayode Olatunde Ogunremi (1957)\n2. Olumuyiwa Olawuyi Olaonipekun (1978)\n3. Mr Amos Kayode Sowunmi (1982)\n4. Hon. Stephen Olukayode Ogundipe (1982)\n5. Goloba Adam Akolawole (1983)\n6. Richard Olayori Salako (1984)\n7. Pastor Ayotunde Moyegun (1993)\n8. Engr. Jude Oseafiana Osemeke (2001)\n9. Mr Nurudeen Olasunkanmi Osho (2001)\n\nTranscribed from the 2025 brochure. Confirm spellings and award classifications with the Association before publication.",
            ],
            [
                'title' => 'Distinguished Old Boy, Merit Awards and Wall of Fame – 2024',
                'slug' => 'distinguished-old-boy-merit-awards-wall-of-fame-2024',
                'category' => 'Honours & Awards', 'record_year' => 2024, 'source_page' => '27–28',
                'summary' => 'NEC approval and awardee names recorded in the 2025 brochure.',
                'body' => "The brochure states that NEC approved the nominations at its meeting of 16 September 2024.\n\nDISTINGUISHED OLD BOY OF THE YEAR 2024\nDr Abayomi Odubela (1967)\n\nMERIT AWARDS\n1. Mr Moses Ayokanmbi (1975)\n2. Mr Rotimi Malaolu (1978) [first name OCR/transcription to verify]\n3. Mr Kayode Olaniran (1982)\n4. Mr Olanrewaju Semiu Lasabi (1983)\n5. Mr Julius Abiodun Folorunso (1993)\n\nWALL OF FAME\n1. Prof. Q.B.O. Anthonio (1948)\n2. Engr. Gbolahan Fatumbi (1976)\n3. Mr Adewale Olabode Kolawole (1978)\n4. Mr Titus Olufemi Sowunmi (1979)\n5. Alhaji Babatunde Dawodu (1979)\n6. Mr Timothy Oyenekan (1982)\n7. Mr Olukayode Anifowose (1992)\n8. Dr Joseph Abayomi Akinjobi (1992)\n9. Alhaji Zaheed Abayomi Abayomi (1993)\n10. Hon. Ayo Shomide (1995)\n11. Mr Ebenezer Akinbode (1999)\n12. Mr Hamza Olusegun Samson (1999)\n\nNames and spellings are transcribed from the brochure and require Association verification.",
            ],
            [
                'title' => 'Distinguished Old Boys – Historical Award Roll, 1989–2023',
                'slug' => 'distinguished-old-boys-historical-award-roll-1989-2023',
                'category' => 'Distinguished Old Boys', 'record_year' => 2023, 'source_page' => '66–70',
                'summary' => 'Historical Distinguished Old Boy recipients listed in the 2025 brochure. The source pairs recipients with award years.',
                'body' => "The brochure’s historical award roll includes the following names and award years (as transcribed):\n\n1989 — Mr Kehinde Onafowokan (SAN)\n1990 — Chief A.A. Egunjobi (1946)\n1991 — Mr J.O. Koleosho (1953)\n1992 — Prof. Olufemi? [name/year alignment requires visual verification]\n1998 — Mr J.O. Koleosho (1953) [verify year/name alignment]\n1999 — recipient entry appears in the brochure; verify scan\n2001 — Snr. Evang. M.A. Akintola (1957) [verify year alignment]\n2006 — Engr. (Dr.) Funsho Kupolokun (1965)\n2007 — Mr J.D. Anubi (1948)\n2008 — Mr J.D. Anubi (1948) [verify layout/year alignment]\n2011 — Elder Sola Adeyemi mni (1974)\n2012 — Alhaji Jelili Olayiwola Adelekan (1945)\n2013 — Hon. Oladimeji Sabur Bankole, CFR (1985)\n2014 — Chief Dr. Nathaniel Olatunji Sotoyinbo (1958)\n2015 — Deacon Oreitan Oluwole Apampa (1944)\n2016 — Venerable Olusola Ladipo-Ajayi (1972)\n2017 — Otunba Samuel Olufemi Deru (1957)\n2018 — Oba Engr. Alao Tepede (1957)\n2019 — Engr. Adekunle Olumuyiwa Mokuolu (1972)\n2020 — Prince Adedeji Solomon Soyebi (1973) [the brochure’s adjacent heading/award-year sequence should be checked]\n2021 — Alh. Fatai Layiwola Aremu Isiaka (1956) [verify sequence]\n2022 — HRM Oba (Dr.) Adedotun Aremu Gbadebo (1962) [verify sequence]\n2023 — Professor Olukayode Oyesiku (1975)\n\nIMPORTANT: This is a transcription aid, not a clean or authoritative award register. The two-column layout and OCR text flow make several early entries ambiguous. Verify every year/name pairing against brochure pages 66–70 before publishing.",
            ],
            [
                'title' => 'School Development Projects – 2024 Report',
                'slug' => 'school-development-projects-2024-report',
                'category' => 'Projects & Impact', 'record_year' => 2024, 'source_page' => '26–27, 49–50',
                'summary' => 'Completed, ongoing and desired school development projects as reported in the 2025 brochure.',
                'body' => "COMPLETED PROJECTS REPORTED FOR 2024\n• Industrial borehole with storage and fetching facilities, provided by the 1979/84 Class Set (the brochure text reads “79/84”; verify the exact set designation).\n• Renovation of the existing block of science laboratories as part of STEM development.\n\nONGOING PROJECTS REPORTED\n• Construction of a block of three classrooms with staff room and toilets, facilitated by Hon. Ayo Somide of the 1995 Set.\n• Reconstruction of the collapsed fence along the teak plantation, undertaken by NEC with support from branches and class sets.\n• Pa Jelil Adelekan Alumni Complex by Lagos Branch; the report asks the branch to progress the project.\n\nDESIRED AREAS OF NEED LISTED\n• Refurbishment of the school bus; renovation of OGD Hall; additional 25-bed hostel; car parks; completion of Alumni Secretariat; landscaping and walkways; road pavement extensions; classroom walkways; streetlights; workshops for block laying, carpentry and metalwork; volleyball and handball courts.\n\nThese are historical 2024 report statuses. Do not represent them as current project status without a fresh update from the school/Association.",
            ],
            [
                'title' => '2008 Set – Oluwaseun Kuforiji Sew-a-thon Announcement',
                'slug' => '2008-set-oluwaseun-kuforiji-sew-a-thon-announcement',
                'category' => 'Alumni Stories', 'record_year' => 2024, 'source_page' => '42 (confirm printed page)',
                'summary' => 'The 2008 Set announced a planned 120-hour sew-a-thon by its Vice President, Oluwaseun Kuforiji.',
                'body' => "The 2008 Class Set report in the brochure announces that its Vice President, Oluwaseun Kuforiji, planned a 120-hour sew-a-thon from 22–27 January 2024 in Ijebu Ode, Ogun State. The article describes it as an attempt and solicits support from NEC.\n\nThe brochure does not establish in this announcement whether the attempt was completed or whether a record was certified. Confirm the outcome and any subsequent recognition before presenting it as an achievement.",
            ],
            [
                'title' => '1983 Set – Member Health Insurance Initiative',
                'slug' => '1983-set-member-health-insurance-initiative-2024',
                'category' => 'Alumni Stories', 'record_year' => 2024, 'source_page' => 'Set reports section (verify exact page)',
                'summary' => 'The 1983 Set reported introducing a health insurance policy for its members in 2024.',
                'body' => 'The 1983 Diamond Set report says that, in response to illness and bereavement among members, the set decided to establish a health insurance policy valued at more than ₦1.6 million for members’ benefit. Confirm the scheme details, provider, coverage and current status with the set before publishing further information.',
            ],
            [
                'title' => 'School and Association Photo Archive – 2024 Events',
                'slug' => 'school-association-photo-archive-2024-events',
                'category' => 'Gallery Archive', 'record_year' => 2024, 'source_page' => '97–98',
                'summary' => 'Photo-page previews from the 2025 brochure covering Agora Night and Congress/Luncheon activities.',
                'body' => "This archive entry is a source index for the photograph pages printed at pages 97 and 98 of the 2025 brochure. The attached preview images are full-page brochure extracts, not individually identified photographs.\n\nBefore publishing individual photographs, confirm the event captions, names, dates and permission to publish. Suggested collections: Agora Night; Congress and Luncheon. The original brochure remains the source of record.",
            ],
            [
                'title' => 'Abeokuta Branch – 40th Anniversary Feature',
                'slug' => 'abeokuta-branch-40th-anniversary-feature',
                'category' => 'Events & Anniversaries', 'record_year' => 2024, 'source_page' => '85',
                'summary' => 'The brochure includes a feature on an Abeokuta 40th anniversary celebration.',
                'body' => 'The brochure contents identify a feature titled “Abeokuta Stands Still as Eminents Celebrate 40th Anniversary”. The extracted text available for this entry does not provide enough verified detail to establish the event date, honourees or full programme. Add those details only after reviewing the original page and confirming with the Association.',
            ],
            [
                'title' => '1999 Set – 25 Years After Leaving School',
                'slug' => '1999-set-25-years-after-leaving-school',
                'category' => 'Events & Anniversaries', 'record_year' => 2024, 'source_page' => '93',
                'summary' => 'The brochure includes a feature on the 1999 Set marking 25 years after leaving school.',
                'body' => 'The 2025 brochure contents list a feature titled “1999 Set celebrated 25 Years of leaving School”. The full article details, event date and participants should be transcribed from the original page and verified before public publication.',
            ],
            [
                'title' => '“Let No Child Be Left Behind” – Brochure Feature',
                'slug' => 'let-no-child-be-left-behind-brochure-feature',
                'category' => 'Projects & Impact', 'record_year' => 2025, 'source_page' => '78',
                'summary' => 'A programme/feature named in the brochure contents; programme details require review and confirmation.',
                'body' => 'The brochure contents identify a feature titled “Let no Child be Left Behind” on page 78. The title alone does not establish programme ownership, eligibility, funding arrangements or current status. Confirm the full page content and current programme details with the Association before publishing calls for support or donations.',
            ],
        ];

        foreach ($records as $record) {
            $record['source_title'] = $source;
            $record['is_published'] = false;
            $record['is_featured'] = false;
            $record['published_at'] = null;
            HistoricalArchive::updateOrCreate(['slug' => $record['slug']], $record);
        }
    }
}
