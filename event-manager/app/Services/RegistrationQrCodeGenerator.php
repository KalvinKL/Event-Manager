<?php

namespace App\Services;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class RegistrationQrCodeGenerator
{
    public function png(string $value): string
    {
        return (new PngWriter)->write(new QrCode(
            data: $value,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 280,
            margin: 12,
        ))->getString();
    }
}
