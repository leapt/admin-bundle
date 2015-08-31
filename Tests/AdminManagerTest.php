<?php

namespace Leapt\AdminBundle\Tests;

use Leapt\AdminBundle\AdminManager;

class AdminManagerTest extends \PHPUnit_Framework_TestCase {
    /**
     * Simple trivial test to check admin registration
     *
     */
    public function testRegisterAdmin()
    {
        $adminManager = new AdminManager();
        $admin = $this->getMock('Leapt\AdminBundle\Admin\AdminInterface');
        $adminManager->registerAdmin('foo', $admin);

        $this->assertInstanceOf('Leapt\AdminBundle\Admin\AdminInterface', $adminManager->getAdmin('foo'));
    }
}