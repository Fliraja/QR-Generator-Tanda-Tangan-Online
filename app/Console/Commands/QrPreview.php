<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;

class QrPreview extends Command
{
    protected $signature = 'qr:preview';
    protected $description = 'Generate QR contoh (dengan logo & margin terbaru) ke public/qr-preview.png buat dicek cepat di browser';

    public function handle(): int
    {
        $qrCode = new QrCode(
            data: url('/verify/preview-test-uuid'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            backgroundColor: new Color(255, 255, 255),
        );

        $logo = new Logo(
            path: public_path('qr-logo.png'),
            resizeToWidth: 70,
            punchoutBackground: true,
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode, $logo);

        $outputPath = public_path('qr-preview.png');
        file_put_contents($outputPath, $result->getString());

        $this->info('QR preview dibuat: ' . $outputPath);
        $this->info('Buka di browser: ' . url('/qr-preview.png') . ' (tambahin ?v=' . time() . ' kalau browser nge-cache)');

        return self::SUCCESS;
    }
}
