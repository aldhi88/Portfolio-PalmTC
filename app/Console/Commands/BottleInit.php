<?php

namespace App\Console\Commands;

use App\Models\TcBottleInit;
use App\Models\TcLaminar;
use App\Models\TcMediumStock;
use App\Models\TcWorker;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BottleInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bottle-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate bottle initioation data';

    /**
     * Create a new command instance.
     *
     * @return voidl
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = [
            1 => ["keyword" => "liquid_column1", "column_name" => 'column 1', "order" => 1],
            2 => ["keyword" => "liquid_column2", "column_name" => 'column 2', "order" => 2],
            3 => ["keyword" => "matur_column1", "column_name" => 'column 1', "order" => 3],
            4 => ["keyword" => "matur_column2", "column_name" => 'column 2', "order" => 4],
            5 => ["keyword" => "germin_column1", "column_name" => 'column 1', "order" => 5],
            6 => ["keyword" => "germin_column2", "column_name" => 'column 2', "order" => 6],
            7 => ["keyword" => "rooting_column1", "column_name" => 'column 1', "order" => 7],
            8 => ["keyword" => "rooting_column2", "column_name" => 'column 2', "order" => 8],
        ];

        foreach ($data as $key => $value) {
            $keyword = $value['keyword'];
            $q = TcBottleInit::where('keyword', $keyword)->get()->count();
            if ($q == 0) {
                TcBottleInit::create($value);
            }
        }
        echo "Success, bottle initiation data has been generate.\n";

        unset($data);
        $q = TcWorker::where('id', 0)->get()->count();
        if ($q == 0) {
            $data['id'] = 0;
            $data['no_pekerja'] = 0;
            $data['code'] = "-";
            $data['name'] = "-";
            $data['date_of_birth'] = Carbon::now();
            $data['status'] = 1;
            DB::unprepared('SET IDENTITY_INSERT tc_workers ON');
            DB::table('tc_workers')->insert($data);
            DB::unprepared('SET IDENTITY_INSERT tc_workers OFF');
        }

        unset($data);
        $q = TcLaminar::where('id', 0)->get()->count();
        if ($q == 0) {
            $data['id'] = 0;
            $data['code'] = "-";
            $data['name'] = "-";
            DB::unprepared('SET IDENTITY_INSERT tc_laminars ON');
            DB::table('tc_laminars')->insert($data);
            DB::unprepared('SET IDENTITY_INSERT tc_laminars OFF');
        }

        unset($data);
        $q = TcMediumStock::where('id', 0)->get()->count();
        if ($q == 0) {
            $data['id'] = 0;
            $data['tc_bottle_id'] = 0;
            $data['tc_agar_id'] = 0;
            $data['tc_medium_id'] = 0;
            $data['tc_worker_id'] = 0;
            $data['stock'] = 0;
            DB::unprepared('SET IDENTITY_INSERT tc_medium_stocks ON');
            DB::table('tc_medium_stocks')->insert($data);
            DB::unprepared('SET IDENTITY_INSERT tc_medium_stocks OFF');
        }
    }
}
