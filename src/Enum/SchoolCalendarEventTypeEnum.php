<?php

namespace App\Enum;

enum SchoolCalendarEventTypeEnum : string
{
    case YEAR_START = 'year_start';
    case YEAR_END = 'year_end';
    case GRADUATION = 'graduation';
    case EXAM_PERIOD_START = 'exam_period_start';
    case EXAM_PERIOD_END = 'exam_period_end';


    case DRESS_DOWN_DAY = 'dress_down_day';

    public function category(): SchoolCalendarEventCategoryEnum
    {
        return match ($this) {
            self::YEAR_START, self::YEAR_END,
            self::EXAM_PERIOD_START, self::EXAM_PERIOD_END
                => SchoolCalendarEventCategoryEnum::MILESTONE,

            self::GRADUATION => SchoolCalendarEventCategoryEnum::CEREMONY,
            self::DRESS_DOWN_DAY => SchoolCalendarEventCategoryEnum::OBSERVANCE,
        };
    }
}
