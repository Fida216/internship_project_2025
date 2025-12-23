<?php

namespace App\Domain\User\Mapper;

use App\Domain\User\DTO\CreateUserDTO;
use App\Domain\User\DTO\UpdateUserDTO;
use App\Domain\User\DTO\UserResponseDTO;
use App\Domain\User\DTO\UsersListResponseDTO;
use App\Domain\User\DTO\PasswordChangeResponseDTO;
use App\Domain\User\DTO\EnumResponseDTO;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserCredential;
use App\Domain\ExchangeOffice\Entity\ExchangeOffice;
use App\Shared\Enum\Status;
use App\Shared\Enum\Role;

class UserMapper
{
    /**
     * Converts CreateUserDTO to UserInfo and UserCredential entities
     */
    public function fromCreateUserDto(CreateUserDTO $dto, ?ExchangeOffice $exchangeOffice = null): array
    {
        $account = new UserCredential();
        $account->setEmail(trim($dto->email));
        $account->setPasswordHash(password_hash($dto->password, PASSWORD_BCRYPT));
        $account->setStatus(Status::from($dto->status));
        $account->setCreatedAt(new \DateTime());

        $user = new UserInfo();
        $user->setFirstName(trim($dto->firstName));
        $user->setLastName(trim($dto->lastName));
        $user->setPhone(trim($dto->phone));
        $user->setRole(Role::from($dto->role));
        $user->setStatus(Status::from($dto->status));
        $user->setCreatedAt(new \DateTime());
        $user->setAccount($account);

        if ($exchangeOffice) {
            $user->setExchangeOffice($exchangeOffice);
        }

        return [
            'user' => $user,
            'account' => $account
        ];
    }

    /**
     * Applies UpdateUserDTO changes to an existing UserInfo entity
     */
    public function applyUpdateUserDto(UpdateUserDTO $dto, UserInfo $user): UserInfo
    {
        if ($dto->firstName !== null) {
            $user->setFirstName(trim($dto->firstName));
        }
        
        if ($dto->lastName !== null) {
            $user->setLastName(trim($dto->lastName));
        }
        
        if ($dto->phone !== null) {
            $user->setPhone(trim($dto->phone));
        }
        
        if ($dto->email !== null) {
            $user->getAccount()->setEmail(trim($dto->email));
        }
        if ($dto->status !== null) {
            $user->setStatus(Status::from($dto->status));
            $user->getAccount()->setStatus(Status::from($dto->status));
        }

        return $user;
    }

    /**
     * Converts UserInfo entity to UserResponseDTO
     */
    public function toUserResponseDto(UserInfo $user): UserResponseDTO
    {
        $exchangeOffice = null;
        if ($user->getExchangeOffice()) {
            $exchangeOffice = [
                'id' => $user->getExchangeOffice()->getId(),
                'name' => $user->getExchangeOffice()->getName()
            ];
        }

        return new UserResponseDTO(
            id: $user->getId(),
            lastName: $user->getLastName(),
            firstName: $user->getFirstName(),
            phone: $user->getPhone(),
            role: $user->getRole()->value,
            status: $user->getStatus()->value,
            email: $user->getAccount()->getEmail(),
            createdAt: $user->getCreatedAt()->format('Y-m-d H:i:s'),
            exchangeOffice: $exchangeOffice
        );
    }

    /**
     * Converts array of UserInfo entities to UsersListResponseDTO
     */
    public function toUsersListResponseDto(array $users): UsersListResponseDTO
    {
        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = $this->toUserResponseDto($user)->toArray();
        }

        return new UsersListResponseDTO($usersData, count($usersData));
    }

    /**
     * Creates a PasswordChangeResponseDTO
     */
    public function toPasswordChangeResponseDto(string $message): PasswordChangeResponseDTO
    {
        return new PasswordChangeResponseDTO($message);
    }

}
