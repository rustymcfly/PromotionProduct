<?php declare(strict_types=1);

namespace RustyMcFly\PromotionProduct\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1702884050UpdateMappingTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702884050;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
alter table product_promotion_mapping
   add order_line_item_download_id binary(16) not null,
    drop foreign key `fk.product-promotion-customers.product_promotion_id`,
    drop foreign key `fk.product-promotion-customers.promotion_individual_code_id`,
    drop primary key,
    add primary key (order_line_item_download_id, product_promotion_id, promotion_individual_code_id, customer_id),
    add constraint `fk.product-promotion-mapping.product_promotion_id` FOREIGN KEY (product_promotion_id) REFERENCES product_promotion (id) ON DELETE CASCADE ON UPDATE CASCADE,
    add constraint `fk.product-promotion-mapping.order_line_item_download_id` FOREIGN KEY (order_line_item_download_id) REFERENCES order_line_item_download (id) ON DELETE CASCADE ON UPDATE CASCADE,
    add constraint `fk.product-promotion-mapping.promotion_individual_code_id` FOREIGN KEY (promotion_individual_code_id) REFERENCES promotion_individual_code (id) ON DELETE CASCADE ON UPDATE CASCADE,
    add constraint `fk.product-promotion-mapping.customer_id` FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE ON UPDATE CASCADE;
SQL
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
