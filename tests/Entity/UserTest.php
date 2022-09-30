<?php

namespace App\Tests\Entity;

use App\Entity\Partner;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    public function getEntity():User
    {
        return (new User())
            ->setEmail('true@email.com')
            ->setPassword('truepassword')
            ->setRoles(['ROLE_TEST'])
            ->setFirstname('prenom')
            ->setLastname('nom de famille');
    }

    /**
     * @throws Exception
     */
    public function testValidPartnerEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
 * @throws Exception
 */
    public function testInvalidBlankFirstNameEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setFirstName(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testFirstNameIsTooLongEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setFirstName('BonjourVotreNomEstCarementTropLong'), 1);
    }

    /**
     * @throws Exception
     */
    public function testInvalidBlankLastNameEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setLastName(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testLastNameIsTooLongEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setLastName('BonjourVotreNomEstCarementTropLong'), 1);
    }

    /**
     * @throws Exception
     */
    public function testEmailNotBlankEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testEmailIsValidEmailEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setEmail('BonjourVotreNomEstCarementTropLong'), 1);
    }

    /**
     * @throws Exception
     */
    public function assertHasErrors(User $user, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
