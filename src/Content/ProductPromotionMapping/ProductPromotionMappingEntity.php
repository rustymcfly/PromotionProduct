<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotionMapping;

use RustyMcFly\PromotionProduct\Content\ProductPromotion\ProductPromotionEntity;
use Shopware\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductPromotionMappingEntity extends Entity
{
    use EntityIdTrait;

    protected $customer;
    protected $customerId;
    protected $promotionIndividualCode;
    protected $promotionIndividualCodeId;
    protected $productPromotion;
    protected $productPromotionId;

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getPromotionIndividualCode(): ?PromotionIndividualCodeEntity
    {
        return $this->promotionIndividualCode;
    }

    /**
     * @param mixed $promotionIndividualCode
     */
    public function setPromotionIndividualCode($promotionIndividualCode): void
    {
        $this->promotionIndividualCode = $promotionIndividualCode;
    }

    /**
     * @return mixed
     */
    public function getPromotionIndividualCodeId()
    {
        return $this->promotionIndividualCodeId;
    }

    /**
     * @param mixed $promotionIndividualCodeId
     */
    public function setPromotionIndividualCodeId($promotionIndividualCodeId): void
    {
        $this->promotionIndividualCodeId = $promotionIndividualCodeId;
    }

    /**
     * @return mixed
     */
    public function getProductPromotion(): ?ProductPromotionEntity
    {
        return $this->productPromotion;
    }

    /**
     * @param mixed $productPromotion
     */
    public function setProductPromotion($productPromotion): void
    {
        $this->productPromotion = $productPromotion;
    }

    /**
     * @return mixed
     */
    public function getProductPromotionId()
    {
        return $this->productPromotionId;
    }

    /**
     * @param mixed $productPromotionId
     */
    public function setProductPromotionId($productPromotionId): void
    {
        $this->productPromotionId = $productPromotionId;
    }

}
