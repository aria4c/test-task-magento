define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko'
], function (Component, customerData, ko) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            this.cartData = customerData.get('cart');

            this.dependencyMessageCart = ko.pureComputed(function () {
                return this.cartData() && this.cartData().dependency_message_cart;
            }, this);

            this.hasDependencyMessageCart = ko.pureComputed(function () {
                return this.dependencyMessageCart() && this.dependencyMessageCart();
            }, this);


            return this;
        }
    });
});
