<?php

namespace App\Imports;

use App\Models\TcLiquidBottle;
use App\Models\TcLiquidOb;
use App\Models\TcLiquidObDetail;
use App\Models\TcLiquidTransaction;
use App\Models\TcLiquidTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class LiquidImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $qTcLiquidBottleId = TcLiquidBottle::select('id')->orderBy('id','desc')->first();
        $lastTcLiquidBottleId = is_null($qTcLiquidBottleId)?1:$qTcLiquidBottleId->id+1;

        $qTcLiquidObsId = TcLiquidOb::select('id')->orderBy('id','desc')->first();
        $lastTcLiquidObsId = is_null($qTcLiquidBottleId)?1:$qTcLiquidObsId->id+1;

        // dd($lastTcLiquidBottleId);

        foreach ($rows as $key => $value) {
            if ($key != 0) {
                $dtTcLiquidBottle[] = [
                    'id' => $lastTcLiquidBottleId++,
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => 0,
                    'tc_laminar_id' => 0,
                    'tc_bottle_id' => $value[1],
                    'sub' => 1,
                    'type' => 'Suspension',
                    'alpha' => 'A',
                    'cycle' => 0,
                    'bottle_count' => $value[2],
                    'status' => 1,
                    'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcLiquidObs[] = [
                    'id' => $lastTcLiquidObsId++,
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => 0,
                    'alpha' => 'A',
                    'cycle' => 0,
                    'total_bottle_liquid' => $value[4],
                    'total_bottle_oxidate' => $value[5],
                    'total_bottle_contam' => $value[6],
                    'total_bottle_other' => $value[7],
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcLiquidObsDetail[] = [
                    'tc_init_id' => $value[0],
                    'tc_liquid_ob_id' => $lastTcLiquidObsId-1,
                    'tc_liquid_bottle_id' => $lastTcLiquidBottleId-1,
                    'bottle_liquid' => $value[4],
                    'bottle_oxidate' => $value[5],
                    'bottle_contam' => $value[6],
                    'bottle_other' => $value[7],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcLiquidTransferBottle[] = [
                    'tc_init_id' => $value[0],
                    'tc_liquid_ob_id' => $lastTcLiquidObsId-1,
                    'tc_liquid_bottle_id' => $lastTcLiquidBottleId-1,
                    'bottle_liquid' => $value[4],
                    'bottle_left' => $value[4],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcLiquidObsTemp[] = [
                    'id' => $lastTcLiquidObsId++,
                    'tc_init_id' => $value[0],
                    'total_bottle_liquid' => 0,
                    'total_bottle_oxidate' => 0,
                    'total_bottle_contam' => 0,
                    'total_bottle_other' => 0,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $dtTcLiquidTransaction[] = [
                    'tc_init_id' => $value[0],
                    'tc_liquid_ob_id' => $lastTcLiquidObsId-2,
                    'tc_liquid_bottle_id' => $lastTcLiquidBottleId-1,
                    'tc_worker_id' => 0,
                    'first_total' => $value[2],
                    'last_total' => $value[2] - ($value[5]+$value[6]+$value[7]),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

            }

        }
        DB::unprepared('SET IDENTITY_INSERT tc_liquid_bottles ON');
        DB::table('tc_liquid_bottles')->insert($dtTcLiquidBottle);
        DB::unprepared('SET IDENTITY_INSERT tc_liquid_bottles OFF');

        DB::unprepared('SET IDENTITY_INSERT tc_liquid_obs ON');
        DB::table('tc_liquid_obs')->insert($dtTcLiquidObs);
        DB::table('tc_liquid_obs')->insert($dtTcLiquidObsTemp);
        DB::unprepared('SET IDENTITY_INSERT tc_liquid_obs OFF');

        TcLiquidObDetail::insert($dtTcLiquidObsDetail);
        TcLiquidTransaction::insert($dtTcLiquidTransaction);
        TcLiquidTransferBottle::insert($dtTcLiquidTransferBottle);
    }
}
