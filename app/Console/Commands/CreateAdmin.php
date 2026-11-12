<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {--name=Administrator} {--generate-password : Generate a secure temporary password}';

    protected $description = 'Create an administrator using a securely prompted password';

    public function handle(): int
    {
        $password = $this->option('generate-password') ? Str::password(24) : $this->secret('Password (at least 12 characters)');
        $data = ['email' => $this->argument('email'), 'name' => $this->option('name'), 'password' => $password];
        $validator = Validator::make($data, ['email' => ['required', 'email', 'unique:users,email'], 'name' => ['required', 'string', 'max:120'], 'password' => ['required', 'string', 'min:12']]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $user = new User($data);
        $user->role = 'admin';
        $user->save();
        $this->info('Administrator created. Sign in at /admin/login.');
        if ($this->option('generate-password')) {
            $this->line('Temporary password: '.$password);
        }

        return self::SUCCESS;
    }
}
