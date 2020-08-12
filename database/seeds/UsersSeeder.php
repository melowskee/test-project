<?php
use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [

            [
                'name'              => 'Test Ting',
                'email'             => 'admin@test-project.local',
                'password'          => bcrypt('1234'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
        ];

        DB::table('users')->insert($users);
    }
}
