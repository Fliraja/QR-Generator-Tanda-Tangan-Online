<?php

namespace App\Http\Controllers;

use App\Models\Signer;
use App\Models\QrGeneration;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrGenerationController extends Controller
{
    public function create()
    {
        $signers = Signer::where('is_active', true)->orderBy('name')->get();
        return view('qr.create', compact('signers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'signer_id' => 'required|exists:signers,id,is_active,1',
            'letter_number' => 'required|string|max:100',
            'perihal' => 'required|string|max:255',
        ]);

        $uuid = Str::uuid();

        QrGeneration::create([
            'uuid' => $uuid,
            'signer_id' => $validated['signer_id'],
            'letter_number' => $validated['letter_number'],
            'perihal' => $validated['perihal'],
            'generated_by' => auth()->id(),
            'ip_address' => $request->ip(),
        ]);

        $qrCode = new QrCode(
            data: url('/verify/' . $uuid),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
        );

        $logo = new Logo(
            path: public_path('icon.png'),
            resizeToWidth: 60,
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode, $logo);

        return response($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qr-ttd-' . $uuid . '.png"',
        ]);
    }
}
