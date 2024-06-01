<?php

namespace App\Tenant\Order\Domain\Services\Transaction;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Transaction;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Helpers\Traits\ApiPaginator;
use App\Tenant\Order\Domain\Resources\TransactionResource;

class ListUserTransactionsService extends Service
{
    use ApiPaginator;
    public function handle($data = [])
    {
        $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
        //dd(auth()->user()->id);
        $transactions = auth()->user()->transactions();
        $total = optional($transactions->orderBy('id', 'desc')->first())->wallet_total ? : 0.00;
        $transactions_list = $transactions->orderBy('created_at', 'desc')->paginate($limit);

        //return new GenericPayload(['total' => $total, 'transactions' => $transactions_list]);
        return new GenericPayload([
            'total' => $total,
            'transactions' => $this->getPaginatedResponse(
                $transactions_list,
                TransactionResource::collection($transactions_list)
            )], Response::HTTP_RESET_CONTENT);
    }
}
