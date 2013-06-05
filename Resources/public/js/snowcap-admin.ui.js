SnowcapAdmin.Ui = (function() {
    /**
     * Admin modal form view
     *
     */
    var Modal = SnowcapBootstrap.Modal.extend({
        initialize: function() {
            SnowcapBootstrap.Modal.prototype.initialize.apply(this);
            this.off('ui:modal:render');
            this.on('ui:modal:render', SnowcapAdmin.Form.collectionFactory);
            this.on('ui:modal:render', SnowcapAdmin.Form.textAutocompleteFactory);
            this.on('ui:modal:render', SnowcapAdmin.Form.autocompleteFactory);
            this.on('ui:modal:success', this.success);
        },

        success: function(data) {
            $('[data-admin=ui-alerts]').admin_ui_alerts('addMessages', data.flashes, true);
        }

    });

    /**
     * Modal factory function
     *
     * @param $context
     */
    var modalFactory = function() {
        var $context = (0 === arguments.length) ? $('body') : arguments[0];
        $context.find('[data-admin=content-modal]').each(function(offset, modalTrigger) {
            var $modalTrigger = $(modalTrigger);
            $($modalTrigger).on('click', function(event) {
                event.preventDefault();
                var options = {
                    url: $modalTrigger.attr('href')
                };
                if($modalTrigger.data('options-modal-class')) {
                    options.modalClass = $modalTrigger.data('options-modal-class');
                }
                new SnowcapAdmin.Ui.Modal(options);
            });
        });
    };

    var Alerts = Backbone.View.extend({
        events: {
            'click [data-dismiss=alert]': 'removeMessage'
        },
        initialize: function() {
            this.$el.find('.alert').each(_.bind(function(index, alert) {
                this.scheduleForRemoval($(alert));
            }, this));
        },
        addMessages: function(messages, clear) {
            if('undefined' === typeof clear) {
                clear = false;
            }
            if(clear) {
                this.clear();
            }
            _.each(messages, _.bind(function(typedMessages, type) {
                _.each(typedMessages, _.bind(function(message) {
                    this.addMessage(type, message);
                }, this));
            }, this));
        },
        addMessage: function(type, message) {
            var $message = $('<div class="alert alert-' + type + '">');
            $message.append($('<a href="#" class="close" data-dismiss="alert">&times;</a> '));
            $message.append(message);
            this.$el.append($message);
            this.scheduleForRemoval($message);
        },
        scheduleForRemoval: function($message) {
            setTimeout(function() {
                $message.slideUp($message.remove);
            }, 5000);
        },
        removeMessage: function(event) {
            event.preventDefault();
            var $message = $(event.currentTarget).parents('.alert');
            $message.slideUp($message.remove);
        },
        clear: function() {
            this.$el.html('');
        }
    });

    /**
     *
     * @returns {*}
     */
    $.fn.admin_ui_alerts = function () {
        var method, args;
        if(arguments.length > 0) {
            method = arguments[0];
            args = _.toArray(arguments).slice(1);
        }
        return this.each(function () {
            var
                $this = $(this),
                alerts = $this.data('snowcap_admin_ui_alerts');
            if (!alerts) {
                $this.data('snowcap_admin_ui_alerts', alerts = new Alerts({el: $this}));
            }
            if('undefined' !== typeof method) {
                alerts[method].apply(alerts, args);
            }
        })
    };

    return {
        Modal: Modal,
        modalFactory: modalFactory
    }
})();

(function($) {

    SnowcapAdmin.Ui.modalFactory();
    $('[data-admin=ui-alerts]').admin_ui_alerts();

})(jQuery);