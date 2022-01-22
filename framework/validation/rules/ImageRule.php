<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

// TODO 1. use file validator inside.
// TODO 2. check max/min Width/Height; ext; size
// TODO 3. check if possible if image were edited or not using EXIF exif_read_data
// TODO 3.1 taken date: DateTimeOriginal
/* TODO 3.2 example data:
Array
(
    [FileName] => Pirate(F).JPG
    [FileDateTime] => 1405733742
    [FileSize] => 4017033
    [FileType] => 2
    [MimeType] => image/jpeg
    [SectionsFound] => ANY_TAG, IFD0, THUMBNAIL, COMMENT, EXIF
    [COMPUTED] => Array
        (
            [html] => width="2592" height="3888"
            [Height] => 3888
            [Width] => 2592
            [IsColor] => 1
            [ByteOrderMotorola] => 1
            [ApertureFNumber] => f/16.0
            [Thumbnail.FileType] => 2
            [Thumbnail.MimeType] => image/jpeg
        )

    [Make] => Canon
    [Model] => Canon EOS DIGITAL REBEL XS
    [Orientation] => 1
    [XResolution] => 4718592/65536
    [YResolution] => 4718592/65536
    [ResolutionUnit] => 2
    [Software] => QuickTime 7.6.9
    [DateTime] => 2011:06:21 17:50:57
    [HostComputer] => Mac OS X 10.5.8
    [YCbCrPositioning] => 1
    [Exif_IFD_Pointer] => 260
    [THUMBNAIL] => Array
        (
            [Compression] => 6
            [XResolution] => 4718592/65536
            [YResolution] => 4718592/65536
            [ResolutionUnit] => 2
            [JPEGInterchangeFormat] => 628
            [JPEGInterchangeFormatLength] => 4867
            [YCbCrPositioning] => 1
        )

    [COMMENT] => Array
        (
            [0] => AppleMark

        )

    [ExposureTime] => 1/200
    [FNumber] => 16/1
    [ExposureProgram] => 2
    [ISOSpeedRatings] => 400
    [ExifVersion] => 0220
    [DateTimeOriginal] => 2011:06:04 08:56:22
    [DateTimeDigitized] => 2011:06:04 08:56:22
    [ShutterSpeedValue] => 499712/65536
    [ApertureValue] => 524288/65536
    [ExposureBiasValue] => 0/1
    [MeteringMode] => 5
    [Flash] => 9
    [FocalLength] => 18/1
    [ColorSpace] => 1
)
*/
//class ImageRule extends RuleBase
//{
//
//    public function validate(Model $model, string $field, array $params): bool
//    {
//        if (isset($params['skipOnEmpty']) && in_array($params['skipOnEmpty'], [true, 'true', 1], true)) {
//            return true;
//        }
//        $fileIsSet = isset($_FILES[$field]) && !empty($_FILES[$field]['tmp_name']);
//        if ($fileIsSet) {
//            return true;
//        }
//
//        // TODO check if correct
//        $file = $_FILES[$field];
//        $minFileSize = $params['minFileSize'];
//        $maxFileSize = $params['maxFileSize'];
//        $extensionsArr = null;
//        // TODO or use existing validator
//        if ($file['size'] > $maxFileSize) {
//            // TODO add error msg
//        }
//        if ($file['size'] < $minFileSize) {
//            // TODO add error msg
//        }
//        if (isset($params['extensions'])) {
//            $extensionsArr = is_array($params['extensions']) ? $params['extensions'] : array($params['extensions']);
//            if (!in_array($file['ext'], $extensionsArr)) {
//                // TODO add error msg
//            }
//        }
//        if ($model->hasError($field)) {
//            return false;
//        }
//
//        return true;
//    }
//
//    public function getMessage(Model $model, string $field, array $params): string
//    {
//        return sprintf("File does is not selected");
//    }
//}
