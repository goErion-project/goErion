<?php

namespace Database\Seeders;

use App\Exceptions\RequestException;
use App\Marketplace\Encryption\Keypair;
use App\Models\Admin;
use App\Models\User;
use App\Models\VendorPurchase;
use Carbon\Carbon;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Faker\Factory;
use Faker\Generator;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    private int $numberOfAccounts = 50;
    private Generator $fakerFactory;
    private int $createdAccounts = 0;

    public function __construct() {
        $this->fakerFactory = Factory::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws RequestException
     * @throws EnvironmentIsBrokenException
     * @throws \SodiumException
     * @throws \Throwable
     */
    public function run(): void
    {
        $start = microtime(true);
        $this->command->info('Creating users...');

        // if there is no buyer, create it
        $buyer = User::where('username','buyer')->first();
        if ($buyer == null){
            $this->command->info('There is no [buyer] account, creating it...');
            $this->generateBuyerAccount();
        } else {
            $this->command->info('Account [buyer] is present');
        }

        // if there is no admin, create it
        $admin = User::where('username','admin')->first();
        if ($admin == null){
            $this->command->info('There is no [admin] account, creating it...');
            $this->generateAdminAccount();
        } else {
            $this->command->info('Account [admin] is present');
        }

        $this->command->info('Starting generation of random accounts...');
        for ($i = 0; $i < $this->numberOfAccounts; $i++){
            $user = new User();
            $username =  $this->generateUsername();
            $user->username = $username;
            $userpassword = Hash::make($username.'123');
            $user->password = $userpassword;
            $user->mnemonic = Hash::make(hash('sha256',$username));
            $user->login_2fa = false;
            $user->referral_code = strtoupper(Str::random(6));
            $userKeyPair =  new Keypair();
            $userPrivateKey = $userKeyPair->getPrivateKey();
            $userPublicKey = $userKeyPair->getPublicKey();
            $userEncryptedPrivateKey = Crypto::encryptWithPassword($userPrivateKey, $userpassword);

            $user->msg_private_key = $userEncryptedPrivateKey;
            $user->msg_public_key = encrypt($userPublicKey);
            $user->pgp_key = 'test';
            $user->save();

            // generate deposit addresses for every user
            $this->generateDepositAddressSeed($user);//$user -> generateDepositAddresses();
            // every fifth user is vendor
           if ($i % 5 == 0){
               $user->becomeVendor('testAddress#'.strtoupper(Str::random(6)));
           }
            $this->command->info('Created User '.($i+1).'/'.$this->numberOfAccounts);
            $this->createdAccounts++;
        }
        $end = (microtime(true) - $start);
        $this->command->info('Successfully generated '.$this->createdAccounts.' users. Elapsed time: '.$this->formatTime($end));

    }

    /**
     * Generate admin account
     *
     * @throws EnvironmentIsBrokenException
     * @throws RequestException
     * @throws \SodiumException
     * @throws \Throwable
     */
    public function generateAdminAccount(): void
    {
        $adminpassword = 'admin123';
        $admin = new User();
        $admin->username = 'admin';
        $admin->password = Hash::make($adminpassword);
        $admin->mnemonic = Hash::make(hash('sha256', "na kraj sela zuta kuca"));
        $admin->login_2fa = false;
        $admin->referral_code = "UUF7NZ";

        $adminKeyPair = new Keypair();
        $adminPrivateKey = $adminKeyPair->getPrivateKey();
        $adminPublicKey = $adminKeyPair->getPublicKey();
        $AdminEcnryptedPrivateKey = Crypto::encryptWithPassword($adminPrivateKey, $adminpassword);

        $admin->msg_private_key = $AdminEcnryptedPrivateKey;
        $admin->msg_public_key = encrypt($adminPublicKey);
        $admin->pgp_key = 'test';
        $admin->save();
        $nowTime = Carbon::now();
        Admin::query()->insert([
            'id' => $admin->id,
            'created_at' => $nowTime,
            'updated_at' => $nowTime
        ]);
        $this->generateDepositAddressSeed($admin);//$admin -> generateDepositAddresses();
        $admin->becomeVendor('test');

        $this->command->info('Created [admin] account');
        $this->createdAccounts++;
    }

    /**
     * Generate a buyer account
     *
     * @throws EnvironmentIsBrokenException
     * @throws \SodiumException
     */
    public function generateBuyerAccount(): void
    {

        $buyerpassword = 'buyer123';
        $buyer = new User();
        $buyer->username = 'buyer';
        $buyer->password = Hash::make($buyerpassword);
        $buyer->mnemonic = Hash::make(hash('sha256', "na kraj sela zuta kuca"));
        $buyer->login_2fa = false;
        $buyer->referral_code = "UUF7NZ";

        $buyerKeyPair = new Keypair();
        $buyerPrivateKey = $buyerKeyPair->getPrivateKey();
        $buyerPublicKey = $buyerKeyPair->getPublicKey();
        $buyerEcnryptedPrivateKey = Crypto::encryptWithPassword($buyerPrivateKey, $buyerpassword);

        $buyer->msg_private_key = $buyerEcnryptedPrivateKey;
        $buyer->msg_public_key = encrypt($buyerPublicKey);

        $buyer->save();

        $this->generateDepositAddressSeed($buyer);//$buyer -> generateDepositAddresses();

        $this->command->info('Created [buyer] account');
        $this->createdAccounts++;

    }

    public function generateUsername(): string
    {
        $faker = $this->fakerFactory;
        $userName = $faker->userName;
        $user = User::where('username',$userName)->first();
        while ($user !== null){
            $userName = $faker->userName;
            $user = User::where('username',$userName)->first();
        }
        return $userName;
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

    private function generateDepositAddressSeed(User $user): void
    {
        $coinsClasses = config('coins.coin_list');

        $coinsToSeed = config('marketplace.seeder_coins');

        $seederCoinsClasses = [];

        foreach ($coinsToSeed as $coin){
            $seederCoinsClasses[$coin] = $coinsClasses[$coin];
        }

        // vendor fee in usd
        $marketVendorFee =  config('marketplace.vendor_fee');


        // for each supported coin generate an instance of the coin
        foreach ($seederCoinsClasses as $short => $coinClass){
            $coinsService = new $coinClass();
            try {
                // Add a new deposit address
                $newDepositAddress = new VendorPurchase();
                $newDepositAddress->user_id = $user->id;

                $newDepositAddress->address = $coinsService->generateAddress(['user' => $user->id]);
                $newDepositAddress->coin = $coinsService->coinLabel();

                $newDepositAddress->save();
            }catch(\Exception $e){
                Log::error($e);
            }
        }
    }
}
