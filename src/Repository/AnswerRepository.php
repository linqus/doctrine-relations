<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public static function createApprovedCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('status', Answer::STATUS_APPROVED))
            ;
    }

    /**
     * @return Answer[] 
     */
    public function findAllApproved(int $max = 10): array 
    {
        return $this->createQueryBuilder('answers')
                ->addCriteria(self::createApprovedCriteria())
                ->setMaxResults($max)
                ->getQuery()
                ->getResult();
    }
    /**
     * @return Answer[]
     */
    public function findPopularAnswers(string $q = null): array
    {
        $queryBuilder = $this->createQueryBuilder('answer')
                            ->addCriteria(self::createApprovedCriteria());
        if ($q) 
        {
            $queryBuilder->andWhere('answer.content like :q or question.question like :q')
                ->setParameter('q','%'.$q.'%');
        }
        return $queryBuilder
                ->addOrderBy('answer.votes','DESC')
                ->innerJoin('answer.question','question')
                ->addSelect('question')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
    }
}
