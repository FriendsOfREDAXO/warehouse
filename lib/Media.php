<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_media;
use rex_string;

/**
 * Media class for consistent image output with REDAXO Media Manager integration.
 * 
 * Extends rex_media to provide fluent interface for generating img tags with proper
 * width/height from cached images and support for Media Manager profiles.
 * 
 * @example
 * <?= Media::get('filename.jpg')->setAlt("Product")->setClass("img-fluid")->getImg('warehouse-article-list') ?>
 */
class Media extends rex_media
{
    protected $attributes = [];
    protected $alt;

    /**
     * Factory method to create a Media instance.
     * 
     * @param string $filename The filename of the media
     * @return self|null Returns Media instance or null if file doesn't exist
     * @api
     */
    public static function get($filename)
    {
        $media = parent::get($filename);
        if ($media instanceof rex_media) {
            // Convert rex_media to Media instance
            $instance = new self();
            // Copy properties from rex_media
            foreach (get_object_vars($media) as $key => $value) {
                $instance->$key = $value;
            }
            return $instance;
        }
        return null;
    }

    /**
     * Set multiple HTML attributes at once.
     * 
     * @param array $attributes Associative array of attribute => value pairs
     * @return self
     * @api
     */
    public function setAttribute(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Set the class attribute (convenience method).
     * 
     * @param string $class CSS class(es)
     * @return self
     * @api
     */
    public function setClass(string $class)
    {
        $this->attributes['class'] = $class;
        return $this;
    }

    /**
     * Set the alt text for the image.
     * 
     * @param string $alt Alternative text
     * @return self
     * @api
     */
    public function setAlt(string $alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * Generate alt text automatically if not set.
     * 
     * @return string
     */
    protected function generateAlt()
    {
        if ($this->alt) {
            return $this->alt;
        }
        return $this->getTitle() ?: $this->getFileName();
    }

    /**
     * Get image as attributes array for use with rex_string::buildAttributes.
     * 
     * @param string $type Media Manager profile/type name (e.g., 'warehouse-article-list')
     * @return array Associative array of img tag attributes
     * @api
     */
    public function getImgAsAttributesArray(string $type = ''): array
    {
        $img = $this->attributes;
        
        // Set src based on profile/type
        if ($type !== '') {
            $img['src'] = \rex_url::frontend('media/' . $type . '/' . $this->getFileName());
            
            // Try to get dimensions from cached file
            $cacheFile = \rex_path::addonCache('media_manager', $type . '/' . $this->getFileName());
            if (file_exists($cacheFile)) {
                $imageInfo = @getimagesize($cacheFile);
                if ($imageInfo !== false) {
                    $img['width'] = $imageInfo[0];
                    $img['height'] = $imageInfo[1];
                }
            }
        } else {
            // Use original media URL without profile
            $img['src'] = \rex_url::media($this->getFileName());
            $img['width'] = $this->getWidth();
            $img['height'] = $this->getHeight();
        }

        // Always set alt text
        $img['alt'] = $this->generateAlt();

        return $img;
    }

    /**
     * Generate complete img tag with attributes.
     * 
     * @param string $type Media Manager profile/type name (e.g., 'warehouse-article-list')
     * @return string Complete HTML img tag
     * @api
     */
    public function getImg(string $type = ''): string
    {
        $img = $this->getImgAsAttributesArray($type);
        $attributes = rex_string::buildAttributes($img);
        return '<img ' . $attributes . ' />';
    }
}
