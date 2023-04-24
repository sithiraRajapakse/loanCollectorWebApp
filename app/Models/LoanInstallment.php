<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanInstallment extends Model
{

    use SoftDeletes;

    protected $fillable = ['loan_id', 'index_number', 'due_date', 'installment_amount',
        'interest_amount', 'paid_at', 'arrears_amount', 'collector_id', 'paid_amount', 'arrears_settlement_amount', 'arrears_settled_at'];

    /**
     * @return BelongsTo
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * @return BelongsTo
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(Collector::class);
    }

}
