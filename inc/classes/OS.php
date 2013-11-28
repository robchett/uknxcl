<?php
namespace classes;

class OS {

    private static $cells;
    private static $cell_codes = [
        'HY',
        'NA', 'NB', 'NC', 'ND',
        'NF', 'NG', 'NH', 'NJ', 'NK',
        'NL', 'NM', 'NN', 'NO',
        'NR', 'NS', 'NT', 'NU',
        'NX', 'NY', 'NX',
        'SD', 'SE', 'TA',
        'SH', 'SJ', 'SK', 'TF', 'TG',
        'SM', 'SN', 'SO', 'SP', 'TL', 'TM',
        'SS', 'ST', 'SU', 'TQ', 'TR',
        'SW', 'SX',
    ];

    public static function cells() {
        if (!isset(self::$cells)) {
            self::$cells = new collection();
            foreach (self::$cell_codes as $code) {
                $cell = new lat_lng_bound(geometry::os_to_lat_long($code . '99999999'), geometry::os_to_lat_long($code . '000000'));
                $cell->code = $code;
                self::$cells[] = $cell;
            }
        }
        return self::$cells;
    }

}
 