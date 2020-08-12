<?php
use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'campaign_id'       => 1,
                'name'              => 'Template 1',
                'description'       => 'Template 1',
                'content'           => 'Hi {Name},

                Do you think this would be useful for {Company}?',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            [
                'campaign_id'       => 1,
                'name'              => 'Template 2',
                'description'       => 'Template 2',
                'content'           => 'Hi {Name},

                What do you think of this, {Name}?',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ],
            ];

        DB::table('templates')->insert($templates);
    }
}
