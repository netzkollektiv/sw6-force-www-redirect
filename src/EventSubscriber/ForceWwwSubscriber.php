<?php

declare(strict_types=1);

namespace Netzkollektiv\ForceWwwRedirect\EventSubscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForceWwwSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Check if redirect to www is enabled
        $isEnabled = $this->systemConfigService->get('NetzkollektivForceWwwRedirect.config.enableForceWww');
        if (!$isEnabled) {
            return;
        }

        $request = $event->getRequest();
        $host = $request->getHost();

        // already has www -> no redirect
        if (str_starts_with($host, 'www.')) {
            return;
        }

        $scheme = $request->getScheme();       // http or https
        $uri = $request->getRequestUri();      // path + query
        $newUrl = sprintf('%s://www.%s%s', $scheme, $host, $uri);

        $response = new RedirectResponse($newUrl, RedirectResponse::HTTP_MOVED_PERMANENTLY);
        $event->setResponse($response);
    }
}
