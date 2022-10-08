<?php

namespace App\DataFixtures;

use App\Entity\Partner;
use App\Entity\PartnerPermission;
use App\Entity\Permission;
use App\Entity\Subsidiary;
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

            // on crée 8 permissions
            foreach ($permissionValues as $permissionsValue) {
                $perm = new Permission();
                $perm->setName($permissionsValue);

                $manager->persist($perm);

                $permissions[] = $perm;
            }

        // 1 membre de l'équipe tech
        $userTech = new User();
        $userTech->setEmail('tech@lions-fitness-club.fr')
            ->setPassword($this->encoder->hashPassword($userTech, 'password'))
            ->setRoles(['ROLE_TECH'])
            ->setFirstName('Jean')
            ->setLastName('Bon');

        $newTech = (new TechTeam())
            ->setUser($userTech);

        $manager->persist($newTech);


        for ($i = 0; $i < 2; $i++) {
            //compte franchise
            $partner = (new Partner())
                ->setName($faker->company())
                ->setPhoneNumber($faker->phoneNumber())
                ->setIsActive($faker->boolean())
                ->setCreatedAt($faker->dateTime())
                ->setUpdatedAt($faker->dateTime());

            // user Franchise pour id de connexion
            $userPartner = new User();
            $userPartner->setEmail($faker->companyEmail())
                ->setPassword($this->encoder->hashPassword($userPartner, 'password'))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setFranchising($partner);


            //on ajoute 8 permission au partner
            $globalPerm1 = (new PartnerPermission())
                ->setPermission($permissions[0])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm1);

            $manager->persist($globalPerm1);

            $globalPerm2 = (new PartnerPermission())
                ->setPermission($permissions[1])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm2);

            $manager->persist($globalPerm1);

            $globalPerm3 = (new PartnerPermission())
                ->setPermission($permissions[2])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm3);

            $manager->persist($globalPerm1);

            $globalPerm4 = (new PartnerPermission())
                ->setPermission($permissions[3])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm4);

            $manager->persist($globalPerm1);

            $globalPerm5 = (new PartnerPermission())
                ->setPermission($permissions[4])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm5);

            $globalPerm6 = (new PartnerPermission())
                ->setPermission($permissions[5])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm6);

            $manager->persist($globalPerm1);

            $globalPerm7 = (new PartnerPermission())
                ->setPermission($permissions[6])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm7);

            $manager->persist($globalPerm1);

            $globalPerm8 = (new PartnerPermission())
                ->setPermission($permissions[7])
                ->setIsActive($faker->boolean());
            $partner->addGlobalPermission($globalPerm8);

            $manager->persist($globalPerm1);
            $manager->persist($globalPerm1);
            $manager->persist($globalPerm2);
            $manager->persist($globalPerm3);
            $manager->persist($globalPerm4);
            $manager->persist($globalPerm5);
            $manager->persist($globalPerm6);
            $manager->persist($globalPerm7);



            for ($k = 0; $k <= random_int(1, 3); $k++) {

                //compte structure(Salle de sport)
                $park = (new Subsidiary())
                    ->setName($faker->company())
                    ->setAddress($faker->address())
                    ->setCity($faker->city())
                    ->setPostalCode($faker->randomNumber(5))
                    ->setDescription($faker->realText(250))
                    ->setLogoUrl($faker->url())
                    ->setUrl($faker->url())
                    ->setPhoneNumber($faker->phoneNumber())
                    ->setIsActive($faker->boolean())
                    ->setPartner($partner)
                    ->setCreatedAt($faker->dateTime())
                    ->setUpdatedAt($faker->dateTime());


                //user Salle de sport pour id de connexion
                $userSubsidiary = new User();
                $userSubsidiary->setEmail($faker->companyEmail())
                    ->setPassword($this > $this->encoder->hashPassword($userSubsidiary, 'password'))
                    ->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName())
                    ->setRoomManager($park);


                $manager->persist($userSubsidiary);
                $manager->persist($park);
            }
            $manager->persist($partner);
            $manager->persist($userPartner);
        }
        $manager->flush();
    }
}
