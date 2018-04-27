<?php

session_start();
header('Location: ' . $_SESSION['raiz_html'] . 'home/');
