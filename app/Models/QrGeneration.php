<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrGeneration extends Model
{
    protected $fillable = ['uuid', 'signer_id', 'letter_number', 'generated_by', 'ip_address'];

    public function signer()
    {
        return $this->belongsTo(Signer::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
