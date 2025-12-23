<?php

namespace App\Shared\Service;

class ErrorCodeResolver
{
    /**
     * Resolves appropriate HTTP error code based on error message
     */
    public function getStatusCode(string $errorMessage): int
    {
        $exactMatches = [
            'Invalid JSON data' => 400,
            'Email and password are required' => 400,
            'Email and password cannot be empty' => 400,
            'Invalid credentials' => 401,
            'Account disabled' => 403,
            'Invalid or expired token' => 401,
            // Transaction specific errors
            'Only agents can create transactions' => 403,
            'Only administrators can update transactions' => 403,
            'Only administrators can delete transactions' => 403,
            'Transaction not found' => 404,
            'Agent must be assigned to an exchange office' => 400,
            'Client not found' => 404,
            'You can only create transactions for clients of your exchange office' => 403,
            'You can only view transactions for clients of your exchange office' => 403,
            'Source and target currencies must be different' => 400,
            'Only agents can access this endpoint' => 403,
            'Only agents and administrators can access this endpoint' => 403,
            'Only administrators can access this endpoint' => 403,
            'Exchange office not found' => 404,
            'Invalid JSON format' => 400,
            'Exchange office ID is required' => 400,
            'Client ID is required' => 400,
            'Transaction ID is required' => 400,
        ];

        if (isset($exactMatches[$errorMessage])) {
            return $exactMatches[$errorMessage];
        }

        // Content-based checks (normal priority - for business validations)
        return match (true) {
            // Resource not found errors (404) - HIGH PRIORITY
            str_contains($errorMessage, 'not found') => 404,
            str_contains($errorMessage, 'not assigned') => 404,

            // Conflict errors (409) - HIGH PRIORITY
            str_contains($errorMessage, 'already exists') => 409,
            str_contains($errorMessage, 'Cannot delete') => 409,

            // Access forbidden errors (403) - HIGH PRIORITY
            str_contains($errorMessage, 'Only administrators') => 403,
            str_contains($errorMessage, 'Only agents') => 403,
            str_contains($errorMessage, 'You can only') => 403,
            str_contains($errorMessage, 'can only') => 403,
            str_contains($errorMessage, 'Access denied') => 403,
            str_contains($errorMessage, 'Agent must be associated') => 403,

            // Authorization errors (401)
            str_contains($errorMessage, 'Invalid token') => 401,
            str_contains($errorMessage, 'expired token') => 401,

            // Validation errors (400) - LOW PRIORITY
            str_contains($errorMessage, 'Validation errors') => 400,
            str_contains($errorMessage, 'Missing required fields') => 400,
            str_contains($errorMessage, 'Missing required parameter') => 400,
            str_contains($errorMessage, 'Role must be') => 400,
            str_contains($errorMessage, 'Exchange office') => 400,
            str_contains($errorMessage, 'exchange office') => 400,
            str_contains($errorMessage, 'Current password is incorrect') => 400,
            str_contains($errorMessage, 'do not match') => 400,
            str_contains($errorMessage, 'Invalid JSON data') => 400,
            str_contains($errorMessage, 'Invalid email') => 400,
            str_contains($errorMessage, 'Invalid status value') => 400,
            str_contains($errorMessage, 'At least one field') => 400,
            str_contains($errorMessage, 'Entity validation errors') => 400,

            // Default (400)
            default => 400,
        };
    }
}
