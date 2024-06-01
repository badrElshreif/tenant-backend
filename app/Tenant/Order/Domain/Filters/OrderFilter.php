<?php
namespace App\Tenant\Order\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;
class OrderFilter extends QueryFilter
{

    public function status($status)
    {
        if($status == 'current'){
            $statuses = ['new', 'accepted', 'delivering', 'ready_for_delivery'];
        }else if($status == 'finished'){
            $statuses = ['delivered', 'canceled', 'rejected'];
        }else{
            $statuses = array($status);
        }
        $this->builder->whereHas('status', function($q) use ($statuses) {
           // $q->where('key', 'like', '%' . $status . '%');
            $q->whereIn('key', $statuses);
        });
    }

    public function address($address)
    {
        $this->builder->whereHas('userAddress', function ($query) use($address){
            $query->where('address', 'like', '%'.$address.'%');
        });
    }

    public function city($city)
    {
        $this->builder->whereHas('userAddress', function ($query) use($city){
            $query->where('city_id', $city);
        });
    }

    public function state($state)
    {
        $this->builder->whereHas('userAddress', function ($query)  use($state){
            $query->where('state_id', $state);
        });
    }

    public function country($country)
    {
        $this->builder->whereHas('userAddress', function ($query)  use($country){
            $query->where('country_id', $country);
        });
    }

    public function paymentMethod($payment_method)
    {
        $this->builder->where('payment_method_id', $payment_method);
    }

    public function customer($customer_id)
    {
        $this->builder->where('user_id', $customer_id);
    }

    public function store($store_id)
    {
        $this->builder->where('store_id', $store_id);
    }

    public function orderId($order_id)
    {
        $this->builder->where('id', $order_id);
    }

    public function statusId($status_id)
    {
        $this->builder->where('status_id', $status_id);
    }

    public function type($type)
    {
        $this->builder->where('type', $type);
    }

    public function startDate($start_date)
    {
        $date = Carbon::parse($start_date)->toDateString();
        //$date = Carbon::parse($value)->format('Y-m-d');
        $this->builder->whereDate('created_at', '>=', $date);
    }

    public function endDate($end_date)
    {
        $date = Carbon::parse($end_date)->toDateString();
        $this->builder->whereDate('created_at', '<=', $date);
    }

    public function costFrom($from)
    {
        $this->builder->where('total', '>=', $from);
    }

    public function costTo($to)
    {
        $this->builder->where('total', '<=', $to);
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('userAddress', function ($query) use($search){
            $query->where('address', 'like', '%'.$search.'%')
            ->orWhereHas('city', function($q) use ($search){
                //$q->whereTranslationLike('name','like', '%'.$search.'%');
                $q->whereRelation('translations', 'name', 'like', '%' . $search . '%');
            });
        })
        ->orWhereHas('orderItems.product', function($q) use ($search){
            $q->where('barcode','like', '%'.$search.'%');
           // ->orWhereTranslationLike('name','like', '%'.$search.'%');
        })
        ->orWhere('store_id', $search)
        ->orWhereHas('store', function($q) use ($search){
            $q->whereRelation('translations', 'name', 'like', '%' . $search . '%');
        })
        ->orWhere('id', $search)
        ->orWhere('total', $search)
        ->orWhere('user_id', $search)
        ->orWhereHas('status', function ($query)  use($search){
            $query->whereRelation('translations', 'name', 'like', '%' . $search . '%');
        })
        ->orWhereHas('paymentMethod', function ($query)  use($search){
            $query->whereRelation('translations', 'name', 'like', '%' . $search . '%');
        })
        ->orWhereHas('user', function($q) use ($search){
            $q->where('phone','like', '%'.$search.'%')
            ->orWhere('email','like', '%'.$search.'%')
            ->orWhere('name','like', '%'.$search.'%');
        });
    }

    public function orderBy($orderBy)
    {
        $this->builder->orderBy($orderBy, request()->orderType ?? 'DESC');
    }

}
