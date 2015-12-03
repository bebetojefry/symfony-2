<?php
namespace App\FrontBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserunlockCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:unlock')
            ->setDescription('Unlock User after 30 minutes');
    }

    protected function getContainer(){
        return $this->getApplication()->getKernel()->getContainer();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userlockRepo = $em->getRepository('AppFrontBundle:Userlock');
        $userlockRepo->removeExpiredlocks();
    }
}