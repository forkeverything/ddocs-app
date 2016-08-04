<?php


namespace App\Factories;


use App\File;
use Illuminate\Http\UploadedFile;

class FileFactory
{

    /**
     * Our root directory for all uploads
     *
     * @var string
     */
    protected $baseDir = 'uploads';

    /**
     * Where to store the File
     *
     * @var
     */
    protected $directory;

    /**
     * UploadedFile Class
     *
     * @var UploadedFile
     */
    protected $uploadedFile;

    /**
     * File Model
     *
     * @var File
     */
    protected $file;

    /**
     * Generated new File name
     *
     * @var
     */
    protected $name;

    /**
     * FileFactory constructor.
     *
     * @param File $file
     * @param UploadedFile $uploadedFile
     */
    public function __construct(File $file, UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        $this->file = $file;
    }

    /**
     * Make a file name, based on the uploaded file.
     *
     * @return string
     */
    protected function makeFileName()
    {
        
//        // If we need DO need to hash name (ie. for security or to avoid over-writes)        
//        $name = sha1(
//            time() . $this->uploadedFile->getClientOriginalName()
//        );

        // Convert uploaded file to lower case and join with '_'
        $name = str_replace(" ", "_", strtolower($this->file->name));

        /**
         * TODO ::: (?) Encrypt file names so if physical files are compromised it'll be harder to find a specific file.
         */

        $extension = $this->uploadedFile->getClientOriginalExtension();

        return "{$name}.{$extension}";
    }

    /**
     * Static wrapper - store a File after upload...
     *
     * @param File $file
     * @param UploadedFile $uploadedFile
     * @return mixed
     */
    public static function store(File $file, UploadedFile $uploadedFile)
    {
        $factory = new static($file, $uploadedFile);
        $factory->setDirectory()
                ->setName()
                ->moveFile()
                ->updateDB();
        return $factory->file;
    }

    /**
     * Determine the directory to store a File
     *
     * @return string
     */
    protected function setDirectory()
    {
        $this->directory = $this->baseDir . '/user/' . $this->file->checklist->user->id . '/checklists/' . hashId($this->file->checklist);
        return $this;
    }

    /**
     * Set name property
     *
     * @return $this
     */
    protected function setName()
    {
        $this->name = $this->makeFileName();
        return $this;
    }

    /**
     * Move the file into the directory with a new name
     *
     * @return $this
     */
    protected function moveFile()
    {
        $this->uploadedFile->move($this->directory, $this->name);
        return $this;
    }

    /**
     * Update our database records...
     * 
     * @return $this
     */
    protected function updateDB()
    {
        $this->file->update([
            'path' => $this->directory . '/' . $this->name,
            'status' => 'received'
        ]);

        return $this;
    }
}