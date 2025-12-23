<?php

namespace App\Domain\Auth\Mapper;

use App\Domain\Auth\DTO\AuthResponseDTO;
use App\Domain\Auth\DTO\CurrentUserResponseDTO;
use App\Domain\User\Entity\UserInfo;

class AuthMapper
{
    /**
     * Converts authentication data to AuthResponseDTO
     */
    public function toAuthResponseDto(string $token, UserInfo $user, int $expiresIn = 86400): AuthResponseDTO
    {
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'role' => $user->getRole(),
            'status' => $user->getStatus(),
            'exchangeOffice' => $user->getExchangeOffice() ? [
                'id' => $user->getExchangeOffice()->getId(),
                'name' => $user->getExchangeOffice()->getName()
            ] : null
        ];

        return new AuthResponseDTO(
            token: $token,
            user: $userData,
            expiresIn: $expiresIn
        );
    }

    /**
     * Converts UserInfo to CurrentUserResponseDTO
     */
    public function toCurrentUserResponseDto(UserInfo $user): CurrentUserResponseDTO
    {
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'role' => $user->getRole(),
            'status' => $user->getStatus(),
            'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            'exchangeOffice' => $user->getExchangeOffice() ? [
                'id' => $user->getExchangeOffice()->getId(),
                'name' => $user->getExchangeOffice()->getName()
            ] : null
        ];

        return new CurrentUserResponseDTO($userData);
    }
}
