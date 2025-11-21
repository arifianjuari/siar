<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class LoginUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:login {email : Email pengguna yang akan login} {--expire=30 : Waktu kedaluwarsa token dalam menit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat URL login khusus untuk user tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $expire = $this->option('expire');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan");
            return Command::FAILURE;
        }

        // Tambah rute khusus untuk login otomatis
        $token = Str::random(64);
        $signature = hash_hmac('sha256', $token . $user->id . $email, config('app.key'));

        // Simpan token login sementara di session
        \Illuminate\Support\Facades\Cache::put('login_token_' . $signature, [
            'user_id' => $user->id,
            'email' => $email,
            'created_at' => now(),
        ], now()->addMinutes($expire));

        // Generate URL login
        $url = URL::to('/autologin/' . $signature);

        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Role: " . ($user->role ? $user->role->name : 'No role'));
        $this->info("Tenant: " . ($user->tenant ? $user->tenant->name : 'No tenant'));
        $this->info("Login URL (valid for {$expire} minutes):");
        $this->newLine();
        $this->line($url);
        $this->newLine();
        $this->info("Tambahkan route berikut di routes/web.php (jika belum ada):");
        $this->newLine();
        $this->line('Route::get(\'/autologin/{token}\', function ($token) {
    $data = \Illuminate\Support\Facades\Cache::get(\'login_token_\' . $token);
    
    if (!$data) {
        return redirect()->route(\'login\')->with(\'error\', \'Token login tidak valid atau sudah kedaluwarsa\');
    }
    
    $user = \App\Models\User::find($data[\'user_id\']);
    
    if (!$user || $user->email !== $data[\'email\']) {
        return redirect()->route(\'login\')->with(\'error\', \'User tidak ditemukan\');
    }
    
    auth()->login($user);
    \Illuminate\Support\Facades\Cache::forget(\'login_token_\' . $token);
    
    return redirect()->route(\'dashboard\');
})->middleware(\'web\')->name(\'autologin\');');

        return Command::SUCCESS;
    }
}
