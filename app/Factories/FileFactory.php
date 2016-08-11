<?php


namespace App\Factories;


use App\File;
use App\FileRequest;
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
     * FileRequest Model
     *
     * @var File
     */
    protected $fileRequest;

    /**
     * Generated new File name
     *
     * @var
     */
    protected $name;

    /**
     * FileFactory constructor.
     *
     * @param FileRequest $fileRequest
     * @param UploadedFile $uploadedFile
     */
    public function __construct(FileRequest $fileRequest, UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        $this->fileRequest = $fileRequest;
    }

    /**
     * Make a file name, based on the uploaded file.
     *
     * @return string
     */
    protected function makeFileName()
    {

        // If we need DO need to hash name (ie. for security or to avoid over-writes)
        $name = sha1(
            time() . $this->uploadedFile->getClientOriginalName()
        );

        // Convert uploaded file to lower case and join with '_'
//        $name = str_replace(" ", "_", strtolower($this->fileRequest->name));

        /**
         * TODO ::: (?) Encrypt file names so if physical files are compromised it'll be harder to find a specific file.
         */

        $extension = $this->uploadedFile->getClientOriginalExtension();

        return "{$name}.{$extension}";
    }

    /**
     * Static wrapper - store a File after upload...
     *
     * @param FileRequest $fileRequest
     * @param UploadedFile $uploadedFile
     * @return FileRequest
     */
    public static function store(FileRequest $fileRequest, UploadedFile $uploadedFile)
    {
        $factory = new static($fileRequest, $uploadedFile);
        $factory->setDirectory()
                ->setName()
                ->moveFile()
                ->createFileModel()
                ->updateDB();
        return $factory->fileRequest->load('uploads');
    }

    /**
     * Determine the directory to store a File
     *
     * @return string
     */
    protected function setDirectory()
    {
        $this->directory = $this->baseDir . '/user/' . $this->fileRequest->checklist->user_id . '/checklists/' . hashId($this->fileRequest->checklist);
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
     * The File model that references the physical file.
     *
     * @return $this
     */
    protected function createFileModel()
    {
        File::create([
            'path' => $this->directory . '/' . $this->name,
            'file_request_id' => $this->fileRequest->id
        ]);

        return $this;
    }

    /**
     * Update our database records...
     *
     * @return $this
     */
    protected function updateDB()
    {
        $this->fileRequest->update([
            'status' => 'received'
        ]);

        return $this;
    }
}