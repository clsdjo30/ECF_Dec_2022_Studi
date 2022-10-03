<?php

namespace App\DataFixtures;

use App\Entity\Partner;
use App\Entity\Permission;
use App\Entity\Subsidiary;
use App\Entity\TechTeam;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
            "Livraison de produits d'entretien"
        ];

        foreach($permissionValues as $permissionValue) {
            $perm = new Permission();

            $perm->setName($permissionValue)
                ->setIsActive($faker->boolean(60));
            $manager->persist($perm);
            $manager->flush();
            $permissions[] = $perm;
        }

        // 1 membre de l'équipe tech
        $userTech =new User();
          $userTech ->setEmail('tech@lions-fitness-club.fr')
            ->setPassword($this->encoder->hashPassword($userTech, 'password'))
            ->setRoles(['ROLE_TECH'])
            ->setFirstName('Jean')
            ->setLastName('Bon')
            ;

        $newTech = (new TechTeam())
        ->setUser($userTech);

        $manager->persist($newTech);

        //10 partenaires possédant 1 salle
        for ($i = 0; $i < 10; $i ++)
        {
            // user Franchise pour id de connexion
            $userPartner = new User();
            $userPartner ->setEmail($faker->companyEmail())
                ->setPassword($this->encoder->hashPassword($userPartner, 'password'))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_PARTNER'])
            ;
            //compte franchise
            $partner = (new Partner())
                ->setName($faker->company())
                ->setPhoneNumber($faker->phoneNumber())
                ->setIsActive($faker->boolean(50))
                ->setUser($userPartner)
                ->setCreatedAt($faker->dateTime())
                ->setUpdatedAt($faker->dateTime());

            foreach ( $permissions as $permission)
            {
                $permission->setIsActive($faker->boolean(50));
                $partner->addGlobalPermission($permission);

            }

            //user Salle de sport pour id de connexion
            [$userSubsidiary, $park] = $this->userSalleDeSportPourIdDeConnexion(
                $faker,
                $partner,
                $manager,
                $userPartner
            );
        }

        //10 partenaires possédant 2 salle
        for ($j = 0; $j < 10; $j ++)
        {
            // user Franchise pour id de connexion
            $userPartner = new User();
            $userPartner ->setEmail($faker->companyEmail())
                ->setPassword($this->encoder->hashPassword($userPartner, 'password'))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_PARTNER'])
            ;
            //compte franchise
            $partner = (new Partner())
                ->setName($faker->company())
                ->setPhoneNumber($faker->phoneNumber())
                ->setIsActive($faker->boolean(55))
                ->setUser($userPartner)
                ->setCreatedAt($faker->dateTime())
                ->setUpdatedAt($faker->dateTime());

            foreach ( $permissions as $permission)
            {
                $permission->setIsActive($faker->boolean(60));
                $partner->addGlobalPermission($permission);

            }

            // on crée 2 salles de sport
            for ($k = 0; $k < 2; $k ++) {
                //user Salle de sport pour id de connexion
                [$userSubsidiary, $park] = $this->userSalleDeSportPourIdDeConnexion(
                    $faker,
                    $partner,
                    $manager,
                    $userPartner
                );
            }
        }
        $manager->flush();
    }

    /**
     * @param \Faker\Generator $faker
     * @param Partner $partner
     * @param ObjectManager $manager
     * @param User $userPartner
     * @return array
     */
    public function userSalleDeSportPourIdDeConnexion(
        \Faker\Generator $faker,
        Partner $partner,
        ObjectManager $manager,
        User $userPartner
    ): array {
        $userSubsidiary = new User();
        $userSubsidiary->setEmail($faker->companyEmail())
            ->setPassword($this > $this->encoder->hashPassword($userSubsidiary, 'password'))
            ->setFirstName($faker->firstName())
            ->setLastName($faker->lastName())
            ->setRoles(['ROLE_STRUCTURE']);

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
            ->setUser($userSubsidiary)
            ->setPartner($partner)
            ->setCreatedAt($faker->dateTime())
            ->setUpdatedAt($faker->dateTime());

        $manager->persist($userPartner);
        $manager->persist($partner);
        $manager->persist($userSubsidiary);
        $manager->persist($park);

        return array($userSubsidiary, $park);
    }
}
