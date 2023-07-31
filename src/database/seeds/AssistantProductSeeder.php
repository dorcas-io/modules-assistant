<?php

namespace Database\Seeders;

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

        $db = DB::connection('core_mysql');

        $multiTenant = env('DORCAS_EDITION', 'business') != 'business';

        if (!$multiTenant) {
            $defaultUsers = $db->table("users")->where('is_employee', 0)->first();
        } else {
            $defaultUsers = $db->table("users")->where('is_employee', 0)->orderBy('created_at','asc')->get();
        }

        if (!empty($config["users"]) && $multiTenant ) {
            $defaultUsers->take($config["users"]);
        }
        
        foreach ($defaultUsers as $user) {

            $faker = Faker::create();

            $company_id = $user->company_id;
            # get the company

            $categories = $db->table("product_categories")->get();
            # check if categories exist

            if (empty($categories->count())) {

                $category_name = "Default";
                $slug = $company_id . '-' . Str::slug($category_name);
                # set the slug
                if ($db->table("product_categories")->where('slug', $slug)->count() > 0) {
                    $slug .= '-' . uniqid();
                }
                $category_id = $db->table("product_categories")->insertGetId([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'company_id' => $company_id,
                    'name' => $category_name,
                    'slug' => $slug,
                    'description' => 'Default Product Category',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            } else {
                $category = $categories->first();
                $category_id = $category->id;
            }

            $products = $db->table("products")->get();

            // Create Products
            for ($i = 0; $i <= $config["count"]; $i++) {

                $amount = rand(2000, 10000);
                $stock = rand(4, 20);
                //$image = $faker->imageUrl(360, 360, 'electronics', true, 'cats');
                $image = null;

                $product_id = $db->table("products")->insertGetId([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'company_id' => $company_id,
                    'name' => "Sample Product " . $products->count() + $i + 1,
                    'description' => "Sample Description",
                    'product_type' => 'default',
                    'unit_price' => $amount,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                # create product

                $productPrices = collect([]);
                # product price container

                $productPrices[] = ['currency' => env('SETTINGS_CURRENCY', 'NGN'), 'unit_price' => $amount];
                # add the price to the array

                $productPrices->each(function ($item, $key) use($db, $product_id) {
                    $db->table("product_prices")->insert([
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'product_id' => $product_id,
                        'currency' => $item["currency"],
                        'unit_price' => $item["unit_price"],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                });
                # update product price

                $db->table("product_category")->insert([
                    'product_id' => $product_id,
                    'product_category_id' => $category_id
                ]);
                # update product category

                $db->table("product_stocks")->insert([
                    'product_id' => $product_id,
                    'action' => 'add',
                    'quantity' => $stock,
                    'comment' => 'Default Stock',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                # update product stock

                $db->table("products")->where('id', $product_id)->update(['inventory' => $stock]);
                # update product stock

                if (!empty($image)) {
                    //$product->images()->create(['url' => $image]);
                    $db->table("product_images")->insert([
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'product_id' => $product_id,
                        'url' => $image,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
                # update product image

            }

            

        }

        
   
    }
}