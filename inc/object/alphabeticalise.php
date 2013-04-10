<?php
class alphabeticalise {

    static function get_select(array $objects, stdClass $options) {
        $class = (isset($options->class) ? $options->class : '');
        $id = (isset($options->id) ? $options->id : '');
        $delimitor = (isset($options->delimitor) ? $options->delimitor : ' ');

        $html = '<select class="' . $class . '" id="' . $id . '">';

        foreach ($objects as $key => $object) {
            $title = array();
            foreach ($options->title as $title_fields) {
                $title[] = $object->$title_fields;
            }
            $title = implode($delimitor, $title);
            $html .= '<option value="' . ((isset($options->value) && isset($object->{$options->value}) ? $object->{$options->value} : $key)) . '">' . $title . '</option>';

        }

        $html .= '</select>';
        return $html;

    }

    static function pilot_array($like = NULL) {
        $array = array();

        $sql = 'SELECT pid,name FROM pilot ' . ($like ? 'WHERE name LIKE "%' . $like . '%"' : '') . ' ORDER BY pilot
        .name';
        $res = db::query($sql);
        while ($row = db::fetch($res)) {
            $array[$row->pid] = $row->name;
            ;
        }
        return $array;
    }

    static function club_array($pilot = NULL) {
        $array = array();
        $sql = "SELECT cid,name FROM club ORDER BY NAME";
        $res = db::query($sql);
        while ($row = db::fetch($res)) {
            $array[$row->cid] = $row->name;
            ;
        }
        return $array;
    }

    static function glider_array($pilot = NULL) {
        $array = array();
        $sql = "SELECT gid,name,manufacturer.title AS manufacturer FROM glider LEFT JOIN manufacturer ON glider.mid = manufacturer.mid ORDER BY manufacturer.title,glider.name";
        $res = db::query($sql);
        while ($row = db::fetch($res)) {
            $array[$row->gid] = $row->manufacturer . ' - ' . $row->name;
            ;
        }
        return $array;
    }
}

?>
