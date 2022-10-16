<?php

namespace App\Tests\Entity;


use App\Entity\Permission;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class PermissionTest extends KernelTestCase
{
    public function getEntity():Permission
    {
        return (new Permission())
            ->setName('permission')
          ;
    }

    /**
     * @throws Exception
     */
    public function testValidPermissionEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * @throws Exception
     */
    public function testBlankNamePermissionEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testNameTooLongPermissionEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setName('Ceci va être une permission beaucoup beaucoup trop longue'), 1);
    }

    /**
     * @throws Exception
     */
    public function assertHasErrors(Permission $permission, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($permission);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
