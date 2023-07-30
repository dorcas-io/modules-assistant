<?php


use Illuminate\Database\Seeder;

class AssistantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //php artisan db:seed --force --class=AssistantSeeder
        $this->call(AssistantProductSeeder::class);
    }
}
