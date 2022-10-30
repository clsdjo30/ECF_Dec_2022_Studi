<?php

namespace App\Tests\Entity;


use App\Entity\Subsidiary;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class SubsidiaryTest extends KernelTestCase
{

    public function getEntity():Subsidiary
    {
        return (new Subsidiary())
            ->setName('Lions Fitness Club Montpellier')
            ->setPhoneNumber('0102030405')
            ->setLogoUrl('LienVersLeLogoDeLaSalle')
            ->setUrl('htt://www.lionsfitnessclub-montpellier.com')
            ->setAddress('20 rue de la liberté')
            ->setCity('Montpellier')
            ->setPostalCode(34000)
            ->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus adipisci aperiam autem consectetur consequuntur delectus eaque enim eos esse est, eum facere illum ipsum iste iure magni maiores maxime molestias mollitia natus nihil nisi nobis numquam optio pariatur porro possimus quae quam qui quidem quos ratione recusandae repellat tempora vitae?')
            ->setIsActive(true)
            ->setCreatedAt(new DateTime('2009-06-08 20:30:00'))
            ->setUpdatedAt(new DateTime('2009-07-10 20:30:00'));
    }

    /**
     * @throws Exception
     */
    public function testValidsubsidiaryEntity(): void
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
        $this->assertHasErrors($this->getEntity()->setName('Ce Nom Est Carrement Trop Long Et De Loin mais pas encore assez je pense'), 1);
    }


    /**
     * @throws Exception
     */
    public function testInvalidBlankUrlEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setUrl(''), 1);
    }

    /**
     * @throws Exception
     */
    public function testDescriptionIsTooShortEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setDescription('ipsum iste iure magni maiores maxime '), 1);
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
    public function testPhoneNumberTooLongEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setPhoneNumber('001415161918'), 1);
    }
    /**
     * @throws Exception
     */
    public function testAddressTooShortEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setAddress('18, rue '), 1);
    }

    /**
     * @throws Exception
     */
    public function testInvalidBlankCityEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setCity(''), 1);
    }
    /**
     * @throws Exception
     */
    public function testPostalCodeTooShortEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setPostalCode(1), 1);
    }
    /**
     * @throws Exception
     */
    public function testPostalCodeTooLongEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setPostalcode(995500), 1);
    }

    /**
     * @throws Exception
     */
    public function testCityTooLongEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setCity('Llanfairpwllgwyngyllgogerychwyrndrobwllllantysiliogogogoch'), 1);
    }

    /**
     * @throws Exception
     */
    public function assertHasErrors(Subsidiary $subsidiary, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($subsidiary);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
