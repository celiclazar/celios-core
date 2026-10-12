<?php

namespace Celios\Core\Services\Security;

use Illuminate\Http\UploadedFile;

interface FileScannerInterface
{
    /**
     * Scan an uploaded file or path for malware and viruses.
     *
     * @param  UploadedFile|string  $file
     * @return bool True if clean
     *
     * @throws \Celios\Core\Exceptions\MalwareDetectedException If malware/virus is detected.
     */
    public function scan(UploadedFile|string $file): bool;
}
