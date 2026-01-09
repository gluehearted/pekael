<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Kegiatan extends Model
{
    use HasFactory;
    protected $table = 'kegiatan';
    protected $guarded = [];

    // 1 Kegiatan punya BANYAK Dokumen (Proposal, Laporan, Bukti)
    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }

    // 1 Kegiatan bisa punya BANYAK Riwayat Audit (Revisi 1, Revisi 2...)
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Helper untuk mengambil dokumen spesifik
    public function getProposalAttribute()
    {
        return $this->dokumens->where('jenis_dokumen', 'PROPOSAL')->first();
    }

    public function getLaporanAttribute()
    {
        return $this->dokumens->where('jenis_dokumen', 'LAPORAN')->first();
    }
}
