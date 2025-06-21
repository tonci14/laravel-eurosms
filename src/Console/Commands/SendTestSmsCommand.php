<?php

namespace Tonci14\LaravelEuroSMS\Console\Commands;

use Illuminate\Console\Command;
use Tonci14\LaravelEuroSMS\Facades\EuroSms;

class SendTestSmsCommand extends Command
{
    protected $signature = 'sms:send-test {phone : Telefónne číslo vo formáte E.164}';

    protected $description = 'Pošli testovaciu SMS správu na zadané číslo pomocou EuroSms';

    public function handle(): int
    {
        $phone = $this->argument('phone');

        try {
            EuroSms::send($phone, 'Testovacia SMS zo systému.');
            $this->info("SMS odoslaná na $phone");
        } catch (\Throwable $e) {
            $this->error("Chyba pri odosielaní: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
