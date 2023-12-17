<?php declare(strict_types=1);

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion;

use RustyMcFly\PromotionProduct\Content\ProductPromotionMapping\ProductPromotionMappingDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Promotion\PromotionDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductPromotionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'product_promotion';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductPromotionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductPromotionCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new OneToManyAssociationField('mappings', ProductPromotionMappingDefinition::class, 'product_promotion_id', 'id')),
            (new ManyToOneAssociationField('promotion', 'promotion_id', PromotionDefinition::class)),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true)),
            (new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', true)),
            (new LongTextField('content', 'content'))->addFlags(new Runtime()),
            (new JsonField('attributes', 'attributes')),
            (new FkField('promotion_id', 'promotionId', PromotionDefinition::class)),
            (new FkField('product_id', 'productId', ProductDefinition::class)),
            (new FkField('media_id', 'mediaId', MediaDefinition::class))
        ]);
    }
}
