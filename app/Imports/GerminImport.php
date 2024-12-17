<?php

namespace App\Imports;

use App\Models\TcGerminBottle;
use App\Models\TcGerminOb;
use App\Models\TcGerminObDetail;
use App\Models\TcGerminTransaction;
use App\Models\TcGerminTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class GerminImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $qTcGerminBottleId = TcGerminBottle::select('id')->orderBy('id','desc')->first();
        $lastTcGerminBottleId = is_null($qTcGerminBottleId)?1:$qTcGerminBottleId->id+1;

        $qTcGerminObsId = TcGerminOb::select('id')->orderBy('id','desc')->first();
        $lastTcGerminObsId = is_null($qTcGerminBottleId)?1:$qTcGerminObsId->id+1;

        // dd($lastTcGerminBottleId);

        foreach ($rows as $key => $value) {
            if ($key != 0) {
                $dtTcGerminBottle[] = [
                    'id' => $lastTcGerminBottleId++,
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => 0,
                    'tc_laminar_id' => 0,
                    'tc_bottle_id' => $value[1],
                    'sub' => 1,
                    'type' => 'Suspension',
                    'alpha' => 'A',
                    'bottle_count' => $value[2],
                    'status' => 1,
                    'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcGerminObs[] = [
                    'id' => $lastTcGerminObsId++,
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => 0,
                    'alpha' => 'A',
                    'total_bottle_germin' => $value[4],
                    'total_bottle_oxidate' => $value[5],
                    'total_bottle_contam' => $value[6],
                    'total_bottle_other' => $value[7],
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcGerminObsDetail[] = [
                    'tc_init_id' => $value[0],
                    'tc_germin_ob_id' => $lastTcGerminObsId-1,
                    'tc_germin_bottle_id' => $lastTcGerminBottleId-1,
                    'bottle_germin' => $value[4],
                    'bottle_oxidate' => $value[5],
                    'bottle_contam' => $value[6],
                    'bottle_other' => $value[7],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcGerminTransferBottle[] = [
                    'tc_init_id' => $value[0],
                    'tc_germin_ob_id' => $lastTcGerminObsId-1,
                    'tc_germin_bottle_id' => $lastTcGerminBottleId-1,
                    'bottle_germin' => $value[4],
                    'bottle_left' => $value[4],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcGerminObsTemp[] = [
                    'id' => $lastTcGerminObsId++,
                    'tc_init_id' => $value[0],
                    'total_bottle_germin' => 0,
                    'total_bottle_oxidate' => 0,
                    'total_bottle_contam' => 0,
                    'total_bottle_other' => 0,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $dtTcGerminTransaction[] = [
                    'tc_init_id' => $value[0],
                    'tc_germin_ob_id' => $lastTcGerminObsId-2,
                    'tc_germin_bottle_id' => $lastTcGerminBottleId-1,
                    'tc_worker_id' => 0,
                    'first_total' => $value[2],
                    'last_total' => $value[2] - ($value[5]+$value[6]+$value[7]),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

            }

        }
        DB::unprepared('SET IDENTITY_INSERT tc_germin_bottles ON');
        DB::table('tc_germin_bottles')->insert($dtTcGerminBottle);
        DB::unprepared('SET IDENTITY_INSERT tc_germin_bottles OFF');

        DB::unprepared('SET IDENTITY_INSERT tc_germin_obs ON');
        DB::table('tc_germin_obs')->insert($dtTcGerminObs);
        DB::table('tc_germin_obs')->insert($dtTcGerminObsTemp);
        DB::unprepared('SET IDENTITY_INSERT tc_germin_obs OFF');

        TcGerminObDetail::insert($dtTcGerminObsDetail);
        TcGerminTransaction::insert($dtTcGerminTransaction);
        TcGerminTransferBottle::insert($dtTcGerminTransferBottle);
    }
}
