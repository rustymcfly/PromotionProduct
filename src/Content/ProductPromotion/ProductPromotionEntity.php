<?php declare(strict_types=1);

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion;

use Shopware\Core\Checkout\Promotion\PromotionEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Swag\PayPal\Pos\DataAbstractionLayer\Extension\ProductExtension;
use Symfony\Component\HttpFoundation\File\File;

class ProductPromotionEntity extends Entity
{
    use EntityIdTrait;

    protected $media;
    protected $mediaId;

    protected $promotion;
    protected $promotionId;

    protected $product;
    protected $productId;

    protected $content;
    protected $attributes;


    /**
     * @return mixed
     */
    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    /**
     * @param mixed $media
     */
    public function setMedia($media): void
    {
        $this->media = $media;
    }

    /**
     * @return mixed
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * @param mixed $mediaId
     */
    public function setMediaId($mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    /**
     * @return mixed
     */
    public function getPromotion(): ?PromotionEntity
    {
        return $this->promotion;
    }

    /**
     * @param mixed $promotion
     */
    public function setPromotion($promotion): void
    {
        $this->promotion = $promotion;
    }

    /**
     * @return mixed
     */
    public function getPromotionId()
    {
        return $this->promotionId;
    }

    /**
     * @param mixed $promotionId
     */
    public function setPromotionId($promotionId): void
    {
        $this->promotionId = $promotionId;
    }

    /**
     * @return mixed
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getContent(): ?File
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes): void
    {
        $this->attributes = $attributes;
    }

}
