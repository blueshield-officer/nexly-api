<?php

namespace App\Controller\Api\Auth;

use App\Entity\User;

use App\Service\MailService;
use App\Service\RedisService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ){}

    #[Route('/register/send-code', name: 'app_api_auth_register_send_code', methods: ['POST'])]
    public function sendCode(
        Request $request,
        UserService $userService,
        RedisService $redisService,
        MailService $mailService,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($error = $this->validate($data, [
            'email'    => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank(), new Assert\Length(min: 8)],
        ])) return $error;

        $email = $data['email'];
        $password = $data['password'];

        $redisKey = ["auth", "code", $email];

        if ($userService->userExists($email))   return new JsonResponse(['message' => 'user_exists'], 422);

        if ($redisService->exists($redisKey))   return new JsonResponse(["message" => "Code was already sent. Please check your email."], 422);


        $code = random_int(100000, 999999);

        $mailService->sendVerificationCode($email, $code);

        $redisService->set(["auth", "register", $email], [
            "email" => $email,
            "password" => $password,
        ], 300);

        $redisService->set(["auth", "code", $email],  $code, 300);

        return new JsonResponse(["message" => "Code was sent. Please check your email."], 200);
    }

    #[Route('/register/verify', name: 'app_api_auth_register_verify', methods: ['POST'])]
    public function verify(
        Request $request,
        RedisService $redisService,
        UserService $userService,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($error = $this->validate($data, [
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'code'  => [new Assert\NotBlank(), new Assert\Length(min: 6)],
        ])) return $error;

        $email = $data['email'];
        $code = $data['code'];

        $redisCode = $redisService->get(["auth", "code", $email]);

        if ($redisCode !== $code) return new JsonResponse(["message" => "Code was not correct"], 422);

        $userData = $redisService->get(["auth", "register", $email]);

        $userService->createUser(
            email: $userData["email"],
            password: $userData["password"],
        );

        $redisService->delete(["auth", "code", $email]);
        $redisService->delete(["auth", "register", $email]);

        return new JsonResponse(["message" => "Registration successful"], 201);
    }

     #[Route('/login', name: 'app_api_auth_login', methods: ['POST'])]
     public function login(#[CurrentUser] ?User $user): JsonResponse
     {
         if (null === $user) {
             return new JsonResponse("Bad", 401);
         }
         return new JsonResponse("Good", 200);
    }

    private function validate(array $data, array $constraints): ?JsonResponse
    {
        $violations = $this->validator->validate($data, new Assert\Collection($constraints));

        if (count($violations) === 0) return null;

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        return new JsonResponse(['errors' => $errors], 422);
    }
}
