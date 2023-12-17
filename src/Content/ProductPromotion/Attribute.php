<?php

namespace RustyMcFly\PromotionProduct\Content\ProductPromotion;

use Shopware\Core\Framework\Struct\Struct;

class Attribute extends Struct
{

    public $x;
    public $y;
    public $property;
    public $value;

    public static function createFromArray(mixed $object): Attribute
    {
        $struct = new self();

        if ($object instanceof Attribute) return $object;
        foreach ($object as $key => $value) {
            $struct->$key = $value;
        }
        return $struct;
    }
}
