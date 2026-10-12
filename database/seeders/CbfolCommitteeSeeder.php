<?php

namespace Database\Seeders;

use App\Models\CbfolCommittee;
use Illuminate\Database\Seeder;

class CbfolCommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $term = 'ສະໄໝທີ IX';
        $termYear = '23 ທັນວາ 2568 (ຄສ 2025)';

        // 1. ປະທານ - ຮອງປະທານ (9 ອົງ)
        $presidents = [
            [
                'name' => 'ພຣະອາຈານໃຫຍ່ ມະຫາເຫວດ ມະເສໄນ',
                'title' => 'ປະທານ ສູນກາງ ອພສ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1-Ol5rUI6d8GA2U__11lLHbgbAO21hVIe',
                'sort_order' => 1,
                'bio' => 'ປະທານ ສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ ສະໄໝທີ IX',
            ],
            [
                'name' => 'ພຣະອາຈານ ມະຫາຄຳມາ ປັນຍາວິຈິດ',
                'title' => 'ຮອງປະທານ ສູນກາງ ອພສ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1qHwX9oRt2LawHdrqtGUlely7ivxcTE47',
                'sort_order' => 2,
                'bio' => 'ຮອງປະທານ ສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ',
            ],
            [
                'name' => 'ພຣະອາຈານ ບຸນສວນ ພັນທະວົງ',
                'title' => 'ຮອງປະທານ ຜູ້ປະຈຳການ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1WGfRNQ09pkiA3xfHpgqaI1upzhmvgz52',
                'sort_order' => 3,
                'bio' => 'ຮອງປະທານ ຜູ້ປະຈຳການ ສູນກາງ ອພສ',
            ],
            [
                'name' => 'ພຣະອາຈານ ບຸນທະວີ ປະສິດທິສັກ',
                'title' => 'ຮອງປະທານ ຫົວໜ້າກຳມາທິການສາທາຣະນຸປະການ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1pwTz_WStOgPOOc7DGK5H-wj5XCI3NyhX',
                'sort_order' => 4,
                'organization' => 'ກຳມາທິການສາທາຣະນຸປະການ',
            ],
            [
                'name' => 'ພຣະອາຈານ ສີທົນ ໄຊຍະວົງສອນ',
                'title' => 'ຮອງປະທານ ຫົວໜ້າກຳມາທິການເຜີຍແຜ່ສີລະທຳ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1h018mwoD220CubnMwMP4tAMoYdk9ux0_',
                'sort_order' => 5,
                'organization' => 'ກຳມາທິການເຜີຍແຜ່ສີລະທຳ ແລະ ວິປັດສະນາກັມມັດຖານ',
            ],
            [
                'name' => 'ພຣະອາຈານ ອະທິປະໄຕ ໄມຕີຈິດ',
                'title' => 'ຮອງປະທານ ຫົວໜ້າຫ້ອງການບໍລິຫານງານ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1wqGBQIRb6nuu5BDbs0COCoptVyqxDkKN',
                'sort_order' => 6,
                'organization' => 'ຫ້ອງການບໍລິຫານງານ ສູນກາງ ອພສ',
            ],
            [
                'name' => 'ພຣະອາຈານ ປອ ພວງປະເສີດ ພູມາວົງ',
                'title' => 'ຮອງປະທານ ຫົວໜ້າກຳມາທິການສຶກສາສົງ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/16_kYdyaER_URcErspL0xaCd5mc-2OeET',
                'sort_order' => 7,
                'organization' => 'ກຳມາທິການສຶກສາສົງ',
            ],
            [
                'name' => 'ພຣະອາຈານ ປອ ດຳລົງ ພິມມະຈັກ',
                'title' => 'ຮອງປະທານ ຫົວໜ້າກຳມາທິການການຕ່າງປະເທດ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1p6Qe2aaKY9qmmuUCTuIWNuDD7rQ94XFO',
                'sort_order' => 8,
                'organization' => 'ກຳມາທິການການຕ່າງປະເທດ',
            ],
            [
                'name' => 'ພຣະອາຈານ ວັນຄຳ ວົງສາສຸລິນ',
                'title' => 'ຮອງປະທານ ຫົວໜ້າກຳມາທິການປົກຄອງສົງ',
                'category' => CbfolCommittee::CATEGORY_PRESIDENT,
                'photo' => 'https://lh3.googleusercontent.com/d/1HVmu6vGQrBWs3sPRy0A05OPfDgWIZKbm',
                'sort_order' => 9,
                'organization' => 'ກຳມາທິການປົກຄອງສົງ',
            ],
        ];

        // 2. ຄະນະບໍລິຫານງານປະຈຳການ (26 ອົງ)
        $standingList = [
            'ພຣະອາຈານໃຫຍ່ ມະຫາເຫວດ ມະເສໄນ',
            'ພຣະອາຈານໃຫຍ່ ມະຫາຄຳມາ ປັນຍາວິຈິດ',
            'ພຣະອາຈານໃຫຍ່ ບຸນສວນ ພັນທະວົງ',
            'ພຣະອາຈານ ປອ. ຖາວອນ ຕາພອນປະເສີດ',
            'ພຣະອາຈານ ອະທິປະໄຕ ໄມຕີຈິດ',
            'ພຣະອາຈານ ວັນຄຳ ວົງສາສຸລິນ',
            'ພຣະອາຈານ ປອ. ພວງປະເສີດ ພູມາວົງ',
            'ພຣະອາຈານ ບຸນທະວີ ປະສິດທິສັກ',
            'ພຣະອາຈານ ສີທົນ ໄຊຍະວົງສອນ',
            'ພຣະອາຈານ ປອ. ດຳຣົງ ພິມມະຈັກ',
            'ພຣະອາຈານ ຄຳວັນ ວໍລະເດດ',
            'ພຣະອາຈານ ດາວເຮືອງ ຄຳປະເສີດ',
            'ພຣະອາຈານ ປອ. ດາວສະເດັດ ເລີດມະນີ',
            'ພຣະອາຈານ ສຸວັນ ຈັນທະລາດ',
            'ພຣະອາຈານ ອ່ອນແກ້ວ ສິດທິວົງ',
            'ພຣະອາຈານ ບຸນເສີຍ ເພົາພາສິດ',
            'ພຣະອາຈານ ບຸນປອນ ສອນສັກສິດ',
            'ພຣະອາຈານ ປອ. ອຳມະລິນ ບຸນວິເສດ',
            'ພຣະອາຈານ ກິແກ້ວ ຈັນທະນົງສີ',
            'ພຣະອາຈານ ປອ. ສີສຸກ ສະເຫຼີມຄຸນ',
            'ພຣະອາຈານ ລຳເງິນ ສຸວັນນະສານ',
            'ພຣະອາຈານ ພົງສະຫວັນ ອານຸສິນ',
            'ພຣະອາຈານ ບຸນທະວີ ສີບຸນເຮືອງ',
            'ພຣະອາຈານ ພູຄຳ ແກ້ວປະທຸມ',
            'ພຣະອາຈານ ພັນທອງ ໂພມີໄຊ',
            'ພຣະອາຈານ ທອງເສີມ ສີບຸນເຮືອງ',
        ];

        // 3. ຄະນະທີ່ປຶກສາ (15 ອົງ)
        $advisors = [
            'ພຣະອາຈານໃຫຍ່ ມະຫານ້ອຍ ຊູເສນ',
            'ພຣະອາຈານໃຫຍ່ ບຸນຍົດ ນັນທະໂຄດ',
            'ພຣະອາຈານໃຫຍ່ ມະຫາບຸນມີ ພູມິນ',
            'ພຣະອາຈານໃຫຍ່ ມະຫາວັນນາ ສຸລິຍະວົງ',
            'ພຣະອາຈານໃຫຍ່ ທອງໃສ ພັນທະວົງ',
            'ພຣະອາຈານໃຫຍ່ ບຸນມີ ຍານະວີໂຣ',
            'ພຣະອາຈານໃຫຍ່ ສົມພັງ ເມດຕາຈິດຕdefault_api:write_to_file',
            'ພຣະອາຈານໃຫຍ່ ຄຳຄູນ ດວງອາໄພວົງ',
            'ພຣະອາຈານໃຫຍ່ ບຸນທັນ ປັນຍາກາໂມ',
            'ພຣະອາຈານໃຫຍ່ ສົມບູນ ສິລິປັນໂຍ',
            'ພຣະອາຈານໃຫຍ່ ສົມສະໜຸກ ບຸດຕະລາດ',
            'ພຣະອາຈານໃຫຍ່ ຄຳພັນ ຍານະວະໂຣ',
            'ພຣະອາຈານໃຫຍ່ ບຸນທາຍ ຈັນທະໄພຈິດ',
            'ພຣະອາຈານໃຫຍ່ ຊ້ອຍ ຈັນທະສຸກ',
            'ພຣະອາຈານໃຫຍ່ ຄຳເງິນ ຄຳວົງ',
        ];

        // 4. ຄະນະກຳມະການ (ຕົວຢ່າງຈາກ 99 ອົງ ຕາມ blog-page_11)
        $committeeMembers = [
            'ພຣະອາຈານ ຄຳໝັ້ນ ສີສະຫວັດ',
            'ພຣະອາຈານ ອຸດົມ ແຫວນຄຳ',
            'ພຣະອາຈານ ໄຊຍະເດດ ວົງໂສພາ',
            'ພຣະອາຈານ ບຸນລ້ວມ ອິນສີຊຽງໃໝ່',
            'ພຣະອາຈານ ຄຳປານ ແສນບຸດຕະລາດ',
            'ພຣະອາຈານ ສຳນຽງ ບຸບຜາວົງ',
            'ພຣະອາຈານ ອິນປັ່ນ ພິລາວັນ',
            'ພຣະອາຈານ ມະຫາອຸດອນ ອະນຸຕຕະໂຣ',
            'ພຣະອາຈານ ແກ້ວພະຈັນ ຍົດປັນຍາ',
            'ພຣະອາຈານ ອາໂນຊາ ໄມຕີຈິດ',
            'ພຣະອາຈານ ປອ.ໄພວັນ ມາລາວົງ',
            'ພຣະອາຈານ ປອ. ຂັນໄຊ ສີສຸດາ',
            'ພຣະອາຈານ ດາດສະດາ ຊາຕິກະເມທີ',
            'ພຣະອາຈານ ດວງຈັນ ວົງພາສີ',
            'ພຣະອາຈານ ບຸນທຳ ບຸນວົງ',
            'ພຣະອາຈານ ຄຳແພງເພັດ ຄູນປະເສີດ',
            'ພຣະອາຈານ ຄຳຜຸຍ ບຸບຜາພອນ',
            'ພຣະອາຈານ ຈັນທະລິນ ອຸດົມສຸກ',
            'ພຣະອາຈານ ບົວວັນ ປຸນຍະສາໂຣ',
            'ພຣະອາຈານ ບຸນທຽນ ປັນຍາແກ້ວ',
            'ພຣະອາຈານ ສົມພອນ ຄຳຈະເລີນ',
            'ພຣະອາຈານ ລຳພັນ ບຸນມີໄຊ',
            'ພຣະອາຈານ ວັນສົມ ບຸດດາວົງ',
            'ພຣະອາຈານ ວັດຊະຣະ ຂັດຕິຍະ',
            'ພຣະອາຈານ ປອ. ໄຊຍະພອນ ສີປັນຍາ',
            'ພຣະອາຈານ ຕຸ່ງ ຈັນທະວົງ',
            'ພຣະອາຈານ ຄຳປັ່ນ ອິນທະວົງ',
            'ພຣະອາຈານ ຫວາງ ພົມມະວົງ',
            'ພຣະອາຈານ ພອນທິບ ເພັງພົມມະຈັນ',
            'ພຣະອາຈານ ປານ ສຸພັນໂທ',
            'ພຣະອາຈານ ແກ້ວອຸດອນ ໂຊຕິທັມໂມ',
            'ພຣະອາຈານ ຈັນດາ ເຄນສັກດາ',
            'ພຣະອາຈານ ຮສ.ປອ. ຄຳຕູ້ ອິນທະວົງ',
            'ພຣະອາຈານ ໂສພາ ປັດໂຊໂຕ',
            'ພຣະອາຈານ ບຸນໂຖ້ຍ ໄຊຍະບຸດ',
            'ພຣະອາຈານ ສີສະຫວາດ ເທບວົງສາ',
            'ພຣະອາຈານ ວຽງສັນ ມະນີຣັດ',
            'ພຣະອາຈານ ພົມມາ ເຮືອງສະໝອນ',
            'ພຣະອາຈານ ແຫ້ວ ລັດສະໝີ',
            'ພຣະອາຈານ ສຸບິນ ແກ້ວປະເສີດ',
        ];

        // Insert Presidents
        foreach ($presidents as $p) {
            CbfolCommittee::create(array_merge($p, [
                'term' => $term,
                'term_year' => $termYear,
                'status' => 'active',
            ]));
        }

        // Insert Standing Committee
        $sortOrder = 1;
        foreach ($standingList as $name) {
            // Find existing if already inserted as president
            $existing = CbfolCommittee::where('name', $name)->first();
            $photo = $existing?->photo;

            CbfolCommittee::create([
                'name' => $name,
                'title' => 'ຄະນະບໍລິຫານງານປະຈຳການ',
                'category' => CbfolCommittee::CATEGORY_STANDING,
                'term' => $term,
                'term_year' => $termYear,
                'photo' => $photo,
                'sort_order' => $sortOrder++,
                'status' => 'active',
                'bio' => 'ຄະນະບໍລິຫານງານປະຈຳການ ສູນກາງ ອພສ ສະໄໝທີ IX',
            ]);
        }

        // Insert Advisors
        $sortOrder = 1;
        foreach ($advisors as $name) {
            CbfolCommittee::create([
                'name' => $name,
                'title' => 'ຄະນະທີ່ປຶກສາ',
                'category' => CbfolCommittee::CATEGORY_ADVISOR,
                'term' => $term,
                'term_year' => $termYear,
                'sort_order' => $sortOrder++,
                'status' => 'active',
                'bio' => 'ຄະນະທີ່ປຶກສາ ສູນກາງ ອພສ ສະໄໝທີ IX',
            ]);
        }

        // Insert Committee Members
        $sortOrder = 1;
        foreach ($committeeMembers as $name) {
            CbfolCommittee::create([
                'name' => $name,
                'title' => 'ຄະນະກຳມະການ',
                'category' => CbfolCommittee::CATEGORY_MEMBER,
                'term' => $term,
                'term_year' => $termYear,
                'sort_order' => $sortOrder++,
                'status' => 'active',
                'bio' => 'ຄະນະກຳມະການບໍລິຫານງານ ສູນກາງ ອພສ ສະໄໝທີ IX',
            ]);
        }
    }
}
