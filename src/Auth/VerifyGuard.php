<?php

namespace Toddish\Verify\Auth;

use Illuminate\Auth\SessionGuard,
    Illuminate\Contracts\Auth\Guard as GuardContract,
    Toddish\Verify\Helpers\Verify as VerifyHelper;

class VerifyGuard extends SessionGuard implements GuardContract
{
    public function verify(array $credentials = array(), $remember = false, $login = true)
    {
        $this->fireAttemptEvent($credentials, $remember, $login);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if (!$this->hasValidCredentials($user, $credentials))
        {
            return VerifyHelper::INVALID_CREDENTIALS;
        }

        if (!$this->provider->isVerified($user))
        {
            return VerifyHelper::UNVERIFIED;
        }

        if ($this->provider->isDisabled($user))
        {
            return VerifyHelper::DISABLED;
        }

        if ($login)
        {
            $this->login($user, $remember);
        }

        return VerifyHelper::SUCCESS;
    }
}