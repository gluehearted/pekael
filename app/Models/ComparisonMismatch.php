<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonMismatch extends Model
{
    protected $fillable = [
        'comparison_id',
        'section',
        'report_text',
        'proposal_text',
        'similarity_score',
        'issue_description',
        'severity',
        'ai_suggestion',
    ];

    protected $casts = [
        'ai_suggestion' => 'array',
        'similarity_score' => 'decimal:2',
    ];

    public function comparison()
    {
        return $this->belongsTo(DocumentComparison::class, 'comparison_id');
    }
}
