<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
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
    public function answerVote(Answer $answer, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $direction = $data['direction'] ?? 'up';

        if ($direction === 'up') {
            $logger->info('Voting up!');
            $answer->voteUp();

        } else {
            $logger->info('Voting down!');
            $answer->voteDown();
        }
        $entityManager->flush();
        return $this->json(['votes' => $answer->getVotes()]);
    }
    /**
     * @Route("/answers/popular", name="app_popular_answers")
     */
    public function popularAnwers(AnswerRepository $answerRepository, Request $request)
    {
        $answers = $answerRepository->findPopularAnswers($request->query->get('q'));
        //dd($answers);
        return $this->render('answer/show.html.twig',[
            'answers' => $answers
        ]);
    }
}
