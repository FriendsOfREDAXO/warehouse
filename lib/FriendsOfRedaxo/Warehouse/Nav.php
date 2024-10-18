<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_category;
use rex_article;

class Nav {

    var $ulClasses = [' nav_block'];
    var $liClasses = [];
    var $liHasChildsClasses = [];
    var $currentPath;
    var $currentCat;
    var $maxLev = 999;
    var $currentCatId;
    var $ignoreOffline = true;
    var $fullTree = false; // ganzen Menübaum anzeigen, unabhängig von der aktuell gewählten Kategorie
    var $metaField = '';
    var $metaValue = '';
    var $activeClass = 'active';
    var $currentClass = 'current';
    var $startCategory;
    var $dataAttribute = array('data-dropdown-menu');
    var $showTopCategory = false; // Wiederholt die Oberkategorie in der darunterliegenden LI Liste
    var $showBackButton = false;
    var $breadcrumbWithHome = false;
    var $breadcrumbLastLink = false;
    var $additionalLi = '';
    var $backButtonText = '<li class="js-drilldown-back"><a>Zurück</a></li>';
    var $ulWrapper = [];
    var $func_ul_end = '';
    var $func_li_end = '';
    var $hasNavPoints = false;

    public function __construct() {
        $this->currentCat = rex_category::getCurrent();
        if (is_object($this->currentCat)) {
            $this->currentCatId = $this->currentCat->getId();
            $this->currentPath = explode('|', trim($this->currentCat->getPath(), '|') . '|' . $this->currentCatId);
            if (!is_array($this->currentPath))
                $this->currentPath = [];
            $this->startCategory = $this->currentPath[0] ? $this->currentPath[0] : $this->currentPath[1];
        }
    }

    public function getNavigation() {
        $out = '';
        $cssclass = (isset($this->ulClasses[0])) ? ' ' . $this->ulClasses[0] : '';
        $dataAttribute = (isset($this->dataAttribute[0])) ? ' ' . $this->dataAttribute[0] : '';
        $categories = rex_category::getRootCategories($this->ignoreOffline);
        if (!empty($categories)) {
            if (!empty($categories)) {
                $out .= '<ul class="lev-0' . $cssclass . '"' . $dataAttribute . '>';
                foreach ($categories as $key => $category) {
                    if ($this->metaField) {
                        if (!$this->filterNav($category))
                            continue;
                    }
                    $out .= $this->_getCategory($category);
                }
                $out .= $this->additionalLi;
                $out .= '</ul>';
                return $out;
            }
        }
    }

    public function getCategoryNav($category_id) {
        if (!$category_id)
            return '';
        $cssclass = (isset($this->ulClasses[0])) ? ' ' . $this->ulClasses[0] : '';
        $dataAttribute = (isset($this->dataAttribute[0])) ? ' ' . $this->dataAttribute[0] : '';
        $out = '';
        $lev = 0;
        $out .= '<ul class="lev-0 nav_block' . $cssclass . '"' . $dataAttribute . '>';
        $_categories = rex_category::get($category_id)->getChildren($this->ignoreOffline);
        foreach ($_categories as $_cat) {
            $out .= $this->_getCategory($_cat, $lev);
        }
        $out .= '</ul>';
        return $out;
    }

    private function filterNav($category) {
        $metaval = explode('|', trim($category->getValue($this->metaField), '|'));
        if (array_search($this->metaValue, $metaval) === false) {
            return false;
        }
        return true;
    }

    private function _getCategory($cat, $lev = 0) {
        $lev++;
        $out = '';
        $class = '';
        $cssclass = (isset($this->ulClasses[$lev])) ? ' ' . $this->ulClasses[$lev] : '';
        $dataAttribute = (isset($this->dataAttribute[$lev])) ? ' ' . $this->dataAttribute[$lev] : '';
        $_categories = $cat->getChildren($this->ignoreOffline);
        $li_end_text = '';
        if ($this->func_li_end) {
            $li_end_text = call_user_func($this->func_li_end, $cat);
        }


        if ($cat->getId() == $this->currentCatId) {
            $class = $this->currentClass;
        } elseif (is_array($this->currentPath) && in_array($cat->getId(), $this->currentPath)) {
            $class = $this->activeClass;
        }
        if (isset($this->liClasses[$lev - 1])) {
            $class .= ' ' . $this->liClasses[$lev - 1];
        }

        if ((!empty($_categories) || $li_end_text) && isset($this->liHasChildsClasses[$lev - 1])) {
            $class .= ' '.$this->liHasChildsClasses[$lev - 1];
        }

        $this->hasNavPoints = true;
        $out .= '
          <li class="' . $class . '">
            <a href="' . $cat->getUrl() . '" class="' . $class . '">' . $cat->getName() . '</a>';
        if (!empty($_categories) && ($this->fullTree || (is_array($this->currentPath) && in_array($cat->getId(), $this->currentPath)) && $lev < $this->maxLev
        )) {
            if (isset($this->ulWrapper[$lev]) && $this->ulWrapper[$lev]) {
                $out .= $this->ulWrapper[$lev][0];
            }
            $out .= '<ul class="lev-' . $lev . $cssclass . '"' . $dataAttribute . '>';

            if ($this->showBackButton) {
                $out .= $this->backButtonText;
            }


            // Wiederholt den übergeordneten Menüpunkt in der Liste
            if ($this->showTopCategory) {
                $out .= '
                <li class="topcategory">
               <a href="' . $cat->getUrl() . '">' . $cat->getName() . '</a>'
                        . '</li>';
            }

            foreach ($_categories as $_cat) {
                $out .= $this->_getCategory($_cat, $lev);
            }
            $out .= '</ul>';
            if (isset($this->ulWrapper[$lev]) && $this->ulWrapper[$lev]) {
                $out .= $this->ulWrapper[$lev][1];
            }
            if ($this->func_ul_end) {
                $out .= call_user_func($this->func_ul_end, $cat);
            }
        }
        $out .= $li_end_text;
        $out .= '</li>';
        return $out;
    }

    public function getBreadcrumb() {
        $out = '';
        if ($this->breadcrumbWithHome) {
            $out .= '<li><a href="' . rex_getUrl(rex_article::getSiteStartArticleId()) . '">' . rex_article::get(rex_article::getSiteStartArticleId())->getName() . '</a></li>';
        }
        if (rex_article::getCurrentId() == rex_article::getSiteStartArticleId()) {
            $out = '';
        }

        foreach ($this->currentPath as $i => $p) {
            $cat = rex_category::get($p);
            if (!is_object($cat)) {
                continue;
            }
            if (count($this->currentPath) == ($i + 1) && !$this->breadcrumbLastLink) {
                $out .= '<li class="without-link">';
                $out .= $cat->getName();
                $out .= '</li>';
            } else {
                $out .= '<li>';
                $out .= '<a href="' . $cat->getUrl() . '">';
                $out .= $cat->getName();
                $out .= '</a>';
                $out .= '</li>';
            }
        }
        return '<ul>' . $out . '</ul>';
    }

}
