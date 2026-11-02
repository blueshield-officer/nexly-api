<?php

namespace App\Controller\Api\Admin\School;

use App\Dto\SchoolDto;
use App\Manager\SchoolManager;
use App\Service\SchoolPictureUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/{schoolUuid}')]
readonly class SchoolController
{
    public function __construct(
        private ValidatorInterface $validator,
        private SchoolPictureUploader $schoolPictureUploader,
    ){}

    #[Route('/picture', methods: ['POST'])]
    public function updatePicture(
        Request $request,
        SchoolDto $schoolDto
    ): JsonResponse
    {
        $file = $request->files->get('picture');

        if (!$file) {
            throw new BadRequestHttpException('No file uploaded under "picture" field.');
        }

        $violations = $this->validator->validate($file, [
            new Assert\File(
                maxSize: '5M',
                mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                mimeTypesMessage: 'Please upload a valid image (jpeg, png, webp).',
            ),
        ]);

        if (count($violations) > 0) {
            throw new UnprocessableEntityHttpException((string) $violations);
        }

        $this->schoolPictureUploader->upload($schoolDto->uuid, $file);

        return new JsonResponse(['pictureUrl' => $this->schoolPictureUploader->getPictureUrl($schoolDto->uuid)]);
    }
}
