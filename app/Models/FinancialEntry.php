<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialEntry extends Model
{
    public const TYPES = [
        'income' => 'Income',
        'expense' => 'Expense',
        'expense_by_member' => 'Expense paid by member',
        'settle_pay' => 'Due paid to person',
        'settle_receive' => 'Due received from person',
        'membership' => 'Membership fee',
        'event' => 'Event ticket',
    ];

    protected $fillable = [
        'type',
        'entry_date',
        'amount',
        'zybra_account_id',
        'zybra_account_name',
        'zybra_party_id',
        'party_name',
        'member_id',
        'zybra_voucher_type',
        'zybra_voucher_id',
        'zybra_voucher_number',
        'reference',
        'description',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
