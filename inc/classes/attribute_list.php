<?php

namespace classes;

use ArrayAccess;
use html\node;
use Stringable;

final class attribute_list implements Stringable
{

    /**
     * @param mixed|null $value
     * @param string[] $class
     * @param array<string, ?scalar> $data
     * @param array<string, ?scalar> $aria
     * @param array<string, string> $style
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $title = null,
        public ?string $src = null,
        public mixed $value = null,
        public ?string $role = null,
        public ?string $rel = null,
        public ?string $alt = null,
        public ?string $target = null,
        public ?string $label = null,
        public ?string $autocomplete = null,
        public ?string $checked = null,
        public ?string $selected = null,
        public ?string $readonly = null,
        public ?string $placeholder = null,
        public ?string $action = null,
        public ?string $method = null,
        public ?string $enctype = null,
        public ?string $acceptCharset = null,
        public ?string $charset = null,
        public ?string $for = null,
        public ?string $media = null,
        public ?string $pattern = null,
        public ?string $srcset = null,
        public ?string $href = null,
        public ?string $loading = null,
        public null|string|int $width = null,
        public null|string|int $height = null,
        public ?int $maxlength = null,
        public ?string $type = null,
        public ?string $colspan = null,
        public ?string $inputmode = null,
        public ?string $multiple = null,
        public ?string $disabled = null,
        public ?string $onclick = null,
        public ?string $tabindex = null,
        public ?string $content = null,
        public ?string $property = null,
        public ?string $align = null,
        public ?string $download = null,
        public ?int $frameborder = null,
        public ?int $marginheight = null,
        public ?int $marginwidth = null,
        public ?string $scrolling = null,
        public ?string $allowfullscreen = null,
        public ?string $webkitallowfullscreen = null,
        public ?string $mozallowfullscreen = null,
        public array $class = [],
        public array $data = [],
        public ?string $ariaHidden = null,
        public ?string $dataToggle = null,
        public ?string $dataShow = null,
        public ?string $dataTarget = null,
        public ?string $dataTrigger = null,
        public ?string $dataDismiss = null,
        public ?string $dataProvides = null,
        public ?string $dataUrl = null,
        public ?string $dataFor = null,
        public ?string $dataCollapseHeight = null,
        public ?string $dataAjaxPost = null,
        public ?string $dataAjaxChange = null,
        public ?string $dataAjaxShroud = null,
        public ?attribute_callable $dataAjaxClick = null,
        public ?string $style = '',
    ) {
    }

    public function __toString()
    {
        return node::get_attributes((array) $this);
    }
}