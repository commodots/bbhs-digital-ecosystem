<?php

namespace Database\Seeders;

use App\Models\HistoricalArchive;
use Illuminate\Database\Seeder;

class BbhsHistoricalArchiveSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            [
                'title' => 'National Executive Council – 2025', 'slug' => 'national-executive-council-2025',
                'category' => 'Leadership', 'record_year' => 2025, 'source_title' => 'BBHS 102nd Founders’ Day brochure', 'source_page' => '8',
                'summary' => 'Historical snapshot of the National Executive Council as printed in the 2025 brochure.',
                'body' => "Hon Justice Olugboyega Ogunfowora (1982) — National President\nBarr. Dare Oloyede (1984) — 1st National Vice President\nAlh. Sowunmi Abdulrasaq (1986) — 2nd National Vice President\nOtunba Oluwole Okunowo (1993) — National Secretary\nDr. Mumin Sanni (1996) — Assistant National Secretary\nAlh. Saheed Adeola (1999) — National Treasurer\nPrince Oluwatoyin Ayoade (1987) — National Financial Secretary\nMr. Olayinka S. Ogunseye (1990) — National Publicity Secretary\nMr. Oyekunle Seun Oyewole (1995) — National Social Secretary\nMr. Seun Gabriel Ogunmefun (2007) — National Ledger Adviser\nMr. Azeez Akanbi (2002) — National Auditor\n‘Bosun Sowunmi Esq (1982) — Ex-Officio I\nAlh. Oluwole Babatunde (1985) — Ex-Officio II\nDr. Abayomi Oluseye Olajide (1994) — Ex-Officio III\n\nTranscribed from the 2025 brochure. Names, spellings and titles require Association verification.",
            ],
            [
                'title' => 'Branch Presidents – 2025 Brochure Snapshot', 'slug' => 'branch-presidents-2025',
                'category' => 'Branch Leadership', 'record_year' => 2025, 'source_title' => 'BBHS 102nd Founders’ Day brochure', 'source_page' => '96',
                'summary' => 'Branch leadership names as printed in the 2025 brochure.',
                'body' => "Abeokuta — Alhaji Wole Babatunde (1985)\nAbuja — QS. Omololu Ilori (1992)\nIbadan — Architect Fatai Falola (1979)\nIjebu/Remo — Prof. Ven Alfred Olufemi Fatade (1976)\nLagos — Mr. Olatunde Oluwasola (1979)\nOta Branch — Elder Sesan Faronbi (1977)\nUK/Ireland — Pastor Ayotunde Moyegun (1993)\nUS/Canada — Mr Mike Adeniyi (1977)\n\nHistorical 2025 snapshot only. Confirm spelling, role and dates before publication.",
            ],
            [
                'title' => 'Graduation Set Representatives – 2025 Brochure Snapshot', 'slug' => 'graduation-set-representatives-2025',
                'category' => 'Graduation Set Representatives', 'record_year' => 2025, 'source_title' => 'BBHS 102nd Founders’ Day brochure', 'source_page' => '96',
                'summary' => 'Set representative names extracted from the brochure. Some years are not listed in the source; omissions must not be interpreted as absence.',
                'body' => "1958 — Dr Nathaniel Sotoyinbo\n1962 — Ambassador Ti Odegbile [CHECK OCR]\n1964 — Chief J.B. Odebiyi\n1967 — Surv. Bukunmi Oduwaiye\n1970 — Mr Charles Oguntola\n1971 — Dr Ade Oletubo Kayode\n1972 — Chief Debo Agbejimi\n1973 — Engr Adeosun Segun\n1974 — Prince Tunde Sodeke\n1975 — Elder Oluwatoyin Adetona\n1976 — Mr Toyin Azeez\n1977 — Prince Adeoye Adeyemi\n1978 — Elder Abayomi Oderinde\n1979 — Dr Taiwo Sogunle\n1980 — Dr Sunday Malomo\n1981 — Dr Adewale Adepoju\n1982 — Prince Tunde Akinde\n1983 — Prince Adesina Somoye\n1984 — Engr Bola Sowunmi\n1985 — Mr Abidemi Adeneye\n1986 — Mr Olusola Ajileye\n1987 — Mr Micheal Oni-Micheal [CHECK SPELLING]\n1988 — Alh Sakiru Bakare\n1990 — Mr Yinka Ogunseye\n1991 — Mr Sola Sotayo\n1992 — Dr Dotun Lisoyi\n1993 — Pastor Ayotunde Moyegun\n1994 — Mr Adewale Olujimi\n1995 — Mr Ibikunle Faneye\n1996 — Barr Rilwan Elias\n1997 — Mr Ibrahim Adepoju\n1998 — Dr Charles Omezi\n1999 — Mr Temitope Okeowo\n2000 — Dr Oluseye Akinsanmi\n2001 — Mr Abiodun Abioye\n2002 — Mr Adeniji Ogunjimi\n2003 — Mr Oluwaseun Ologunebi\n2004 — Engr Waheed Olayiwola\n2005 — Mr Patrick Sonde\n2006 — Engr Temitope Soneye\n2007 — Mr Saheed Ogunmuyiwa\n2008 — Mr Oluwaseyi Ogunbona\n2009 — Mr Odeh Emmanuel\n2010 — Mr Felix Olumide\n2011 — Mr Benjamin Elemide\n2012 — Fatu Rasheed [CHECK OCR]\n2013 — Mr Olatoye Ademusan\n2014 — Dr Anthony Adegbite\n\nThis is a transcription for review, not a verified current office-holder list.",
            ],
            [
                'title' => '2024 School Reports – Historical Summary', 'slug' => '2024-school-reports',
                'category' => 'School Reports', 'record_year' => 2024, 'source_title' => 'BBHS 102nd Founders’ Day brochure', 'source_page' => '49–52',
                'summary' => 'Historical school report figures included in the 2025 brochure.',
                'body' => "Senior School: Principal Mr. Folahan Ademola; reported enrolment 1,204; report states 90% achieved at least five WAEC/NECO credits including English and Mathematics and over 95% JAMB admission.\nJunior School: Principal Mr. Sofela Oludayo Olufemi; reported enrolment 1,293; BECE overall pass rate 85.2%.\nMission School: Principal Mrs. Oluwabukanla Abiola Daniel; reported enrolment 36.\n\nAll figures relate to the report period in the brochure and must not be presented as current statistics.",
            ],
            [
                'title' => 'School Anthem – Heritage Record', 'slug' => 'school-anthem-heritage',
                'category' => 'Heritage', 'record_year' => 2025, 'source_title' => 'BBHS 102nd Founders’ Day brochure', 'source_page' => '6',
                'summary' => 'The brochure attributes the School Anthem to Late J.S. Sojobi (1925–1974).',
                'body' => 'The source identifies Late J.S. Sojobi (1925–1974) as composer. Verify the anthem text and rights before publishing downloadable or recorded versions.',
            ],
            [
                'title' => '102nd Founders’ Day Brochure', 'slug' => '102nd-founders-day-brochure-2025',
                'category' => 'Publications', 'record_year' => 2025, 'source_title' => 'BBHS 102nd Founders’ Day brochure', 'source_page' => 'Full publication',
                'summary' => 'The 2025 brochure for the 102nd Founders’ Day, held Saturday 18 January 2025.',
                'body' => 'Upload the original PDF as an attachment through the archive editor after running the seeder.',
            ],
        ];

        foreach ($records as $record) {
            $record['is_published'] = false;
            $record['is_featured'] = false;
            $record['published_at'] = null;
            HistoricalArchive::updateOrCreate(['slug' => $record['slug']], $record);
        }
    }
}
