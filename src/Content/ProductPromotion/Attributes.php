<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion;

use Shopware\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeCollection;
use Shopware\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeEntity;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Attributes extends Collection
{

    private function guessTheCode($entity, $code): string
    {
        try {
            return vsprintf($entity->getPromotion()->getIndividualCodePattern(), str_split( $code));
        } catch (\Throwable) {
            return $this->guessTheCode($entity, $code . $code);
        }


    }
    /**
     * @param ProductPromotionEntity $entity
     * @return Attributes
     */
    public static function createFrom(Struct $entity): static
    {
        $struct = new self();
        $propertyAccessor = new PropertyAccessor(PropertyAccessor::MAGIC_GET | PropertyAccessor::MAGIC_CALL, PropertyAccessor::DO_NOT_THROW);
        if ($entity->attributes)
            foreach ($entity->attributes as $value) {

                try {
                    $key = $value["property"] ?? 'name';
                    if (str_contains($key, 'discounts.')) {
                        $key = str_replace('discounts.', 'discounts.elements[' . $entity->getPromotion()->getDiscounts()->first()?->getId() . '].', $key);
                    }
                    if (str_contains($key, 'individualCodes.')) {
                        if (!$entity->getPromotion()->getIndividualCodes()) {
                            $entity->getPromotion()->setIndividualCodes(new PromotionIndividualCodeCollection());
                        }
                        if (!$entity->getPromotion()->getIndividualCodes()->count()) {
                            $codeEntity = new PromotionIndividualCodeEntity();
                            $codeEntity->setId(Uuid::randomHex());
                            $codeEntity->setUniqueIdentifier($codeEntity->getId());
                            $codeEntity->setCode($struct->guessTheCode($entity, 'BEISPIELCODE'));
                            $entity->getPromotion()->getIndividualCodes()->add($codeEntity);
                        }
                        $key = str_replace('individualCodes.', 'individualCodes.elements[' . $entity->getPromotion()->getIndividualCodes()->first()?->getId() . '].', $key);
                    }

                    $value["value"] = $propertyAccessor->getValue($entity, $key);
                } catch (\Throwable ) {
                }
                $struct->add(Attribute::createFromArray($value));
            }
        return $struct;
    }

    /**
     * @param Attribute $element
     */
    public function add($element): void
    {
        $this->elements[$element->property] = $element;
    }
}
