<?php

namespace App\Formatter;


class SchoolFormatter
{
    public function format(array $school): array
    {
        $formatted = [
            'id' => $school['id'],
            'uuid' => $school['uuid'],
            'name' => $school['name'],
            'private' => $school['private'],
            'timezone' => $school['timezone'],
            'districtId' => $school['districtId'],
            'colors' => [],
            'grades' => [],
        ];

        if (!is_null($school['colors'])) {
            $formatted['colors'] = [];
            foreach ($school['colors'] as $color) {
                if ($color['base']) $formatted['colors']['primary'] = '#' . $color['hex'];
                else {
                    if (!isset($color['base']['secondaries'])) $color['base']['secondaries'] = [];
                    $formatted['colors']['secondaries'][] = '#' .$color['hex'];
                }
            }
        }

        if (!is_null($school['grades'])) {
            usort($school['grades'], fn($a, $b) => $a['sortOrder'] <=> $b['sortOrder']);
            foreach ($school['grades'] as $grade) {
                $formatted['grades'][(string) $grade['uuid']] = [
                    'grade' => $grade['grade'],
                    'name' => $grade['gradeName'],
                    'color' => '#' . $grade['hex']
                ];
            }
        }

        if (!is_null($school['sis']))
            $formatted['sis'] = $school['sis']['sisId'];

        return $formatted;
    }
}
