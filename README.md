PHP Webpush API implementation
=========

Allows to send messages via WebPushAPI, on different providers (Google Chrome, Mozilla), asynchronously, on multiple recipients, using multiple API keys.

> [!IMPORTANT]  
> This repository is no longer maintained and may be removed in a near future. You may consider creating a fork if you still require it.
  
Looks stable, but still experimental.

Pusher is inspired and based on the awesome [minishlink/web-push](https://github.com/web-push-libs/web-push-php) library, but with a different approach:
* Guzzle 6 has been prefered for sending messages, thanks to its asynchronous and parallel requests management
* A **Push** object is a bag that contains a **Message**, and **Recipients** associated to their **handlers**.
* A **Handler** is responsible for the correct delivery of a **Message** to a **Recipient** - currently implemented: GCM handler, Mozilla handler.
* Multiple **Handlers** can be used for a single **Push**, and a **Handler** may have several  instances (i.e. when you use multiple GCM API keys)
* Every **Handler** must be able to return a _Promise_ for handling a **Push**, to make things asynchronous and allow bulk processing
* The **Pusher** service is responsible to send a **Push** and change its state (_pending_ => _done_).
* When the **Push** is done, it can tell which **Recipients** have not received the message and why (you may then unsubscribe them)

Several types of **Message** are implemented:
* A **Ping** message is a message without payload. Usually your service worker should fetch the payload at that moment (as it was in the earlier Webpush API implementations).
* A **Notification** message contains a json with all the info to display a Webpush notification (title, body, icon, ...)
* A **ServerMessage** contains a JSON which should be handled by your service worker to be sent to an active window instead of displaying a notification. This may help in changing remotely the DOM of an opened page.

Some example JS files are provided.

Installation
-----
`composer require bentools/pusher`


Example usage
---------
Consider the following _subscription_ object:
```json
{
  "endpoint": "https://updates.push.services.mozilla.com/wpush/v1/gAAAAABYmwfiuCps0P3TPZXSNc8aWol6_2Nqu0VVY6lpJ_xsIrtC8YyfPz_XnobR_Wh2PezdDZFonsfoezNsXykv4",
  "keys": {
    "auth": "5coZoiZAodiBZHCkWX5LoAbA",
    "p256dh": "BHI7P_CAsz3knooINFZZPFONPYTRTzEacYpOx4-hVigOuWjzkRWdkTZmmrAI3U11_z-lU"
  }
}
```

```php

use BenTools\Pusher\Model\Message\Notification;
use BenTools\Pusher\Model\Push\Push;
use BenTools\Pusher\Model\Handler\MozillaHandler;
use BenTools\Pusher\Model\Recipient\Recipient;
use BenTools\Pusher\Pusher;
use GuzzleHttp\Client as GuzzleClient;

require_once __DIR__ . '/vendor/autoload.php';

$recipient = Recipient::unwrapJSON($json);
$guzzle    = new GuzzleClient();
$mozilla   = new MozillaHandler($guzzle);
$pusher    = new Pusher();
$push      = new Push();
$push->addRecipient($recipient, $mozilla);

$message   = new Notification(
    'Sounds interesting', // title
    'Seems to be working :)', // body
    'https://pbs.twimg.com/profile_images/555076551818354689/F26py9T__reasonably_small.png', // icon
    'https://github.com/bpolaszek/bentools-pusher', // link
    'hello world' // tag
);
$message->setTTL(60);

$push->setMessage($message);
$pusher->push($push);

if ($push->hasErrors()) {
    foreach ($push->getFailedRecipients() AS $recipient) {
        echo $push->getFailureReason($recipient);
        // Remove recipient from database or set it inactive
    }
}


```

TODO
-------
* Implement VAPID authentication
* Google FCM handler
* Tests
* Recipes


License
-------
MIT
