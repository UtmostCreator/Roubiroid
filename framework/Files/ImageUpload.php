<?php

namespace Framework\core;

use Framework\common\helpers\Files;
use Framework\core\notification\Message;
use Framework\core\notification\Notification;

class ImageUpload extends UploadedFile
{
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png']; // all accepted formats
    /* validation */
    public const MAX_BYTES_SIZE = 3145728;
    public const MAX_KB_SIZE = 3072;
    public const MAX_MB_SIZE = 3;
    public const MAX_WIDTH = 1920;
    public const MAX_HEIGHT = 1080;
    /* end of validation */

    public $targetFormats = []; // user specified formats to accept
    /* @props */
    public $width = 0;
    public $height = 0;



    public static function getInstance(Model $model, string $fieldName)
    {
        $inst = new self();
        $inst = parent::getInstance($model, $fieldName);
//        $baseInstanceArrayOfProps = get_object_vars($baseInst);
//        $inst = Helper::setObjectVars($inst, $baseInstanceArrayOfProps);

        list($width, $height) = getimagesize('path_to_image');
        $inst->width = $width;
        $inst->height = $height;
//        DD::dd($inst);

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
            } else {
                /* @TODO can not create a dir */
                return false;
            }
        }

        if ($this->validate()) {
            $uploadResult = move_uploaded_file($this->getTempName(), $this->getDest());
            if ($uploadResult) {
//                echo htmlspecialchars( basename( $this->getNameWithExt()));
                $desc = "File {$this->getOriginalName()} was saved as " . htmlspecialchars(basename($this->getNameWithExt()));
                $msg = new Message('Success', 'File Uploading', $desc, Message::ADMIN_VISIBLE);
                Notification::addMessage($msg);
                return true;
            }
        }

        return false;
    }


    /* @TODO move to Validator (to validate it in a more fashionable manner/correct way) or resolve it somehow */
    public function validate()
    {
        if (!$this->getTempName()) {
            return false;
        }

//        if(getimagesize($this->tempName) === false) {
//            $this->errors['size'] = 'File size in incorrect';
//            return false;
//        }
        if (!in_array($this->specificExt, self::ALLOWED_EXTENSIONS)) {
            $this->errors['format'] = 'This format is not allow! Your format: ' . $this->specificExt;
            return false;
        }
        if ($this->getSizeInMB() > self::MAX_MB_SIZE || $this->sizeInKB > self::MAX_KB_SIZE || $this->size > self::MAX_BYTES_SIZE) {
            $this->errors['size'] = 'You have exceeded the max file size! Your file size is ' . $this->sizeInMB . ' MBs!';
            return false;
        }

        if (!file_exists($this->tempName)) {
            $this->errors['fileExistence'] = 'File you have selected have been deleted or moved.';
            return false;
        }
        if (!is_dir($this->targetDir)) {
            $this->errors['targetDir'] = 'Target dir does not exist!';
            return false;
        }

        return true;
    }

    private function getDest()
    {
        return $this->targetDir . $this->getNameWithExt();
    }


    public function changeQuality()
    {
    }

    /**
     * @param string $tempFileNameAndPath $_FILES['myFile']['tmp_name']
     * @param int $newHeight
     * @param int $newWidth
     * @param int $quality
     */
    public function resizeImageAndResample($tempFileNameAndPath, $newHeight, $newWidth, $quality = 90)
    {
        $maxDim = 800;
        $file_name = $tempFileNameAndPath;
        list($width, $height, $type, $attr) = getimagesize($file_name);
        if ($width > $maxDim || $height > $maxDim) {
            $target_filename = $file_name;
            $ratio = $width / $height;
            if ($ratio > 1) {
                $new_width = $maxDim;
                $new_height = $maxDim / $ratio;
            } else {
                $new_width = $maxDim * $ratio;
                $new_height = $maxDim;
            }
            $src = imagecreatefromstring(file_get_contents($file_name));
            $dst = imagecreatetruecolor($new_width, $new_height);
            $uploadPath = 'storage/uploads/';
            $uploadName = 'filename_1';
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($src);
            $case = 'jpg';
            switch ($case) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($dst, $uploadPath . $uploadName . '.jpg', $quality);
                    break;
                imagepng($dst, $target_filename); // adjust format as needed
                break;
            }
            imagepng($dst, $target_filename); // adjust format as needed
            imagedestroy($dst);
        }
    }
}
