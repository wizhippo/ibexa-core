<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Security\Authentication;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler as BaseSuccessHandler;

final class DefaultAuthenticationSuccessHandler extends BaseSuccessHandler
{
    private EventDispatcherInterface $eventDispatcher;

    private ConfigResolverInterface $configResolver;

    private PermissionResolver $permissionResolver;

    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setPermissionResolver(PermissionResolver $permissionResolver): void
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface && isset($this->permissionResolver)) {
            $this->permissionResolver->setCurrentUserReference($user->getAPIUser());
        }

        return parent::onAuthenticationSuccess($request, $token);
    }

    protected function determineTargetUrl(Request $request): string
    {
        if (isset($this->configResolver)) {
            $defaultPage = $this->configResolver->getParameter('default_page');
            if ($defaultPage !== null) {
                $this->setOptions([
                    'default_target_path' => $defaultPage,
                ]);
            }
        }

        if (isset($this->eventDispatcher)) {
            $event = new DetermineTargetUrlEvent(
                $request,
                $this->getOptions(),
                $this->getFirewallName() ?? ''
            );

            $this->eventDispatcher->dispatch($event);

            $this->setOptions($event->getOptions());
        }

        return parent::determineTargetUrl($request);
    }
}
