<?php declare(strict_types=1);

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractProductPromotionRoute
{
    abstract public function getDecorated(): AbstractProductPromotionRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context): ?ProductPromotionRouteResponse;
}
