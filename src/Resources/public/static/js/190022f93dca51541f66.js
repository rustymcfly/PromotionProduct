"use strict";(window["webpackJsonpPluginpromotion-product"]=window["webpackJsonpPluginpromotion-product"]||[]).push([[118],{118:function(t,o,e){e.r(o),e.d(o,{default:function(){return i}});var i={template:'<div>\r\n    <div>Product Promotions</div>\r\n    <div v-for="download in downloads">\r\n        <p>{{ download.promotion.name }}</p>\r\n        <div style="display:flex; justify-content: space-between">\r\n            <div style="display: flex; flex-direction: column; width: 90%">\r\n                <sw-promotion-v2-sales-channel-select\r\n                        :promotion="download.promotion"\r\n                        @change="updateAttributes(download)"\r\n                        :entity-collection="download.promotion.salesChannels"></sw-promotion-v2-sales-channel-select>\r\n                <sw-field v-model="download.promotion.individualCodePattern" label="Individual Code Pattern"\r\n                          @change="updateAttributes(download)"></sw-field>\r\n                <div v-for="(attribute, index) in download.attributes"\r\n                     style="border-bottom: 1px solid; margin-bottom: 1rem">\r\n                    <div>\r\n                        <div style="display:flex; justify-content: end;"><span\r\n                                    @click="download.attributes.splice(index, 1)">&times</span></div>\r\n                        <sw-select-field v-model="attribute.property" label="Property" :options="propertyOptions"\r\n                                         @change="updateAttributes(download)"></sw-select-field>\r\n                        <sw-field v-model="attribute.y" label="Position Y (mm)"\r\n                                  @change="updateAttributes(download)"></sw-field>\r\n                        <sw-select-field v-model="attribute.x" label="Position X" :options="xOptions"\r\n                                         @change="updateAttributes(download)"></sw-select-field>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            <sw-button @click="addAttribute(download)">+</sw-button>\r\n        </div>\r\n        <div style="width: 100%">\r\n            <iframe :src="download.content" :ref="download.id"\r\n                    style="height: calc(6rem + 297mm); width: 210mm; object-fit: contain"></iframe>\r\n        </div>\r\n    </div>\r\n</div>\r\n',props:{product:{type:Object,required:!0}},inject:["promotionCodeApiService","repositoryFactory"],computed:{promotionProductRepository(){return this.repositoryFactory.create("product_promotion")},promotionRepository(){return this.repositoryFactory.create("promotion")},propertyOptions(){let t=[];Object.entries(Shopware.EntityDefinition.get("product").properties).sort().map(([o,e])=>{t.push({id:"product."+o,name:"product."+o})});let o=Object.keys(Shopware.EntityDefinition.get("product_translation").properties).sort().map(t=>({id:"product.translated."+t,name:"product.translated."+t}));return[...t,...o,...Object.keys(Shopware.EntityDefinition.get("promotion").properties).sort().map(t=>({id:"promotion."+t,name:t})),...Object.keys(Shopware.EntityDefinition.get("promotion_discount").properties).sort().map(t=>({id:"promotion.discounts."+t,name:"discounts."+t})),...Object.keys(Shopware.EntityDefinition.get("promotion_individual_code").properties).sort().map(t=>({id:"promotion.individualCodes."+t,name:"individualCodes."+t}))]},xOptions(){return[{id:"left",name:"Linksb\xfcndig"},{id:"center",name:"Zentriert"},{id:"right",name:"Rechtsb\xfcndig"}]}},data(){return{downloads:[],parentProduct:{}}},created(){this.parentProduct=Shopware.State.get("swProductDetail").parentProduct},async mounted(){let t=new Shopware.Data.Criteria;t.addAssociation("promotion"),t.addAssociation("promotion.salesChannels"),t.addAssociation("promotion.discounts"),t.addAssociation("promotion.individualCodes"),t.addAssociation("media"),t.addAssociation("product"),t.addFilter(Shopware.Data.Criteria.multi("or",(this.product.downloads||this.parentProduct.downloads).map(t=>Shopware.Data.Criteria.equals("mediaId",t.mediaId)))),t.addFilter(Shopware.Data.Criteria.equals("productId",this.product.id));let o=await this.promotionProductRepository.search(t);for(let t of this.product.downloads||this.parentProduct.downloads){let e=o.find(o=>o.mediaId===t.mediaId);if(!e){(e=this.promotionProductRepository.create()).productId=this.product.id,e.mediaId=t.mediaId,e.media=t.media,e.promotion=this.promotionRepository.create(),e.promotionId=Shopware.Utils.createId(),e.promotion.id=e.promotionId,e.promotion.useCodes=!0,e.promotion.useIndividualCodes=!0,e.promotion.code=await this.promotionCodeApiService.generateCodeFixed(),e.promotion.active=!0,e.promotion.maxRedemptionsPerCustomer=1;let i=this.repositoryFactory.create("promotion_discount").create();i.value=(this.product.price||Shopware.State.get("swProductDetail").parentProduct.price)[0].gross,i.type="absolute",i.scope="cart",i.considerAdvancedRules=!1,e.promotion.discounts.add(i),o.add(e)}e.promotion.name=`${this.product.name} Promotion`}await this.promotionRepository.saveAll(o.map(t=>t.promotion)),await this.promotionProductRepository.saveAll(o);let e=await this.promotionProductRepository.search(t);for(let t of e)Array.isArray(t.attributes)||(t.attributes=[]),await this.getFile(t);this.downloads=e},methods:{async addAttribute(t){t.attributes.push({x:"center",y:100}),await this.updateAttributes(t),this.$forceUpdate()},async updateAttributes(t){await this.promotionProductRepository.save(t),await this.promotionRepository.save(t.promotion),await this.getFile(t),this.$refs[t.id].src=t.content},async getFile(t){let o={Accept:this.contentType,Authorization:`Bearer ${Shopware.Service("loginService").getToken()}`,"Content-Type":"application/json"},e=await (await fetch(`/api/promotionFile/${t.id}`,{headers:o})).blob();t.content=URL.createObjectURL(e)}}}}}]);