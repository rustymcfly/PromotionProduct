<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotionMapping;

use RustyMcFly\PromotionProduct\Content\ProductPromotion\ProductPromotionDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItemDownload\OrderLineItemDownloadDefinition;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductPromotionMappingDefinition extends EntityDefinition
{

    const ENTITY_NAME = 'product_promotion_mapping';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ProductPromotionMappingCollection::class;
    }

    public function getEntityClass(): string
    {
        return ProductPromotionMappingEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('order_line_item_download_id', 'orderLineItemDownloadId', OrderLineItemDownloadDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_promotion_id', 'productPromotionId', ProductPromotionDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('promotion_individual_code_id', 'promotionIndividualCodeId', PromotionIndividualCodeDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class),
            new ManyToOneAssociationField('orderLineItemDownload', 'order_line_item_download_id', OrderLineItemDownloadDefinition::class),
            new ManyToOneAssociationField('productPromotion', 'product_promotion_id', ProductPromotionDefinition::class),
            new ManyToOneAssociationField('promotionIndividualCode', 'promotion_individual_code_id', PromotionIndividualCodeDefinition::class)
        ]);
    }
}
