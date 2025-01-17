#!/usr/bin/env php
<?= password_hash(stream_get_contents(STDIN), PASSWORD_DEFAULT);