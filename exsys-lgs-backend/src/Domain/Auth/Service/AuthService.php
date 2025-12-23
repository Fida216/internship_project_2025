<?php

namespace App\Domain\Auth\Service;

use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserCredential;
use App\Shared\Enum\Status;
use App\Domain\Auth\DTO\LoginDTO;
use App\Domain\Auth\DTO\AuthResponseDTO;
use App\Domain\Auth\Mapper\AuthMapper;
use App\Domain\User\Repository\UserInfoRepository;
use App\Shared\Service\ErrorCodeResolver;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthService
{
    private UserInfoRepository $userInfoRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private AuthMapper $authMapper;
    private ErrorCodeResolver $errorCodeResolver;
    private string $jwtSecret;

    public function __construct(
        UserInfoRepository $userInfoRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        AuthMapper $authMapper,
        ErrorCodeResolver $errorCodeResolver
    ) {
        $this->userInfoRepository = $userInfoRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->authMapper = $authMapper;
        $this->errorCodeResolver = $errorCodeResolver;
        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }



    public function authenticate(LoginDTO $loginDto): array
{
    $violations = $this->validator->validate($loginDto);

    if (count($violations) > 0) {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }
        throw new \InvalidArgumentException(implode(', ', $errors));
    }

    $user = $this->userInfoRepository->findOneByEmail($loginDto->email);

    if (!$user || !password_verify($loginDto->password, $user->getPassword())) {
        throw new \InvalidArgumentException('Invalid credentials');
    }

    if ($user->getStatus() !== Status::ACTIVE) {
        throw new \InvalidArgumentException('Account disabled');
    }

    $token = $this->generateJwtToken($user);
    $authResponseDto = $this->authMapper->toAuthResponseDto($token, $user);

    return $authResponseDto->toArray();
}


    public function generateJwtToken(UserInfo $user): string
    {
        $payload = [
            'user_id' => $user->getId(),
            'lastName' => $user->getlastName(),
            'firstName'=> $user->getFirstName(),
            'email' => $user->getUserIdentifier(),
            'role' => $user->getRole(),
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60)
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function getCurrentUserFromRequest(Request $request): array
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            throw new \InvalidArgumentException('Invalid or expired token');
        }

        $currentUserDto = $this->authMapper->toCurrentUserResponseDto($user);
        
        return $currentUserDto->toArray();
    }

    public function getUserFromToken(Request $request): ?UserInfo
    {
        $authHeader = $request->headers->get('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return $this->userInfoRepository->find($decoded->user_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getErrorStatusCode(string $errorMessage): int
    {
        return $this->errorCodeResolver->getStatusCode($errorMessage);
    }
    
}
