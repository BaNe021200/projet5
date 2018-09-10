<?php
/**
 * Created by PhpStorm.
 * User: connector
 * Date: 15/05/2018
 * Time: 12:46
 */

namespace App\Model;


class Projet5_visits
{
    /**
     * @var string $ip ip du visiteur
     */
    private $ip;

    /**
     * @var int $timestamp  heure de la visit
     */
    private $timestamp;

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return Projet5_visits
     */
    public function setIp(string $ip): Projet5_visits
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Projet5_visits
     */
    public function setTimestamp(int $timestamp): Projet5_visits
    {
        $this->timestamp = $timestamp;
        return $this;
    }



}