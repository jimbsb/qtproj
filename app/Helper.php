<?php

namespace App;

use Carbon\Carbon;

class Helper {

    
    public static function parser($type,$data): string
    {
        switch($type)
        {
            case 'time':
                return self::timestamp($data['timestamp'], $type, $data['params']);
                // return json_encode($data);
            default:
                return '-';
        }
    }
            
    public static function timestamp($value, ?string $type = null, array $params = []): string
    {
        if (blank($value) || blank($type)) {
            return filled($value) ? $value : '-';
        }

        $tz = $params['tz'] ?? config('app.timezone');

        return match ($type) {
            'time'          => Carbon::parse($value)->tz($tz)->diffForHumans($params),
            'date'          => Carbon::parse($value)->tz($tz)->format($params['format'] ?? 'd/m/Y'),
            'datetime'      => Carbon::parse($value)->tz($tz)->format($params['format'] ?? 'd/m/Y H:i'),
            default         => (string) $value,
        };
    }
}