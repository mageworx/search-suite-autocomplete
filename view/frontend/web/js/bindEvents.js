define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mageUtils'
], function ($, Component, registry, utils) {
    'use strict';

    return Component.extend({
        defaults: {
            minSearchLength: 2
        },

        initialize: function () {
            this._super();

            utils.limit(this, 'load', this.searchDelay); // execute 'load' after delay

            $(this.inputSelector)
                .unbind('input') // unbind all magento events
                .on('input', $.proxy(this.load, this)) // bind searchsuiteautocomplete load event
                .on('input', $.proxy(this.searchButtonStatus, this)) // bind show/hide search button event
                .on('focus', $.proxy(this.showPopup, this)); // bind show popup event
            $(document).on('click', $.proxy(this.hidePopup, this)); // bind hide popup event

            $(document).ready($.proxy(this.searchButtonStatus, this));
        },

        load: function (event) {
            var self = this;
            var searchText = $(self.inputSelector).val();

            if (searchText.length < self.minSearchLength) {
                return false;
            }

            registry.get('searchsuiteautocompleteDataProvider', function (dataProvider) {
                dataProvider.searchText = searchText;
                dataProvider.load();
            });
        },

        showPopup: function (event) {
            var self = this,
                searchField = $(self.inputSelector),
                searchFieldHasFocus = searchField.is(':focus') && searchField.val().length >= self.minSearchLength;

            registry.get('searchsuiteautocomplete_form', function (autocomplete) {
                autocomplete.showPopup(searchFieldHasFocus);
            });
        },

        hidePopup: function (event) {
            if ($(this.searchFormSelector).has($(event.target)).length <= 0) {
                registry.get('searchsuiteautocomplete_form', function (autocomplete) {
                    autocomplete.showPopup(false);
                });
            }
        },

        searchButtonStatus: function (event) {
            var self = this,
                searchField = $(self.inputSelector),
                searchButton = $(self.searchFormSelector + ' ' + self.searchButtonSelector),
                searchButtonDisabled = (searchField.val().length > 0) ? false : true;

            searchButton.attr('disabled', searchButtonDisabled);
        },

        spinnerShow: function () {
            var spinner = $(this.searchFormSelector);
            spinner.addClass('loading');
        },

        spinnerHide: function () {
            var spinner = $(this.searchFormSelector);
            spinner.removeClass('loading');
        }

    });
});
