<?php

class Application_Model_DbTable_UserTable extends Zend_Db_Table_Abstract {

    protected $_name = "user";
    
    
    public function createUser($username, $password, $vname, $nachname, $email ){
        $key = $this->insert(
                array(
                    'username' => $username,
                    'vorname' => $vname,
                    'nachname' => $nachname,
                    'password' => $password,
                    'email' => $email
                )
            );
            return $key;
    }

    public function getUserByName($name = null) {
        $rows = null;

        if (!empty($name)) {
            $select = $this->select()->where('username = ?', $name);
            $rows = $this->fetchAll($select);
        }

        return $rows;
    }

    public function getUserByMail($mail = null) {
        $rows = null;

        if (!empty($mail)) {
            $select = $this->select()->where('email = ?', $mail);
            $rows = $this->fetchAll($select);

            if (!empty($rows)) {
                $rows = $rows->current();
            }
        }

        return $rows;
    }

    public function getUserById($id = null) {
        $user = null;

        if ($id != null) {
            $user = $this->find($id);
            if (!empty($user)) {
                $user = $user->current();
            }
        }

        return $user;
    }

    public function getAllUser() {
        $users = $this->fetchAll();

        if (!empty($users)) {
            $users = $users->toArray();
        }

        return $users;
    }

    public function getUserByRole($role = null) {
        $roles = null;

        if ($role != null) {
            $select = $this->select()->where('rolle_id = ?', $role); //eventuell noch freigeschaltet = true
            $roles = $this->fetchAll($select);
            if (!empty($roles)) {
                $roles = $roles->toArray();
            }
        }

        return $roles;
    }

    public function getAllInactiveUsers() {
        $select = $this->select()->where('freigeschaltet = ?', false);

        return $this->fetchAll($select)->toArray();
    }

}
