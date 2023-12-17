<?php declare(strict_types=1);

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ProductPromotionEntity $entity)
 * @method void set(string $key, ProductPromotionEntity $entity)
 * @method ProductPromotionEntity[] getIterator()
 * @method ProductPromotionEntity[] getElements()
 * @method ProductPromotionEntity|null get(string $key)
 * @method ProductPromotionEntity|null first()
 * @method ProductPromotionEntity|null last()
 */
class ProductPromotionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductPromotionEntity::class;
    }
}
