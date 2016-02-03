<?php
/**
 * Created by PhpStorm.
 * User: anton.vityazev
 * Date: 19.01.2016
 * Time: 15:31
 */
namespace UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}