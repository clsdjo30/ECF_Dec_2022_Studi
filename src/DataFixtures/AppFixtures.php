<?php

namespace App\DataFixtures;

use App\Entity\Partner;
use App\Entity\PartnerPermission;
use App\Entity\Permission;
use App\Entity\Subsidiary;
use App\Entity\SubsidiaryPermission;
use App\Entity\TechTeam;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $encoder;

    public function __construct(
        UserPasswordHasherInterface $encoder,

    ) {
        $this->encoder = $encoder;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $permissions = [];

        $permissionValues = [
            "Gestion des Planning",
            "Formations des équipes",
            "Ventes de boisson",
            "Maintenance des appareils",
            "Analyse et conseil",
            "Gestion de la communication digitale",
            "Envoi de newsletter",
            "Livraison de produits d'entretien",
        ];
        foreach ($permissionValues as $permissionValue) {
            $perm = new Permission();

            $perm->setName($permissionValue);

            $manager->persist($perm);
            $permissions[] = $perm;
        }


        // 1 membre de l'équipe tech
        $newTech = (new TechTeam());

        $userTech = new User();
        $userTech->setEmail('tech@lions-fitness-club.fr')
            ->setPassword($this->encoder->hashPassword($userTech, 'password'))
            ->setRoles(['ROLE_TECH'])
            ->setFirstName('Jean')
            ->setLastName('Bon')
            ->setTechTeam($newTech);


        $manager->persist($newTech);
        $manager->persist($userTech);


        for ($i = 0; $i < 1; $i++) {


            //compte franchise
            $partner = (new Partner())
                ->setName($faker->company())
                ->setPhoneNumber($faker->phoneNumber())
                ->setIsActive($faker->boolean())
                ->setCreatedAt($faker->dateTime())
                ->setUpdatedAt($faker->dateTime());

            $manager->persist($partner);

            // user Franchise pour id de connexion
            $userPartner = new User();
            $userPartner->setEmail($faker->companyEmail())
                ->setPassword($this->encoder->hashPassword($userPartner, 'password'))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_PARTNER'])
                ->setFranchising($partner);

            foreach($permissions as $permission) {
                $newPartnerPerm = (new PartnerPermission())
                    ->setIsActive($faker->boolean())
                    ->setPermission($permission)
                ;
                $partner->addGlobalPermission($newPartnerPerm);


                $manager->persist($newPartnerPerm);
            }




            for ($j = 0; $j <= random_int(1, 3); $j++) {

                //compte structure(Salle de sport)
                $park = (new Subsidiary())
                    ->setName($faker->company())
                    ->setAddress($faker->address())
                    ->setCity($faker->city())
                    ->setPostalCode($faker->randomNumber(5))
                    ->setDescription($faker->realText(250))
                    ->setLogoUrl($faker->image(null, 600, 600, 'animals', true, true, 'cats', true, 'jpg'))
                    ->setUrl($faker->url())
                    ->setPhoneNumber($faker->phoneNumber())
                    ->setIsActive($faker->boolean())
                    ->setPartner($partner)
                    ->setCreatedAt($faker->dateTime())
                    ->setUpdatedAt($faker->dateTime());


                //user Salle de sport pour id de connexion
                $userSubsidiary = new User();
                $userSubsidiary->setEmail($faker->companyEmail())
                    ->setPassword($this->encoder->hashPassword($userSubsidiary, 'password'))
                    ->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName())
                    ->setRoles(['ROLE_SUBSIDIARY'])
                ;

                $park->addSubsidiaryPermission($faker->randomElement($permissions));

                foreach ($park->getSubsidiaryPermissions() as $subPerm) {
                    $subPerm->setIsActive($faker->boolean());
                }
                $park->setUser($userSubsidiary);

                $manager->persist($userSubsidiary);
                $manager->persist($park);
            }
            $manager->persist($partner);
            $manager->persist($userPartner);


        }

        $manager->flush();

    }
}
