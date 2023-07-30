<?php

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class AssistantProductSeeder extends Seeder
{

    const BRANDS = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        //php artisan db:seed --force --class=AssistantProductSeeder

        $config = config('modules-assistant-seeder.seeders.seeds.products', []);

        if (empty($config)) {
            exit(0);
        }

        dd($config);

        $multiTenant = env('DORCAS_EDITION', 'business') != 'business';

        if (!$multiTenant) {
            $defaultUsers = User::with('company')->first();
        } else {
            $defaultUsers = User::with('company')->orderBy('created_at','asc')->get(); //->where('is_partner', 0)
        }

        if (!empty($config["users"]) && $multiTenant ) {
            $defaultUsers->take($config["users"]);
        }

        
        foreach ($defaultUsers as $user) {

            $faker = Faker::create();

            $company = $user->company;
            # get the company

            $categories = $company->productCategories();
            # check if categories exist

            if (empty($categories->count())) {
                $category_name = "Default";
                $slug = $company->id . '-' . Str::slug($category_name);
                # set the slug
                if (ProductCategory::where('slug', $slug)->count() > 0) {
                    $slug .= '-' . uniqid();
                }
                $category = $company->productCategories()->create([
                    'name' => $category_name,
                    'slug' => $slug,
                    'description' => 'Default Product Category'
                ]);
            } else {
                $category = $categories->first();
            }
            dd($category);

            // Create Products
            for ($i = 0; $i <= $config["count"]; $i++) {

                $amount = rand(2000, 10000);
                $stock = rand(4, 20);
                $image = $faker->imageUrl(360, 360, 'electronics', true, 'cats');

                $product = $company->products()->create([
                    'name' => $faker->word,
                    'description' => $faker->sentence,
                    'product_type' => 'default',
                    'unit_price' => $amount
                ]);
                # create product

                $productPrices = collect([]);
                # product price container

                $productPrices[] = ['currency' => env('SETTINGS_CURRENCY', 'NGN'), 'unit_price' => $amount];
                # add the price to the array

                $product->prices()->createMany($productPrices);
                # update product price

                $categories = ProductCategory::where('uuid', $category->uuid)->pluck('id');

                $product->categories()->sync($categories);
                # update product category

                $product->stocks()->create(['action' => 'add', 'quantity' => $stock, 'comment' => 'Default Stock']);
                # update product stock

                $product->update(['inventory' => $stock]);
                # update product stock

                if (!empty($image)) {
                    $product->images()->create(['url' => $image]);
                }
                # update product image

            }

            

        }

        
   
    }
}