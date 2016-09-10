<?php


namespace App\Factories;


use App\FileRequest;
use App\Upload;
use Illuminate\Http\UploadedFile;

class UploadFactory
{

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
     * @var FileRequest
     */
    protected $fileRequest;

    /**
     * Generated new File name
     *
     * @var
     */
    protected $name;

    /**
     * Path of file that's returned after we store it.
     *
     * @var
     */
    protected $path;

    /**
     * UploadFactory constructor.
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

        // Convert uploaded file to lower case and join with '_'
        $this->name = str_replace(" ", "_", strtolower($this->fileRequest->file->name)). '_v' . $this->fileRequest->version;

        $extension = $this->uploadedFile->getClientOriginalExtension();

        return "{$this->name}.{$extension}";
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
                ->moveFile()
                ->createModel()
                ->updateDB();

        // fetch a fresh copy - without eager-loaded baggage.
        return $factory->fileRequest;
    }

    /**
     * Determine the directory to store a File
     *
     * @return string
     */
    protected function setDirectory()
    {
        $this->directory = env('APP_ENV', 'local') . '/user/' . $this->fileRequest->checklist->user_id . '/checklists/' . hashId('checklist', $this->fileRequest->checklist);
        return $this;
    }


    /**
     * Move the file into the directory with a new name
     *
     * @return $this
     */
    protected function moveFile()
    {
        $this->path = $this->uploadedFile->storeAs($this->directory, $this->makeFileName(), 's3');
        return $this;
    }

    /**
     * The Upload model that references the physical file.
     *
     * @return $this
     */
    protected function createModel()
    {
        Upload::create([
            'file_name' => $this->name,
            'path' => $this->path,
            'file_request_id' => $this->fileRequest->id,
            'size' => $this->uploadedFile->getClientSize()
        ]);

        return $this;
    }

    /**
     * Update the FileRequest to received.
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