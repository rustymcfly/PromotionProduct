export {};

declare global {
    type FixedString<N extends number> = { 0: string, length: N } & string;
    type IdString = FixedString<16>
    type PropType<T> = PropConstructor<T> | PropConstructor<T>[];
    type PropConstructor<T = any> = {
        new(...args: any[]): T & {};
    } | {
        (): T;
    } | PropMethod<T>;
    type PropMethod<T, TConstructor = any> = [T] extends [
            ((...args: any) => any) | undefined
    ] ? {
        new(): TConstructor;
        (): T;
        readonly prototype: TConstructor;
    } : never;

    declare namespace EntitySchema {
        interface Entities {
            customer_group: Entity<'customer_group'>;
            customer: Entity<'customer'>;
            product: Entity<'product'>;
            media: Entity<'media'>;
            tax: Entity<'tax'>;

            product_promotion: product_promotion;
            promotion: promotion;
            promotion_discount: promotion_discount
        }


        interface TranslationEntity {
            languageId: IdString;
            language: Entity<'language'>;
        }

        interface TranslatableEntity<T extends keyof EntitySchema.Entities> extends EntitySchema.Entity<T> {
            [p: keyof EntitySchema.Entity<T>]: string;

            translated: { [p: keyof Entities[T]]: any };
            translations: EntitySchema.EntityCollection<T>;
        }

        interface product_promotion_attributes {
            x: 'center' | 'left' | 'right'
            y: number
        }


        interface product_promotion {
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

        interface promotion {
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

        interface promotion_discount {
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
    }
}

declare module '*.html.twig'
declare module '*.svg'
