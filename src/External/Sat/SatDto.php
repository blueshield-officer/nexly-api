<?php

namespace App\External\Sat;

readonly class SatDto
{
    public function __construct(
        public \DateTimeImmutable $testDate,
        public \DateTimeImmutable $registrationDeadline,
        public \DateTimeImmutable $lateRegistrationDeadline,
        public bool               $anticipated
    ) {}

    public static function fromStrings(
        string $testDate,
        string $registrationDeadline,
        string $lateRegistrationDeadline,
        bool $anticipated
    ): self {
        return new self(
            self::parse($testDate),
            self::parse($registrationDeadline),
            self::parse($lateRegistrationDeadline),
            $anticipated
        );
    }

    private static function parse(string $date): \DateTimeImmutable
    {
        $date = trim($date);

        foreach (['M. j, Y', 'F j, Y'] as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $date);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        throw new \UnexpectedValueException("Cannot parse date: \"$date\"");
    }
}
