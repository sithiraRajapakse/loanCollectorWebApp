<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = ['date', 'loan_number', 'customer_id', 'scheme_id',
        'start_date', 'end_date', 'loan_amount', 'interest_rate', 'interest_total',
        'no_of_installments', 'installment_amount', 'last_installment_type',
        'completed_at', 'completed_by_id', 'created_by_id', 'is_extended', 'extended_date', 'total_arrears'];

    /**
     * @return BelongsTo
     */
    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany
     */
    public function loanInstallments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function arrearsTotal() {
        return $this->loanInstallments->pluck('arrears_amount')->sum();
    }

}
