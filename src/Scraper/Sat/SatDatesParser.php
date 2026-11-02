<?php

namespace App\Scraper\Sat;

use App\External\Sat\SatDto;
use Symfony\Component\DomCrawler\Crawler;

class SatDatesParser
{
    public function parse(string $html): array
    {
        $crawler = new Crawler($html);
        $results = [];

        $crawler->filter('table')->each(function (Crawler $table) use (&$results) {
            $table->filter('tbody tr')->each(function (Crawler $row) use (&$results) {

                $cellTexts = array_merge(
                    $row->filter('th')->each(fn(Crawler $c) => $this->cellText($c)),
                    $row->filter('td')->each(fn(Crawler $c) => $this->cellText($c)),
                );

                if (count($cellTexts) !== 3) return;

                $satDto = SatDto::fromStrings(
                    $cellTexts[0],
                    $cellTexts[1],
                    $cellTexts[2],
                    false
                );

                $results[$satDto->testDate->format('Y-m-d')] = $satDto;
            });
        });

        return $results;
    }

    private function cellText(Crawler $cell): string
    {
        foreach ($cell->filter('a') as $node) {
            $node->parentNode->removeChild($node);
        }
        return trim($cell->text());
    }
}
