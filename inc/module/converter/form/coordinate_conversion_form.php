<?php

namespace module\converter\form;

use classes\ajax;
use classes\geometry;
use classes\gps_datums;
use classes\lat_lng;
use Exception;
use form\form;
use html\a;

class coordinate_conversion_form extends form {

    public $OS6;
    public $OS8;
    public $OSGB36_lat;
    public $OSGB36_lng;
    public $WGS84_lat;
    public $WGS84_lng;
    public int|string $source = 0;

    public function __construct() {

        $fields = [
            form::create('field_select', 'source')
                ->set_attr('default', 0)
                ->set_attr('options', ['OS6', 'OS8', 'OSGB36', 'WGS84']),
            form::create('field_string', 'OSGB36_lat')
                ->set_attr('required', false),
            form::create('field_string', 'OSGB36_lng')
                ->set_attr('required', false),
            form::create('field_string', 'WGS84_lat')
                ->set_attr('required', false),
            form::create('field_string', 'WGS84_lng')
                ->set_attr('required', false),
            form::create('field_string', 'OS6'),
            form::create('field_string', 'OS8')
                ->set_attr('required', false),
        ];
        parent::__construct($fields);
        $this->id = 'coordinate_conversion_form';
        $this->attributes['data-ajax-change'] = '\module\converter\form\coordinate_conversion_form:do_form_submit';
    }

    public function set_from_request() {
        parent::set_from_request();
        switch ($this->source) {
            case 0:
                $this->OS8 = null;
                $this->OSGB36_lat = null;
                $this->OSGB36_lng = null;
                $this->WGS84_lat = null;
                $this->WGS84_lng = null;
                break;
            case 1:
                $this->OS6 = null;
                $this->OSGB36_lat = null;
                $this->OSGB36_lng = null;
                $this->WGS84_lat = null;
                $this->WGS84_lng = null;
                break;
            case 2:
                $this->OS6 = null;
                $this->OS8 = null;
                $this->WGS84_lat = null;
                $this->WGS84_lng = null;
                $this->OSGB36_lat = geometry::coordinate_normalise($this->OSGB36_lat);
                $this->OSGB36_lng = geometry::coordinate_normalise($this->OSGB36_lng);
                break;
            case 3:
                $this->OS6 = null;
                $this->OS8 = null;
                $this->OSGB36_lat = null;
                $this->OSGB36_lng = null;
                $this->WGS84_lat = geometry::coordinate_normalise($this->WGS84_lat);
                $this->WGS84_lng = geometry::coordinate_normalise($this->WGS84_lng);
                break;
            default:
                break;
        }
        $this->set_disabled();
    }

    public function set_disabled() {
        foreach ($this->fields as $field) {
            $field->set_attr('disabled', true)
                ->set_attr('required', false);
        }
        $this->get_field_from_name('source')
            ->set_attr('disabled', false);

        switch ($this->source) {
            case 0:
                $this->get_field_from_name('OS6')
                    ->set_attr('disabled', false)
                    ->set_attr('required', true);
                break;
            case 1:
                $this->get_field_from_name('OS8')
                    ->set_attr('disabled', false)
                    ->set_attr('required', true);
                break;
            case 2:
                $this->get_field_from_name('OSGB36_lat')
                    ->set_attr('disabled', false)
                    ->set_attr('required', true);
                $this->get_field_from_name('OSGB36_lng')
                    ->set_attr('disabled', false)
                    ->set_attr('required', true);
                break;
            case 3:
                $this->get_field_from_name('WGS84_lat')
                    ->set_attr('disabled', false)
                    ->set_attr('required', true);
                $this->get_field_from_name('WGS84_lng')
                    ->set_attr('disabled', false)
                    ->set_attr('required', true);
                break;
        }
    }

    public function do_submit(): bool {
        $point = $this->get_source_as_wgs84();
        $this->set_values_from_point($point);
        ajax::update((string)$this->get_html());
        return true;
    }

    /**
     * @return lat_lng
     * @throws Exception
     */
    public function get_source_as_wgs84(): lat_lng {
        return match ($this->source) {
            0 => geometry::os_to_lat_long($this->OS6),
            1 => geometry::os_to_lat_long($this->OS8),
            2 => gps_datums::convert(new lat_lng($this->OSGB36_lat, $this->OSGB36_lng), 'OSGB36', 'WGS84'),
            3 => new lat_lng($this->WGS84_lat, $this->WGS84_lng),
            default => new lat_lng(0, 0),
        };
    }

    public function set_values_from_point(lat_lng $point) {
        $this->OS6 = geometry::lat_long_to_os($point);
        $this->OS8 = geometry::lat_long_to_os($point, 8);
        $this->WGS84_lat = $point->lat();
        $this->WGS84_lng = $point->lng();
        $osgb36 = gps_datums::convert($point, 'WGS84', 'OSGB36');
        $this->OSGB36_lat = $osgb36->lat();
        $this->OSGB36_lng = $osgb36->lng();
    }

    public function get_html(): string {
        $this->set_disabled();
        if ($this->OSGB36_lng && $this->OSGB36_lat) {
            $this->post_fields_text = a::create('a.button', ['onClick' => 'javascript:map.planner.add_point_full(' . $this->WGS84_lat . ', ' . $this->WGS84_lng . ');'], 'Add to map');
        }
        return parent::get_html();
    }

}
 