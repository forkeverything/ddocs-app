<?php


namespace App\Factories;


use App\FileRequest;
use App\ProjectFile;
use App\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use League\Flysystem\Exception;

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
     * The Model we're attaching this Upload to.
     *
     * @var Model
     */
    protected $target;

    /**
     * UploadFactory constructor.
     *
     * @param Model $target
     * @param UploadedFile $uploadedFile
     */
    public function __construct(Model $target, UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        $this->target = $target;
    }

    /**
     * Util method to check if the target has a certain
     * type shortname.
     *
     * @param $type
     * @return bool
     */
    protected function targetType($type)
    {
        $className = '';
        if($type === 'project') $className = 'App\\ProjectFile';
        if($type === 'request') $className = 'App\\FileRequest';
        return get_class($this->target) === $className;
    }

    /**
     * Make a file name, based on the uploaded file.
     *
     * @return string
     */
    protected function makeFileName()
    {
        // Convert uploaded file to lower case and join with '_'
        if($this->targetType('request')) {
            $this->name = str_replace(" ", "_", strtolower($this->target->file->name)). '_v' . $this->target->version;
        } elseif ($this->targetType('project')) {
            $this->name = str_replace(" ", "_", strtolower($this->target->name)) . '_' . str_random(6);
        }

        $extension = $this->uploadedFile->getClientOriginalExtension();

        return "{$this->name}.{$extension}";
    }

    /**
     * Static wrapper - store a File after upload...
     *
     * @param Model $target
     * @param UploadedFile $uploadedFile
     * @return Upload
     * @throws Exception
     * @internal param Model $model
     */
    public static function store(Model $target, UploadedFile $uploadedFile)
    {
        if(! $target instanceof FileRequest && ! $target instanceof ProjectFile) throw new Exception("Expected either a FileRequest or ProjectFile to attach Upload to.");
        $factory = new static($target, $uploadedFile);
        return $factory->setDirectory()
                       ->moveFile()
                       ->createModel();
    }

    /**
     * Determine the directory to store a File
     *
     * @return string
     */
    protected function setDirectory()
    {
        if($this->targetType('request')) {
            $this->directory = env('APP_ENV', 'local') . '/user/' . $this->target->checklist->user_id . '/checklists/' . hashId('checklist', $this->target->checklist);
        } elseif ($this->targetType('project')) {
            $this->directory = env('APP_ENV', 'local') . '/user/' . $this->target->folder->project->user_id . '/projects/' . $this->target->folder->project_id;
        }

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
        return $this->target->uploads()->create([
            'file_name' => $this->name,
            'path' => $this->path,
            'size' => $this->uploadedFile->getClientSize()
        ]);
    }
}