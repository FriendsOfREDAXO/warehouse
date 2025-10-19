<?php

namespace FriendsOfRedaxo\Warehouse;

class Media
{
    protected $path;
    protected $attributes = [];
    protected $profile;
    protected $alt;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function setAttribute(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    protected function getImageInfo()
    {
        // Für REDAXO: Bildprofil-basierte Größen werden vom Media Manager gesteuert
        // getimagesize funktioniert nur mit lokalen Pfaden, nicht mit URLs
        // Die Breite/Höhe werden hier nicht gesetzt, da sie durch das Bildprofil definiert werden
        return null;
    }

    protected function generateAlt()
    {
        // Automatischer Alt-Tag, falls keiner gesetzt.
        if ($this->alt) {
            return $this->alt;
        }
        return basename($this->path);
    }

    public function getImg()
    {
        $info = $this->getImageInfo();
        $attributes = $this->attributes;

        if ($info) {
            $attributes['width'] = $info['width'];
            $attributes['height'] = $info['height'];
        }

        $attributes['alt'] = $this->generateAlt();

        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }

        // In REDAXO wird das Bildprofil über die URL gesteuert
        // Format: /media/profile_name/filename.jpg
        // Das Profil kann später außerhalb des Addons konfiguriert werden
        $src = $this->path;
        if ($this->profile && !empty($this->profile)) {
            // Wenn ein Profil gesetzt ist, wird es in die URL eingebaut
            // Dies ermöglicht die Nutzung des REDAXO Media Managers
            // Die konkrete Implementierung hängt von der Media Manager Konfiguration ab
            // Für jetzt wird das Profil als Platzhalter dokumentiert
        }

        return '<img src="' . htmlspecialchars($src) . '"' . $attrString . ' />';
    }
}
