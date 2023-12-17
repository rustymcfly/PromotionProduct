<?php declare(strict_types=1);


namespace RustyMcFly\PromotionProduct\Content\ProductPromotion\SalesChannel;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class ProductPromotionRoute extends AbstractProductPromotionRoute
{
    public function __construct(private readonly EntityRepository $productRepository)
    {
    }

    public function getDecorated(): AbstractProductPromotionRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(
        path: '/store-api/product-promotion',
        name: 'store-api.product-promotion.search',
        methods: ['GET', 'POST']
    )]
    public function load(Criteria $criteria, SalesChannelContext|Context $context):  ?ProductPromotionRouteResponse
    {
        if ($context instanceof SalesChannelContext) {
            $context = $context->getContext();
        }

        return new ProductPromotionRouteResponse($this->productRepository->search($criteria, $context)->first());
    }

    public function getFile(Context $context, Criteria $criteria): File
    {
        $criteria->addAssociation('promotion');
        $criteria->addAssociation('promotion.discounts');
        $promotion = $this->load($criteria, $context)->getPromotion();
        return $promotion->getContent();
    }

}
