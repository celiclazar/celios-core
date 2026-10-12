<?php

namespace Celios\Core\Services\Security;

use Illuminate\Http\UploadedFile;

class NullFileScanner implements FileScannerInterface
{
    /**
     * Pass-through scanner used for local development and test environments.
     */
    public function scan(UploadedFile|string $file): bool
    {
        return true;
    }
}
