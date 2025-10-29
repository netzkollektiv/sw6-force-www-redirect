<?php

declare(strict_types=1);

namespace Netzkollektiv\ForceWwwRedirect\EventSubscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForceNonWwwSubscriber implements EventSubscriberInterface
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

        // Check if redirect to non-www is enabled
        $isEnabled = $this->systemConfigService->get('NetzkollektivForceWwwRedirect.config.enableForceNonWww');
        if (!$isEnabled) {
            return;
        }

        $request = $event->getRequest();
        $host = $request->getHost();

        // doesn't have www -> no redirect
        if (!str_starts_with($host, 'www.')) {
            return;
        }

        // remove www. prefix
        $nonWwwHost = substr($host, 4);

        $scheme = $request->getScheme();       // http or https
        $uri = $request->getRequestUri();      // path + query
        $newUrl = sprintf('%s://%s%s', $scheme, $nonWwwHost, $uri);

        $response = new RedirectResponse($newUrl, RedirectResponse::HTTP_MOVED_PERMANENTLY);
        $event->setResponse($response);
    }
}
