<?php
namespace App\Tenant\Order\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class PaymentFilter extends QueryFilter
{

    public function store($store_id)
    {
        $this->builder->where('store_id', $store_id);
    }

    public function paymentId($payment_id)
    {
        $this->builder->where('id', $payment_id);
    }

    public function publicSearch($search)
    {
        $this->builder->where('store_id', $search)
        ->orWhere('id', $search);
    }

}
