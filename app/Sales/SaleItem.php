<?php

namespace App\Sales;

/**
 * A single line on a completed sale. Product details are snapshotted so the
 * receipt never changes when the catalog is later edited.
 */
final class SaleItem implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $saleId,
        public int $productId,
        public string $productName,
        public int $unitPrice,
        public int $quantity,
        public int $subtotal,
    ) {}

    public function formattedUnitPrice(): string
    {
        return '₱'.number_format($this->unitPrice);
    }

    public function formattedSubtotal(): string
    {
        return '₱'.number_format($this->subtotal);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->saleId,
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'unit_price' => $this->unitPrice,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
