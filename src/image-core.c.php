<?php
if(!defined('ABSPATH')) exit();

class ImageCore
{
    
    public $ImageBlock;              // Store the image of imagick
    public $UploadSource;            // The array of FILES
    public $UploadPath;              // A variable that store the tmp_name

    const OK                       = 0;
    const IMAGE_FILESIZE_TOO_LARGE = 1;
    const IMAGE_ONLY_PART          = 2;
    const IMAGE_EMPTY              = 3;
    const IMAGE_FORMAT_INCORRECT   = 4;
    const IMAGE_SIZE_TOO_LARGE     = 5;
    const IMAGE_SIZE_TOO_SMALL     = 6;
    const IMAGE_EMPTY_SIZE         = 7;
    


    
    /**
     * Create Image Block
     *
     * Create an image block from upload file for imagick to use.
     * 
     * @param  array $Photo   The $FILES array of the photo.
     * @return ImageCore
     */

    function Create($Photo)
    {
        $this->Check($Photo);
        
        $this->ImageBlock = new imagick($this->Path);
        $this->Filter     = Imagick::FILTER_POINT;           //Default filter
        $this->Quality    = _w('image_quality'); //Default quality
        
        return $this;
    }
    
    
    
    
    /**
     * Clear
     *
     * Clean the previous data.
     * 
     * @return ImageCore
     */
    
    private function Clear()
    {
        $this->ImageBlock = $this->UploadSource = $this->UploadPath = $this->Source = $this->Path = $this->Quality = $this->Filter = NULL;
        
        return $this;
    }
    
    
    
    
    /**
     * Check Image
     *
     * Check the information of the image, is it safe?
     * 
     * @param  array $Photo   The $FILES array of the photo.
     * @return ImageCore
     */
    
    function Check($Photo)
    {
        /** Set source and the path of the temp file */
        $this->Source = $Photo;
        $this->Path   = $Photo['tmp_name'];
        
        $this->BasicCheck()
             ->CheckFileSize()      //Check the size of the file.
             ->CheckImageType()     //Check the type of the image.
             ->CheckImageSize();    //Check the image size.
        
        return $this;
    }
    
    
    
    
    /**
     * Basic Check
     * 
     * @return ImageCore
     */
    
    private function BasicCheck()
    {
        /** If the temp name is empty */
        switch($this->Source['error'])
        {
            case 1:
            case 2:
                return Aira::Add('IMAGE_FILESIZE_TOO_LARGE');
                break;
            
            case 3:
                return Aira::Add('IMAGE_ONLY_PART');
                break;
            
            case 4:
                return Aira::Add('IMAGE_EMPTY');
                break;
        }

        /** If the temp name is empty */
        if($this->Source['tmp_name'] == '')
            return Aira::Add('IMAGE_EMPTY');
        
        return $this;
    }
    
    
    
    
    /**
     * Check File Size
     *
     * Is the size of the file under the website limit?
     * 
     * @return ImageProcess
     */
    
    private function CheckFileSize()
    {
        if($this->Source['size'] > MAX_FILESIZE)
            return Aira::Add('IMAGE_FILESIZE_TOO_LARGE');
        
        return $this;
    }
    
    

    
    /**
     * Check Image Type
     *
     * Is the type of the image in the allowed list?
     */

    private function CheckImageType()
    {
        $AllowedType = explode(', ', _w('allowed_image_types'));

        if(!in_array($this->GetType(), $AllowedType))
            return Aira::Add('IMAGE_FORMAT_INCORRECT');
        
        return $this;
    }
    
    
    
    
    /**
     * Check Image Size
     *
     * Is the width or the height of the image larger than the website limit?
     */
    
    private function CheckImageSize()
    {
        $ImageInfo = $this->GetSize();
        
        $MaxW = _w('max_image_width');
        $MaxH = _w('max_image_height');

        if($ImageInfo['Width'] > $MaxW || $ImageInfo['Height'] > $MaxH)
            return Aira::Add('IMAGE_SIZE_TOO_LARGE');
        elseif($ImageInfo['Width'] < 1 || $ImageInfo['Height'] < 1)
            return Aira::Add('IMAGE_SIZE_TOO_SMALL');

        /** Store width and height too */
        $this->SrcWidth  = $ImageInfo['Width'];
        $this->SrcHeight = $ImageInfo['Height'];
        
        return $this;
    }
    
    
    
    
    /**
     * Crop Image
     *
     * Crop the image to a target size.
     */
    
    public function Crop($Width, $Height, $X='0', $Y='0')
    {
        $this->ImageBlock -> cropImage($Width, $Height, $X, $Y);
        
        return $this;
    }
    
    
    
    
    /**
     * Resize
     *
     * Resize with aspect ratio or not.
     */
    
    public function Resize($Width='', $Height='', $AspectRatio=true, $ResizeForce=false)
    {
        /** If the aspect ration is true, which means we should resize by aspect ratio. */
        if($AspectRatio)
        {
            $Ratio  = $this->AspectRatio($this->SrcWidth, $this->SrcHeight, $Width, $Height);
            
            $Width  = $Ratio['Width'];
            $Height = $Ratio['Height'];
        }
        
        if($this->SrcWidth > $Width || $this->SrcHeight > $Height || $ResizeForce)
        {
            $this->ImageBlock -> resizeImage($Width, $Height, $this->Filter, 1);

            /** More sharper after resized because resize cause this image a little blurred */
            $this->ImageBlock -> unsharpMaskImage(0 , 0.5 , 1 , 0.05);
        }
        
        return $this;
    }    
    

    
    
    /**
     * Scale
     *
     * Scale the image, more clear than resize.
     */
    
    public function Scale($Width='', $Height='', $AspectRatio=true, $ResizeForce=false)
    {
        if($AspectRatio)
        {
            $Ratio  = $this->AspectRatio($this->SrcWidth, $this->SrcHeight, $Width, $Height);
            
            $Width  = $Ratio['Width'];
            $Height = $Ratio['Height'];
        }
        
        
        if($this->SrcWidth > $Width || $this->SrcHeight > $Height || $ResizeForce)
            $this->ImageBlock -> scaleImage($Width, $Height);
        
        return $this;
    }

    
    
    
    /**
     * Thumbnail
     *
     * Like resize but more faster, and remove exif automantically.
     */
    
    public function Thumbnail($Width='', $Height='', $AspectRatio=true)
    {
        if($AspectRatio)
        {
            $Ratio  = $this->AspectRatio($this->SrcWidth, $this->SrcHeight, $Width, $Height);
            
            $Width  = $Ratio['Width'];
            $Height = $Ratio['Height'];
        }
        
        if($this->SrcWidth > $Width || $this->SrcHeight > $Height)
            $this->ImageBlock -> thumbnailImage($Width, $Height);
        
        return $this;
    }
    
    
    
    
    /**
     * Aspect Ratio
     *
     * Calculate the best ratio of this picture.
     */
    
    public function AspectRatio($SrcWidth='', $SrcHeight='', $MaxWidth='', $MaxHeight='')
    {
        /** Calculate the best width and height */
        if($SrcWidth > $MaxWidth && $SrcWidth > $SrcHeight)
        {
            $Width  = (int)$MaxWidth;
            $Height = intval($SrcHeight / $SrcWidth * $MaxWidth);
        }
        elseif($SrcHeight > $MaxHeight && $SrcHeight > $SrcWidth)
        {
            $Width  = intval($SrcWidth / $SrcHeight * $MaxHeight);
            $Height = (int)$MaxHeight;
        }
        elseif($SrcWidth > $MaxWidth && $SrcHeight > $MaxHeight)
        {
            $Width  = $MaxWidth;
            $Height = $MaxHeight;
        }
        /** If it's small, then we return original size */
        else
        {
            $Width  = $SrcWidth;
            $Height = $SrcHeight;
        }
        
        return ['Width'  => $Width,
                'Height' => $Height];
    }
    
    
    
    
    /**
     * Save
     * 
     * Compress the image which is in the ImageBlock, then store it to the disk,
     * We can't use 'destory' to replace 'clear', otherwise the session will be invalid.
     */
    
    public function Write($PathWithName='', $Temp=false)
    {
        /** If the file size of the image is over than the non-compress size, which means we must compress it */
        if($this->Source['size'] > _w('image_non_compress_size') && $this->Quality)
        {
            $ImageSizeKB = round($this->Source['size'] / 1024);
    
            if($ImageSizeKB > 2048) $this->Quality = round($this->Quality / 1.1);
            if($ImageSizeKB > 3072) $this->Quality = round($this->Quality / 1.2);
            if($ImageSizeKB > 4096) $this->Quality = round($this->Quality / 1.3);
            
            $this->SetQuality($this->Quality);
        }
        
        
        /** Generate image to temp file or to a target path */
        if($Temp)
        {
            $TempPath = tempnam(sys_get_temp_dir(), '');
            $this->ImageBlock -> writeImage($TempPath);
        }
        else
        {
            $Fullname = $PathWithName . $this->GetType(true);
            $this->ImageBlock -> writeImage($Fullname);
        }
        
        $this->ImageBlock -> clear();
        $this->Clear();
        
        return (isset($TempPath)) ? $TempPath : $Fullname;
    }
    
    
    
    
    /**
     * Set Quality
     *
     * Trun float (0.5) to percent (50).
     */
    
    public function SetQuality($Quality)
    {
        /** No compress */
        if(!$Quality)
        {
            $this->Quality = false;
        }
        else
        {
            $this->Quality = $Quality;
            $this->ImageBlock -> setImageCompressionQuality($this->Quality);
        }
        
        return $this;
    }
    
    
    
    
    /**
     * Set Filter
     *
     * Set the filter for resize blah blah usage.
     **/
    
    public function Filter($Filter)
    {
        switch($Filter)
        {
            case 'LANCZOS':
                $this->Filter = Imagick::FILTER_LANCZOS;
                break;
            
            case 'POINT':
                $this->Filter = Imagick::FILTER_POINT;
                break;
        }
        
        return $this;
    }
    
    
    
    
    /**
     * Change Image Type
     *
     * Change the image type while saving the image.
     */
    
    public function ChangeType($Type='')
    {
        switch($Type)
        {
            case 'PNG':
                $this->ImageBlock -> setImageFormat('png');
                break;
            case 'PNG8':
                $this->ImageBlock -> setImageFormat('png8');
                break;
            case 'JPEG':
                $this->ImageBlock -> setImageFormat('jpeg');
                break;
        }
        
        return $this;
    }
    
    
    
    
    /**
     * Get Type
     *
     * Return a MIME type of the image.
     */
    
    public function GetType($Extension=false)
    {
        $FInfo    = finfo_open(FILEINFO_MIME_TYPE);
        $MIMEType = finfo_file($FInfo, $this->Path);
        finfo_close($FInfo);
        
        /** Return extension only if needed */
        if($Extension)
        {
            switch($MIMEType)
            {
                case 'image/png':
                    return '.png';
                    break;

                case 'image/gif':
                    return '.gif';
                    break;

                case 'image/jpeg':
                    return '.jpg';
                    break;

                default:
                    return false;
            }
        }

        /** Otherwise, we return the MIME type */
        return $MIMEType;
    }
    
    
    
    
    /**
     * Get Size
     *
     * Get the size of the image.
     */
    
    function GetSize($Type=NULL, $Source=false)
    {   
        $Source = ($Source) ?: $this->Path;

        list($Width, $Height) = getimagesize($Source);
        
        if(!is_numeric($Width) || !is_numeric($Height))
        {
            @$ErrorData = 'W:"' . $Width . '" H:"' . $Height . '"';
            return Aira::Add('IMAGE_EMPTY_SIZE');
        }

        switch($Type)
        {
            case 'Width':  return $Width;  break;
            case 'Height': return $Height; break;
        }
        
        return ['Width'  => $Width,
                'Height' => $Height];
    }    
}
?>