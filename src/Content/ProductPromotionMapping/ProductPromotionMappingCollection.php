<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotionMapping;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ProductPromotionMappingCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductPromotionMappingEntity::class;
    }
}
