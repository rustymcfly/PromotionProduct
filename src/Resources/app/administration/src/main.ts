import template from './template.html.twig'
import productPromotionTemplate from './productPromotionTemplate.html.twig'


Shopware.Component.override('sw-product-detail-base', {
    template
})
Shopware.Component.override('sw-promotion-v2-sales-channel-select', {
    model: {
        prop: 'promotion',
        event: 'change',
    },
    methods: {
        handleWithRepository(deleted, added) {
            this.$super('handleWithRepository', deleted, added)
            Shopware.Feature.flags.VUE3 ? this.$emit('update:promotion') : this.$emit('change')
        },
        handleLocalMode(deleted, added) {
            this.$super('handleLocalMode', deleted, added)
            Shopware.Feature.flags.VUE3 ? this.$emit('update:promotion') : this.$emit('change')
        }
    }
})

Shopware.Component.register('product-promotions', {
    template: productPromotionTemplate,
    props: {
        product: {
            type: Object as EntitySchema.Entity<'product'>,
            required: true,
        }
    },
    inject: [
        'promotionCodeApiService',
        'repositoryFactory'
    ],
    computed: {
        promotionProductRepository() {
            return this.repositoryFactory.create('product_promotion')
        },
        promotionRepository() {
            return this.repositoryFactory.create('promotion')
        },
        propertyOptions() {

            const promotionProduct = Object.keys(Shopware.EntityDefinition.get('product').properties).sort().map(key => ({
                id: 'product.' + key,
                name: 'product.' + key
            }))

            const promotionProductTranslation = Object.keys(Shopware.EntityDefinition.get('product_translation').properties).sort().map(key => ({
                id: 'product.translated.' + key,
                name: 'product.translated.' + key
            }))

            const promotionValues = Object.keys(Shopware.EntityDefinition.get('promotion').properties).sort().map(key => ({
                id: 'promotion.' + key,
                name: key
            }))
            const promotionDiscountValues = Object.keys(Shopware.EntityDefinition.get('promotion_discount').properties).sort().map(key => ({
                id: 'promotion.discounts.' + key,
                name: 'discounts.' + key
            }))
            const promotionDiscountCodes = Object.keys(Shopware.EntityDefinition.get('promotion_individual_code').properties).sort().map(key => ({
                id: 'promotion.individualCodes.' + key,
                name: 'individualCodes.' + key
            }))
            return [...promotionProduct, ...promotionProductTranslation, ...promotionValues, ...promotionDiscountValues, ...promotionDiscountCodes]
        },
        xOptions() {
            return [
                {
                    id: 'left',
                    name: 'Linksbündig'
                },
                {
                    id: 'center',
                    name: 'Zentriert'
                },
                {
                    id: 'right',
                    name: 'Rechtsbündig'
                }
            ]
        }
    },
    data() {
        return {
            downloads: [] as EntitySchema.EntityCollection<'media'>,
            parentProduct: [] as EntitySchema.Entity<'product'>
        }
    },
    created() {
        this.parentProduct = Shopware.State.get('swProductDetail').parentProduct
    },
    async mounted() {
        const criteria = new Shopware.Data.Criteria()
        criteria.addAssociation('promotion')
        criteria.addAssociation('promotion.salesChannels')
        criteria.addAssociation('promotion.discounts')
        criteria.addAssociation('promotion.individualCodes')
        criteria.addAssociation('media')
        criteria.addAssociation('product')
        criteria.addFilter(Shopware.Data.Criteria.multi('or', (this.product.downloads || this.parentProduct.downloads).map(item => Shopware.Data.Criteria.equals('mediaId', item.mediaId))))
        criteria.addFilter(Shopware.Data.Criteria.equals('productId', this.product.id))
        const promotions = await this.promotionProductRepository.search(criteria)

        for (const download of (this.product.downloads || this.parentProduct.downloads)) {
            let promotion: EntitySchema.Entity<'product_promotion'> = promotions.find(item => item.mediaId === download.mediaId)
            if (!promotion) {
                promotion = this.promotionProductRepository.create()
                promotion.productId = this.product.id
                promotion.mediaId = download.mediaId
                promotion.media = download.media
                promotion.promotion = this.promotionRepository.create()
                promotion.promotionId = Shopware.Utils.createId()
                promotion.promotion.id = promotion.promotionId
                promotion.promotion.useCodes = true
                promotion.promotion.useIndividualCodes = true
                promotion.promotion.code = await this.promotionCodeApiService.generateCodeFixed()
                promotion.promotion.active = true
                promotion.promotion.maxRedemptionsPerCustomer = 1
                const discount = this.repositoryFactory.create('promotion_discount').create()
                discount.value = (this.product.price || Shopware.State.get('swProductDetail').parentProduct.price)[0].gross
                discount.type = "absolute"
                discount.scope = "cart"
                discount.considerAdvancedRules = false
                promotion.promotion.discounts.add(discount)
                promotions.add(promotion)
            }
            promotion.promotion.name = `${this.product.name} Promotion`
        }

        await this.promotionRepository.saveAll(promotions.map(item => item.promotion))
        await this.promotionProductRepository.saveAll(promotions)

        const savedPromotions = await this.promotionProductRepository.search(criteria)
        for (const promotion of savedPromotions) {
            if (!Array.isArray(promotion.attributes)) promotion.attributes = []
            await this.getFile(promotion)
        }
        this.downloads = savedPromotions
    },
    methods: {
        async addAttribute(download: EntitySchema.Entity<'product_promotion'>) {
            download.attributes.push({x: 'center', y: 100})
            await this.updateAttributes(download)
            this.$forceUpdate()
        },
        async updateAttributes(download: EntitySchema.Entity<'product_promotion'>) {
            await this.promotionProductRepository.save(download)
            await this.promotionRepository.save(download.promotion)
            await this.getFile(download)
            this.$refs[download.id].src = download.content
        },
        async getFile(promotion: EntitySchema.Entity<'product_promotion'>) {
            const headers = {
                Accept: this.contentType,
                Authorization: `Bearer ${Shopware.Service('loginService').getToken()}`,
                'Content-Type': 'application/json',
            };
            const blob = await (await fetch(`/api/promotionFile/${promotion.id}`, {
                headers
            })).blob()
            promotion.content = URL.createObjectURL(blob);
        }
    }
})
