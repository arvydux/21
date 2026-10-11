<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class PrinterTestLithuanian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'printer:test-lt
                            {--ip=* : Printer IP address (default: kitchen and client printers)}
                            {--from=0 : First character table number to try}
                            {--to=63 : Last character table number to try}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print Lithuanian letters in every character table to find the one the printer supports';

    private const DEFAULT_PRINTERS = ['192.168.32.100', '192.168.32.101'];

    /**
     * "ąčęėįšųūž ĄČĘĖĮŠŲŪŽ" encoded in each Baltic code page.
     *
     * @var array<string, string>
     */
    private const LITHUANIAN_LETTERS = [
        '1257' => "\xE0\xE8\xE6\xEB\xE1\xF0\xF8\xFB\xFE \xC0\xC8\xC6\xCB\xC1\xD0\xD8\xDB\xDE",
        '775' => "\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7\xD8 \xB5\xB6\xB7\xB8\xBD\xBE\xC6\xC7\xCF",
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $printerIps = $this->option('ip') ?: self::DEFAULT_PRINTERS;
        $lines = $this->testLines((int) $this->option('from'), (int) $this->option('to'));

        foreach ($printerIps as $printerIp) {
            try {
                $connector = new NetworkPrintConnector($printerIp, 9100, 2);
                $printer = new Printer($connector);
                $printer->text("LT test {$printerIp}\nNr / code page: letters\n\n");
                $printer->setFont(Printer::FONT_B);
                foreach ($lines as $line) {
                    $connector->write($line);
                }
                $connector->write("\x1Bt\x00");
                $printer->feed(2);
                $printer->cut();
                $printer->close();
                $this->info("Printed on {$printerIp}");
            } catch (Exception $e) {
                $this->error("Couldn't print on {$printerIp}: ".$e->getMessage());
            }
        }

        return self::SUCCESS;
    }

    /**
     * Raw ESC/POS lines: select character table n, then print the letters encoded in each code page.
     *
     * @return list<string>
     */
    public function testLines(int $from, int $to): array
    {
        $lines = [];

        foreach (range($from, $to) as $tableNumber) {
            foreach (self::LITHUANIAN_LETTERS as $codePage => $letters) {
                $lines[] = "\x1Bt".chr($tableNumber).str_pad((string) $tableNumber, 3, ' ', STR_PAD_LEFT)." / {$codePage}: {$letters}\n";
            }
        }

        return $lines;
    }
}
