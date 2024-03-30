import { RouteRecord } from "vue-router/types/router";
import Repository from "admin/core/data/repository.data";
import EntityFactory from "admin/core/data/entity-factory.data";
import TestHelper from "./test/Helper/TestHelper";

export {};
declare module "*.svg";
declare global {
    interface global {
        helper: TestHelper;
    }

    type FixedString<N extends number> = { 0: string; length: N } & string;
    type IdString = FixedString<16>;
    type PropType<T> = PropConstructor<T> | PropConstructor<T>[];
    type PropConstructor<T = any> =
        | {
        new (...args: any[]): T & {};
    }
        | {
        (): T;
    }
        | PropMethod<T>;
    type PropMethod<T, TConstructor = any> = [T] extends [
                ((...args: any) => any) | undefined
        ]
        ? {
            new (): TConstructor;
            (): T;
            readonly prototype: TConstructor;
        }
        : never;

    type SwRouteRecord = RouteRecord & { component: string; children: {}[] };

    declare namespace EntitySchema {
        interface Entities {
            customer_group: Entity<"customer_group">;
            customer: Entity<"customer"> & customer;
            product: product;
            product_cover: Entity<"product">;
            currency: Entity<"currency">;
            cms_page: cms_page;
            cms_section: cms_section;
            cms_block: cms_block;
            cms_slot: cms_slot;
            property_group_option: Entity<"property_group_option">;
            media: media;
            tax: Entity<"tax">;
            test_translatable_entity: test_translatable_entity;
            test_translatable_entity_translation: test_translatable_entity_translation;
            product_promotion: product_promotion
            promotion: promotion
        }

        interface product_promotion_attributes {
            x: 'center' | 'left' | 'right'
            y: number
        }


        interface product_promotion extends Entity<'product_promotion'> {
            media: Entity<'media'>
            product: Entity<'product'>
            promotion: Entity<'promotion'>
            mediaId: IdString
            productId: IdString
            promotionId: IdString
            id: IdString
            attributes: product_promotion_attributes[]
            content: any
        }

        interface promotion extends Entity<'promotion'>  {
            id: IdString
            name: string
            active: boolean
            validFrom: string
            validUntil: string
            maxRedemptionsGlobal: number
            maxRedemptionsPerCustomer: number
            priority: number
            exclusive: boolean
            code: string
            useCodes: boolean
            useIndividualCodes: boolean
            individualCodePattern: string
            useSetgroups: boolean
            customerRestriction: boolean
            preventCombination: boolean

            orderCount: number
            ordersPerCustomerCount: number

            setgroups: Entity<any>

            salesChannels: EntityCollection<'sales_channel'>
            discounts: EntityCollection<'promotion_discount'>
            individualCodes: EntityCollection<'promotion_individual_code'>

            personaRules: EntityCollection<'promotion_persona_rule'>
            personaCustomers: EntityCollection<'promotion_persona_customer'>
            orderRules: EntityCollection<'promotion_order_rule'>
            cartRules: EntityCollection<'promotion_cart_rule'>

            exclusion_ids: string[]
            customFields: {}
        }

        interface promotion_discount extends Entity<'promotion_discount'> {
            id: IdString
            promotionId: IdString
            scope: string
            type: string
            value: number
            considerAdvancedRules: boolean
            maxValue: number

            sorterKey: FixedString<32>
            applierKey: FixedString<32>
            usageKey: FixedString<32>
            pickerKey: FixedString<32>
            promotion: Entity<'promotion'>
            discountRules: EntityCollection<'rule'>
            promotionDiscountPrices: EntityCollection<'promotion_discount_prices'>

        }
        interface product extends Entity<'product'> {
            cover: Entity<'product_cover'>
        }

        interface product_cover extends Entity<'product_cover'> {
            media: Entity<'media'>
            mediaId: IdString
        }

        interface cms_page extends Entity<"cms_page"> {
            sections: EntityCollection<'cms_section'>
        }
        interface cms_section extends Entity<"cms_section"> {
            blocks: EntityCollection<'cms_block'>
        }
        interface cms_block extends Entity<"cms_block"> {
            slots: EntityCollection<'cms_slot'>
            type: string
        }
        interface cms_slot extends Entity<"cms_slot"> {
            type: string
        }

        interface customer {
            defaultBillingAddress: Entity<"customer_address">;
            defaultShippingAddress: Entity<"customer_address">;
            addresses: EntityCollection<"customer_address">;
        }

        interface media extends Entity<"media"> {
            url: string;
            mimeType: string;
        }

        interface TranslationEntity<T extends keyof EntitySchema.Entities> extends Entity<T>{
            languageId: IdString;
            language: Entity<"language">;
        }

        type EntityKey<T> = keyof EntitySchema.Entities<T>;

        interface TranslatableEntity<T extends keyof EntitySchema.Entities>
            extends EntitySchema.Entity<T> {
            [p: EntityKey[T]]: any;

            translated: { [p in keyof Entities[T]]: any };
            translations: EntitySchema.EntityCollection<T>;
        }

        interface test_translatable_entity_translation extends TranslationEntity<'test_translatable_entity_translation'> {
            label: string;
        }

        interface test_translatable_entity
            extends TranslatableEntity<"test_translatable_entity_translation"> {}
    }

    interface WriteResult {
        id: IdString;
    }

    interface CloneBehaviour<EntityName extends keyof EntitySchema.Entities> {
        overwrites?: {
            [p in keyof Entities[EntityName]]: any;
        };
        cloneChildren?: boolean;
    }

    class TypedRepository<
        EntityName extends keyof EntitySchema.Entities
    > extends Repository<EntityName> {
        create(
            context?: typeof Shopware.Context.api
        ): EntitySchema.Entity<EntityName>;

        clone(
            id: IdString,
            context: typeof Shopware.Context.api,
            behaviour: CloneBehaviour
        ): Promise<WriteResult>;
        clone(
            id: IdString,
            context: typeof Shopware.Context.api
        ): Promise<WriteResult>;
        clone(id: IdString): Promise<WriteResult>;

        get(
            id: IdString,
            context?: typeof Shopware.Context.api,
            criteria?: typeof Shopware.Data.Criteria
        ): Promise<EntitySchema.Entity<EntityName>>;

        search(
            criteria: typeof Shopware.Data.Criteria,
            context?: typeof Shopware.Context.api
        ): Promise<EntitySchema.EntityCollection<EntityName>>;

        delete(
            id: IdString,
            context?: typeof Shopware.Context.api
        ): Promise<WriteResult>;

        saveAll(
            collection:
                | EntitySchema.EntityCollection<EntityName>
                | EntitySchema.Entity<EntityName>[],
            context?: typeof Shopware.Context.api
        ): Promise<WriteResult[]>;

        save(
            entity: EntitySchema.Entity<EntityName>,
            context?: typeof Shopware.Context.api
        ): Promise<WriteResult>;

        entityFactory: EntityFactory;
        entityName: EntityName;
    }
}

declare module "*.html.twig";
declare module "*.svg";
