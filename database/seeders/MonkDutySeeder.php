<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Monk;
use App\Models\DutySchedule;

class MonkDutySeeder extends Seeder
{
    public function run(): void
    {
        // Clear old data (cascades to duty_schedules and absences)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DutySchedule::truncate();
        Monk::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // day_of_week: 1=ວັນຈັນ  2=ວັນອັງຄານ  3=ວັນພຸດ
        //              4=ວັນພະຫັດ 5=ວັນສຸກ     6=ວັນເສົາ  7=ວັນອາທິດ
        // [type, name, surname]

        $schedule = [
            1 => [ // ວັນຈັນ
                ['monk',   'ແສງ',     'ມະນີ'],
                ['monk',   'ຕຸ້ຍ',    ''],
                ['novice', 'ເສືອ',    ''],
                ['novice', 'ບ໊ອບບີ້', ''],
                ['novice', 'ໂຈ',      ''],
                ['novice', 'ຊື່ນ',    'ມະໂນ'],
                ['novice', 'ລັດ',     ''],
            ],
            2 => [ // ວັນອັງຄານ
                ['monk',   'ຫຼ້າ',    ''],
                ['monk',   'ເນບ',     ''],
                ['novice', 'ໂອດີ',   ''],
                ['novice', 'ສົມ',     'ບູນ'],
                ['novice', 'ນິ',      'ພົນ'],
                ['novice', 'ເຈີຣີ້', ''],
            ],
            3 => [ // ວັນພຸດ
                ['monk',   'ບຸນ',    'ຄົງ'],
                ['monk',   'ທອງ',    'ລຸນ'],
                ['novice', 'ພິລຸດ',  ''],
                ['novice', 'ສຸ',     'ກັນ'],
                ['novice', 'ສອນ',    ''],
                ['novice', 'ໂຢດ',    ''],
                ['novice', 'ໄກ',     'ສອນ'],
            ],
            4 => [ // ວັນພະຫັດ
                ['monk',   'ທ່ອນ',   'ຈັນ'],
                ['monk',   'ຄຳ',     'ປະເສີດ'],
                ['novice', 'ຈາມ',    ''],
                ['novice', 'ສຳ',     'ລວຍ'],
                ['novice', 'ແບ້ງ',   ''],
                ['novice', 'ຄູນ',    ''],
                ['novice', 'ເງິນ',   ''],
            ],
            5 => [ // ວັນສຸກ
                ['monk',   'ເພີບ',   ''],
                ['monk',   'ຄຳ',     'ເພັດ'],
                ['novice', 'ໄຊ',     'ຄຳ'],
                ['novice', 'ບຸນ',    'ແກ້ວ'],
                ['novice', 'ສຸ',     'ມິ'],
                ['novice', 'ດາວ',    ''],
                ['novice', 'ລິ',     'ໂມດ'],
            ],
            6 => [ // ວັນເສົາ
                ['monk',   'ສາຍ',    'ຝົນ'],
                ['monk',   'ດວງ',    ''],
                ['novice', 'ບຸນ',    'ຊົງ'],
                ['novice', 'ເທ້',    ''],
                ['novice', 'ທອມມີ້', ''],
                ['novice', 'ຕິ',     'ນ້ອຍ'],
            ],
            7 => [ // ວັນອາທິດ
                ['monk',   'ສຸລິ',   'ສາ'],
                ['monk',   'ກົ້ມ',   ''],
                ['monk',   'ທົງ',    ''],
                ['novice', 'ຕິ',     'ໃຫຍ່'],
                ['novice', 'ແອດດີ້', ''],
                ['novice', 'ພິນ',    ''],
                ['novice', 'ລຸຍ',    ''],
            ],
        ];

        foreach ($schedule as $day => $members) {
            foreach ($members as [$type, $name, $surname]) {
                $monk = Monk::create([
                    'name'    => $name,
                    'surname' => $surname,
                    'type'    => $type,
                    'pansa'   => 0,
                    'status'  => 'active',
                ]);

                DutySchedule::create([
                    'monk_id'       => $monk->id,
                    'schedule_type' => 'weekly',
                    'day_of_week'   => $day,
                    'duty_name'     => 'ຈຸດໂດຍ',
                    'duty_date'     => null,
                    'description'   => null,
                ]);
            }
        }
    }
}
