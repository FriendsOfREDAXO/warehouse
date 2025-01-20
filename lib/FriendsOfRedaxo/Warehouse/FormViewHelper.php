<?php

namespace FriendsOfRedaxo\Warehouse;


use rex_clang;
use rex_form;

class FormViewHelper
{
    public static function clangTabs($type, $key = null, $curClang = null, $uid = ''): string
    {
        $nav = null;
        $clangCount = '';
        if ($type == 'wrapper') {
            $nav = [];
            foreach (rex_clang::getAll() as $clang) {
                $nav[$clang->getId()] = $clang->getName();
            }
            $clangCount = ' clangCount'. rex_clang::count();
        }
        return self::tabs($type, $key, $curClang, $uid, $nav, 'base-clangtabs' . $clangCount);
    }

    public static function tabs($type, $key = null, $curKey = null, $uid = '', $nav = null, $baseClass = 'base-tabs'): string
    {
        $output = '';
        switch ($type) {
            case 'wrapper':
                $output .= ('<div class="'.$baseClass.'"><ul class="nav nav-tabs" role="tablist">');
                if (is_array($nav)) {
                    foreach ($nav as $nKey => $value) {
                        $active = '';
                        if ($key == $nKey) {
                            $active = ' active';
                        }
                        $output .= ("<li role=\"presentation\" class=\"$active\"><a href=\"#pnl{$uid}{$nKey}\" aria-controls=\"home\" role=\"tab\" data-toggle=\"tab\">{$value}</a></li>");
                    }
                }
                $output .= ('</ul><div class="tab-content base-tabform">');
                break;

            case 'close_wrapper':
                $output .= ('</div></div>');
                break;

            case 'inner_wrapper':
                $active = '';
                if ($key == $curKey) {
                    $active = ' active';
                }
                $output .= ("\n\n\n<div id=\"pnl$uid$key\" role=\"tabpanel\" class=\"tab-pane $active\">\n");
                break;

            case 'close_inner_wrapper':
                $output .= ('</div>');
                break;
        }
        return $output;
    }

    /**
     * @param rex_form $form
     * @param $type
     * @param string $name
     * @param string $baseClass
     * @author Joachim Doerr
     */
    public static function addCollapsePanel(rex_form $form, $type, $name = '', $baseClass = 'base')
    {
        $in = '';
        switch ($type) {
            case 'wrapper':
                $keya = uniqid('a');
                $form->addRawField("<div class=\"panel-group '.$baseClass.'-panel\" id=\"$keya\">");
                break;
            case 'close_wrapper':
                $form->addRawField('</div>');
                break;
            case 'inner_wrapper_open':
                $in = 'in';
            case 'inner_wrapper':
                $keyp = uniqid('p');
                $keyc = uniqid('c');
                $form->addRawField("<div class=\"panel panel-default\" id=\"$keyp\"><div class=\"panel-heading\"><h4 class=\"panel-title\"><a data-toggle=\"collapse\" data-target=\"#$keyc\" href=\"#$keyc\" class=\"collapsed\">$name</a></h4></div> <div id=\"$keyc\" class=\"panel-collapse collapse$in\"><div class=\"panel-body\">");
                break;
            case 'close_inner_wrapper':
                $form->addRawField('</div></div></div>');
                break;
        }
    }
}