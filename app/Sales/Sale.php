<?php

namespace App\Sales;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * A completed checkout: the customer, the totals and the line items.
 */
final class Sale implements \JsonSerializable
{
    /**
     * @param  Collection<int, SaleItem>  $items
     */
    public function __construct(
        public int $id,
        public string $reference,
        public string $customerName,
        public ?string $customerEmail,
        public int $itemsCount,
        public int $total,
        public ?int $userId = null,
        public ?string $createdAt = null,
        public Collection $items = new Collection,
    ) {}

    public function formattedTotal(): string
    {
        return '₱'.number_format($this->total);
    }

    public function orderDate(): ?string
    {
        return $this->createdAt ? Carbon::parse($this->createdAt)->format('M j, Y g:i A') : null;
    }

    public function hasItems(): bool
    {
        return $this->items->isNotEmpty();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'items_count' => $this->itemsCount,
            'total' => $this->total,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
            'items' => $this->items->map(fn (SaleItem $item) => $item->toArray())->all(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
