<?php

namespace App\Tests\Entity;

use App\Entity\Partner;
use App\Entity\User;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class   PartnerTest extends KernelTestCase
{
    public function getEntity():Partner
    {
        return (new Partner)
            ->setName('Lions Fitness Club')
            ->setPhoneNumber('0102030405')
            ->setIsActive(true)
            ->setCreatedAt(new DateTime('2009-06-08 20:30:00'))
            ->setUpdatedAt(new DateTime('2009-07-10 20:30:00'));
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
    public function testInvalidBlankNameEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testToLongNameEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setName('CeNomEstCarrementTropLongEtDeLoin'), 1);
    }

    /**
     * @throws Exception
     */
    public function testInvalidBlankPhoneNumberEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setPhoneNumber(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testToLongBlankPhoneNumberEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setPhoneNumber('00102030405'), 1);
    }

    public function testValidPartnerGetUserEntity(): void
    {
        $user = new User();
        $partner = (new Partner())->setUser($user);

        $this->assertTrue((bool)$partner->getUser());
    }

    /**
     * @throws Exception
     */
    public function assertHasErrors(Partner $partner, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($partner);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

}
