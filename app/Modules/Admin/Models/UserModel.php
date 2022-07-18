<?php namespace App\Modules\Admin\Models;

class UserModel
{
    public function getUsers()
    {
        return [
            UserEntity::of('PL0001', 'Mufid Jamaluddin'),
            UserEntity::of('PL0002', 'Andre Jhonson'),
            UserEntity::of('PL0003', 'Indira Wright'),
        ];
    }
}