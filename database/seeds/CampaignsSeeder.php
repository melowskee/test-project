<?php
use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaigns = [
            [
                'id'                => 1,
                'name'              => 'Brass Rabbit',
                'description'       => 'Brass Rabbit',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]
            ];

        DB::table('campaigns')->insert($campaigns);
    }
}
