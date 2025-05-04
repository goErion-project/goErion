<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DigitalProduct;
use App\Models\Image;
use App\Models\Offer;
use App\Models\PhysicalProduct;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Vendor;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private array $imagesArray = [];
    private $fakerFactory;

    /**
     * How many products to create
     * @var int
     */
    private int $productsToCreate = 100;
    /**
     * How many images to create before reusing them
     * @var int
     */
    private int $maximumAmoutOfImagesToCreate = 5;

    public function __construct() {
        $this->fakerFactory = Factory::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $start = microtime(true);
        $faker = $this->fakerFactory;
        /*
         * Categories
         * Drinks
         *      Alcoholic
         *          Beer
         *          Vine
         *      Non Alcoholic
         *          Sodas
         *          Juices
         */
        $this->command->info('Creating categories...');
        $drinks = new Category();
        $drinks -> name = 'Drinks';
        $drinks -> save();

        // first level categories
        $nonAlchoholic = new Category;
        $nonAlchoholic -> name = 'Non-Alcoholic';
        $nonAlchoholic -> parent_id = $drinks -> id;
        $nonAlchoholic -> save();

        $alcoholic = new Category;
        $alcoholic -> name = 'Alcoholic';
        $alcoholic -> parent_id = $drinks -> id;
        $alcoholic -> save();

        // second level categories
        $beer = new Category;
        $beer -> name = 'Beer';
        $beer -> parent_id = $alcoholic -> id;
        $beer -> save();

        $vine = new Category;
        $vine -> name = 'Vine';
        $vine -> parent_id = $alcoholic -> id;
        $vine -> save();

        $sodas = new Category;
        $sodas -> name = 'Sodas';
        $sodas -> parent_id = $nonAlchoholic -> id;
        $sodas -> save();

        $juices = new Category;
        $juices -> name = 'Juices';
        $juices -> parent_id = $nonAlchoholic -> id;
        $juices -> save();


        /**
         * Products
         */

        $this->command->info('Creating products...');
        for($i = 0; $i <= $this->productsToCreate; $i++){
            $newProduct = new Product;
            $newProduct -> name = $faker->streetSuffix;
            $newProduct -> description = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $newProduct -> rules = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $newProduct -> quantity = rand(1,20);
            $newProduct -> mesure = 'item';
            $newProduct -> coins = 'btc,xmr,stb';
            $newProduct -> category_id = Category::query()->inRandomOrder() -> first() -> id;
            $newProduct -> user_id = Vendor::inRandomOrder() -> first() -> id;
            if($newProduct -> user_id == null) dd(Vendor::all() -> first() -> id);
            $newProduct -> save();

            // Shipings
            $newShiping = new Shipping;
            $newShiping -> product_id = $newProduct -> id;
            $newShiping -> name = 'Shipping for ' . $newProduct -> name;
            $newShiping -> price = mt_rand() / mt_getrandmax() * 5; // max price is lower than 5$
            $newShiping -> duration = '1-2 weeks';
            $newShiping -> from_quantity = 1;
            $newShiping -> to_quantity = 30;
            $newShiping -> save();

            // Offers
            $newOffer = new Offer();
            $newOffer -> product_id = $newProduct -> id;
            $newOffer -> min_quantity = 1;
            $newOffer -> price = mt_rand() / mt_getrandmax() * 30; // max price is lower than 30
            $newOffer -> save();

            // Images
            $newImage = new Image();
            $randomImage = $this->getRandomImage();
            $newImage -> image = $randomImage;// change image here
            $newImage -> product_id = $newProduct -> id;
            $newImage -> first = true;
            $newImage -> save();

            // Physical product
            if($i % 2 == 0) {
                $newPhysical = new PhysicalProduct();
                $newPhysical -> id = $newProduct -> id;
                $newPhysical -> countries_option = 'all';
                $newPhysical -> countries = '';
                $newPhysical -> country_from = 'SRB';
                $newPhysical -> save();

            }
            // Digital product
            else{
                $newDigital = new DigitalProduct();
                $newDigital -> id = $newProduct -> id;
                $newDigital -> autodelivery = false;
                $newDigital -> content = '';
                $newDigital -> save();
            }


            $this->command->info('Created Product '.$i.'/'.$this->productsToCreate);
        }
        $end = (microtime(true) - $start);

        $this->command->info('Successfully created '.$this->productsToCreate.' products. Elapsed time: '.$this->formatTime($end));

    }

    /**
     * Check if there are X (where x is specified max mount) images in an array, if there is return random image, if not create new image and add it to the array
     *
     * @return mixed|string
     */
    private function getRandomImage(): mixed
    {

        $faker = $this->fakerFactory;
        //count images in an array;
        if (count($this->imagesArray) < $this->maximumAmoutOfImagesToCreate ) {
            // if there are less than X (where x is specified max mount) images, create a new image and put name it in an array
            $newImage = 'products/'. $faker->image(storage_path('app/public/products'),256,256, null, false);
            $this->imagesArray []= $newImage;
            return $newImage;
        } else {
            // if there are X (where x is a specified max mount) or more images
            $randIndex = array_rand($this->imagesArray);
            return $this->imagesArray[$randIndex];
        }
    }

    /**
     *  Accepts the number of seconds elapsed and returns hours:minutes:seconds
     *
     * @param $s
     * @return string
     */
    private function formatTime($s): string
    {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
    }
}
