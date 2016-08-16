<?php

namespace Kijho\HelpDeskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kijho\HelpDeskBundle\Entity as Entity;

/**
 * Description of LoadBasicData
 * @author Cesar Giraldo <cesargiraldo1108@gmail.com> 03/05/2016
 */
class LoadBasicData implements FixtureInterface, ContainerAwareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {

        //add example client
        $client = new Entity\User();
        $client->setName('John Doe');
        $client->setEmail('client@example.com');
        $client->setIsTicketOperator(false);
        $client->setPassword('client');
        $manager->persist($client);
        
        //add support user
        $operator = new Entity\User();
        $operator->setName('Albert Einstein');
        $operator->setEmail('operator@example.com');
        $operator->setIsTicketOperator(true);
        $operator->setPassword('operator');
        $manager->persist($operator);

        //create category tickets
        $category = new Entity\TicketCategory();
        $category->setName('General');
        $category->setSlug('general');
        $category->setEmail('example@domain.com');
        $manager->persist($category);
        
        $manager->flush();
    }

}
