<?php

namespace App\Shared\EventListener;

use App\Domain\User\Entity\UserInfo;
use App\Shared\Enum\Role;
use App\Shared\Enum\Status;
use App\Domain\User\Repository\UserInfoRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\InvalidTokenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthorizationListener implements EventSubscriberInterface
{
    private UserInfoRepository $userInfoRepository;
    private string $jwtSecret;


    private array $publicRoutes = [
        'POST:/api/auth/login',
        'GET:/api/doc', 
        'GET:/api/doc.json', 
        'POST:/api/ollama/generate-offer-message',
        'POST:/api/ollama/improve-message',
        'GET:/api/shared/transactions/details'

    ];


    private array $agentOnlyRoutes = [
        'POST:/api/clients',
        'PUT:/api/clients',
        'GET:/api/clients/my-clients',
        'GET:/api/exchange-offices/my-office',
        'POST:/api/transactions',
        'GET:/api/transactions/my-office',
        'POST:/api/agent/quick-message',
        'GET:/api/agent/quick-message',
        'GET:/api/agent/quick-messages',
        'POST:/api/marketing-campaigns',
        'GET:/api/marketing-campaigns',
        'GET:/api/marketing-campaigns/list',
        'PATCH:/api/marketing-campaigns/status',
        'POST:/api/agent/marketing-action',
        'GET:/api/agent/marketing-action',
        'GET:/api/agent/marketing-actions/by-campaign',
        'POST:/api/marketing-campaigns/add-target-clients',
        'DELETE:/api/marketing-campaigns/remove-target-clients',

    ];

    private array $adminOnlyRoutes = [
        'POST:/api/users',
        'GET:/api/users/agents/grouped-by-exchange-office',
        'PATCH:/api/users/status',
        'PUT:/api/users/update',
        'POST:/api/exchange-offices',
        'PUT:/api/exchange-offices',
        'DELETE:/api/exchange-offices',
        'GET:/api/exchange-offices',
        'GET:/api/clients/by-office',
        'GET:/api/clients/groupby_exchange_office',
        'GET:/api/clients/admin/by-exchange-office',
        'PATCH:/api/exchange-offices/status',
        'PUT:/api/users/reset-password',
        'GET:/api/transactions/by-exchange-office',
        'PUT:/api/transactions/update',
        'DELETE:/api/transactions/delete',
    ];

    private array $agentOrAdminRoutes = [
        'DELETE:/api/clients',
        'GET:/api/clients/details',
        'GET:/api/auth/me',
        'PUT:/api/users/change-password',
        'GET:/api/enums/genders',
        'GET:/api/enums/nationalities',
        'GET:/api/enums/acquisition-sources',
        'GET:/api/enums/roles',
        'GET:/api/enums/statuses',
        'GET:/api/enums/all',
        'GET:/api/enums/currencies',
        'GET:/api/enums/campaign-statuses',
        'GET:/api/transactions/by-client',
        'GET:/api/countries',
        'GET:/api/users',
        'GET:/api/clients/segment-history',
        'GET:/api/enums/channel-types',
        ];

    public function __construct(UserInfoRepository $userInfoRepository)
    {
        $this->userInfoRepository = $userInfoRepository;
        $this->jwtSecret = $_ENV['JWT_SECRET'] ;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();
        $method = $request->getMethod();
        $methodPath = $method . ':' . $path;
        if ($method === 'OPTIONS') {
            $event->setResponse(new JsonResponse(null, Response::HTTP_NO_CONTENT));
            return;
        }


        if ($this->matchesRoutes($methodPath, $this->publicRoutes)) {
            return;
        }

        if ($this->matchesRoutes($methodPath, $this->agentOnlyRoutes)) {
            $user = $this->requireAgentAccess($request);
            if ($user instanceof JsonResponse) {
                $event->setResponse($user);
                return;
            }
            $request->attributes->set('authenticated_user', $user);
            return;
        }

        if ($this->matchesRoutes($methodPath, $this->adminOnlyRoutes)) {
            $user = $this->requireAdminAccess($request);
            if ($user instanceof JsonResponse) {
                $event->setResponse($user);
                return;
            }
            $request->attributes->set('authenticated_user', $user);
            return;
        }

        if ($this->matchesRoutes($methodPath, $this->agentOrAdminRoutes)) {
            $user = $this->requireAgentOrAdminAccess($request);
            if ($user instanceof JsonResponse) {
                $event->setResponse($user);
                return;
            }
            $request->attributes->set('authenticated_user', $user);
            return;
        }

        if (str_starts_with($path, '/api/') && !str_contains($path, '/doc')) {
            $event->setResponse(new JsonResponse([
                'error' => 'Endpoint not configured for authentication',
                'message' => 'This endpoint requires security configuration',
                'path' => $methodPath
            ], Response::HTTP_FORBIDDEN));
        }
    }

    public function requireAgentAccess(Request $request): UserInfo|JsonResponse
    {
        $currentUser = $this->getUserFromToken($request);
        if (!$currentUser) {
            return new JsonResponse(['error' => 'Access denied. Invalid token.'], Response::HTTP_UNAUTHORIZED);
        }

        if ($currentUser->getRole() !== Role::AGENT) {
            return new JsonResponse(['error' => 'Access denied. Only agents can access this resource.'], Response::HTTP_FORBIDDEN);
        }

        return $currentUser;
    }

    public function requireAdminAccess(Request $request): UserInfo|JsonResponse
    {
        $currentUser = $this->getUserFromToken($request);
        if (!$currentUser) {
            return new JsonResponse(['error' => 'Access denied. Invalid token.'], Response::HTTP_UNAUTHORIZED);
        }

        if ($currentUser->getRole() !== Role::ADMIN) {
            return new JsonResponse(['error' => 'Access denied. Only administrators can access this resource.'], Response::HTTP_FORBIDDEN);
        }

        return $currentUser;
    }

    public function requireAgentOrAdminAccess(Request $request): UserInfo|JsonResponse
    {
        $currentUser = $this->getUserFromToken($request);
        if (!$currentUser) {
            return new JsonResponse(['error' => 'Access denied. Invalid token.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!in_array($currentUser->getRole(), [Role::AGENT, Role::ADMIN])) {
            return new JsonResponse(['error' => 'Access denied. Only agents and administrators can access this resource.'], Response::HTTP_FORBIDDEN);
        }

        return $currentUser;
    }

    private function getUserFromToken(Request $request): ?UserInfo
    {
        $authHeader = $request->headers->get('Authorization');
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            
            if (!isset($decoded->email)) {
                return null;
            }

            $user = $this->userInfoRepository->findOneByEmail($decoded->email);
            
            if (!$user || $user->getStatus() !== Status::ACTIVE) {
                return null;
            }

            return $user;
        } catch (ExpiredException $e) {
            return null;
        } catch (InvalidTokenException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function matchesRoutes(string $methodPath, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $regex = str_replace(['{id}', '{', '}'], ['[^/]+', '\{', '\}'], $pattern);
            $regex = str_replace('/', '\/', $regex);
            $regex = '/^' . $regex . '$/';
            
            if (preg_match($regex, $methodPath)) {
                return true;
            }
        }
        
        return false;
    }

    public function addPublicRoute(string $methodPath): void
    {
        if (!in_array($methodPath, $this->publicRoutes)) {
            $this->publicRoutes[] = $methodPath;
        }
    }

    public function addAgentOnlyRoute(string $methodPath): void
    {
        if (!in_array($methodPath, $this->agentOnlyRoutes)) {
            $this->agentOnlyRoutes[] = $methodPath;
        }
    }

    public function addAdminOnlyRoute(string $methodPath): void
    {
        if (!in_array($methodPath, $this->adminOnlyRoutes)) {
            $this->adminOnlyRoutes[] = $methodPath;
        }
    }

    public function addAgentOrAdminRoute(string $methodPath): void
    {
        if (!in_array($methodPath, $this->agentOrAdminRoutes)) {
            $this->agentOrAdminRoutes[] = $methodPath;
        }
    }

    public function getPublicRoutes(): array
    {
        return $this->publicRoutes;
    }

    public function getAgentOnlyRoutes(): array
    {
        return $this->agentOnlyRoutes;
    }

    public function getAdminOnlyRoutes(): array
    {
        return $this->adminOnlyRoutes;
    }

    public function getAgentOrAdminRoutes(): array
    {
        return $this->agentOrAdminRoutes;
    }
}
