<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseService
{
    protected Auth $auth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->auth = $factory->createAuth();
    }

    public function verifyIdToken(string $idToken)
    {
        return $this->auth->verifyIdToken($idToken);
    }


    public function getUser(string $uid)
    {
        return $this->auth->getUser($uid);
    }
}
