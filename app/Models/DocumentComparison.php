<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentComparison extends Model
{
    protected $fillable = [
        'report_filename',
        'report_path',
        'proposal_filename',
        'proposal_path',
        'status',
        'overall_score',
        'summary',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'overall_score' => 'decimal:2',
    ];

    public function mismatches()
    {
        return $this->hasMany(ComparisonMismatch::class, 'comparison_id');
    }
}
