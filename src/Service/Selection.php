<?php
namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Selection
 * @package App\Service
 */
class Selection
{
    public function __construct(
        private array $products,
        private int $deliverySum
    ) {}

    /**
     * @return array
     */
    public function getProductGroups(): array
    {
        $this->sortProducts();
        $productGroups = [];
        $group = [];

        foreach ($this->products as $product) {
            if ($this->sumPricesInGroup($group) >= $this->deliverySum) {
                $productGroups[] = $group;
                $group = [];
            }

            $group[] = $product;
        }

        return $productGroups;
    }

    /**
     * @param array $group
     * @return int
     */
    private function sumPricesInGroup(array $group): int
    {
        return array_sum(array_column($group, 'price'));
    }

    private function sortProducts(): void
    {
        usort($this->products, function ($item1, $item2) {
            return $item2['price'] <=> $item1['price'];
        });
    }
}