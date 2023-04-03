<?php

namespace App\Entity;

use App\Repository\AppListTableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppListTableRepository::class)
 */
class AppListTable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $app_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $developer;

    /**
     * @ORM\Column(type="integer")
     */
    private $download_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_reviews_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apk_file_link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppName(): ?string
    {
        return $this->app_name;
    }

    public function setAppName(string $app_name): self
    {
        $this->app_name = $app_name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDeveloper(): ?string
    {
        return $this->developer;
    }

    public function setDeveloper(string $developer): self
    {
        $this->developer = $developer;

        return $this;
    }

    public function getDownloadCount(): ?int
    {
        return $this->download_count;
    }

    public function setDownloadCount(int $download_count): self
    {
        $this->download_count = $download_count;

        return $this;
    }

    public function getUserReviewsId(): ?int
    {
        return $this->user_reviews_id;
    }

    public function setUserReviewsId(int $user_reviews_id): self
    {
        $this->user_reviews_id = $user_reviews_id;

        return $this;
    }

    public function getApkFileLink(): ?string
    {
        return $this->apk_file_link;
    }

    public function setApkFileLink(string $apk_file_link): self
    {
        $this->apk_file_link = $apk_file_link;

        return $this;
    }
}
