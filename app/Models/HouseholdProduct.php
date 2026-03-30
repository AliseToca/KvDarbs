<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use App\Models\Household;
use App\Models\Product;
use App\Models\Unit;

class HouseholdProduct extends Model
{
    protected $table = 'household_products';

    protected $fillable = [
        'household_id',
        'product_id',
        'amount',
        'expiration_date'
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Iegūstiet derīguma termiņa sadalījumu no šodienas dienās, nedēļās un mēnešos.
     * Atgriež null, ja derīguma termiņš nav iestatīts vai tas jau ir beidzies.
     */
    public function getExpiryBreakdown(): ?array
    {
        if (!$this->expiration_date) {
            return null;
        }

        $now  = Carbon::today();
        $expiry = Carbon::parse($this->expiration_date)->startOfDay();

        if ($expiry->isToday()) {
            return [
                'expired'  => false,
                'days'     => 0,
                'weeks'    => 0,
                'months'   => 0,
                'label'    => trans('household.expiration.expires_today'),
            ];
        }

        if ($expiry->isPast()) {
            $daysExpired   = $expiry->diffInDays($now);
            $weeksExpired  = (int) floor($daysExpired / 7);
            $monthsExpired = $expiry->diffInMonths($now);

            return [
                'expired' => true,
                'days'    => $daysExpired,
                'weeks'   => $weeksExpired,
                'months'  => $monthsExpired,
                'label'   => $this->buildExpiredLabel($daysExpired, $weeksExpired, $monthsExpired),
            ];
        }

        $days   = $now->diffInDays($expiry);
        $weeks  = (int) floor($days / 7);
        $months = $now->diffInMonths($expiry);

        return [
            'expired' => false,
            'days'    => $days,
            'weeks'   => $weeks,
            'months'  => $months,
            'label'   => $this->buildExpiryLabel($days, $weeks, $months),
        ];
    }

    /**
     * Izveidojiet cilvēkam lasāmu derīguma termiņa marķējumu.
     */
    private function buildExpiryLabel(int $days, int $weeks, int $months): string
    {
        if ($days === 0) {
            return trans('household.expiration.expires_today');
        }

        if ($days <= 7) {
            return trans('household.expiration.expires_in') . ' ' . $days . ' ' . ($days > 1
                    ? trans('household.expiration.days.plural')
                    : trans('household.expiration.days.singular'));
        }

        if ($weeks <= 4) {
            return trans('household.expiration.expires_in') . ' ' . $weeks . ' ' . ($weeks > 1
                    ? trans('household.expiration.weeks.plural')
                    : trans('household.expiration.weeks.singular'));
        }

        return trans('household.expiration.expires_in') . ' ' . $months . ' ' . ($months > 1
                ? trans('household.expiration.months.plural')
                : trans('household.expiration.months.singular'));
    }

    private function buildExpiredLabel(int $days, int $weeks, int $months): string
    {
        if ($days === 0) {
            return trans('household.expiration.expires_today');
        }

        if ($days <= 7) {
            return trans('household.expiration.expired_ago') . ' ' . $days . ' ' . ($days > 1
                    ? trans('household.expiration.days.plural')
                    : trans('household.expiration.days.singular'));
        }

        if ($weeks <= 4) {
            return trans('household.expiration.expired_ago') . ' ' . $weeks . ' ' . ($weeks > 1
                    ? trans('household.expiration.weeks.plural')
                    : trans('household.expiration.weeks.singular'));
        }

        return trans('household.expiration.expired_ago') . ' ' . $months . ' ' . ($months > 1
                ? trans('household.expiration.months.plural')
                : trans('household.expiration.months.singular'));
    }
}
