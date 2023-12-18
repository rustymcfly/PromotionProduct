<?php declare(strict_types=1);

namespace RustyMcFly\PromotionProduct\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1702209398CreateProductPromotionTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702209398;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS product_promotion
(
    id           BINARY(16)  NOT NULL,
    product_id   binary(16) NOT NULL,
    promotion_id binary(16) NOT NULL,
    media_id     binary(16) NOT NULL,
    attributes   longtext,
    created_at   DATETIME(3) NOT NULL,
    updated_at   DATETIME(3),
    check (json_valid(attributes)),
    PRIMARY KEY (id, product_id, promotion_id, media_id),
    constraint `fk.product-promotion.product_id` FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE ON UPDATE CASCADE,
    constraint `fk.product-promotion.promotion_id` FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE ON UPDATE CASCADE,
    constraint `fk.product-promotion.media_id` FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE set null ON UPDATE CASCADE
);
CREATE TABLE IF NOT EXISTS product_promotion_mapping
(
    id                           binary(16)  NOT NULL,
    product_promotion_id         binary(16)  NOT NULL,
    order_line_item_download_id  binary(16)  NOT NULL,
    promotion_individual_code_id binary(16)  NOT NULL,
    created_at                   DATETIME(3) NOT NULL,
    updated_at                   DATETIME(3),
    primary key (id, order_line_item_download_id, product_promotion_id, promotion_individual_code_id),
    constraint `fk.product-promotion-customers.product_promotion_id` FOREIGN KEY (product_promotion_id) REFERENCES product_promotion (id) ON DELETE CASCADE ON UPDATE CASCADE,
    constraint `fk.product-promotion-customers.order_line_item_download_id` FOREIGN KEY (order_line_item_download_id) REFERENCES order_line_item_download (id) ON DELETE CASCADE ON UPDATE CASCADE,
    constraint `fk.product-promotion-customers.promotion_individual_code_id` FOREIGN KEY (promotion_individual_code_id) REFERENCES promotion_individual_code (id) ON DELETE CASCADE ON UPDATE CASCADE
)
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
