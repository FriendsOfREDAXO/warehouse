<?php

/**
 * @var rex_addon $this
 */

rex_response::sendRedirect(rex_url::backendPage('/warehouse/settings/log'), \rex_response::HTTP_MOVED_TEMPORARILY);
