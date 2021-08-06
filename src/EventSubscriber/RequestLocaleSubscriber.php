<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestLocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected array $appLocales;

    /**
     * RequestLocaleSubscriber constructor.
     */
    public function __construct(string $appLocales)
    {
        $this->appLocales = explode(',', $appLocales);
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $acceptLanguage = $request->headers->get("accept-language");
        if (empty($acceptLanguage)) {
            return;
        }

        // Symfony expects underscore instead of dash in locale
        $locale = str_replace('-', '_', $acceptLanguage);
        if (in_array($locale, $this->appLocales)) {
            $request->setLocale($locale);
        }
    }

    /**
     * @return array[][]
     */
    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
