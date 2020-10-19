<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run( )
    {
        // $this->call(UserSeeder::class);
        //$this->call(ArticlesSeeder::class );
        //$this->call(TodoSeeder::class );
        $this->call(ProductsSeeder::class );

    }
}
