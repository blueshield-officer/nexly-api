<?php

namespace App\Sis\Contract;

use App\Sis\Importer\ArTimeImporter;
use App\Sis\Importer\PowerschoolImporter;

interface SisImporterResolverInterface
{
    public function resolve(
        string $schoolUuid,
        string $apiKey
    ) : ArTimeImporter | PowerschoolImporter;
}
