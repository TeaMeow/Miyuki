<?php

class Miyuki
{
    public $filter  = Imagick::FILTER_POINT;
    public $quality = 80;
    
    
    function __construct($imagePath = null)
    {
        if($imagePath !== null)
            $this->create($imagePath);
    }
    
    
    
    
    /**
     * Create
     * 
     * Create an image block for later to use.
     * 
     * @param string $imagePath   The path of the image which we are going to process with.
     * 
     * @return Miyuki
     */
    
    function create($imagePath)
    {
        $this->imageBlock = new imagick($imagePath);
        $this->imagePath  = $imagePath;
        $this->imageType  = $this->getType();
        $this->imageSize  = $this->getSize();
        
        return $this;
    }
    
    
    
    
    /**
     * Crop
     *
     * Crop the image to a target size.
     * 
     * @param int $width    The width of the cropped image.
     * @param int $height   The height of the cropped image.
     * @param int $x        The x start point of the uncropped image.
     * @paeam int $y        The y start point of the uncropped image.
     * 
     * @return Miyuki
     */
    
    public function crop($width, $height, $x = 0, $y = 0)
    {
        $this->imageBlock -> cropImage($width, $height, $x, $y);
        
        return $this;
    }
    
    
    
    
    /**
     * Resize
     *
     * resize the image.
     * 
     * @param int  $width
     * @param int  $height
     * @param bool $aspectRatio    Set true to keep the aspect ratio.
     * @param bool $force          Set true to force to resize the image.
     * 
     * @return Miyuki
     */
    
    public function resize($width, $Height, $AspectRatio = true, $force = false)
    {
        $imageWidth  = $this->imageSize['width'];
        $imageHeight = $this->imageSize['height'];
        
        /** Resize the image with the fixed aspect ratio if we want */
        if($aspectRatio)
        {
            $ratio  = $this->aspectRatio($imageWidth, $imageHeight, $width, $height);

            $width  = $ratio['width'];
            $height = $ratio['height'];
        }
        
        if($imageWidth > $width || $imageHeight > $height || $force)
        {
            $this->imageBlock -> resizeImage($width, $height, $this->filter, 1);

            /** Sharp the resized image cause the image will be a little bit blurred after resized */
            $this->imageBlock -> unsharpMaskImage(0, 0.5, 1, 0.05);
        }
        
        return $this;
    }    
    
    
    
    
    /**
     * Scale
     *
     * Scale the image and IGNORE any filters.
     * 
     * @param int  $width
     * @param int  $height
     * @param bool $aspectRatio    Set true to keep the aspect ratio.
     * @param bool $force          Set true to force to scale the image.
     * 
     * @return Miyuki
     */
    
    public function Scale($width, $height, $aspectRatio = true, $force = false)
    {
        $imageWidth  = $this->imageSize['width'];
        $imageHeight = $this->imageSize['height'];
        
        if($aspectRatio)
        {
            $ratio  = $this->aspectRatio($imageWidth, $imageHeight, $width, $height);
            
            $width  = $ratio['width'];
            $height = $ratio['height'];
        }
        
        
        if($imageWidth > $width || $imageHeight > $height || $force)
            $this->imageBlock -> scaleImage($width, $height);
        
        return $this;
    }
    
    
    
    
    /**
     * Thumbnail
     *
     * More faster than resize with less file size, and remove the exif automantically.
     * 
     * @param int  $width
     * @param int  $height
     * @param bool $aspectRatio    Set true to keep the aspect ratio.
     * 
     * @return 
     */
    
    public function thumbnail($width, $height, $aspectRatio = true)
    {
        $imageWidth  = $this->imageSize['width'];
        $imageHeight = $this->imageSize['height'];
        
        if($aspectRatio)
        {
            $ratio  = $this->aspectRatio($imageWidth, $imageHeight, $width, $height);
            
            $width  = $ratio['width'];
            $height = $ratio['height'];
        }
        
        if($imageWidth > $width || $imageHeight > $height)
            $this->imageBlock -> thumbnailImage($width, $height);
        
        return $this;
    }
    
    
    
    
    /**
     * Aspect Ratio
     * 
     * Calculate and return the best ratio of the image.
     * 
     * @param int $srcWidth
     * @param int $srcHeight
     * @param int $maxWidth
     * @param int $maxHeight
     * 
     * @return array
     */
    
    public function aspectRatio($srcWidth, $srcHeight, $maxWidth, $maxHeight)
    {
        /** Calculate the best width and height */
        if($srcWidth > $maxWidth && $srcWidth > $srcHeight)
        {
            $width  = (int)$maxWidth;
            $height = intval($srcHeight / $srcWidth * $maxWidth);
        }
        elseif($srcHeight > $maxHeight && $srcHeight > $srcWidth)
        {
            $width  = intval($srcWidth / $srcHeight * $maxHeight);
            $height = (int)$maxHeight;
        }
        elseif($srcWidth > $maxWidth && $srcHeight > $maxHeight)
        {
            $width  = $maxWidth;
            $height = $maxHeight;
        }
        /** We return the original size if it's small */
        else
        {
            $width  = $srcWidth;
            $height = $srcHeight;
        }
        
        return ['width'  => $width,
                'height' => $height];
    }
    
    
    
    
    /**
     * Write
     * 
     * Save the processed image and return the path.
     * 
     * @param string|bool $path     The path with the file name, when false we will save the image
     *                              into a temporary folder.
     * 
     * @return string
     */
    
    public function write($path)
    {
        if($path === false)
            $path = tempnam(sys_get_temp_dir(), '');
        
        $path .= '.' . $this->imageType['extension'];

        $this->imageBlock -> writeImage($path);
        $this->imageBlock -> clear();
        //$this->clear();
        
        return $path;
    }
    
    
    
    
    /**
     * Set Quality
     * 
     * Change the quality of the final image.
     * 
     * @param int $quality   The quality value, min: 0.1, max: 1.
     * 
     * @return Miyuki
     */
    
    function setQuality($quality)
    {
        /** Convert the float into int for imagick */
        $this->quality = $quality * 100;
        
        $this->imageBlock -> setImageCompressionQuality($this->quality);
        
        return $this;
    }
    
    
    
    
    /**
     * Set Compression
     * 
     * Set the image compression.
     * 
     * @param string $compression   The name of the compression.
     * 
     * @return Miyuki
     */
    
    function setCompression($compression)
    {
        switch($compression)
        {
            case 'UNDEFINED':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_UNDEFINED);
                break;
            
            case 'NO':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_NO);
                break;
            
            case 'BZIP':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_BZIP);
                break;
                
            case 'FAX':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_FAX);
                break;
            
            case 'GROUP4':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_GROUP4);
                break;
                
            case 'JPEG':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_JPEG);
                break;
                
            case 'JPEG2000':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_JPEG2000);
                break;
                
            case 'LOSSLESSJPEG':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_LOSSLESSJPEG);
                break;
                
            case 'LZW':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_LZW);
                break;
                
            case 'RLE':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_RLE);
                break;
                
            case 'ZIP':
                $this->imageBlock -> setImageCompression(imagick::COMPRESSION_ZIP);
                break;
        }
        
        return $this;
    }
    
    
    
    
    /**
     * Set Filter
     * 
     * Change the filter.
     * 
     * @param string $filter   The name of the filter.
     *
     * @return Miyuki
     */
    
    function setFilter($filter)
    {
        switch($filter)
        {
            case 'LANCZOS':
                $this->filter = Imagick::FILTER_LANCZOS;
                break;
                
            case 'POINT':
                $this->filter = Imagick::FILTER_POINT;
                break;
        }
        
        return $this;
    }
    
    
    
    
    /**
     * Set Type
     * 
     * Change the type of the final image.
     * 
     * @param string $type   The type name.
     * 
     * @return Miyuki
     */
    
    function setType($type)
    {
        switch($Type)
        {
            case 'png':
                $this->imageBlock -> setImageFormat('png');
                break;
            case 'png8':
                $this->imageBlock -> setImageFormat('png8');
                break;
            case 'jpeg':
                $this->imageBlock -> setImageFormat('jpeg');
                break;
        }
        
        return $this;
    }
    
    
    
    
    /**
     * Check File Size
     * 
     * Returns true when the file size is under the limit.
     * 
     * @param int $limit   KB.
     * 
     * @return bool
     */
     
    function checkFilesize($limit)
    {
        $limit = $limit * 1000;
        
        return filesize($this->imagePath) < $limit;
    }
    
    
    
    
    /**
     * Check Type
     * 
     * Returns true when the type of the image is valid.
     * 
     * @param string $allowed   A string which contains the allowed types like 'jpg, png'.
     *
     * @return bool
     */
     
    function checkType($allowed)
    {
   
        $allowed = explode(', ', $allowed);

        return in_array($this->imageType['extension'], $allowed);
    }
    
    
    
    
    /**
     * Check Size
     * 
     * Returns true if the width and the height of the image are okay :D
     * 
     * @param int $width       The max width of the image should be.
     * @param int $height      The max height of the image should be.
     * @param int $minWidth    The min width of the image.
     * @param int $maxHeight   The min height of the image.
     * 
     * @return bool
     */
    
    function checkSize($width, $height, $minWidth = 1, $minHeight = 1)
    {
        if($this->imageSize['width']  > $width  || $this->imageSize['width']  < $minWidth ||
           $this->imageSize['height'] > $height || $this->imageSize['height'] < $minHeight)
            return false;
        
        return true;
    }
    
    
    
    
    /**
     * Get Type 
     * 
     * Returns the type of the current image block.
     * 
     * @return array
     */
    
    function getType()
    {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mime     = finfo_file($finfo, $this->imagePath);
        $extesion = '';
        
        finfo_close($finfo);
        
        switch($mime)
        {
            case 'image/png' : $extesion = 'png';  break;
            case 'image/gif' : $extesion = 'gif';  break;
            case 'image/jpg' : $extesion = 'jpg';  break;
            case 'image/jpeg': $extesion = 'jpg';  break;
            case 'image/bmp' : $extesion = 'bmp';  break;
            case 'image/webp': $extesion = 'webp'; break;

            default:
                $extesion = false;
        }
        
        return ['mime'      => $mime, 
                'extension' => $extesion];
    }
    
    
    
    
    /**
     * Get Size
     * 
     * Returns the width and the height of the image.
     * 
     * @return array
     */
    
    function getSize()
    {
        list($width, $height) = getimagesize($this->imagePath);
        
        return ['width'  => $width,
                'height' => $height];
    }
 
}
?>