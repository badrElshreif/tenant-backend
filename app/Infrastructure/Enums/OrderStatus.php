<?php

namespace App\Infrastructure\Enums;


enum OrderStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    // Method to return labels
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Order is Pending',
            self::Shipped => 'Order has been Shipped',
            self::Delivered => 'Order Delivered',
            self::Cancelled => 'Order Cancelled',
        };
    }

    // Method to return CSS classes (for example in views)
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-500',
            self::Shipped => 'bg-blue-500',
            self::Delivered => 'bg-green-500',
            self::Cancelled => 'bg-red-500',
        };
    }

    // Method to return an array of all possible statuses with labels
    public static function options(): array
    {
        return array_map(fn($status) => ['value' => $status->value, 'label' => $status->label()], self::cases());
    }
}
