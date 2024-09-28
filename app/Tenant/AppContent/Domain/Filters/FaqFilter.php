<?php
namespace App\Tenant\AppContent\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Infrastructure\Domain\Filters\QueryFilter;

class FaqFilter extends QueryFilter
{

    public function status($status)
    {
        if($status == 'true')
            $status = 1;
        if($status == 'false')
            $status = 0;
        $this->builder->where('is_active', $status);
    }

    public function question($question)
    {
        $this->builder->whereHas('translations', function($q) use ($question) {
            $q->where('question', 'like', '%' . $question . '%');
        });
    }

    public function answer($answer)
    {
        $this->builder->whereHas('translations', function($q) use ($answer) {
            $q->where('answer', 'like', '%' . $answer . '%');
        });
    }

    public function publicSearch($search)
    {
        $this->builder->whereHas('translations', function($q) use ($search) {
            $q->where('question', 'like', '%' . $search . '%')
            ->orWhere('answer', 'like', '%' . $search . '%');
        });
    }


}
