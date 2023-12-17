<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion\Storefront;

use RustyMcFly\PromotionProduct\Content\ProductPromotion\SalesChannel\AbstractProductPromotionRoute;
use RustyMcFly\PromotionProduct\Content\ProductPromotionMapping\ProductPromotionMappingEntity;
use RustyMcFly\PromotionProduct\Subscriber\PromotionProductLoadedSubscriber;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ProductPromotionController extends StorefrontController
{
    public function __construct(private readonly EntityRepository $productPromotionRepository, private readonly PromotionProductLoadedSubscriber $loadedSubscriber)
    {
    }

    #[Route(
        path: '/product-promotion/{id}',
        name: 'storefront.product-promotion.search',
        methods: ['GET']
    )]
    public function detail(string $id, SalesChannelContext $context)
    {
        $criteria = new Criteria([$id]);
        $criteria->addAssociation('productPromotion');
        $criteria->addAssociation('productPromotion.promotion');
        $criteria->addAssociation('productPromotion.product');
        $criteria->addAssociation('promotionIndividualCode');
        /**
         * @var ProductPromotionMappingEntity $promotionMapping
         */
        $promotionMapping = $this->productPromotionRepository->search($criteria,  $context->getContext())->first();

        $this->loadedSubscriber->getFile($promotionMapping->getProductPromotion(), $promotionMapping->getPromotionIndividualCode()->getCode());
        return new Response($promotionMapping->getProductPromotion()->getContent()->getContent(), Response::HTTP_OK, ["content-type" => $promotionMapping->getProductPromotion()->getContent()->getMimeType()]);
    }
}
