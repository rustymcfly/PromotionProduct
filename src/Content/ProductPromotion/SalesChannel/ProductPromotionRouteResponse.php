<?php declare(strict_types=1);


namespace RustyMcFly\PromotionProduct\Content\ProductPromotion\SalesChannel;

use RustyMcFly\PromotionProduct\Content\ProductPromotion\ProductPromotionEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

/**
 * @property ProductPromotionEntity $object
 */
class ProductPromotionRouteResponse extends StoreApiResponse
{
    public function getPromotion(): ProductPromotionEntity
    {
        return $this->object;
    }
}
