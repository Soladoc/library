<?php
require_once 'auth.php';
require_once 'redirect.php';

redirect_to(Auth\location_home());
