<?php

namespace Kijho\HelpDeskBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/*
 * TicketProvider
 */

class TicketProvider {

    protected $container;
    protected $em;
    protected $clientStorage;
    protected $operatorStorage;
    protected $translator;

    public function __construct(ContainerInterface $container, EntityManager $em) {
        $this->container = $container;
        $this->em = $em;
        $this->clientStorage = $this->container->getParameter('help_desk.client_provider');
        $this->operatorStorage = $this->container->getParameter('help_desk.operator_provider');
        $this->translator = $this->container->get('translator');
    }

    public function getTicketClient($clientId) {
        if (!empty($clientId)) {
            $client = $this->em->getRepository($this->clientStorage)->find($clientId);
            return $client;
        }
        return null;
    }

    public function getTicketOperator($operatorId) {
        if (!empty($operatorId)) {
            $operator = $this->em->getRepository($this->operatorStorage)->find($operatorId);
            return $operator;
        }
        return null;
    }

    public function getCountClientTickets($clientId, $status = null) {
        return $this->em->getRepository('HelpDeskBundle:Ticket')->findCountTickets($clientId, $status);
    }
    
    public function getCountTickets($status = null) {
        return $this->em->getRepository('HelpDeskBundle:Ticket')->findCountTickets(null, $status);
    }

    /**
     * Permite obtener el numero de segundos que hay entre dos fechas
     * @param \DateTime $startDate fecha inicial
     * @param \DateTime $endDate fecha final
     * @return integer numero total de segundos
     */
    public function getSecondsBetweenDates($startDate, $endDate) {
        $timeFirst = strtotime($startDate->format('Y-m-d H:i:s'));
        $timeSecond = strtotime($endDate->format('Y-m-d H:i:s'));
        $differenceInSeconds = $timeSecond - $timeFirst;
        return $differenceInSeconds;
    }

    /**
     * Permite obtener en lenguaje natural el tiempo que representa
     * una cantidad de segundos determinada
     * @author Cesar Giraldo <cesargiraldo1108@gmail.com> 27/04/2016
     * @param integer $secs canidad de segundos
     * @return string descripcion del tiempo transcurrido
     */
    public function getElapsedTime($secs) {
        
        $year = $this->translator->trans('help_desk.global.year');
        $years = $this->translator->trans('help_desk.global.years');
        $week = $this->translator->trans('help_desk.global.week');
        $weeks = $this->translator->trans('help_desk.global.weeks');
        $day = $this->translator->trans('help_desk.global.day');
        $days = $this->translator->trans('help_desk.global.days');
        $hour = $this->translator->trans('help_desk.global.hour');
        $hours = $this->translator->trans('help_desk.global.hours');
        $minute = $this->translator->trans('help_desk.global.minute');
        $minutes = $this->translator->trans('help_desk.global.minutes');
        
        $bit = array();
        
        $countYears = $secs / 31556926 % 12;
        $countWeeks = $secs / 604800 % 52;
        $countDays = $secs / 86400 % 7;
        $countHours = $secs / 3600 % 24;
        $countMinutes = $secs / 60 % 60;
        $countSeconds = $secs / 60;
        
        if ($countYears > 1) {
            $bit[$years] = $countYears;
        } else {
            $bit[$year] = $countYears;
        }
        
        if ($countWeeks > 1) {
            $bit[$weeks] = $countWeeks;
        } else {
            $bit[$week] = $countWeeks;
        }
        
        if ($countDays > 1) {
            $bit[$days] = $countDays;
        } else {
            $bit[$day] = $countDays;
        }
        
        if ($countHours > 1) {
            $bit[$hours] = $countHours;
        } else {
            $bit[$hour] = $countHours;
        }
        
        if ($countMinutes > 1) {
            $bit[$minutes] = $countMinutes;
        } else {
            $bit[$minute] = $countMinutes;
        }
        
       /*if ($countSeconds > 1) {
            $bit[$seconds] = $countSeconds;
        } else {
            $bit[$second] = $countSeconds;
        }*/


        foreach ($bit as $k => $v) {
            if ($v > 0) {
                $ret[] = $v . ' '.$k;
            }
        }

        return join(' ', $ret);
    }
}
