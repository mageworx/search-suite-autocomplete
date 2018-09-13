define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'underscore'
], function ($, Component, registry, _) {
    'use strict';

    $.Suggestion = function (data) {
        this.url = data.url;
        this.title = data.title;
        this.num_results = data.num_results;
    };

    $.Product = function (data) {
        this.name = data.name;
        this.sku = data.sku;
        this.image = data.image;
        this.reviews_rating = data.reviews_rating;
        this.short_description = data.short_description;
        this.description = data.description;
        this.price = data.price;
        this.url = data.url;
        this.add_to_cart = data.add_to_cart;
    };

    return Component.extend({
        defaults: {
            localStorage: $.initNamespaceStorage('searchsuiteautocomplete').localStorage,
            searchText: ''
        },

        load: function () {
            var self = this;

            if (this.xhr) {
                this.xhr.abort();
            }

            this.xhr = $.ajax({
                method: "get",
                dataType: "json",
                url: this.url,
                data: {q: this.searchText},
                beforeSend: function () {
                    self.spinnerShow();
                    if (self.loadFromLocalSorage(self.searchText)) {
                        self.showPopup();
                    }
                },
                success: $.proxy(function (response) {
                    self.parseData(response);
                    self.saveToLocalSorage(response, self.searchText);
                    self.spinnerHide();
                    self.showPopup();
                })
            });
        },

        showPopup: function () {
            registry.get('searchsuiteautocompleteBindEvents', function (binder) {
                binder.showPopup();
            });
        },

        spinnerShow: function () {
            registry.get('searchsuiteautocompleteBindEvents', function (binder) {
                binder.spinnerShow();
            });
        },

        spinnerHide: function () {
            registry.get('searchsuiteautocompleteBindEvents', function (binder) {
                binder.spinnerHide();
            });
        },

        parseData: function (response) {
            this.setSuggested(this.getResponseData(response, 'suggest'));
            this.setProducts(this.getResponseData(response, 'product'));
        },

        getResponseData: function (response, code) {
            var data = []

            if (_.isUndefined(response.result)) {
                return data;
            }

            $.each(response.result, function (index, obj) {
                if (obj.code == code) {
                    data = obj;
                }
            });

            return data;
        },

        setSuggested: function (suggestedData) {
            var suggested = [];

            if (!_.isUndefined(suggestedData.data)) {
                suggested = $.map(suggestedData.data, function (suggestion) {
                    return new $.Suggestion(suggestion) });
            }

            registry.get('searchsuiteautocomplete_form', function (autocomplete) {
                autocomplete.result.suggest.data(suggested);
            });
        },

        setProducts: function (productsData) {
            var products = [];

            if (!_.isUndefined(productsData.data)) {
                products = $.map(productsData.data, function (product) {
                    return new $.Product(product)
                });
            }

            registry.get('searchsuiteautocomplete_form', function (autocomplete) {
                autocomplete.result.product.data(products);
                autocomplete.result.product.size(productsData.size);
                autocomplete.result.product.url(productsData.url);
            });
        },

        loadFromLocalSorage: function (queryText) {
            if (!this.localStorage) {
                return;
            }

            var hash = this._hash(queryText);
            var data = this.localStorage.get(hash);

            if (!data) {
                return false;
            }

            this.parseData(data);

            return true;
        },

        saveToLocalSorage: function (data, queryText) {
            if (!this.localStorage) {
                return;
            }

            var hash = this._hash(queryText);

            this.localStorage.remove(hash);
            this.localStorage.set(hash, data);
        },

        _hash: function (object) {
            var string = JSON.stringify(object) + "";

            var hash = 0, i, chr, len;
            if (string.length == 0) {
                return hash;
            }
            for (i = 0, len = string.length; i < len; i++) {
                chr = string.charCodeAt(i);
                hash = ((hash << 5) - hash) + chr;
                hash |= 0;
            }
            return 'h' + hash;
        }

    });
});
