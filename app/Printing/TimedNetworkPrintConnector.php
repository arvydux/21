<?php

namespace App\Printing;

use Exception;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

/**
 * A network printer connection that gives up when the printer stops taking data,
 * so a printer that is out of paper or stuck can't freeze the till.
 */
class TimedNetworkPrintConnector extends NetworkPrintConnector
{
    public function __construct(string $ip, int $port = 9100, private int $timeout = 2)
    {
        parent::__construct($ip, $port, $timeout);

        stream_set_blocking($this->fp, false);
    }

    /**
     * @param  string  $data
     *
     * @throws Exception When the printer doesn't take the data in time.
     */
    public function write($data): void
    {
        if ($this->fp === false) {
            throw new Exception('PrintConnector has been closed, cannot send output.');
        }

        for ($sent = 0; $sent < strlen($data); $sent += $written) {
            $read = $except = null;
            $write = [$this->fp];
            $ready = @stream_select($read, $write, $except, $this->timeout);
            $written = $ready ? @fwrite($this->fp, substr($data, $sent, 65536)) : false;

            if ($written === false) {
                $this->finalize();

                throw new Exception('Printer stopped taking data.');
            }
        }
    }
}
