<?php
/**
 * Created by PhpStorm.
 * User: connector
 * Date: 15/05/2018
 * Time: 12:55
 */

namespace App\Model;
use PDO;

class VisitsManager extends Manager
{

    public function __construct()
    {
        parent::__construct('projet5_visits');
    }

    public function create(Projet5_visits &$visits)
    {
        $this->pdostatement=$this->pdo->prepare('
        INSERT 
        INTO projet5_visits VALUES(:ip,:stamp)');
        $this->pdostatement->bindValue(':ip',$visits->getIp(),PDO::PARAM_STR);
        $this->pdostatement->bindValue(':stamp',$visits->getTimestamp(),PDO::PARAM_INT);

        $nbVisits =$this->pdostatement->execute();


        if(!$nbVisits)
        {
            return false;
        }
        else
            {
                return true;
            }


    }

    public function update(Projet5_visits &$visits)
    {
        $this->pdostatement=$this->pdo->prepare('UPDATE projet5_visits SET ip=:ip, timestamp=:stamp WHERE ip=:ip');
        $this->pdostatement->bindValue(':ip',$visits->getIp(),PDO::PARAM_STR);
        $this->pdostatement->bindValue(':stamp',$visits->getTimestamp(),PDO::PARAM_INT);

        return $this->pdostatement->execute();
    }

    public function delete()
    {
        $timestamp_5min = time() - (60 * 5);
        $this->pdostatement=$this->pdo->query('
        
        DELETE 
        FROM projet5_visits WHERE timestamp <' .$timestamp_5min);
        $deleteVisit = $this->pdostatement->execute();
    }

    public function countVisit()
    {
        $this->pdostatement=$this->pdo->query('
        SELECT COUNT(ip) 
        FROM projet5_visits');
        $executeOK=$this->pdostatement->execute();
        if($executeOK)
        {
            $countVisit=$this->pdostatement->fetch();
            if($countVisit===false)
            {
                return null;
            }
            else{
                return $countVisit;
            }
        }else
        {
            return false;
        }

    }

    public function saveIp()
    {
        $this->pdostatement=$this->pdo->prepare('REPLACE INTO projet5_visits VALUES(:ip,:stamp)');
        $this->pdostatement->bindValue(':ip',$_SERVER['REMOTE_ADDR'],PDO::PARAM_STR);
        $this->pdostatement->bindValue(':stamp',time(),PDO::PARAM_INT);

        $nbVisits =$this->pdostatement->execute();


        if(!$nbVisits)
        {
            return false;
        }
        else
        {
            return true;
        }
    }


}