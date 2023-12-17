<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion\Admin;

use RustyMcFly\PromotionProduct\Content\ProductPromotion\SalesChannel\AbstractProductPromotionRoute;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type EntityPathSegment array{entity: string, value: ?string, definition: EntityDefinition, field: ?Field}
 */
#[Route(defaults: ['_routeScope' => ['api']])]
class AdminFileRoute extends AbstractController
{

    public function __construct(private readonly AbstractProductPromotionRoute $productPromotionRoute)
    {
    }

    #[Route(path: '/api/promotionFile/{id}', name: 'api.getFile', methods: ['GET'], requirements: ['version' => '\d+', 'id' => '[0-9a-f]{32}'])]
    public function getFile(string $id, Context $context): Response
    {
        $promotion = $this->productPromotionRoute->getFile($context, new Criteria([$id]));
        return new Response($promotion->getContent(), Response::HTTP_OK, ["content-type" => $promotion->getMimeType()]);
    }
}
