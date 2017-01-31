var thisScript = document.getElementsByTagName('script')[document.getElementsByTagName('script').length - 1];
var subscriber = (function (options) {
    subscriber = {

        options     : {
            notificationUrl       : null,
            onNewSubscription     : null,
            onExistingSubscription: null,
            onServerMessage       : null
        },
        worker      : null,
        subscription: null,
        isNew       : false,

        isSubscribed: function () {
            return null !== this.subscription;
        },

        getChannel: function () {
            return new MessageChannel()
        },

        sendToSW: function sendToSW(message) {
            return this.worker.postMessage(message, [this.getChannel().port2]);
        },

        subscribe: function () {
            var that = this;

            // Register a Service Worker.
            return navigator.serviceWorker.register(thisScript.src.replace('pushClient.js', 'pushServiceWorker.js'))
                .then(function (registration) {

                    that.worker = registration.active || registration.installing;

                    // Use the PushManager to get the user's subscription to the push service.
                    return registration.pushManager.getSubscription()

                        .then(function (subscription) {

                            // If a subscription was found, return it.
                            if (subscription) {
                                that.subscription = subscription;
                                return that.notifyExistingSubscription(subscription);
                            }

                            else {
                                return registration.pushManager.subscribe({userVisibleOnly: true}).then(function (subscription) {
                                    that.subscription = subscription;
                                    that.isNew        = true;
                                    return that.notifyNewSubscription(subscription);
                                });
                            }

                        });
                });
        },

        notifyExistingSubscription: function(subscription) {
            var that = this;
            if (that.options.onExistingSubscription instanceof Function) {
                that.options.onExistingSubscription(subscription);
            }
            return that;
        },

        notifyNewSubscription: function(subscription) {
            var that = this;
            if (that.options.onNewSubscription instanceof Function) {
                that.options.onNewSubscription(subscription);
            }
            return that;
        },

        initIncomingMessagesHandler: function() {
            var that     = this;
            if (that.options.onServerMessage instanceof Function) {
                navigator.serviceWorker.addEventListener('message', function (event) {
                    var jsonData = JSON.parse(event.data);
                    that.options.onServerMessage(event, jsonData.serverMessage);
                });
            }
            return that;
        },

        init: function (options) {
            var that     = this;
            this.options = options || {};
            return this.subscribe().then(function () {
                for (key in that.options) {
                    if ('notificationUrl' === key) {
                        that.sendToSW(JSON.stringify({set: {notificationUrl: that.options[key]}}));
                    }
                }
                return that.initIncomingMessagesHandler();
            });
        }
    };
    return subscriber.init(options);
});
