<?php
namespace App\Tenant\AppContent\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class ContactUsFilter extends QueryFilter
{

    public function read($read)
    {
        if($read = 'true'):
            $this->builder->where('read_at', '!=', null);
        else:
            $this->builder->where('read_at', null);
        endif;
    }

    public function email($email)
    {
        $this->builder->where('email', $email);
    }

    public function phone($phone)
    {
        $this->builder->where('phone', $phone);
    }

    public function message($message)
    {
        $this->builder->where('body', $message);
    }

    public function title($title)
    {
        $this->builder->where('title', $title);
    }
    public function publicSearch($title)
    {
        $this->builder->where('title','like','%'.$title.'%')
        ->orWhere('body','like','%'.$title.'%')
        ->orWhere('name','like','%'.$title.'%')
        ->orWhere('email','like','%'.$title.'%')
        ->orWhere('phone','like','%'.$title.'%');
    }

    public function orderBy($orderBy)
    {
        $this->builder->orderBy($orderBy, request()->orderType ?? 'DESC');
    }

}
