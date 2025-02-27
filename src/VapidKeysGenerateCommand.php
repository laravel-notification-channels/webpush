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
     */
    public function handle(): void
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
     * @param  array{'publicKey': string, 'privateKey': string}  $keys
     */
    protected function setKeysInEnvironmentFile(array $keys): bool
    {
        $currentKeys = $this->laravel['config']['webpush.vapid'];

        if (strlen((string) $currentKeys['public_key']) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($keys);

        return true;
    }

    /**
     * Write a new environment file with the given keys.
     *
     * @param  array{'publicKey': string, 'privateKey': string}  $keys
     */
    protected function writeNewEnvironmentFileWith(array $keys): void
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
     */
    protected function keyReplacementPattern(string $keyName): string
    {
        $key = $this->laravel['config']['webpush.vapid'];

        $key = $keyName === 'VAPID_PUBLIC_KEY' ? $key['public_key'] : $key['private_key'];

        $escaped = preg_quote('='.$key, '/');

        return sprintf('/^%s%s/m', $keyName, $escaped);
    }
}
