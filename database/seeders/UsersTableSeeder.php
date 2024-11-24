<?php

namespace Database\Seeders;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Models\User;
use App\Services\WalletService;
use App\Settings\AppSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = $this->createUser(UserType::Admin, 'Owner', 'superman', '09123456789');
        (new WalletService)->deposit($admin, 10 * 100_000, TransactionName::CapitalDeposit);

        $master = $this->createUser(UserType::Master, 'Master 1', 'MK898437', '09112345678', $admin->id);
        (new WalletService)->transfer($admin, $master, 8 * 100_000, TransactionName::CreditTransfer);

        $agent_1 = $this->createUser(UserType::Agent, 'Agent 1', 'MKA898737', '09112345674', $master->id);
        (new WalletService)->transfer($master, $agent_1, 5 * 100_000, TransactionName::CreditTransfer);

        $player_1 = $this->createUser(UserType::Player, 'Player 1', 'MKP111111', '09111111111', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player_1, 30000, TransactionName::CreditTransfer);

         $player2 = $this->createUser(UserType::Player, 'Player3', 'Player003', '09111111113', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player2, 0.00, TransactionName::CreditTransfer);
        $player3 = $this->createUser(UserType::Player, 'Player4', 'Player004', '09111111114', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player3, 0.00, TransactionName::CreditTransfer);
        $player4 = $this->createUser(UserType::Player, 'Player5', 'Player005', '09111111115', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player4, 0.00, TransactionName::CreditTransfer);

         $systemWallet = $this->createUser(UserType::SystemWallet, 'SystemWallet', 'systemWallet', '09222222222');
        (new WalletService)->deposit($systemWallet, 50 * 100_0000, TransactionName::CapitalDeposit);

    }

    private function createUser(UserType $type, $name, $user_name, $phone, $parent_id = null)
    {
        return User::create([
            'name' => $name,
            'user_name' => $user_name,
            'phone' => $phone,
            'password' => Hash::make('delightmyanmar'),
            'agent_id' => $parent_id,
            'status' => 1,
            'type' => $type->value,
        ]);
    }
}