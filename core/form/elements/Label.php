<?php


namespace app\core\form\elements;


use app\common\helpers\StringHelper;

//class Label
//{
//    /**
//     * @param string $for
//     * @param string $text
//     * @param string $class
//     * @param bool $required
//     * @return string
//     */
//    public static function build(string $for, string $text, string $class, bool $required = false)
//    {
//        $required = $required ? ' ' . self::markIfRequired($required) : '';
//        return sprintf('<label for="%s" class="%s" >%s%s</label>', $for, $class, $text, $required);
//    }
//
//    public function label($title, $for = true)
//    {
//        $labelText = strlen($title) > 0 ? StringHelper::uppercaseWordsAndReplaceSpecifier('_', $title) : $this->name;
//
//        $extraTypeClass = $this->type === 'radio' ? 'form-check-label' : '';
//        $this->label = "<label class='{$extraTypeClass}' ";
//        $this->label .= $for ? "for='{$this->name}'> {$labelText}" : "> {$labelText}";
//        $this->label .= $this->markIfRequired();
//        $this->label .= "</label>";
//
//        return $this;
//    }
//
//    /**
//     * @param bool $required
//     * @return string
//     */
//    public static function markIfRequired(bool $required): string
//    {
//        return $required ? '<span class="text-danger">*</span>' : '';
//    }
//
//
//}
