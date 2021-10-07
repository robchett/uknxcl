<?php

namespace module\converter\form;

use classes\ajax;
use classes\geometry;
use classes\gps_datums;
use classes\lat_lng;
use Exception;
use form\form;
use html\a;

class coordinate_conversion_form extends form
{

    public string $OS6;
    public string $OS8;
    public string $OSGB36_lat;
    public string $OSGB36_lng;
    public string $WGS84_lat;
    public string $WGS84_lng;
    public int|string $source = 0;

    public function __construct()
    {

        $fields = [
            new \form\field_select(
                'source',
                default: '0',
                options: ['OS6', 'OS8', 'OSGB36', 'WGS84'],
            ),
            new \form\field_string(
                'OSGB36_lat',
                required: false,
            ),
            new \form\field_string(
                'OSGB36_lng',
                required: false,
            ),
            new \form\field_string(
                'WGS84_lat',
                required: false,
            ),
            new \form\field_string(
                'WGS84_lng',
                required: false,
            ),
            new \form\field_string('OS6'),
            new \form\field_string(
                'OS8',
                required: false,
            ),
        ];
        parent::__construct($fields);
        $this->id = 'coordinate_conversion_form';
        $this->attributes->dataAjaxChange = '\module\converter\form\coordinate_conversion_form:do_form_submit';
    }

    public function set_from_request(): void
    {
        parent::set_from_request();
        switch ($this->source) {
            case 0:
                $this->OS8 = '';
                $this->OSGB36_lat = '';
                $this->OSGB36_lng = '';
                $this->WGS84_lat = '';
                $this->WGS84_lng = '';
                break;
            case 1:
                $this->OS6 = '';
                $this->OSGB36_lat = '';
                $this->OSGB36_lng = '';
                $this->WGS84_lat = '';
                $this->WGS84_lng = '';
                break;
            case 2:
                $this->OS6 = '';
                $this->OS8 = '';
                $this->WGS84_lat = '';
                $this->WGS84_lng = '';
                $this->OSGB36_lat = geometry::coordinate_normalise($this->OSGB36_lat);
                $this->OSGB36_lng = geometry::coordinate_normalise($this->OSGB36_lng);
                break;
            case 3:
                $this->OS6 = '';
                $this->OS8 = '';
                $this->OSGB36_lat = '';
                $this->OSGB36_lng = '';
                $this->WGS84_lat = geometry::coordinate_normalise($this->WGS84_lat);
                $this->WGS84_lng = geometry::coordinate_normalise($this->WGS84_lng);
                break;
            default:
                break;
        }
        $this->set_disabled();
    }

    public function set_disabled(): void
    {
        foreach ($this->fields as $field) {
            $field->disabled = true;
            $field->required = false;
        }
        $this->get_field_from_name('source')->disabled = false;

        switch ($this->source) {
            case 0:
                $this->get_field_from_name('OS6')->disabled = false;
                $this->get_field_from_name('OS6')->required = true;
                break;
            case 1:
                $this->get_field_from_name('OS8')->disabled = false;
                $this->get_field_from_name('OS8')->required = true;
                break;
            case 2:
                $this->get_field_from_name('OSGB36_lat')->disabled = false;
                $this->get_field_from_name('OSGB36_lat')->required = true;
                $this->get_field_from_name('OSGB36_lng')->disabled = false;
                $this->get_field_from_name('OSGB36_lng')->required = true;
                break;
            case 3:
                $this->get_field_from_name('WGS84_lat')->disabled = false;
                $this->get_field_from_name('WGS84_lat')->required = true;
                $this->get_field_from_name('WGS84_lng')->disabled = false;
                $this->get_field_from_name('WGS84_lng')->required = true;
                break;
        }
    }

    public function do_submit(): bool
    {
        $point = $this->get_source_as_wgs84();
        $this->set_values_from_point($point);
        ajax::update($this->get_html());
        return true;
    }

    /**
     * @return lat_lng
     * @throws Exception
     */
    public function get_source_as_wgs84(): lat_lng
    {
        return match ($this->source) {
            0 => geometry::os_to_lat_long($this->OS6),
            1 => geometry::os_to_lat_long($this->OS8),
            2 => gps_datums::convert(new lat_lng((float) $this->OSGB36_lat, (float) $this->OSGB36_lng), 'OSGB36', 'WGS84'),
            3 => new lat_lng((float) $this->WGS84_lat, (float) $this->WGS84_lng),
            default => new lat_lng(0, 0),
        };
    }

    public function set_values_from_point(lat_lng $point): void
    {
        $this->OS6 = geometry::lat_long_to_os($point);
        $this->OS8 = geometry::lat_long_to_os($point, 8);
        $this->WGS84_lat = (string) $point->lat();
        $this->WGS84_lng = (string) $point->lng();
        $osgb36 = gps_datums::convert($point, 'WGS84', 'OSGB36');
        $this->OSGB36_lat = (string) $osgb36->lat();
        $this->OSGB36_lng = (string) $osgb36->lng();
    }

    public function get_html(): string
    {
        $this->set_disabled();
        if ($this->OSGB36_lng && $this->OSGB36_lat) {
            $this->post_fields_text = "<a class='button' onClick='javascript:map.planner.add_point_full({$this->WGS84_lat},{$this->WGS84_lng});'>Add to map</a>";
        }
        return parent::get_html();
    }
}
