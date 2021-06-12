<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class FileUploader
 */
class FileUploader
{
    /**
     * @var string
     */
    protected string $targetDirectory;
    /**
     * @var SluggerInterface
     */
    protected SluggerInterface $slugger;

    /**
     * FileUploader constructor.
     * @param string $targetDirectory
     * @param SluggerInterface $slugger
     */
    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $file
     * @param string|null $fileName
     * @return string
     */
    public function upload(UploadedFile $file, string $directory = '', string $fileName = null): string
    {
        if (!$fileName) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        } else {
            $fileName = $this->slugger->slug($fileName) . '.' . $file->guessExtension();
        }
        $file->move($this->getTargetDirectory() . $directory, $fileName);

        return $directory . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
