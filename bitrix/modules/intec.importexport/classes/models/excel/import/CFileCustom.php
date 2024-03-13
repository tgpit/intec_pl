<?php
namespace intec\importexport\models\excel\import;

use Bitrix\Main\IO;
use Bitrix\Main\File;
use Bitrix\Main\File\Internal;
use Bitrix\Main\Security;

class CFileCustom extends \CFile
{
    public static function SaveFile($arFile, $strSavePath, $forceRandom = false, $skipExtension = false, $dirAdd = '', $checkDuplicates = true)
    {
        $strFileName = GetFileName($arFile["name"]);

        if(isset($arFile["del"]) && $arFile["del"] <> '')
        {
            static::Delete($arFile["old_file"]);
            if($strFileName == '')
                return "NULL";
        }

        if($arFile["name"] == '')
        {
            if(isset($arFile["description"]) && intval($arFile["old_file"])>0)
            {
                static::UpdateDesc($arFile["old_file"], $arFile["description"]);
            }
            return false;
        }

        if (isset($arFile["content"]))
        {
            if (!isset($arFile["size"]))
            {
                $arFile["size"] = strlen($arFile["content"]);
            }
        }
        else
        {
            try
            {
                $file = new IO\File(IO\Path::convertPhysicalToLogical($arFile["tmp_name"]));
                $arFile["size"] = $file->getSize();
            }
            catch(IO\IoException $e)
            {
                $arFile["size"] = 0;
            }
        }

        $arFile["ORIGINAL_NAME"] = $strFileName;

        //translit, replace unsafe chars, etc.
        $strFileName = self::transformName($strFileName, $forceRandom, $skipExtension); // for original file name

        //transformed name must be valid, check disk quota, etc.
        if (self::validateFile($strFileName, $arFile) !== "")
        {
            return false;
        }

        if($arFile["type"] == "image/pjpeg" || $arFile["type"] == "image/jpg")
        {
            $arFile["type"] = "image/jpeg";
        }

        $original = null;

        $io = \CBXVirtualIo::GetInstance();

        $bExternalStorage = false;
        foreach(GetModuleEvents("main", "OnFileSave", true) as $arEvent)
        {
            if(ExecuteModuleEventEx($arEvent, array(&$arFile, $strFileName, $strSavePath, $forceRandom, $skipExtension, $dirAdd, $checkDuplicates)))
            {
                $bExternalStorage = true;
                break;
            }
        }

        if(!$bExternalStorage)
        {
            // we should keep number of files in a folder below 10,000
            // three chars from md5 give us 4096 subdirs

            $upload_dir = \COption::GetOptionString("main", "upload_dir", "upload");

            if($forceRandom != true && \COption::GetOptionString("main", "save_original_file_name", "N") == "Y")
            {
                //original name
                $subdir = $dirAdd;
                if($subdir == '')
                {
                    while(true)
                    {
                        $random = Security\Random::getString(32);
                        $subdir = substr(md5($random), 0, 3)."/".$random;

                        if(!$io->FileExists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$subdir."/".$strFileName))
                        {
                            break;
                        }
                    }
                }
                $strSavePath = rtrim($strSavePath, "/")."/".$subdir;
            }
            else
            {
                //random name
                $fileExtension = ($skipExtension == true || ($ext = GetFileExtension($strFileName)) == ''? '' : ".".$ext);
                while(true)
                {
                    $subdir = substr(md5($strFileName), 0, 3);
                    $strSavePath = rtrim($strSavePath, "/")."/".$subdir;

                    if(!$io->FileExists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$strFileName))
                    {
                        break;
                    }

                    //try the new name
                    $strFileName = Security\Random::getString(32).$fileExtension;
                }
            }

            $arFile["SUBDIR"] = $strSavePath;
            $arFile["FILE_NAME"] = $strFileName;

            $dirName = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/";
            $physicalFileName = $io->GetPhysicalName($dirName.$strFileName);

            CheckDirPath($dirName);

            if(is_set($arFile, "content"))
            {
                if(file_put_contents($physicalFileName, $arFile["content"]) === false)
                {
                    return false;
                }
            }
            else
            {
                if(!copy($arFile["tmp_name"], $physicalFileName) && !move_uploaded_file($arFile["tmp_name"], $physicalFileName))
                {
                    return false;
                }
            }

            if(isset($arFile["old_file"]))
            {
                static::Delete($arFile["old_file"]);
            }

            @chmod($physicalFileName, BX_FILE_PERMISSIONS);

            //flash is not an image
            $flashEnabled = !static::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);

            $image = new File\Image($physicalFileName);

            $imgInfo = $image->getInfo($flashEnabled);

            if($imgInfo)
            {
                $arFile["WIDTH"] = $imgInfo->getWidth();
                $arFile["HEIGHT"] = $imgInfo->getHeight();

                if($imgInfo->getFormat() == File\Image::FORMAT_JPEG && empty($arFile['no_rotate']))
                {
                    $exifData = $image->getExifData();
                    if (isset($exifData['Orientation']) && $exifData['Orientation'] > 1)
                    {
                        if($image->load())
                        {
                            if($image->autoRotate($exifData['Orientation']))
                            {
                                $quality = \COption::GetOptionString('main', 'image_resize_quality');
                                if($image->save($quality))
                                {
                                    //swap width and height
                                    if ($exifData['Orientation'] >= 5 && $exifData['Orientation'] <= 8)
                                    {
                                        $arFile["WIDTH"] = $imgInfo->getHeight();
                                        $arFile["HEIGHT"] = $imgInfo->getWidth();
                                    }
                                    $arFile['size'] = filesize($physicalFileName);
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $arFile["WIDTH"] = 0;
                $arFile["HEIGHT"] = 0;
            }

            //calculate a hash for the control of duplicates
            $arFile["FILE_HASH"] = static::CalculateHash($physicalFileName, $arFile["size"]);

            //control of duplicates
            if($checkDuplicates && $arFile["FILE_HASH"] <> '')
            {
                $original = static::FindDuplicate($arFile["size"], $arFile["FILE_HASH"]);
                if($original !== null)
                {
                    //points to the original's physical path
                    $arFile["SUBDIR"] = $original->getFile()->getSubdir();
                    //$arFile["FILE_NAME"] = $original->getFile()->getFileName(); //nenf for original file name

                    $originalPath = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"];

                    if($physicalFileName <> $io->GetPhysicalName($originalPath))
                    {
                        unlink($physicalFileName);
                        @rmdir($io->GetPhysicalName($dirName));
                    }
                }
            }
        }
        else
        {
            //from clouds
            if(isset($arFile["original_file"]) && $arFile["original_file"] instanceof Internal\EO_FileHash)
            {
                $original = $arFile["original_file"];
            }
        }

        if($arFile["WIDTH"] == 0 || $arFile["HEIGHT"] == 0)
        {
            //mock image because we got false from CFile::GetImageSize()
            if(strpos($arFile["type"], "image/") === 0 && $arFile["type"] <> 'image/svg+xml')
            {
                $arFile["type"] = "application/octet-stream";
            }
        }

        if($arFile["type"] == '' || !is_string($arFile["type"]))
        {
            $arFile["type"] = "application/octet-stream";
        }

        /****************************** QUOTA ******************************/
        if (\COption::GetOptionInt("main", "disk_space") > 0 && $original === null)
        {
            \CDiskQuota::updateDiskQuota("file", $arFile["size"], "insert");
        }
        /****************************** QUOTA ******************************/

        $NEW_IMAGE_ID = static::DoInsert(array(
            "HEIGHT" => $arFile["HEIGHT"],
            "WIDTH" => $arFile["WIDTH"],
            "FILE_SIZE" => $arFile["size"],
            "CONTENT_TYPE" => $arFile["type"],
            "SUBDIR" => $arFile["SUBDIR"],
            "FILE_NAME" => $arFile['FILE_NAME'],
            "MODULE_ID" => $arFile["MODULE_ID"],
            "ORIGINAL_NAME" => $arFile["ORIGINAL_NAME"],
            "DESCRIPTION" => (isset($arFile["description"])? $arFile["description"] : ''),
            "HANDLER_ID" => (isset($arFile["HANDLER_ID"])? $arFile["HANDLER_ID"] : ''),
            "EXTERNAL_ID" => (isset($arFile["external_id"])? $arFile["external_id"]: md5(mt_rand())),
            "FILE_HASH" => ($original === null? $arFile["FILE_HASH"] : ''),
        ));

        if($original !== null)
        {
            //save information about the duplicate for future use (on deletion)
            static::AddDuplicate($original->getFileId(), $NEW_IMAGE_ID);
        }

        static::CleanCache($NEW_IMAGE_ID);

        return $NEW_IMAGE_ID;
    }
}