<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    /**
     * @Route("/answers/{id}/vote", methods="POST", name="answer_vote")
     */
    public function answerVote($id, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $direction = $data['direction'] ?? 'up';

        // todo - use id to query the database
        $answer = $entityManager->getRepository(Answer::class)->find($id);
        $currentVoteCount = $answer->getVotes();
        // use real logic here to save this to the database
        if ($direction === 'up') {
            $logger->info('Voting up!');
            $currentVoteCount = $currentVoteCount + 1;

        } else {
            $logger->info('Voting down!');
            $currentVoteCount = $currentVoteCount - 1;
        }
        $answer->setVotes($currentVoteCount);
        $entityManager->flush();
        return $this->json(['votes' => $currentVoteCount]);
    }
}
