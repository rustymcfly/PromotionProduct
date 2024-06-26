<?php

namespace RustyMcFly\PromotionProduct\Subscriber;

use RustyMcFly\PromotionProduct\Content\ProductPromotion\Attribute;
use RustyMcFly\PromotionProduct\Content\ProductPromotion\Attributes;
use RustyMcFly\PromotionProduct\Content\ProductPromotion\ProductPromotionCollection;
use RustyMcFly\PromotionProduct\Content\ProductPromotion\ProductPromotionEntity;
use setasign\Fpdi\Fpdi;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeDefinition;
use Shopware\Core\Checkout\Promotion\Util\PromotionCodeService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\Kernel;
use Shopware\Core\System\StateMachine\Event\StateMachineStateChangeEvent;
use Shopware\Storefront\Page\Account\Order\AccountOrderDetailPageLoadedEvent;
use Shopware\Storefront\Page\Account\Order\AccountOrderPageLoadedEvent;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

class PromotionProductLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Kernel $kernel, private readonly PromotionCodeService $promotionCodeService, private readonly EntityRepository $individualCodeRepository, private readonly EntityRepository $productPromotionRepository, private readonly EntityRepository $productPromotionMappingRepository, private readonly EntityRepository $orderTransactionRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'product_promotion.loaded' => 'onPromotionLoaded',
            'order.loaded' => 'onOrderLoaded',
            'state_machine.order_transaction.state_changed' => 'onTransactionUpdated',
            CheckoutFinishPageLoadedEvent::class => 'onPageLoaded',
            AccountOverviewPageLoadedEvent::class => 'onPageLoaded',
            AccountOrderDetailPageLoadedEvent::class => 'onPageLoaded',
            AccountOrderPageLoadedEvent::class => 'onPageLoaded'
        ];
    }

    public function onTransactionUpdated(StateMachineStateChangeEvent $event): void
    {

        if ($event->getNextState()->getTechnicalName() === OrderTransactionStates::STATE_PAID && $event->getTransitionSide() === "state_enter") {

            $mappings = [];
            /**
             * @var $order OrderEntity
             * @var $orderTransaction OrderTransactionEntity
             */
            $criteria = new Criteria([$event->getTransition()->getEntityId()]);
            $criteria->addAssociation('order');
            $criteria->addAssociation('order.orderCustomer.customer');
            $criteria->addAssociation('order.orderCustomer');
            $criteria->addAssociation('order.lineItems');
            $criteria->addAssociation('order.lineItems.downloads');
            $orderTransaction = $this->orderTransactionRepository->search($criteria, $event->getContext())->first();
            $order = $orderTransaction->getOrder();
            $lineItems = $order->getLineItems()->filterByType(LineItem::PRODUCT_LINE_ITEM_TYPE);


            $criteria = new Criteria();
            $criteria->addAssociation('product');
            $criteria->addAssociation('promotion');
            $criteria->addFilter(new EqualsAnyFilter('product.productNumber', $lineItems->getPayloadsProperty('productNumber')));
            /**
             * @var $promotionItems ProductPromotionCollection
             */
            $promotionItems = $this->productPromotionRepository->search($criteria, $event->getContext())->getEntities();

            foreach ($promotionItems as $productPromotionEntity) {
                $promotionLineItem = $lineItems->filter(function (OrderLineItemEntity $lineItem) use ($productPromotionEntity) {
                    return $lineItem->getPayload()['productNumber'] === $productPromotionEntity->getProduct()->getProductNumber();
                })->first();
                $codes = $this->promotionCodeService->generateIndividualCodes($productPromotionEntity->getPromotion()->getIndividualCodePattern(), $promotionLineItem->getQuantity());

                $discountCodes = [];
                for ($i = 1; $i <= $promotionLineItem->getQuantity() * $promotionLineItem->getDownloads()->count(); $i++) {
                    $discountCodes[] = [
                        "promotionId" => $productPromotionEntity->getPromotionId(),
                        "scope" => "cart",
                        "type" => "absolute",
                        "code" => $codes[$i - 1],
                        "consider_advanced_rules" => false
                    ];
                }

                $writeResult = $this->individualCodeRepository->upsert($discountCodes, $event->getContext());


                foreach ($writeResult->getPrimaryKeys(PromotionIndividualCodeDefinition::ENTITY_NAME) as $id) {

                    foreach ($promotionLineItem->getDownloads() as $download) {
                        $mappings[] = [
                            "customerId" => $order->getOrderCustomer()->getCustomer()->getId(),
                            "promotionIndividualCodeId" => $id,
                            "orderLineItemDownloadId" => $download->getId(),
                            "productPromotionId" => $productPromotionEntity->getId()
                        ];
                    }
                }
            }

            $this->productPromotionMappingRepository->upsert($mappings, $event->getContext());
        }
    }

    public function onPageLoaded(PageLoadedEvent $event)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $event->getSalesChannelContext()->getCustomerId()));
        $criteria->addAssociation('productPromotion');
        $criteria->addAssociation('productPromotion.promotion');
        $criteria->addAssociation('productPromotion.media');
        $criteria->addAssociation('productPromotion.product');
        $criteria->addAssociation('promotionIndividualCode');
        $criteria->addAssociation('orderLineItemDownload');
        $event->getPage()->addExtension('promotions', $this->productPromotionMappingRepository->search($criteria, $event->getContext()));
    }

    public function onPromotionLoaded(EntityLoadedEvent $entityLoadedEvent): void
    {
        /**
         * @var $promotionProducts ProductPromotionCollection
         */
        $promotionProducts = $entityLoadedEvent->getEntities();
        foreach ($promotionProducts as $promotionProduct) {
            $promotionProduct->setAttributes(Attributes::createFrom($promotionProduct));
            $this->getFile($promotionProduct);
        }
    }

    public function onOrderLoaded(EntityLoadedEvent $event)
    {
        $orderEntities = $event->getEntities();
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addAssociation('product');
        foreach ($orderEntities as $orderEntity) {
            $promotions = $this->productPromotionRepository->search($criteria, $event->getContext());
        }
    }

    public function getFile(ProductPromotionEntity $entity, string $code = null)
    {
        $fpdf = new Fpdi();

        $attributes = $entity->getAttributes();

        if ($code && $attributes->get('promotion.individualCodes.code')) {
            $attributes->get('promotion.individualCodes.code')->value = $code;
        }

        $pdfTempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $entity->getId();
        if (!$entity->getMedia()->getPath()) return;

        $fpdf->setSourceFile($this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $entity->getMedia()->getPath());

        $fpdf->AddPage();

        $tplIdx = $fpdf->importPage(1);

        $fpdf->useTemplate($tplIdx);

        $fpdf->SetFont('Arial', '', $pdfAttributes->fontSize?->value ?? 30);

        $fpdf->SetTextColor(10, 19, 21);

        $mid_x = $fpdf->GetPageWidth() / 2;;
        try {

            foreach ($attributes as $key => $attribute) {
                if (!in_array($key, ["fontSize", "font"]) && $attribute instanceof Attribute) {
                    $text = $attribute->value ?? $attribute->property ?? 'undefined';
                    if($attribute instanceof PriceCollection) {
                        $text = $attribute->first()->getGross();
                    }
                    $x = $attribute->x === 'center' ? $mid_x - ($fpdf->GetStringWidth($text) / 2) - 5 : ($attribute->x === 'left' ? 25 : $fpdf->GetPageWidth() - $fpdf->GetStringWidth($text) - 25);
                    $fpdf->Text($x, $attribute->y ?? 30, iconv('UTF-8', 'windows-1252', $text));
                }
            }
        } catch (\Throwable $exception) {
            $this->kernel->getContainer()->get('monolog.logger.business_events')->error($exception->getMessage(), (array)$exception);
        }
        if (file_exists($pdfTempPath))
            unlink($pdfTempPath);


        $fpdf->Output('F', $pdfTempPath);
        $file = new File($pdfTempPath);

        $entity->setContent($file);
    }
}
