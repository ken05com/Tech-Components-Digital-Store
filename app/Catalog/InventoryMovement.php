<?php

namespace App\Catalog;

use Illuminate\Support\Carbon;

/**
 * A single stock movement row (received, sold, or adjusted).
 */
final class InventoryMovement implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $productId,
        public string $type,
        public int $quantity,
        public int $balance,
        public ?string $reason = null,
        public ?int $userId = null,
        public ?string $createdAt = null,
    ) {}

    public function direction(): string
    {
        return $this->quantity >= 0 ? 'in' : 'out';
    }

    public function signedQuantity(): string
    {
        return ($this->quantity >= 0 ? '+' : '').$this->quantity;
    }

    public function tone(): string
    {
        return match ($this->type) {
            'in' => 'emerald',
            'out' => 'accent',
            default => 'slate',
        };
    }

    public function label(): string
    {
        return ucfirst($this->type);
    }

    public function date(): ?string
    {
        return $this->createdAt ? Carbon::parse($this->createdAt)->format('M j, Y g:i A') : null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'balance' => $this->balance,
            'reason' => $this->reason,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
