importScripts('//cdnjs.cloudflare.com/ajax/libs/localforage/1.4.3/localforage.js');

self.sendMessageToClients = function (message) {
    clients.matchAll({includeUncontrolled: true})
        .then(function (windowClients) {
            for (i = 0; i < windowClients.length; i++) {
                windowClients[i].postMessage(message);
            }
        });
};

self.showJsonNotification = function (jsonData) {
    self.sendMessageToClients(JSON.parse(jsonData));
    return self.registration.showNotification(jsonData.notification.title, jsonData.notification);
};

self.showTextNotification = function (payload) {
    return self.registration.showNotification('Notification', {
        body: payload
    });
};

self.fetchNotificationData = function () {
    return localforage.getItem('notificationUrl').then(function (notificationUrl) {
        self.registration.pushManager.getSubscription().then(function (subscription) {
            return fetch(notificationUrl, {
                method     : 'POST',
                mode       : 'cors',
                credentials: 'include',
                cache      : 'default',
                headers    : new Headers({
                    'Accept'      : 'application/json',
                    'Content-Type': 'application/json'
                }),
                body       : JSON.stringify(subscription)
            });
        });
    });
};

self.processMessage = function (payload) {

    // No payload ==> retrieve notification from server
    if ('undefined' === typeof payload) {
        localforage.getItem('notificationUrl').then(function (notificationUrl) {
            if (null === notificationUrl) {
                throw new Error("Unknown notification url");
            }
            self.registration.pushManager.getSubscription().then(function (subscription) {
                return fetch(notificationUrl, {
                    method     : 'POST',
                    mode       : 'cors',
                    credentials: 'include',
                    cache      : 'default',
                    headers    : new Headers({
                        'Accept'      : 'application/json',
                        'Content-Type': 'application/json'
                    }),
                    body       : JSON.stringify(subscription)
                })
                    .then(function(response) {
                    return response.json().then(function(data) {
                        return self.showJsonNotification(data);
                    });
                });
            });
        }).catch(function() {
            return self.registration.showNotification('Notification', {
                body: 'An update is available'
            });
        })
    }

    // Payload ==> try to parse it
    else {
        try {
            var jsonData = JSON.parse(payload);
            var promises = [];
            for (key in jsonData) {
                if ('notification' === key) {
                    promises.push(self.registration.showNotification(jsonData.notification.title, jsonData.notification));
                }
                else if ('serverMessage' === key) {
                    promises.push(self.sendMessageToClients(payload));
                }
            }
            return Promise.race(promises);
        } catch (e) {
            return self.registration.showNotification('Notification', {
                body: payload
            });
        }
    }

};

// Register event listener for the 'push' event.
self.addEventListener('push', function (event) {
    console.log('SW received push event', event);
    var pushMessageData = event.data;
    var payload         = pushMessageData ? pushMessageData.text() : undefined;
    event.waitUntil(self.processMessage(payload));
});

self.addEventListener('notificationclick', function (event) {

    event.notification.close();
    var url = event.notification.data.link;

    if (url.length > 0) {

        event.waitUntil(
            clients.matchAll({
                type: 'window'
            })
                .then(function (windowClients) {
                    for (var i = 0; i < windowClients.length; i++) {
                        var client = windowClients[i];
                        if (client.url === url && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    if (clients.openWindow) {
                        return clients.openWindow(url);
                    }
                })
        );

    }
});


self.addEventListener('pushsubscriptionchange', function (event) {
    console.log('Subscription expired');
    event.waitUntil(
        self.registration.pushManager.subscribe({userVisibleOnly: true})
            .then(function (subscription) {
                console.log('Subscribed after expiration', subscription.endpoint);
                /*return fetch('register', {
                 method: 'post',
                 headers: {
                 'Content-type': 'application/json'
                 },
                 body: JSON.stringify({
                 endpoint: subscription.endpoint
                 })
                 });*/
            })
    );
});

self.addEventListener('message', function (event) {

    var data = JSON.parse(event.data);

    if (undefined !== data.set) {
        var promises = [];
        for (key in data.set) {
            promises.push(localforage.setItem(key, data.set[key]));
        }
        event.waitUntil(Promise.race(promises));
    }
});