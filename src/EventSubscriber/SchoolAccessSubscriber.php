<?php
namespace App\EventSubscriber;

use App\Dto\SchoolDto;
use App\Security\Voter\SchoolAccessVoter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class SchoolAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authChecker,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER_ARGUMENTS => 'onControllerArguments'];
    }

    public function onControllerArguments(ControllerArgumentsEvent $event): void
    {
        $controller = $event->getController();
        $controllerObject = is_array($controller) ? $controller[0] : $controller;

        if (!str_starts_with($controllerObject::class, 'App\\Controller\\Api\\Admin\\School\\')) {
            return;
        }

        foreach ($event->getArguments() as $argument) {
            if ($argument instanceof SchoolDto) {
                if (!$this->authChecker->isGranted(SchoolAccessVoter::ACCESS, $argument)) {
                    throw new AccessDeniedHttpException('You do not have access to this school.');
                }
                return;
            }
        }
    }
}
