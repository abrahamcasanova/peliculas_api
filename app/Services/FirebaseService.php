<?php

namespace App\Services;

//use Kreait\Firebase;
use Kreait\Firebase\Factory;
//use Kreait\Firebase\Database;
//use Kreait\Firebase\ServiceAccount;

class FirebaseService
{
    public function __construct()
    {
        $this->firebase = (new Factory)->withServiceAccount(__DIR__.config('firebase.firebase_dir'));    
        //$factory = (new Factory)->withServiceAccount("/Applications/MAMP/htdocs/Appnovasolutions/zeus_api/app/Services/zeus-c5912-090dc31094f7.json");   
    }

    /**
     * Verify password agains firebase
     * @param $email
     * @param $password
     * @return bool|string
     */
    public function verifyPassword($email, $password)
    {
        try {
            $response = $this->firebase->getAuth()->verifyPassword($email, $password);
            return $response->uid;

        } catch(FirebaseEmailExists $e) {
            logger()->info('Error login to firebase: Tried to create an already existent user');
        } catch(Exception $e) {
            logger()->error('Error login to firebase: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Verify password agains firebase
     * @param $email
     * @param $password
     * @return bool|string
     */
    public function serviceUser()
    {
        $auth = null;
        try {
            $auth = $this->firebase->createAuth();
        } catch(FirebaseEmailExists $e) {
            logger()->info('Error login to firebase: Tried to create an already existent user');
        } catch(Exception $e) {
            logger()->error('Error login to firebase: ' . $e->getMessage());
        }
        return $auth;
    }
}