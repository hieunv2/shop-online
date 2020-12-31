<?php


class CartManager extends BaseModal
{
    /**
     * @var mysqli
     */
    private $db;

    private $name;


    public function __construct($dbConnection, $name)
    {
        $this->$name = $name;
        if ($dbConnection instanceof mysqli) {
            $this->db = $dbConnection;
        } else {
            throw new Exception('Connection injected should be of Mysqli object');
        }
    }

    public function addOrder($query)
    {
        if ($result = $this->db->query($query)) {
            return $result;
        } else {
            die($this->db->error);
        }
    }
}
