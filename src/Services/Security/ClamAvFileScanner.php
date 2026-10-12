<?php

namespace Celios\Core\Services\Security;

use Celios\Core\Exceptions\MalwareDetectedException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ClamAvFileScanner implements FileScannerInterface
{
    /**
     * Scan an uploaded file or path for malware using ClamAV daemon or CLI.
     */
    public function scan(UploadedFile|string $file): bool
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! file_exists($filePath) || ! is_readable($filePath)) {
            Log::warning('[SECURITY_SCANNER] File does not exist or is unreadable for scanning', ['path' => $filePath]);

            return true;
        }

        // 1. Try scanning via ClamAV Socket / Network Daemon
        $daemonResult = $this->scanViaDaemon($filePath);
        if ($daemonResult !== null) {
            return $daemonResult;
        }

        // 2. Try scanning via clamscan CLI binary
        $binaryResult = $this->scanViaBinary($filePath);
        if ($binaryResult !== null) {
            return $binaryResult;
        }

        // 3. Fallback when ClamAV is unavailable
        $failClosed = (bool) config('security.scanner.fail_closed', false);
        if ($failClosed) {
            throw new \RuntimeException('Security antivirus scanner is unavailable and fail_closed policy is active.');
        }

        Log::info('[SECURITY_SCANNER] ClamAV daemon/binary unavailable; skipping file scan according to fail_open policy.');

        return true;
    }

    /**
     * Connect to clamd via UNIX or TCP socket and scan stream or path.
     */
    protected function scanViaDaemon(string $filePath): ?bool
    {
        $socketPath = config('security.scanner.clamav.socket', '/var/run/clamav/clamd.ctl');
        $host = config('security.scanner.clamav.host', '127.0.0.1');
        $port = (int) config('security.scanner.clamav.port', 3310);
        $timeout = (int) config('security.scanner.clamav.timeout', 10);

        $socket = null;

        // Try UNIX domain socket first
        if (file_exists($socketPath)) {
            $socket = @fsockopen('unix://'.$socketPath, -1, $errno, $errstr, $timeout);
        }

        // Fall back to TCP socket
        if (! $socket && $host && $port) {
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        }

        if (! $socket) {
            return null;
        }

        stream_set_timeout($socket, $timeout);

        // Send SCAN command
        fwrite($socket, "SCAN {$filePath}\n");
        $response = trim((string) fgets($socket));
        fclose($socket);

        if (empty($response)) {
            return null;
        }

        // Response format: "/path/to/file: OK" or "/path/to/file: Eicar-Signature FOUND"
        if (str_ends_with($response, 'OK')) {
            return true;
        }

        if (str_contains($response, 'FOUND')) {
            $virusName = trim(explode(':', $response)[1] ?? 'Unknown');
            $virusName = str_replace('FOUND', '', $virusName);
            $virusName = trim($virusName);

            Log::alert('[SECURITY_AUDIT] Malware detected by ClamAV daemon', [
                'file' => basename($filePath),
                'virus' => $virusName,
                'raw_response' => $response,
            ]);

            throw new MalwareDetectedException("Malware detected in uploaded file: {$virusName}", $virusName);
        }

        return null;
    }

    /**
     * Execute clamscan CLI binary.
     */
    protected function scanViaBinary(string $filePath): ?bool
    {
        $binary = config('security.scanner.clamav.binary_path', '/usr/bin/clamscan');

        if (! file_exists($binary) || ! is_executable($binary)) {
            return null;
        }

        $command = escapeshellcmd($binary).' --no-summary '.escapeshellarg($filePath);
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        // 0 = Clean, 1 = Infected, 2 = Error
        if ($returnCode === 0) {
            return true;
        }

        if ($returnCode === 1) {
            $rawOutput = implode(' ', $output);
            Log::alert('[SECURITY_AUDIT] Malware detected by clamscan CLI', [
                'file' => basename($filePath),
                'output' => $rawOutput,
            ]);

            throw new MalwareDetectedException('Malware detected in uploaded file: '.$rawOutput);
        }

        return null;
    }
}
