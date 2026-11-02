<?php

namespace App\Controller\Api;

use App\Dto\Request\FeedbackBugPostRequest;
use App\Dto\Request\FeedbackIdeaPostRequest;
use App\Entity\FeedbackBug;
use App\Entity\FeedbackIdea;
use App\Entity\User;
use App\Enum\FeedbackStatusEnum;
use App\Repository\SchoolRepository;
use App\Service\InternalServerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Target;


#[Route('/feedback')]
final class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface     $em,
        private readonly InternalServerService      $internalServerService,
    ) {}

    #[Route('/bug', name: 'app_feedback_bug', methods: ['POST'])]
    public function reportBug(
        #[Target('feedback_bug.limiter')] RateLimiterFactory $rateLimiterFeedbackBug,
        #[MapRequestPayload] FeedbackBugPostRequest $request,
        SchoolRepository $schoolRepository,
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $school = $schoolRepository->findOneByUuidOrFail($request->schoolUuid);

        $rateLimiter = $rateLimiterFeedbackBug->create((string) $user->getId());

        $limit = $rateLimiter->consume();
        if (!$limit->isAccepted()) {
            throw new TooManyRequestsHttpException($limit->getRetryAfter()->getTimestamp() - time());
        }

        $content = trim($request->content);

        $feedbackBug = new FeedbackBug();
        $feedbackBug->setSchool($school);
        $feedbackBug->setContent($content);
        $feedbackBug->setUser($user);

        $this->em->persist($feedbackBug);
        $this->em->flush();

        $this->internalServerService->post('/webhooks/business/bug', [
            'email' => $user->getEmail(),
            'message' => $content,
        ]);

        return new JsonResponse([], 201);
    }

    #[Route('/idea', name: 'app_feedback_idea', methods: ['POST'])]
    public function sendIdea(
        #[Target('feedback_idea.limiter')] RateLimiterFactory $rateLimiterFeedbackIdea,
        #[MapRequestPayload] FeedbackIdeaPostRequest $request,
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $rateLimiter = $rateLimiterFeedbackIdea->create((string) $user->getId());

        $limit = $rateLimiter->consume();
        if (!$limit->isAccepted()) {
            throw new TooManyRequestsHttpException($limit->getRetryAfter()->getTimestamp() - time());
        }

        $content = trim($request->content);

        $feedbackIdea = new FeedbackIdea();
        $feedbackIdea->setUser($user);
        $feedbackIdea->setContent($content);
        $feedbackIdea->setStatus(FeedbackStatusEnum::InReview);

        $this->em->persist($feedbackIdea);
        $this->em->flush();

        var_dump($user->getEmail(), $content);

        // TODO Make something out of it
        $this->internalServerService->post('/webhooks/business/idea', [
            'email' => $user->getEmail(),
            'message' => $content,
        ]);

        return new JsonResponse([], 201);
    }
}
