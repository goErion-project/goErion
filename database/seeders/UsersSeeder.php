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
use Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UsersSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     *
     * @return void
     * @throws EnvironmentIsBrokenException
     * @throws RequestException
     * @throws \SodiumException
     * @throws \Throwable
     */
    public function run(): void
    {
        $this->command->info('Creating admin user...');

        // Check if the admin account already exists
        $admin = User::where('username', 'admin')->first();
        if ($admin === null) {
            $this->generateAdminAccount();
        } else {
            $this->command->info('Account [admin] already exists.');
        }
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
        $adminPassword = 'admin123';
        $admin = new User();
        $admin->username = 'Admin';
        $admin->password = Hash::make($adminPassword);
        $admin->mnemonic = Hash::make(hash('sha256', "eric onyango oginga erion ke"));
        $admin->login_2fa = false;
        $admin->referral_code = "UUF7NZ";

        $adminKeyPair = new Keypair();
        $adminPrivateKey = $adminKeyPair->getPrivateKey();
        $adminPublicKey = $adminKeyPair->getPublicKey();
        $adminEncryptedPrivateKey = Crypto::encryptWithPassword($adminPrivateKey, $adminPassword);

        $admin->msg_private_key = $adminEncryptedPrivateKey;
        $admin->msg_public_key = encrypt($adminPublicKey);
        $admin->pgp_key = 'test';
        $admin->save();

        $nowTime = Carbon::now();
        Admin::insert([
            'id' => $admin->id,
            'created_at' => $nowTime,
            'updated_at' => $nowTime
        ]);

        $this->generateDepositAddressSeed($admin);
        $admin->becomeVendor('test');

        $this->command->info('Created [admin] account.');
    }

    /**
     * Generate deposit address seed for the user.
     *
     * @param User $user
     */
    private function generateDepositAddressSeed(User $user): void
    {
        $coinsClasses = config('coins.coin_list');
        $coinsToSeed = config('marketplace.seeder_coins');
        $seederCoinsClasses = [];

        foreach ($coinsToSeed as $coin) {
            $seederCoinsClasses[$coin] = $coinsClasses[$coin];
        }

        foreach ($seederCoinsClasses as $short => $coinClass) {
            $coinsService = new $coinClass();
            try {
                $newDepositAddress = new VendorPurchase();
                $newDepositAddress->user_id = $user->id;
                $newDepositAddress->address = $coinsService->generateAddress(['user' => $user->id]);
                $newDepositAddress->coin = $coinsService->coinLabel();
                $newDepositAddress->save();
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
    }
}
