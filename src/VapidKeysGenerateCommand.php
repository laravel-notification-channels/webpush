<?php

namespace NotificationChannels\WebPush;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;
use Minishlink\WebPush\VAPID;

class VapidKeysGenerateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * @var string
     */
    protected $signature = 'webpush:vapid
                        {--show : Display the keys instead of modifying files}
                        {--force : Force the operation to run when in production}';

    /**
     * @var string
     */
    protected $description = 'Generate VAPID keys.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $keys = VAPID::createVapidKeys();

        if ($this->option('show')) {
            $this->line('<comment>VAPID_PUBLIC_KEY='.$keys['publicKey'].'</comment>');
            $this->line('<comment>VAPID_PRIVATE_KEY='.$keys['privateKey'].'</comment>');

            return;
        }

        if (! $this->setKeysInEnvironmentFile($keys)) {
            return;
        }

        $this->info('VAPID keys set successfully.');
    }

    /**
     * Set the keys in the environment file.
     *
     * @param  array $keys
     * @return bool
     */
    protected function setKeysInEnvironmentFile($keys)
    {
        $currentKeys = $this->laravel['config']['webpush.vapid'];

        if (strlen($currentKeys['public_key']) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($keys);

        return true;
    }

    /**
     * Write a new environment file with the given keys.
     *
     * @param  array $keys
     * @return void
     */
    protected function writeNewEnvironmentFileWith($keys)
    {
        $contents = file_get_contents($this->laravel->environmentFilePath());

        if (! Str::contains($contents, 'VAPID_PUBLIC_KEY')) {
            $contents .= PHP_EOL.'VAPID_PUBLIC_KEY=';
        }

        if (! Str::contains($contents, 'VAPID_PRIVATE_KEY')) {
            $contents .= PHP_EOL.'VAPID_PRIVATE_KEY=';
        }

        $contents = preg_replace(
            [
                $this->keyReplacementPattern('VAPID_PUBLIC_KEY'),
                $this->keyReplacementPattern('VAPID_PRIVATE_KEY'),
            ],
            [
                'VAPID_PUBLIC_KEY='.$keys['publicKey'],
                'VAPID_PRIVATE_KEY='.$keys['privateKey'],
            ],
            $contents
        );

        file_put_contents($this->laravel->environmentFilePath(), $contents);
    }

    /**
     * Get a regex pattern that will match env $keyName with any key.
     *
     * @param  string $keyName
     * @return string
     */
    protected function keyReplacementPattern($keyName)
    {
        $key = $this->laravel['config']['webpush.vapid'];

        if ($keyName === 'VAPID_PUBLIC_KEY') {
            $key = $key['public_key'];
        } else {
            $key = $key['private_key'];
        }

        $escaped = preg_quote('='.$key, '/');

        return "/^{$keyName}{$escaped}/m";
    }
}
