<?php

class DB {

    private $db_server      = '62.84.123.21';
    private $db_user        = 'recred';
    private $db_password    = 'P3yHQ8=(;7]>eR5_';
    private $db_name        = 'rucred';

    public function __construct()
    {
        try {

            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO('mysql:host='.$this->db_server.';dbname='.$this->db_name, $this->db_user, $this->db_password, $opt);

        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    //181690
    public function getUserData($userId){
        $userData = $this->pdo->query('SELECT * FROM s_users WHERE id = '.$userId)->fetch(PDO::FETCH_ASSOC);
        if($userData){
            return $userData;
        } else {
            return array("message" => "user is not exsist");
        }
    }

}