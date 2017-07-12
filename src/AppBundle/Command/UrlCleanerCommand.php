<?php

namespace AppBundle\Command;

use AppBundle\Entity\Url;
use AppBundle\Repository\UrlRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

class UrlCleanerCommand extends EndlessContainerAwareCommand
{
    const TIMEOUT = 600; //10 minutes

    protected function configure()
    {
        $this
            ->setName('app:clean-urls')
            ->setTimeout(static::TIMEOUT)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $removeDate = new \DateTime('- 15 days');
        $this->getUrlRepository()->deleteAllBeforeDateTime($removeDate);
    }

    /**
     * @return UrlRepository
     */
    protected function getUrlRepository()
    {
        return $this->getEntityManager()->getRepository(Url::class);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}
