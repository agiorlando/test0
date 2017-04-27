<?php

namespace AppBundle\Command\Database;

use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:test')
            ->setDescription('Perform all sorts of tests')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $c = new Customer();
        $c->setFirstName('Ruben');
        $c->setLastName('Knol');
        $c->setCountry('de');
        $c->setGender('m');
        $c->setEmail('c.minor6@gmail.com');
        $c->setBonusPercentage(10);

        $repo = $this->getContainer()->get('app.repository.customer');
        // $repo->insert($c);
    }
}
