<?php
namespace module\converter\form;

use classes\ajax;
use classes\geometry;
use classes\gps_datums;
use classes\lat_lng;
use form\form;

class coordinate_conversion_form extends form {

    public $OS6;
    public $OS8;
    public $OSGB36_lat;
    public $OSGB36_lng;
    public $WGS84_lat;
    public $WGS84_lng;
    public $source = 0;

    public function __construct() {

        $fields = [
            form::create('field_select', 'source')
                ->set_attr('default', '')
                ->set_attr('options', ['OS6', 'OS8', 'OSGB36', 'WGS84']),
            form::create('field_float', 'OSGB36_lat')
                ->set_attr('required', false),
            form::create('field_float', 'OSGB36_lng')
                ->set_attr('required', false),
            form::create('field_float', 'WGS84_lat')
                ->set_attr('required', false),
            form::create('field_float', 'WGS84_lng')
                ->set_attr('required', false),
            form::create('field_string', 'OS6'),
            form::create('field_string', 'OS8')
                ->set_attr('required', false)
        ];
        parent::__construct($fields);
        $this->id = 'coordinate_conversion_form';
        $this->attributes['data-ajax-change'] = '\module\converter\form\coordinate_conversion_form:do_form_submit';
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

    public function get_html() {
        $this->set_disabled();
        return parent::get_html();
    }

    /** @return lat_lng */
    public function get_source_as_wgs84() {
        switch ($this->source) {
            case 0:
                $point = geometry::os_to_lat_long($this->OS6);
                break;
            case 1:
                $point = geometry::os_to_lat_long($this->OS8);
                break;
            case 2:
                $point = gps_datums::convert(new lat_lng($this->OSGB36_lat, $this->OSGB36_lng), 'OSGB36', 'WGS84');
                break;
            case 3:
                $point = new lat_lng($this->WGS84_lat, $this->WGS84_lng);
                break;
            default:
                $point = new lat_lng(0, 0);
                break;

        }
        return $point;
    }

    public function set_from_request() {
        switch ($this->source) {
            case 0:
                unset($this->OS8);
                unset($this->OSGB36_lat);
                unset($this->OSGB36_lng);
                unset($this->WGS84_lat);
                unset($this->WGS84_lng);
                break;
            case 1:
                unset($this->OS6);
                unset($this->OSGB36_lat);
                unset($this->OSGB36_lng);
                unset($this->WGS84_lat);
                unset($this->WGS84_lng);
                break;
            case 2:
                unset($this->OS6);
                unset($this->OS8);
                unset($this->WGS84_lat);
                unset($this->WGS84_lng);
                break;
            case 3:
                unset($this->OS6);
                unset($this->OS8);
                unset($this->OSGB36_lat);
                unset($this->OSGB36_lng);
                break;
            default:
                break;
        }
        $this->set_disabled();
    }

    public function set_values_from_point(lat_lng $point) {
        $this->OS6 = geometry::lat_long_to_os($point, 6);
        $this->OS8 = geometry::lat_long_to_os($point, 8);
        $this->WGS84_lat = $point->lat();
        $this->WGS84_lng = $point->lng();
        $osgb36 = gps_datums::convert($point, 'WGS84', 'OSGB36');
        $this->OSGB36_lat = $osgb36->lat();
        $this->OSGB36_lng = $osgb36->lng();
    }

    public function do_submit() {
        $point = $this->get_source_as_wgs84();
        $this->set_values_from_point($point);
        ajax::update($this->get_html()->get());

    }

}
 