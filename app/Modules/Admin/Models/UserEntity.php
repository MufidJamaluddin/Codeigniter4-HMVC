<?php namespace App\Modules\Admin\Models;

class UserEntity
{
    protected $id;
    protected $name;

    public function __construct()
    {

    }

    public static function of($uid, $uname)
    {
        $user = new UserEntity();
        $user->setId($uid);
        $user->setName($uname);

        return $user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}