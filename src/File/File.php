<?php
namespace Pipe\File;

class File
{
    /**
     * @var string
     */
    protected $basePath = '';

    /**
     * @var string
     */
    protected $fileName = '';

    public function send($filename = 'file')
    {
        header('Content-Type: application/pdf');
        //header('Content-Disposition: attachment; filename=' . $filename . $this->getExtension());
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        copy($this->getFile(), 'php://output');

        die();
    }

    public function has()
    {
        return $this->getFile() && file_exists($this->getFile());
    }

    public function remove()
    {
        $file = $this->getFile();

        if($file && file_exists($file)) {
            @unlink($file);
        }

        return $this;
    }

    public function save($file)
    {
        @copy($file, $this->getFile());
    }

    /*public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }*/

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->fileName ? $this->basePath . $this->fileName : false;
    }

    public function setBasePath($path)
    {
        $this->basePath = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    public function getUniqueFileName($baseFile)
    {
        $extension = $this->getExtension($baseFile);

        while(true) {
            $fileName  = substr(md5(rand(0, 2000000000)), 0, 15) . $extension;

            if(!file_exists($this->basePath . $fileName)) {
                return $fileName;
            }
        }
    }

    public function getExtension($filename)
    {
        if(!$filename) $filename = $this->getFileName();
        return strrchr($filename, '.');
    }

    static public function getCleanFileName($filename)
    {
        return rtrim(basename($filename), strrchr($filename, '.'));
    }
}
