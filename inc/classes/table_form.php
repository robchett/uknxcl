<?php

namespace classes;

use classes\interfaces\model_interface;
use form\field_string;
use form\form;
use form\schema;

class table_form extends form {

    /**
     * @param class-string<model_interface> $__class
     */
    public function __construct(public string $__class) {
        parent::__construct([
            ...array_values(schema::getFromClass($__class)->fields),
            new field_string('__class', hidden: true),
        ]);
        $this->action = get_called_class() . ':submit';
    }

    public static function submit(): bool {
        /** @psalm-suppress ArgumentTypeCoercion */
        $form = new self((string) $_REQUEST['__class']);
        return $form->do_form_submit();
    }

    public function do_submit(): bool
    {
        $data = [];
        foreach ($this->fields as $field) {
            if ($field->field_name == '__class') {
                continue;
            }
            /** @psalm-suppress MixedAssignment */
            $data[$field->field_name] = $field->get_value($this);
        }
        return $this->__class::do_save($data) > 0;
    }
}
 