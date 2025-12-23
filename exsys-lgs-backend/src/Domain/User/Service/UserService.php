<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserCredential;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Shared\Enum\Status;
use App\Shared\Enum\Role;
use App\Domain\User\DTO\CreateUserDTO;
use App\Domain\User\DTO\UpdateUserDTO;
use App\Domain\User\DTO\UpdateUserStatusDTO;
use App\Domain\User\DTO\ChangePasswordDTO;
use App\Domain\User\DTO\ResetPasswordDTO;
use App\Domain\User\DTO\UserResponseDTO;
use App\Domain\User\DTO\UsersListResponseDTO;
use App\Domain\User\DTO\PasswordChangeResponseDTO;
use App\Domain\User\DTO\EnumResponseDTO;
use App\Domain\User\DTO\UserFilterDTO;
use App\Domain\User\Mapper\UserMapper;
use App\Domain\User\Repository\UserInfoRepository;
use App\Domain\ExchangeOffice\Repository\ExchangeOfficeRepository;
use App\Shared\Service\ErrorCodeResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserInfoRepository $userInfoRepository;
    private ExchangeOfficeRepository $exchangeOfficeRepository;
    private ValidatorInterface $validator;
    private UserMapper $userMapper;
    private ErrorCodeResolver $errorCodeResolver;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserInfoRepository $userInfoRepository,
        ExchangeOfficeRepository $exchangeOfficeRepository,
        ValidatorInterface $validator,
        UserMapper $userMapper,
        ErrorCodeResolver $errorCodeResolver
    ) {
        $this->entityManager = $entityManager;
        $this->userInfoRepository = $userInfoRepository;
        $this->exchangeOfficeRepository = $exchangeOfficeRepository;
        $this->validator = $validator;
        $this->userMapper = $userMapper;
        $this->errorCodeResolver = $errorCodeResolver;
    }

   
    public function createUserFromDto(CreateUserDTO $dto): UserResponseDTO
{
    $existingUser = $this->userInfoRepository->findOneByEmail($dto->email);
    if ($existingUser) {
        throw new \InvalidArgumentException('A user with this email already exists');
    }
    $exchangeOffice = null;
    if ($dto->role === 'agent') {
        if (!$dto->exchangeOfficeId) {
            throw new \InvalidArgumentException('Exchange office ID is required for agents');
        } else {
            $exchangeOffice = $this->exchangeOfficeRepository->find($dto->exchangeOfficeId);
            if (!$exchangeOffice) {
                throw new \InvalidArgumentException('Exchange office not found');
            }
        }
    }
    
    $entities = $this->userMapper->fromCreateUserDto($dto, $exchangeOffice);
    $user = $entities['user'];
    $account = $entities['account'];

    $errors = [
        ...$this->validator->validate($account),
        ...$this->validator->validate($user),
    ];

    if (count($errors) > 0) {
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($errors));
        throw new \InvalidArgumentException('Entity validation errors: ' . implode(', ', $messages));
    }

    $this->entityManager->persist($account);
    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $this->userMapper->toUserResponseDto($user);
}

    public function getAllUsersFromRequest(UserInfo $currentUser, ?UserFilterDTO $filterDto = null): UsersListResponseDTO
    {
        $users = $this->getAllUsers($currentUser, $filterDto);
        
        return $this->userMapper->toUsersListResponseDto($users);
    }

    public function getAllUsers(UserInfo $currentUser, ?UserFilterDTO $filterDto = null): array
    {
        if ($currentUser->getRole() === Role::AGENT) {
            // Agents can only see users from their exchange office
            $exchangeOffice = $currentUser->getExchangeOffice();
            if (!$exchangeOffice) {
                throw new \InvalidArgumentException('Agent must be associated with an exchange office');
            }
            return $this->userInfoRepository->findByExchangeOffice($exchangeOffice);
        }

        // Admins can see all users with optional filters
        if ($filterDto) {
            return $this->userInfoRepository->findByFilters($filterDto);
        }

        return $this->userInfoRepository->findAll();
    }

    public function getGroupedAgentsByExchangeOffice(): array
    {
        $exchangeOffices = $this->exchangeOfficeRepository->findAll();
        
        $result = [];
        
        // For each exchange office, get its agents
        foreach ($exchangeOffices as $exchangeOffice) {
            $agents = $this->userInfoRepository->findByExchangeOffice($exchangeOffice);
            
            // Filter only agents (in case there are other roles)
            $agentsOnly = array_filter($agents, function($user) {
                return $user->getRole() === Role::AGENT;
            });
            
            $result[] = [
                'exchangeOffice' => [
                    'id' => $exchangeOffice->getId()->toString(),
                    'name' => $exchangeOffice->getName(),
                    'address' => $exchangeOffice->getAddress(),
                    'phone' => $exchangeOffice->getPhone(),
                    'email' => $exchangeOffice->getEmail(),
                    'owner' => $exchangeOffice->getOwner(),
                    'status' => $exchangeOffice->getOfficeStatus()->value,
                    'createdAt' => $exchangeOffice->getCreatedAt()?->format('Y-m-d H:i:s')
                ],
                'agents' => array_map(function($agent) {
                    return $this->userMapper->toUserResponseDto($agent)->toArray();
                }, array_values($agentsOnly))
            ];
        }
        return $result;
    }

 
public function updateUserStatus(string $userId, string $status): UserResponseDTO
    {

        $user = $this->userInfoRepository->find($userId);
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }


        $userStatus = Status::from($status);
        $user->setStatus($userStatus);
        $user->getAccount()->setStatus($userStatus);

        $this->entityManager->flush();

        return $this->userMapper->toUserResponseDto($user);
    }

    public function changePassword(ChangePasswordDTO $dto, UserInfo $currentUser): PasswordChangeResponseDTO
{
    if (!password_verify($dto->oldPassword, $currentUser->getAccount()->getPasswordHash())) {
        throw new \InvalidArgumentException('Current password is incorrect');
    }

    $currentUser->getAccount()->setPasswordHash(password_hash($dto->newPassword, PASSWORD_BCRYPT));
    $this->entityManager->flush();

    return $this->userMapper->toPasswordChangeResponseDto('Password changed successfully');
}



    public function resetPassword(string $userId, ResetPasswordDTO $dto): PasswordChangeResponseDTO
    {
        $targetUser = $this->userInfoRepository->find($userId);
        if (!$targetUser) {
            throw new \InvalidArgumentException('User not found');
        }

        $targetUser->getAccount()->setPasswordHash(password_hash($dto->newPassword, PASSWORD_BCRYPT));
        $this->entityManager->flush();

        return $this->userMapper->toPasswordChangeResponseDto('Password reset successfully');
    }

   
public function updateUser(string $userId, UpdateUserDTO $dto): UserResponseDTO
    {
        $user = $this->userInfoRepository->find($userId);
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        if ($dto->email && $dto->email !== $user->getAccount()->getEmail()) {
            $existingUser = $this->userInfoRepository->findOneByEmail($dto->email);
            if ($existingUser) {
                throw new \InvalidArgumentException('A user with this email already exists');
            }
        }



        $updatedUser = $this->userMapper->applyUpdateUserDto($dto, $user);

        $this->entityManager->flush();

        return $this->userMapper->toUserResponseDto($updatedUser);
    }

    public function getErrorStatusCode(string $errorMessage): int
    {
        return $this->errorCodeResolver->getStatusCode($errorMessage);
    }
}
