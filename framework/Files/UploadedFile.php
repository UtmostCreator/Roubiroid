<?php

namespace Framework\Files;

use Framework\helpers\Files;
use Framework\helpers\Hash;
use Helper;
use roubiframe\core\notification\Notification;

class UploadedFile
{
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png']; // all accepted formats
    public const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png']; // all accepted formats
    public const MAX_WIDTH = 1920;
    public const MAX_HEIGHT = 1080;
    public const DEFAULT_QUALITY = 75;
    public const MAX_JPEG_QUALITY = 100;
    public const MIN_JPEG_QUALITY = 0;
    public const IMAGE = 'image';

    public $targetFormats = []; // user specified formats to accept

    /* validation */
    public $fileType = 'file';

    public $MAX_BYTES_SIZE = 3145728;
    public $MAX_KB_SIZE = 3072;
    public $MAX_MB_SIZE = 3;

    public $MIN_BYTES_SIZE = 1000;
    public $MIN_KB_SIZE = 1;
    public $MIN_MB_SIZE = 0.01;

    public $errors = [];

    /* image validation */
    /* end of image validation */

    /* end of validation */

    /* @props */
    public $valid = false;

    public $baseWidth = 0;
    public $baseHeight = 0;

    public $newWidth = 0;
    public $newHeight = 0;
    /* end of validation */

    /* FILE information */

    /* Image Info */
    public $imageType = null;
    public $attr = null;
    public $resampledImage = null;
    /* Image Info */

    protected $targetDir;
    protected $originalName;
    protected $desiredName;
    protected $specificExt;
    protected $baseExt;
    protected $size; // bytes size
    protected $sizeInKB;
    protected $sizeInMB;
    protected $error;
    protected $tempName;
    /* END of FILE information */
    /**
     * @var int
     */
    protected $quality = 75;
    /**
     * @var bool
     */
    protected $uploaded = false;


    public static function getInstance(Model $model, string $fieldName)
    {
        /* @TODO resolve it somehow with it and in Validator class */
        $fileIsSet = isset($_FILES[$fieldName]) && !empty($_FILES[$fieldName]['tmp_name']);
        if (!$fileIsSet) {
            return true;
        }
        $inst = new self();
        $file = $_FILES[$fieldName];


        $inst->targetDir = 'storage/uploads/';
        $inst->originalName = substr($file['name'], 0, strrpos($file['name'], '.'));
        $inst->desiredName = Hash::create($file['name'] . Helper::generateRandomString('4'));
        $inst->specificExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $inst->baseExt = substr($file['type'], strrpos($file['type'], '/') + 1);
        $inst->setFileSizes($file['size']);
        $inst->error = $file['error'];
        $inst->tempName = $file['tmp_name'];

        if (in_array($inst->specificExt, self::ALLOWED_IMAGE_EXTENSIONS)) {
            $inst->fileType = self::IMAGE;
            list($inst->baseWidth, $inst->baseHeight, $inst->imageType, $inst->attr) = getimagesize($file['tmp_name']);
        }
        /*
        // Open a the file from local folder
                $fp = fopen(realpath('storage/uploads/7a9a46ba265a80130e553541b7cf2ddb.jpg'), 'rb');
        // Read the exif headers
                $headers = exif_read_data($fp);
        // Print the headers
                echo 'EXIF Headers:' . '<br>';
                print("<pre>".print_r($headers, true)."</pre>");

                */ //        DD::dl($inst);
//        DD::dd($_FILES);
        $model->{$fieldName} = $inst;
        return $inst;
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getTempName()
    {
        return $this->tempName;
    }

    public function getExtension()
    {
        return $this->specificExt;
    }

    public function getNameWithExt()
    {
        return $this->desiredName . '.' . $this->baseExt;
    }

    public function getSizeInMB()
    {
        return $this->sizeInMB;
    }

    public function getSize()
    {
        return $this->size;
    }


    public function saveAs($targetDir = 'storage/uploads/', $desiredName = '', $createDirIfNotExist = true)
    {
        $this->desiredName = strlen($desiredName) > 1 ? $desiredName : $this->desiredName;
        $targetDir = Files::normalizeSlashes($targetDir);
        if (strlen($targetDir) <= 2) {
            // @TODO add message wrong directory is specified!
            return false;
        }

        if ($createDirIfNotExist) {
            if (Files::canICreateADirectory($this->targetDir)) {
                $isCreated = mkdir($this->targetDir, 0755, true);

                if ($isCreated) {
                    // @TODO add message directory has been created successfully;
                }
            }
        }

        if ($this->validate()) {
            if ($this->fileType === self::IMAGE) {
                switch ($this->specificExt) {
                    case 'jpg':
                    case 'jpeg':
                        $uploadResult = imagejpeg($this->resampledImage, $this->targetDir . $this->desiredName . '.jpeg', $this->quality);
                        break;
                    case 'png':
                        $pngQuality = $this->calculatePNGQuality();
                        $uploadResult = imagepng($this->resampledImage, $this->targetDir . $this->desiredName . '.png', $pngQuality); // adjust format as needed
                        break;
                    default:
                        $uploadResult = move_uploaded_file($this->getTempName(), $this->getDest());
                        break;
                }
//            imagepng( $this->resampledImage, $target_filename ); // adjust format as needed

                if (imagedestroy($this->resampledImage) || $uploadResult) {//DD::dd($this);
                    $desc = "File {$this->getOriginalName()} was saved as " . $this->getEscapedFileNameAndExt();
                    $msg = new Message('Success', 'File Uploading', $desc, Message::ADMIN_VISIBLE);
                    Notification::addMessage($msg);
                    $this->uploaded = true;
                    return true;
                }
            }

            $uploadResult = move_uploaded_file($this->getTempName(), $this->getDest());
            if ($uploadResult) {
//                echo $this->>getEscapedNamehtmlspecialchars( basename( $this->getNameWithExt()))();;
                $desc = "File {$this->getOriginalName()} was saved as " . $this->getEscapedFileNameAndExt();
                $msg = new Message('Success', 'File Uploading', $desc, Message::ADMIN_VISIBLE);
                Notification::addMessage($msg);
                $this->uploaded = true;
                return true;
            }
        }

        return false;
    }


    /* @TODO move to Validator (to validate it in a more fashionable manner/correct way) or resolve it somehow */
    public function validate()
    {
//        DD::dd($this);
        $this->valid = false;
        if (strlen($this->getTempName()) <= 0) {
            $this->errors['filename'] = 'File does not have a name!';
        }

        if ($this->fileType === self::IMAGE) {
            if (getimagesize($this->tempName) === false) {
                $this->errors['size'] = 'File size in incorrect';
            }
        }
        if (!in_array($this->specificExt, self::ALLOWED_EXTENSIONS)) {
            $this->errors['extension'] = 'This extension is not allowed! Your extension: ' . $this->specificExt;
        }
//        DD::dd($this->getSizeInMB());
        if ($this->getSizeInMB() > $this->MAX_MB_SIZE) {
            $this->errors['size'] = 'You have exceeded the max file size! Your file size is ' . $this->sizeInMB . ' MBs!' . ' Max File Size is: ' . $this->MAX_MB_SIZE . ' MBs';
        }

        if (!file_exists($this->tempName)) {
            $this->errors['fileExistence'] = 'File you have selected have been deleted or moved.';
        }
        if (!is_dir($this->targetDir)) {
            if (Files::canICreateADirectory($this->targetDir)) {
                Files::createDir($this->targetDir);
            } else {
                $this->errors['targetDir'] = 'Impossible to create a directory!';
            }
            if (!is_dir($this->targetDir)) {
                $this->errors['targetDir'] = 'Target dir does not exist!';
            }
        }


//        DD::dd($this->errors);
        if (!empty($this->errors)) {
            return $this->valid;
        }

        $this->valid = true;
        return $this->valid;
    }

    private function getDest()
    {
        return $this->targetDir . $this->getNameWithExt();
    }


    public function changeQuality($newValue)
    {
        $this->quality = $newValue >= self::MIN_JPEG_QUALITY && $newValue <= self::MAX_JPEG_QUALITY ? $newValue : self::DEFAULT_QUALITY;
    }

    /**
     * @param int $newHeight
     * @param int $newWidth
     * @param int $quality
     * @param bool $crop
     * @return bool
     */
    public function resizeImageAndResample($newHeight, $newWidth, $quality = self::DEFAULT_QUALITY, $crop = false)
    {
        if (!$this->validate()) {
            return false;
        }

        /* it works only if it is below the MAX_WIDTH, MAX_HEIGHT */

        $this->changeQuality($quality);
        $ratio = $this->baseWidth / $this->baseHeight;
        $allowed = ($newHeight > 0 && $newHeight < self::MAX_HEIGHT) && ($newWidth > 0 && $newWidth < self::MAX_WIDTH);

        if ($crop) {
            if ($this->baseWidth > $this->baseHeight) {
//                echo '1';
                $this->baseWidth = ceil($this->baseWidth - ($this->baseWidth * abs($ratio - $newWidth / $newHeight)));
            } else {
//                echo '2';
                $this->baseHeight = ceil($this->baseHeight - ($this->baseHeight * abs($ratio - $newWidth / $newHeight)));
            }
            if ($allowed) {
//                echo '3';
                $this->newWidth = $newWidth;
                $this->newHeight = $newHeight;
            } else {
//                echo '4';
                $this->newWidth = self::MAX_WIDTH;
                $this->newHeight = self::MAX_HEIGHT;
            }
        } else {
//            echo '5';
            if ($newWidth / $newHeight > $ratio) {
//                echo '51';
                $this->newWidth = $newHeight * $ratio;
                $this->newHeight = $newHeight;
            } else {
                $this->newHeight = $newWidth / $ratio;
                $this->newWidth = $newWidth;
            }
        }

        /*            if($allowed) {
                        $this->newWidth = $newWidth;
                        $this->newHeight = $newHeight;
                    } else if( $ratio > 1) {
                        $this->newWidth = self::MAX_WIDTH;
                        $this->newHeight = self::MAX_HEIGHT/$ratio;
                    } else {
                        $this->newWidth = self::MAX_WIDTH*$ratio;
                        $this->newHeight = self::MAX_HEIGHT;
                    }*/
        $src = imagecreatefromstring(file_get_contents($this->tempName));
//        DD::dd($this);
        $this->resampledImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
        imagecopyresampled($this->resampledImage, $src, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->baseWidth, $this->baseHeight);
        imagedestroy($src);

        return true;
    }

    private function getEscapedFileNameAndExt()
    {
        return htmlspecialchars(basename($this->getNameWithExt()));
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setFileSizes($sizeInBytes)
    {
        $this->size = is_int($sizeInBytes) && $sizeInBytes > 0 ? $sizeInBytes : 0;
        $this->sizeInKB = Helper::floatNumberFormatter($this->size / 1024);
        $this->sizeInMB = Helper::floatNumberFormatter($this->sizeInKB / 1024);
    }

    public function setMaxFileSize($sizeInBytes)
    {
        $this->MAX_BYTES_SIZE = is_int($sizeInBytes) && $sizeInBytes > 0 ? $sizeInBytes : 0;
        $this->MAX_KB_SIZE = Helper::floatNumberFormatter($this->MAX_BYTES_SIZE / 1024);
        $this->MAX_MB_SIZE = Helper::floatNumberFormatter($this->MAX_KB_SIZE / 1024);
    }

    public function setMinFileSize($sizeInBytes)
    {
        $this->MIN_BYTES_SIZE = is_int($sizeInBytes) && $sizeInBytes > 0 ? $sizeInBytes : 0;
        $this->MIN_KB_SIZE = Helper::floatNumberFormatter($this->MIN_BYTES_SIZE / 1024);
        $this->MIN_MB_SIZE = Helper::floatNumberFormatter($this->MIN_KB_SIZE / 1024);
    }

    private function calculatePNGQuality()
    {
        $maxPNGQuality = 9;
        $minPNGQuality = 0;
        $pngQuality = (int)floor($this->quality / (self::MAX_JPEG_QUALITY + 10));
        $pngQuality = $pngQuality <= $minPNGQuality && $pngQuality <= $maxPNGQuality ? $pngQuality : 7;
        return $pngQuality;
    }

    public function isUploaded()
    {
        return $this->uploaded;
    }
}
