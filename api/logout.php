<?php
// Destroying the session
session_start();
session_destroy();
header('Location: /~tomanfi2');
die();