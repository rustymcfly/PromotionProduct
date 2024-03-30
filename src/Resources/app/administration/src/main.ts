import template from './sw-product-detail-base.html.twig'


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

Shopware.Component.register('product-promotions', () => import('./product-promotions'))
