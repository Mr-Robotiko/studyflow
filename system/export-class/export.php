<?php

class Export {

    public static function toJson($weekObj) {
        $data = self::normalizeWeekObject($weekObj);

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private static function normalizeWeekObject($weekObj) {

        return [
            'weekNumber' => method_exists($weekObj, 'getWeekNumber') ? $weekObj->getWeekNumber() : null,
            'days'       => method_exists($weekObj, 'getDays') ? $weekObj->getDays() : [],
            'notes'      => method_exists($weekObj, 'getNotes') ? $weekObj->getNotes() : null,
            'startDate'  => method_exists($weekObj, 'getStartDate') ? $weekObj->getStartDate() : null,
        ];
    }
}
?>
